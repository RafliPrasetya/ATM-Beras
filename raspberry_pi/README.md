# ATM Beras — Raspberry Pi Setup Guide

Panduan lengkap untuk menjalankan sistem ATM Beras di Raspberry Pi.

---

## Prasyarat Hardware

| Komponen | Keterangan |
|---|---|
| Raspberry Pi 4 (2GB+) | Otak utama perangkat |
| RFID Reader RC522 | Terhubung via SPI ke GPIO |
| Numpad USB | Input pilihan kg (colok ke USB port Pi) |
| Monitor (HDMI) | Tampilan kiosk ATM |
| Motor Driver L298N + Motor DC | Menggerakkan dispenser beras |

---

## Langkah 1: Setup Raspberry Pi OS

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Install Python pip
sudo apt install python3-pip python3-venv -y

# Aktifkan SPI (untuk RFID RC522)
sudo raspi-config
# → Interface Options → SPI → Enable
```

---

## Langkah 2: Install Dependencies Python

```bash
cd ~/atm-beras/raspberry_pi

# Buat virtual environment
python3 -m venv venv
source venv/bin/activate

# Install packages
pip install -r requirements.txt
```

---

## Langkah 3: Konfigurasi Token & Server

Buat file `.env` di folder `raspberry_pi/`:

```bash
nano ~/atm-beras/raspberry_pi/.env
```

Isi dengan:
```env
ATM_SERVER_URL=https://atmberas.com
ATM_TOKEN=isi_token_dari_tabel_machine_tokens_di_database
ATM_MACHINE_ID=1
ATM_FLASK_PORT=8765
ATM_PIN_MOTOR=18
ATM_DETIK_PER_KG=3.0
```

> **Cara dapat token:** Login ke dashboard admin → menu Mesin → lihat token mesin yang sudah dibuat.

Lalu load env sebelum menjalankan script:
```bash
export $(cat .env | xargs)
```

---

## Langkah 4: Konfigurasi Halaman Kiosk di Laravel

Buka file `resources/views/kiosk/index.blade.php` di server Laravel dan update:

```javascript
const MACHINE_TOKEN = 'isi_token_yang_sama_dengan_.env_di_Pi';
const MACHINE_ID    = 1;  // samakan dengan ATM_MACHINE_ID
```

---

## Langkah 5: Jalankan Script Python

```bash
cd ~/atm-beras/raspberry_pi
source venv/bin/activate
python atm_beras_pi.py
```

Kamu akan melihat output seperti:
```
=======================================================
  ATM Beras — Raspberry Pi Controller
  Server : https://atmberas.com
  Mesin  : ID=1
  Flask  : localhost:8765
  GPIO   : Aktif
  RFID   : Aktif
=======================================================
[12:00:00] INFO — Flask server berjalan di http://localhost:8765
[12:00:00] INFO — Menunggu kartu RFID...
```

---

## Langkah 6: Buka Chromium Kiosk Mode

Buka terminal baru, jalankan:

```bash
chromium-browser \
  --kiosk \
  --no-sandbox \
  --disable-infobars \
  --disable-session-crashed-bubble \
  --app=https://atmberas.com/kiosk
```

Atau untuk mode development (server lokal):
```bash
chromium-browser --kiosk --app=http://localhost:8000/kiosk
```

---

## Langkah 7: Autostart saat Raspberry Pi Nyala

```bash
mkdir -p ~/.config/autostart

# Script startup
cat > ~/start_atm.sh << 'EOF'
#!/bin/bash
cd ~/atm-beras/raspberry_pi
export $(cat .env | xargs)
source venv/bin/activate
python atm_beras_pi.py &

sleep 5  # Tunggu Flask siap

chromium-browser \
  --kiosk \
  --no-sandbox \
  --app=https://atmberas.com/kiosk
EOF

chmod +x ~/start_atm.sh

# Autostart entry
cat > ~/.config/autostart/atm-beras.desktop << 'EOF'
[Desktop Entry]
Type=Application
Name=ATM Beras
Exec=/home/pi/start_atm.sh
EOF
```

---

## Wiring RFID RC522 ke Raspberry Pi

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
| "RPi.GPIO tidak tersedia" | Script jalan di PC (mode simulasi) — normal |
| Browser tampil "Tidak Terhubung ke Pi" | Pastikan `atm_beras_pi.py` sudah jalan dulu |
| Motor tidak bergerak | Cek wiring L298N + nilai `ATM_PIN_MOTOR` |
| RFID tidak terbaca | Cek SPI sudah diaktifkan (`raspi-config`) |
| "Token tidak valid" | Samakan token di `.env` dengan `machine_tokens` di DB |
