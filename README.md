# Pengembangan Sistem ATM Beras Berbasis Web dengan E-KTP Sebagai Kunci Akses Distribusi

Repository ini berisi source code Tugas Akhir yang berjudul **"Pengembangan Sistem ATM Beras Berbasis Web dengan E-KTP Sebagai Kunci Akses Distribusi"**, yang dikembangkan sebagai solusi distribusi beras otomatis untuk mustahik (penerima manfaat zakat).

---

## 📖 Deskripsi Proyek

Distribusi beras kepada mustahik yang selama ini dilakukan secara manual seringkali menghadapi berbagai kendala, seperti antrean panjang, ketidakakuratan pencatatan, serta sulitnya memantau stok dan riwayat pengambilan secara real-time. Proses manual ini juga rentan terhadap kesalahan administrasi dan memakan waktu yang cukup lama.

Sistem **ATM Beras** hadir sebagai solusi digital **berbasis web** yang mengotomatisasi seluruh proses distribusi beras. Sistem ini memanfaatkan **e-KTP (Kartu Tanda Penduduk Elektronik)** sebagai **kunci akses distribusi**, di mana chip RFID yang tertanam pada e-KTP digunakan untuk mengidentifikasi dan memverifikasi mustahik secara cepat dan akurat. Dengan pendekatan ini, mustahik cukup menempelkan e-KTP mereka pada reader untuk mengambil jatah beras secara mandiri, tanpa memerlukan kartu tambahan.

Sistem ini dibangun dengan pendekatan **tiga komponen utama** yang saling terintegrasi:

1. **Web Admin (Laravel)** — Dashboard untuk admin mengelola data mesin, mustahik, transaksi, berita, dan memantau seluruh operasional sistem melalui antarmuka web.
2. **Kiosk Interface** — Tampilan layar sentuh fullscreen yang berjalan di Raspberry Pi via Chromium, menjadi antarmuka langsung antara mustahik dan mesin ATM Beras.
3. **Raspberry Pi Controller (Python)** — Script yang menjalankan pembacaan chip RFID pada e-KTP, komunikasi dengan server Laravel via REST API, dan pengendalian motor dispenser beras melalui GPIO.

Selain itu, sistem ini juga dilengkapi dengan:
- **Landing Page** publik yang menampilkan informasi program dan berita terkait distribusi beras.
- **Notifikasi WhatsApp** otomatis untuk memberitahu mustahik terkait pengambilan beras.
- **Ekspor laporan PDF** untuk dokumentasi riwayat pengambilan beras.
- **Self-provisioning** pada Raspberry Pi, sehingga perangkat baru dapat dikonfigurasi secara otomatis tanpa intervensi teknis yang rumit.

---

## 👥 Fitur Berdasarkan Peran (Role)

### 🛡️ Admin (Web Admin)
- Login web admin (username & password)
- **Dashboard**: statistik jumlah mesin, mustahik aktif, total transaksi, dan grafik distribusi beras
- **Manajemen Mesin** (CRUD): tambah, edit, hapus mesin — termasuk pengelolaan wilayah (provinsi → kabupaten → kecamatan → kelurahan), penjadwalan operasional, dan toggle status mesin (aktif/nonaktif/maintenance)
- **Manajemen Mustahik** (CRUD): tambah, edit, hapus data penerima manfaat — lengkap dengan UID e-KTP (RFID), NIK, alamat, jatah beras, dan status aktif/nonaktif
- **Tambah Jatah Beras**: menambahkan kuota beras mustahik secara manual
- **Riwayat Pengambilan**: melihat seluruh riwayat transaksi pengambilan beras per mustahik
- **Ekspor PDF**: mengunduh laporan riwayat pengambilan per mustahik dan laporan pengambilan keseluruhan
- **Manajemen Berita** (CRUD): membuat, mengedit, dan menghapus berita/artikel yang ditampilkan di landing page
- **Manajemen Admin**: mengelola akun administrator sistem
- **Notifikasi WhatsApp**: pengiriman notifikasi otomatis ke mustahik via WhatsApp (Fonnte API)

### 📺 Kiosk (Tampilan Mesin ATM Beras)
- Tampilan fullscreen di monitor Raspberry Pi (Chromium Kiosk Mode)
- Menampilkan informasi identitas mustahik setelah e-KTP di-tap pada reader
- Pilihan jumlah beras yang ingin diambil (dalam kilogram) via numpad
- Indikator status mesin (online/offline, stok tersedia)
- Komunikasi real-time dengan Raspberry Pi melalui Flask local server

### 🔌 Raspberry Pi (IoT Controller)
- Membaca chip RFID pada e-KTP (mendukung 3 mode: **RC522** via GPIO, **USB RFID Reader**, dan **Simulasi**)
- Validasi UID e-KTP ke server Laravel via REST API
- Mengendalikan **motor DC dispenser** beras melalui driver L298N + GPIO
- Menjalankan **Flask local server** sebagai jembatan komunikasi antara Chromium Kiosk dan hardware
- **Self-provisioning**: otomatis mendaftarkan diri ke server dan mendapatkan token saat pertama kali dinyalakan
- Update stok beras otomatis ke server setelah transaksi

### 🌐 Landing Page (Publik)
- Halaman informasi publik tentang program ATM Beras
- Menampilkan berita/artikel terbaru dari admin
- Fitur **Cek Penerima**: masyarakat dapat mengecek apakah mereka terdaftar sebagai mustahik

---

## 🧰 Teknologi yang Digunakan

### Backend & Web Admin (Laravel)
| Kategori | Teknologi |
|---|---|
| Framework | Laravel 12 (PHP 8.2+) |
| Basis Data | MySQL |
| Autentikasi Web | Laravel Breeze (Session-Based) |
| Frontend Web | Blade, Bootstrap 5, Tailwind CSS, Alpine.js |
| Build Tool | Vite |
| Ekspor PDF | barryvdh/laravel-dompdf |
| Notifikasi WA | Fonnte API (WhatsApp Gateway) |
| Interaksi UI | SweetAlert2, Bootstrap Icons |

### Raspberry Pi (Python)
| Kategori | Teknologi |
|---|---|
| Bahasa | Python 3 |
| Web Server Lokal | Flask 3.0 + Flask-CORS |
| HTTP Client | Requests |
| Manajemen Konfigurasi | python-dotenv |
| RFID Reader (e-KTP) | mfrc522 (RC522 via SPI) / USB HID |
| GPIO Control | RPi.GPIO |

### Hardware (Perangkat Keras)
| Komponen | Fungsi |
|---|---|
| Raspberry Pi 4 (2GB+) | Otak utama perangkat ATM |
| RFID Reader RC522 / USB RFID | Membaca chip RFID pada e-KTP mustahik |
| Numpad USB | Input pilihan kilogram beras |
| Monitor HDMI | Menampilkan antarmuka kiosk |
| Motor Driver L298N + Motor DC | Menggerakkan dispenser beras |

---

## 🗄️ Struktur Database (MySQL)

| Tabel | Deskripsi |
|---|---|
| `admins` | Data administrator sistem (nama, username, email, password) |
| `provinces` | Master data provinsi |
| `regencies` | Master data kabupaten/kota (FK → provinces) |
| `districts` | Master data kecamatan (FK → regencies) |
| `villages` | Master data kelurahan/desa (FK → districts) |
| `machines` | Data mesin ATM Beras (kode mesin, lokasi, status, stok beras, jadwal operasional) |
| `machine_tokens` | Token autentikasi Bearer untuk setiap perangkat Raspberry Pi |
| `mustahiks` | Data penerima manfaat/mustahik (nama, UID e-KTP, NIK, no HP, alamat, jatah beras, status) |
| `transactions` | Riwayat transaksi pengambilan beras (mustahik, mesin, jumlah gram, tanggal) |
| `news` | Berita/artikel yang ditampilkan di landing page |
| `api_logs` | Log aktivitas API dari perangkat Raspberry Pi |

**Relasi utama:**
- `machines` → `villages` (lokasi mesin)
- `mustahiks` → `villages` (alamat mustahik)
- `transactions` → `mustahiks` + `machines` (riwayat pengambilan)
- `machine_tokens` → `machines` (autentikasi perangkat)

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

### Persyaratan Umum

- **Git**
- **PHP** ^8.2
- **Composer**
- **MySQL** (disarankan via Laragon/XAMPP untuk lokal)
- **Node.js** & **npm** (untuk build asset frontend)
- **Python 3** & **pip** (untuk Raspberry Pi controller)

### 1. Clone Repository

```bash
git clone https://github.com/RafliPrasetya/ATM-Beras.git
cd ATM-Beras
```

Struktur folder setelah clone:

```
ATM-Beras/
├── app/                 # Laravel — Controllers, Models, Services
├── database/            # Migrations & Seeders
├── resources/views/     # Blade Templates (Admin, Kiosk, Landing)
├── routes/              # Web & API Routes
├── raspberry_pi/        # Python Script untuk Raspberry Pi
├── public/              # Assets publik
└── README.md
```

---

### 2. Setup Backend & Web Admin (Laravel)

#### a. Install dependency PHP

```bash
composer install
```

#### b. Salin file environment

```bash
cp .env.example .env
```

#### c. Konfigurasi `.env`

Sesuaikan bagian berikut pada file `.env`:

```env
APP_NAME="ATM Beras"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=atm_beras
DB_USERNAME=root
DB_PASSWORD=
```

> **Catatan:** Sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` dengan konfigurasi MySQL di komputer Anda. Pastikan database dengan nama pada `DB_DATABASE` sudah dibuat terlebih dahulu (bisa lewat phpMyAdmin/HeidiSQL/Laragon).

#### d. Generate application key

```bash
php artisan key:generate
```

#### e. Jalankan migrasi & seeder database

```bash
php artisan migrate:fresh --seed
```

Perintah ini akan membuat seluruh tabel dan mengisi data awal (akun admin, data wilayah, data transaksi uji coba).

#### f. Install dependency frontend & build assets

```bash
npm install
npm run build
```

#### g. Jalankan server lokal

```bash
php artisan serve
```

Aplikasi web admin dapat diakses di `http://127.0.0.1:8000`.

> **Alternatif:** Jika menggunakan Laragon, cukup jalankan proyek di dalam folder `www/` Laragon lalu akses melalui virtual host yang dikonfigurasi (misalnya `http://atm-beras.test`).

> **Mode Development:** Untuk menjalankan server, queue, dan Vite secara bersamaan:
> ```bash
> composer dev
> ```

---

### 3. Setup Raspberry Pi

Panduan lengkap untuk setup Raspberry Pi tersedia di **[`raspberry_pi/README.md`](raspberry_pi/README.md)**, mencakup:
- Prasyarat hardware
- Instalasi OS & dependencies Python
- Konfigurasi `.env` (hanya perlu isi `ATM_SERVER_URL` dan `ATM_MACHINE_CODE`)
- Pemilihan mode pembacaan e-KTP (RC522 / USB / Simulasi)
- Menjalankan script & self-provisioning otomatis
- Setup autostart saat Pi dinyalakan
- Wiring diagram RFID RC522
- Troubleshooting

---

### 4. Akun Uji Coba (Hasil Seeder)

| Role | Username / Email | Password |
|---|---|---|
| Admin | `admin` / `admin@gmail.com` | `admin123` |

> Lihat `AdminSeeder.php` pada folder `database/seeders/` untuk detail akun uji.

---

## 📂 Struktur Direktori Repository

```
ATM-Beras/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                    # Controller API untuk Raspberry Pi
│   │   │   │   ├── MachineController   # Status & update stok mesin
│   │   │   │   ├── ProvisionController # Self-provisioning perangkat
│   │   │   │   ├── RfidController      # Validasi e-KTP (RFID)
│   │   │   │   ├── SystemController    # Heartbeat & health check
│   │   │   │   └── TransactionController # Proses transaksi beras
│   │   │   ├── AdminController         # CRUD admin
│   │   │   ├── AuthController          # Login & logout
│   │   │   ├── DashboardController     # Statistik dashboard
│   │   │   ├── KioskController         # Tampilan kiosk ATM
│   │   │   ├── LandingController       # Landing page & cek penerima
│   │   │   ├── MachineController       # CRUD mesin (web admin)
│   │   │   ├── MustahikController      # CRUD mustahik + laporan
│   │   │   └── NewsController          # CRUD berita
│   │   ├── Middleware/                 # Auth middleware (admin, machine token)
│   │   ├── Requests/                   # Form Request validasi
│   │   └── Resources/                  # API Resources
│   ├── Models/
│   │   ├── Admin, Machine, MachineToken
│   │   ├── Mustahik, Transaction, News
│   │   ├── Province, Regency, District, Village
│   │   └── ApiLog
│   └── Services/
│       ├── ApiLogService               # Logging aktivitas API
│       ├── RfidValidationService       # Logika validasi e-KTP
│       ├── TransactionService          # Logika proses transaksi
│       └── WhatsAppService             # Integrasi notifikasi WA
│
├── database/
│   ├── migrations/                     # Skema tabel database
│   └── seeders/
│       ├── AdminSeeder                 # Akun admin default
│       ├── WilayahSeeder               # Data provinsi s/d kelurahan
│       └── TransactionSeeder           # Data transaksi uji coba
│
├── resources/views/
│   ├── admin/                          # Halaman web admin
│   │   ├── mesin/                      # Kelola mesin ATM
│   │   ├── mustahik/                   # Kelola mustahik & laporan
│   │   ├── berita/                     # Kelola berita
│   │   └── admin/                      # Kelola akun admin
│   ├── kiosk/                          # Tampilan kiosk fullscreen
│   ├── landing/                        # Landing page publik
│   ├── layouts/                        # Template layout
│   └── partials/                       # Komponen reusable
│
├── routes/
│   ├── web.php                         # Route web admin, kiosk, landing
│   └── api.php                         # Route API untuk Raspberry Pi
│
├── raspberry_pi/                       # Script IoT Raspberry Pi
│   ├── atm_beras_pi.py                 # Controller utama (RFID + Flask + GPIO)
│   ├── requirements.txt                # Dependencies Python
│   ├── .env.example                    # Template konfigurasi
│   └── README.md                       # Panduan setup Pi
│
└── README.md
```

---

## 🔗 Alur Kerja Sistem (System Flow)

```
┌──────────────────────────────────────────────────────────┐
│                    ADMIN (Web Browser)                    │
│  Kelola Mesin → Kelola Mustahik → Monitoring Dashboard   │
└──────────────────┬───────────────────────────────────────┘
                   │ HTTP
                   ▼
┌──────────────────────────────────────────────────────────┐
│              SERVER LARAVEL (REST API + Web)              │
│  • Autentikasi Admin (Session)                           │
│  • Autentikasi Mesin (Bearer Token)                      │
│  • CRUD Data (Mesin, Mustahik, Transaksi, Berita)        │
│  • Validasi e-KTP & Proses Transaksi                     │
│  • Notifikasi WhatsApp                                   │
│  • Ekspor Laporan PDF                                    │
└──────────────────┬───────────────────────────────────────┘
                   │ REST API
                   ▼
┌──────────────────────────────────────────────────────────┐
│              RASPBERRY PI (Python Controller)             │
│  ┌────────────┐  ┌──────────┐  ┌───────────────────┐    │
│  │ e-KTP Reader│→│ Flask    │→│ Motor Dispenser    │    │
│  │ (RC522/USB) │  │ Server   │  │ (L298N + Motor DC) │    │
│  └────────────┘  └──────────┘  └───────────────────┘    │
│        ↕              ↕                                   │
│  ┌──────────────────────────┐                            │
│  │ Chromium Kiosk (Browser) │                            │
│  │ Tampilan layar mesin ATM │                            │
│  └──────────────────────────┘                            │
└──────────────────────────────────────────────────────────┘
```

**Alur Pengambilan Beras:**
1. Mustahik menempelkan **e-KTP** pada reader
2. Raspberry Pi membaca UID chip RFID e-KTP → validasi ke server Laravel
3. Server mengecek identitas, status, dan sisa jatah beras
4. Jika valid → tampilkan data mustahik di layar kiosk
5. Mustahik memilih jumlah beras (kg) via numpad
6. Server memproses transaksi → kurangi jatah → catat riwayat
7. Raspberry Pi mengaktifkan motor dispenser → beras keluar
8. Notifikasi WhatsApp dikirim ke mustahik (opsional)

---

## 👨‍💻 Pengembang

| | |
|---|---|
| **Nama** | Rafli Prasetya |
| **Program Studi** | Sarjana Terapan Teknologi Rekayasa Perangkat Lunak |
| **Institusi** | Politeknik Negeri Banyuwangi |

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan Tugas Akhir (skripsi) di Politeknik Negeri Banyuwangi. Penggunaan di luar keperluan akademik mohon menghubungi pengembang terlebih dahulu.
