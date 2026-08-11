# ATM Beras — Raspberry Pi Setup Guide

Panduan lengkap untuk menjalankan sistem ATM Beras di Raspberry Pi.

---

## Prasyarat Hardware

| Komponen | Keterangan |
|---|---|
| Raspberry Pi 4 (2GB+) | Otak utama perangkat |
| RFID Reader RC522 | Terhubung via SPI ke GPIO (opsional, tergantung mode) |
| USB RFID Reader | Alternatif RC522, colok langsung ke USB |
| Numpad USB | Input pilihan kg |
| Monitor (HDMI) | Tampilan kiosk ATM |
| Motor Driver L298N + Motor DC | Menggerakkan dispenser beras |

---

## Langkah 1: Daftarkan Mesin di Web Admin (Dilakukan Oleh Admin)

Sebelum setup Raspberry Pi, admin harus mendaftarkan mesin terlebih dahulu:

1. Login ke dashboard admin
2. Buka menu **Manajemen Mesin**
3. Klik **Tambah Mesin**
4. Isi data mesin (kode mesin, lokasi, dll)
5. Catat **Kode Mesin** (misal: `001`) — ini yang akan dipakai di Raspberry Pi

> [!IMPORTANT]
> Mesin **harus sudah terdaftar di Web Admin** sebelum menjalankan script di Raspberry Pi.
> Token tidak perlu dibuat manual — akan dibuat otomatis saat Pi pertama kali dinyalakan.

---

## Langkah 2: Setup Raspberry Pi OS

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Install Python pip dan venv
sudo apt install python3-pip python3-venv -y

# (Jika menggunakan RFID RC522) Aktifkan SPI
sudo raspi-config
# → Interface Options → SPI → Enable → Reboot
```

---

## Langkah 3: Transfer File ke Raspberry Pi

Dari komputer, copy folder `raspberry_pi/` ke Pi:

```bash
scp -r raspberry_pi/ pi@<IP_RASPBERRY_PI>:~/atm-beras/
```

Atau clone langsung di Pi:

```bash
git clone <url-repo> ~/atm-beras
```

---

## Langkah 4: Install Dependencies Python

```bash
cd ~/atm-beras/raspberry_pi

# Buat virtual environment
python3 -m venv venv
source venv/bin/activate

# (Jika menggunakan RFID RC522) Uncomment library di requirements.txt dulu:
# nano requirements.txt
# Hapus tanda # pada baris: mfrc522 dan RPi.GPIO

# Install semua packages
pip install -r requirements.txt
```

---

## Langkah 5: Buat File Konfigurasi `.env`

```bash
cd ~/atm-beras/raspberry_pi

# Salin template
cp .env.example .env

# Edit konfigurasi
nano .env
```

Isi **hanya 2 field wajib** ini:

```env
# URL server Laravel (wajib)
ATM_SERVER_URL=https://namadomain-kamu.com

# Kode mesin — HARUS sudah terdaftar di Web Admin (wajib)
ATM_MACHINE_CODE=001

# ── Baris di bawah ini JANGAN diisi — akan terisi otomatis ──────────
ATM_TOKEN=
ATM_MACHINE_ID=

# ── Opsional (ubah jika perlu) ────────────────────────────────────────
ATM_FLASK_PORT=8765
ATM_PIN_MOTOR=18
ATM_DETIK_PER_KG=3.0
```

> [!NOTE]
> `ATM_TOKEN` dan `ATM_MACHINE_ID` **tidak perlu diisi manual**.
> Script akan mengisinya secara otomatis saat pertama kali dijalankan (self-provisioning).

---

## Langkah 6: Pilih Mode RFID

Buka `atm_beras_pi.py` dan sesuaikan mode RFID di baris paling atas:

```python
# Pilih salah satu:
RFID_MODE = "rc522"      # Sensor RC522 via GPIO (kabel jumper)
RFID_MODE = "usb"        # USB RFID Reader (plug-and-play)
RFID_MODE = "simulation" # Simulasi otomatis (untuk testing tanpa hardware)
```

---

## Langkah 7: Jalankan Script Python

```bash
cd ~/atm-beras/raspberry_pi
source venv/bin/activate
python atm_beras_pi.py
```

Saat **pertama kali** dijalankan, kamu akan melihat proses self-provisioning:

```
=======================================================
  ATM Beras — Raspberry Pi Controller
  Server       : https://namadomain.com
  Machine Code : 001
  Flask        : localhost:8765
  GPIO         : Aktif
  RFID         : Aktif
=======================================================
[INFO] [PROVISION] Token belum tersedia, memulai auto-provisioning...
[INFO] [PROVISION] Memulai provisioning untuk mesin '001'...
[INFO] [PROVISION] ✅ Berhasil! Machine ID=1, Token=***AbCdEfGh
[INFO] [ENV] Tersimpan: ATM_TOKEN=***
[INFO] [ENV] Tersimpan: ATM_MACHINE_ID=1
=======================================================
  Machine ID   : 1
  Token        : ***AbCdEfGh
=======================================================
[INFO] Flask server berjalan di http://localhost:8765
[INFO] Menunggu kartu RFID...
```

> Saat **restart berikutnya**, provisioning akan dilewati otomatis karena token sudah tersimpan di `.env`.

---

## Langkah 8: Buka Chromium Kiosk Mode

Buka terminal baru (jangan tutup terminal Python), jalankan:

```bash
chromium-browser \
  --kiosk \
  --no-sandbox \
  --disable-infobars \
  --disable-session-crashed-bubble \
  --app=https://namadomain.com/kiosk
```

---

## Langkah 9: Autostart saat Raspberry Pi Nyala

Buat script startup:

```bash
cat > ~/start_atm.sh << 'EOF'
#!/bin/bash
cd ~/atm-beras/raspberry_pi
source venv/bin/activate
python atm_beras_pi.py &

# Tunggu Flask siap (self-provisioning bisa butuh beberapa detik)
sleep 8

chromium-browser \
  --kiosk \
  --no-sandbox \
  --disable-infobars \
  --app=https://namadomain.com/kiosk
EOF

chmod +x ~/start_atm.sh
```

Daftarkan ke autostart:

```bash
mkdir -p ~/.config/autostart

cat > ~/.config/autostart/atm-beras.desktop << 'EOF'
[Desktop Entry]
Type=Application
Name=ATM Beras
Exec=/home/pi/start_atm.sh
EOF
```

---

## Wiring RFID RC522 ke Raspberry Pi (Jika Mode rc522)

| RC522 Pin | Raspberry Pi GPIO (BCM) |
|---|---|
| SDA/CS | GPIO 8 (CE0) |
| SCK | GPIO 11 (SCLK) |
| MOSI | GPIO 10 |
| MISO | GPIO 9 |
| IRQ | Tidak digunakan |
| GND | GND |
| RST | GPIO 25 |
| 3.3V | 3.3V |

---

## Troubleshooting

| Masalah | Solusi |
|---|---|
| `ValueError: invalid literal for int` | Kosongkan `ATM_MACHINE_ID=` di `.env` (jangan diisi 0 atau apapun) |
| `[PROVISION] ❌ Gagal (HTTP 404)` | Pastikan `ATM_MACHINE_CODE` sudah terdaftar di Web Admin |
| `[PROVISION] ❌ Tidak bisa terhubung` | Cek `ATM_SERVER_URL` dan koneksi internet Pi |
| Browser tampil "Tidak Terhubung ke Pi" | Pastikan `atm_beras_pi.py` sudah jalan sebelum Chromium dibuka |
| Motor tidak bergerak | Cek wiring L298N + nilai `ATM_PIN_MOTOR` di `.env` |
| RFID tidak terbaca (RC522) | Cek SPI sudah diaktifkan via `raspi-config` |
| "RPi.GPIO tidak tersedia" | Normal jika dijalankan di PC — mode simulasi aktif otomatis |

---

## Reset Token (Jika Diperlukan)

Jika token bermasalah atau perlu ganti mesin:

```bash
# Edit .env, kosongkan token dan machine ID
nano ~/atm-beras/raspberry_pi/.env

# Ubah menjadi:
# ATM_TOKEN=
# ATM_MACHINE_ID=
# (ATM_MACHINE_CODE ganti sesuai kebutuhan)

# Restart script — provisioning akan jalan ulang otomatis
python atm_beras_pi.py
```
