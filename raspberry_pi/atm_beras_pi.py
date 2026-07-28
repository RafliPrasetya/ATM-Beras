"""
ATM Beras — Raspberry Pi Controller
====================================
Script utama yang dijalankan di Raspberry Pi.

Menjalankan DUA thread secara bersamaan:
  1. Thread RFID  : Membaca kartu RFID dan memanggil Laravel API
  2. Thread Flask : Server lokal (localhost:8765) sebagai jembatan
                    komunikasi dengan Chromium Kiosk (browser)

Alur:
  [RFID Reader] → baca UID → POST /api/rfid/validate
       ↓ (sukses) → update state → Chromium baca via GET /state
  [Chromium] → user pilih kg + Enter → POST /api/transaction/process
       ↓ (sukses) → POST localhost:8765/activate-motor
  [Flask] → terima → aktifkan GPIO motor dispenser

Setup:
  pip install -r requirements.txt
  python atm_beras_pi.py
"""

import os
import time
import threading
import logging

import requests
from flask import Flask, jsonify, request
from flask_cors import CORS

# Mode RFID yang digunakan:
#   "rc522"      : Menggunakan sensor RC522 (GPIO SPI / kabel jumper)
#   "usb"        : Menggunakan USB RFID Reader (Keyboard Emulator / colok port USB)
#   "simulation" : Simulasi otomatis kartu dummy
RFID_MODE = "usb"

# ─── Coba import library GPIO (untuk motor dispenser) ────────────────────────
try:
    if RFID_MODE == "simulation":
        raise ImportError
    import RPi.GPIO as GPIO
    GPIO_AVAILABLE = True
except ImportError:
    GPIO_AVAILABLE = False
    print("[WARN] RPi.GPIO tidak tersedia atau dinonaktifkan — motor akan disimulasikan")

# ─── Coba import library RFID RC522 (hanya untuk mode rc522) ──────────────────
try:
    if RFID_MODE != "rc522":
        raise ImportError
    from mfrc522 import SimpleMFRC522  # type: ignore
    RFID_AVAILABLE = True
except ImportError:
    RFID_AVAILABLE = False
    print("[WARN] RFID RC522 dinonaktifkan atau library tidak tersedia")



# ─── LOGGING ─────────────────────────────────────────────────────────────────
logging.basicConfig(
    level=logging.INFO,
    format="[%(asctime)s] %(levelname)s — %(message)s",
    datefmt="%H:%M:%S",
)
log = logging.getLogger("atm-beras")

# ═══════════════════════════════════════════════════════════════════════════════
# KONFIGURASI — WAJIB DIISI SESUAI DATABASE
# ═══════════════════════════════════════════════════════════════════════════════
SERVER_URL    = os.getenv("ATM_SERVER_URL",   "http://127.0.0.1:8000")    # URL Laravel (ganti ke URL hosting saat deploy)
MACHINE_TOKEN = os.getenv("ATM_TOKEN",        "IXvt2v9OxyZyMkWbQOBXRmfpDbgdGtsSjQzcMww7KUCTm9AzZteL7w9GKNyTN81f")  # dari tabel machine_tokens
MACHINE_ID    = int(os.getenv("ATM_MACHINE_ID", "1"))                   # ID mesin di DB
FLASK_PORT    = int(os.getenv("ATM_FLASK_PORT", "8765"))                # Port lokal Flask

# GPIO — pin yang digunakan (BCM numbering)
PIN_MOTOR     = int(os.getenv("ATM_PIN_MOTOR", "18"))   # Pin motor dispenser
DETIK_PER_KG  = float(os.getenv("ATM_DETIK_PER_KG", "3.0"))  # Durasi motor per kg

API_BASE    = f"{SERVER_URL.rstrip('/')}/api"
API_HEADERS = {
    "Authorization": f"Bearer {MACHINE_TOKEN}",
    "Content-Type":  "application/json",
    "Accept":        "application/json",
}

# ═══════════════════════════════════════════════════════════════════════════════
# STATE BERSAMA
# Dibaca oleh Chromium via GET /state
# Ditulis oleh thread RFID
# ═══════════════════════════════════════════════════════════════════════════════
_state_lock = threading.Lock()
state: dict = {
    "step":     "scan_rfid",   # scan_rfid | pilih_jumlah | motor_aktif | selesai
    "mustahik": None,          # dict: nama, nik, sisa_kuota, machine, allowed_options
    "rfid_uid": None,          # UID kartu yang sedang aktif
    "pesan":    "Menunggu kartu RFID...",
    "error":    None,          # Pesan error yang tampil di browser
}

def update_state(**kwargs):
    with _state_lock:
        state.update(kwargs)

def get_state():
    with _state_lock:
        return dict(state)

# ═══════════════════════════════════════════════════════════════════════════════
# SETUP GPIO
# ═══════════════════════════════════════════════════════════════════════════════
def setup_gpio():
    if not GPIO_AVAILABLE:
        return
    GPIO.setmode(GPIO.BCM)
    GPIO.setwarnings(False)
    GPIO.setup(PIN_MOTOR, GPIO.OUT, initial=GPIO.LOW)
    log.info(f"GPIO siap. Pin motor: BCM {PIN_MOTOR}")

def cleanup_gpio():
    if GPIO_AVAILABLE:
        GPIO.cleanup()

# ═══════════════════════════════════════════════════════════════════════════════
# MOTOR DISPENSER
# ═══════════════════════════════════════════════════════════════════════════════
def aktifkan_motor(jumlah_kg: int):
    """Aktifkan motor dispenser selama (jumlah_kg × DETIK_PER_KG) detik."""
    durasi = jumlah_kg * DETIK_PER_KG
    log.info(f"[MOTOR] Mulai — {jumlah_kg} kg, durasi {durasi:.1f} detik")
    update_state(step="motor_aktif", pesan=f"Mengeluarkan {jumlah_kg} kg beras...")

    if GPIO_AVAILABLE:
        GPIO.output(PIN_MOTOR, GPIO.HIGH)
        time.sleep(durasi)
        GPIO.output(PIN_MOTOR, GPIO.LOW)
    else:
        # Simulasi di PC
        log.info(f"[SIMULASI] Motor ON selama {durasi:.1f}s")
        time.sleep(durasi)
        log.info("[SIMULASI] Motor OFF")

    log.info("[MOTOR] Selesai")
    update_state(
        step="selesai",
        pesan=f"{jumlah_kg} kg beras berhasil dikeluarkan!",
    )

    # Setelah 10 detik, reset otomatis ke scan_rfid
    time.sleep(10)
    reset_state()

# ═══════════════════════════════════════════════════════════════════════════════
# RFID READER THREAD
# ═══════════════════════════════════════════════════════════════════════════════
def rfid_loop():
    """Loop utama: baca RFID → panggil API → update state."""
    log.info("Thread RFID dimulai")

    if RFID_MODE == "usb":
        log.info("[RFID] Mode USB aktif. Thread RFID dinonaktifkan, menunggu input dari browser...")
        while True:
            time.sleep(1)

    if RFID_AVAILABLE:
        reader = SimpleMFRC522()
    else:
        reader = None


    while True:
        current = get_state()

        # Hanya baca RFID jika sedang di step scan_rfid
        if current["step"] != "scan_rfid":
            time.sleep(0.3)
            continue

        try:
            # ── Baca UID ──────────────────────────────────────────────
            if RFID_AVAILABLE and reader:
                log.info("Menunggu kartu RFID...")
                rfid_id, _ = reader.read()   # Blocking sampai kartu ditempel
                rfid_uid = str(rfid_id).strip()
            else:
                # Simulasi: tunggu 5 detik lalu pakai UID dummy
                log.info("[SIMULASI] Menunggu 5s lalu scan UID dummy...")
                time.sleep(5)
                rfid_uid = "4586545862"       # UID simulasi

            log.info(f"Kartu terbaca: UID={rfid_uid}")
            update_state(error=None)

            # ── Validasi ke Laravel API ───────────────────────────────
            resp = requests.post(
                f"{API_BASE}/rfid/validate",
                json={"rfid_uid": rfid_uid, "machine_id": MACHINE_ID},
                headers=API_HEADERS,
                timeout=10,
            )
            data = resp.json()

            # Laravel ApiResponse menggunakan key 'status' (bukan 'success')
            if resp.status_code == 200 and data.get("status") is True:
                mustahik_data = data["data"]
                log.info(f"Validasi sukses: {mustahik_data.get('nama')}")
                update_state(
                    step="pilih_jumlah",
                    mustahik=mustahik_data,
                    rfid_uid=rfid_uid,
                    pesan=f"Selamat datang, {mustahik_data.get('nama')}",
                    error=None,
                )

                # Tunggu sampai browser selesai memproses (step berubah dari pilih_jumlah)
                # atau timeout 120 detik
                timeout = 120
                elapsed = 0
                while elapsed < timeout:
                    time.sleep(0.5)
                    elapsed += 0.5
                    s = get_state()
                    if s["step"] == "scan_rfid":
                        break  # Reset dipanggil → kembali ke loop awal

            else:
                pesan_error = data.get("message", "Kartu tidak dikenali")
                log.warning(f"Validasi gagal: {pesan_error}")
                update_state(error=pesan_error)
                time.sleep(2)  # Jeda sebelum scan berikutnya

        except requests.exceptions.Timeout:
            log.error("Timeout koneksi ke server")
            update_state(error="Server tidak merespons, coba lagi")
            time.sleep(3)

        except requests.exceptions.ConnectionError:
            log.error("Tidak dapat terhubung ke server")
            update_state(error="Tidak ada koneksi internet")
            time.sleep(5)

        except Exception as e:
            log.exception(f"Error tak terduga di RFID loop: {e}")
            time.sleep(2)

# ═══════════════════════════════════════════════════════════════════════════════
# FLASK LOCAL SERVER — Jembatan Python ↔ Chromium
# ═══════════════════════════════════════════════════════════════════════════════
app = Flask(__name__)
CORS(app)  # Izinkan request dari Chromium (beda origin: localhost vs server URL)

@app.after_request
def add_cors_headers(response):
    response.headers["Access-Control-Allow-Private-Network"] = "true"
    response.headers["Access-Control-Allow-Headers"] = "Content-Type, Authorization, Access-Control-Allow-Private-Network"
    response.headers["Access-Control-Allow-Methods"] = "GET, POST, OPTIONS"
    return response


@app.route("/state", methods=["GET"])
def api_get_state():
    """Chromium polling state ini setiap 500ms untuk update tampilan."""
    res = get_state()
    res["machine_token"] = MACHINE_TOKEN
    res["machine_id"] = MACHINE_ID
    res["rfid_mode"] = RFID_MODE
    return jsonify(res)

@app.route("/state-sync", methods=["POST"])
def api_state_sync():
    """Sinkronisasi state dari browser jika browser melakukan pembacaan RFID sendiri (mode USB)."""
    try:
        data = request.get_json(silent=True) or {}
        update_state(
            step=data.get("step", "scan_rfid"),
            rfid_uid=data.get("rfid_uid"),
            mustahik=data.get("mustahik"),
            pesan=data.get("pesan", "Sinkronisasi browser"),
            error=None
        )
        log.info(f"[FLASK] State disinkronkan dari browser: {data.get('step')}")
        return jsonify({"success": True})
    except Exception as e:
        log.error(f"[FLASK] Gagal sinkronisasi state: {e}")
        return jsonify({"success": False, "message": str(e)}), 500



@app.route("/activate-motor", methods=["POST"])
def api_activate_motor():
    """
    Dipanggil oleh Chromium SETELAH transaksi dikonfirmasi sukses oleh Laravel.
    Jalankan motor di thread terpisah agar tidak blocking response.
    """
    try:
        data      = request.get_json(silent=True) or {}
        jumlah_kg = int(data.get("jumlah_kg") or 0)

        if jumlah_kg <= 0:
            return jsonify({"success": False, "message": "jumlah_kg tidak valid"}), 400

        current = get_state()
        log.info(f"[FLASK] Perintah aktivasi motor: {jumlah_kg} kg (state saat ini: {current['step']})")

        # Jalankan motor terlepas dari step — otorisasi sudah dilakukan Laravel
        threading.Thread(target=aktifkan_motor, args=(jumlah_kg,), daemon=True).start()

        return jsonify({"success": True, "message": f"Motor diaktifkan untuk {jumlah_kg} kg"})

    except Exception as e:
        log.exception(f"[FLASK] Error saat aktivasi motor: {e}")
        # Tetap return 200 agar browser tidak gagal — transaksi DB sudah berhasil
        return jsonify({"success": False, "message": f"Error motor: {str(e)}"})


@app.route("/reset", methods=["POST"])
def api_reset():
    """Chromium memanggil ini saat user menekan Esc atau setelah result screen timeout."""
    log.info("[FLASK] Reset state ke scan_rfid")
    reset_state()
    return jsonify({"success": True})

@app.route("/health", methods=["GET"])
def api_health():
    """Health check sederhana."""
    return jsonify({
        "status":       "ok",
        "gpio":         GPIO_AVAILABLE,
        "rfid":         RFID_AVAILABLE,
        "machine_id":   MACHINE_ID,
    })

def reset_state():
    update_state(
        step="scan_rfid",
        mustahik=None,
        rfid_uid=None,
        pesan="Menunggu kartu RFID...",
        error=None,
    )

# ═══════════════════════════════════════════════════════════════════════════════
# MAIN
# ═══════════════════════════════════════════════════════════════════════════════
if __name__ == "__main__":
    log.info("=" * 55)
    log.info("  ATM Beras — Raspberry Pi Controller")
    log.info(f"  Server : {SERVER_URL}")
    log.info(f"  Mesin  : ID={MACHINE_ID}")
    log.info(f"  Flask  : localhost:{FLASK_PORT}")
    log.info(f"  GPIO   : {'Aktif' if GPIO_AVAILABLE else 'Simulasi'}")
    log.info(f"  RFID   : {'Aktif' if RFID_AVAILABLE else 'Simulasi'}")
    log.info("=" * 55)

    setup_gpio()

    try:
        # Thread 1: Baca RFID
        rfid_thread = threading.Thread(target=rfid_loop, name="rfid-loop", daemon=True)
        rfid_thread.start()

        # Thread 2: Flask server (blocking — jadi di main thread)
        log.info(f"Flask server berjalan di http://localhost:{FLASK_PORT}")
        app.run(
            host="127.0.0.1",
            port=FLASK_PORT,
            debug=False,
            use_reloader=False,
        )

    except KeyboardInterrupt:
        log.info("Dihentikan oleh user (Ctrl+C)")

    finally:
        cleanup_gpio()
        log.info("ATM Beras dihentikan.")
