<div align="center">

<img src="assets/images/logo-tutwuri-handayani.webp" alt="Logo" width="100" height="100" />

# CRM SDN 105382 Galang

**Sistem Informasi dan Manajemen Sekolah**

[![CI](https://github.com/USERNAME/appCRM-SDN105382/actions/workflows/ci.yml/badge.svg)](https://github.com/USERNAME/appCRM-SDN105382/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4-blue.svg)](https://www.php.net/)
[![Node Version](https://img.shields.io/badge/Node-%3E%3D18-green.svg)](https://nodejs.org/)

Sistem Informasi Terpadu SD Negeri 105382 Galang untuk manajemen data siswa, nilai akademik,
pengumuman, dan komunikasi real-time antara sekolah dengan orang tua murid.

[Fitur](#-fitur) • [Instalasi](#-instalasi) • [Dokumentasi](#-dokumentasi) • [Kontribusi](#-kontribusi)

</div>

---

## ✨ Fitur

### Untuk Admin
- 📊 Dashboard statistik lengkap
- 👥 Manajemen pengguna (admin, guru, orang tua)
- 🎓 Manajemen data siswa & kelas
- 📝 Input & kelola nilai akademik
- 📢 Buat pengumuman untuk wali murid
- 🖼️ Kelola galeri foto kegiatan sekolah
- 📈 Laporan & export data (CSV)
- 💬 Moderasi aspirasi orang tua

### Untuk Guru
- 🎓 Input nilai siswa di kelas yang diwalikan
- 📊 Monitoring perkembangan siswa
- 📢 Buat pengumuman kelas
- 💬 Komunikasi langsung dengan orang tua

### Untuk Orang Tua
- 👶 Pantau data & perkembangan anak
- 📝 Lihat rapor digital
- 📢 Terima pengumuman sekolah
- 💬 Chat real-time dengan guru
- 💭 Kirim aspirasi & masukan
- 📱 Responsive — bisa diakses dari HP

### Fitur Teknis
- 🔒 Keamanan berlapis (CSRF, XSS, rate limiting)
- 🖼️ Konversi gambar otomatis ke WebP
- ⚡ Realtime chat via Socket.io
- 🎨 UI modern dengan Tailwind CSS
- 📱 Fully responsive
- 🌐 SEO-friendly

---

## 🛠️ Teknologi

| Komponen | Teknologi |
|----------|-----------|
| **Backend** | PHP 7.4+ · CodeIgniter 3 |
| **Frontend** | Tailwind CSS 3 · Vanilla JS |
| **Database** | MySQL 5.7+ / MariaDB 10.3+ |
| **Realtime** | Node.js 18+ · Socket.io 4 |
| **Image** | WebP Convert (rosell-dk) |
| **Font** | Inter · Plus Jakarta Sans |

---

## 📋 Persyaratan Sistem

- **PHP** >= 7.4 (disarankan 8.1+)
- **Composer** untuk dependency PHP
- **Node.js** >= 18 untuk build assets & Socket.io
- **MySQL** >= 5.7 atau MariaDB >= 10.3
- **Web server** Apache (dengan mod_rewrite) atau Nginx
- **Extension PHP**: `mysqli`, `gd`, `mbstring`, `intl`, `json`

---

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/USERNAME/appCRM-SDN105382.git
cd appCRM-SDN105382
```

### 2. Install Dependencies

```bash
# Install dependencies PHP
composer install

# Install dependencies Node (Tailwind & tools)
npm install
```

### 3. Setup Environment

```bash
# Copy file environment
cp .env.example .env

# Edit .env
nano .env  # atau gunakan editor favorit Anda
```

Isi `.env` dengan konfigurasi Anda:
```env
APP_URL=http://localhost:8080/
DB_HOST=localhost
DB_NAME=db_crm_sd_negeri_105382
DB_USER=root
DB_PASS=
ENCRYPTION_KEY=your-32-character-random-key
```

### 4. Setup Database

```bash
# Via command line
mysql -u root -p < database/schema.sql

# Atau via phpMyAdmin
# 1. Buat database: db_crm_sd_negeri_105382
# 2. Import file database/schema.sql
```

### 5. Build Assets

```bash
# Development (watch mode)
npm run dev

# Production build
npm run build
```

### 6. Setup Socket.io Server

```bash
cd node_server

# Install dependencies
npm install

# Copy environment
cp .env.example .env
nano .env
```

Isi `node_server/.env`:
```env
PORT=3000
CORS_ORIGIN=http://localhost:8080
DB_HOST=localhost
DB_NAME=db_crm_sd_negeri_105382
DB_USER=root
DB_PASS=
```

### 7. Jalankan Aplikasi

Buka **3 terminal** berbeda:

**Terminal 1 — Tailwind watch:**
```bash
npm run dev
```

**Terminal 2 — Web server:**
```bash
# Opsi A: PHP built-in
php -S localhost:8080 -t public

# Opsi B: Pakai Laragon/XAMPP
# Akses: http://localhost:8080
```

**Terminal 3 — Socket.io server:**
```bash
cd node_server
npm run dev
```

Buka aplikasi di: **http://localhost:8080**

---

## 👤 Akun Default

Setelah import `database/schema.sql`:

| Role | Username | Password | Akses |
|------|----------|----------|-------|
| Admin | `admin` | `admin123` | Full access |
| Guru | `guru1` | `guru123` | Kelas & nilai |
| Orang Tua | `ortu1` | `ortu123` | Anak & komunikasi |

> ⚠️ **PENTING:** Segera ubah password default setelah login pertama!

---

## 📁 Struktur Direktori

```
.
├── .github/              # GitHub Actions & templates
├── application/          # CodeIgniter application
│   ├── config/           # Konfigurasi
│   ├── controllers/      # Request handlers
│   ├── core/             # Base classes
│   ├── helpers/          # Utility functions
│   ├── hooks/            # Application hooks
│   ├── libraries/        # Custom libraries
│   ├── models/           # Data layer
│   └── views/            # Template & UI
├── assets/               # Static assets
│   ├── css/              # Tailwind CSS
│   ├── images/           # Images
│   ├── js/               # JavaScript
│   └── uploads/          # User uploads
├── database/             # SQL schema & migrations
├── docs/                 # Dokumentasi
├── node_server/          # Socket.io server
├── system/               # CodeIgniter core
└── tests/                # Test suite
```

---

## 📚 Dokumentasi

- [Panduan Instalasi](docs/INSTALLATION.md)
- [Arsitektur](docs/ARCHITECTURE.md)
- [API Documentation](docs/API.md)
- [Contributing Guide](docs/CONTRIBUTING.md)
- [Security Policy](SECURITY.md)
- [Changelog](CHANGELOG.md)

---

## 🔒 Keamanan

Aplikasi ini menerapkan **multi-layer security**:

- ✅ **CSRF Protection** — Semua form
- ✅ **XSS Filtering** — Global input sanitization
- ✅ **SQL Injection Prevention** — Query Builder
- ✅ **Password Hashing** — bcrypt cost 12
- ✅ **Rate Limiting** — Login & API endpoints
- ✅ **Session Regeneration** — Cegah session fixation
- ✅ **HTTP Security Headers** — CSP, X-Frame-Options
- ✅ **File Upload Validation** — MIME type & size
- ✅ **Login Activity Logging** — Audit trail

Untuk melaporkan kerentanan, baca [SECURITY.md](SECURITY.md).

---

## 🤝 Kontribusi

Kontribusi selalu diterima! Baca panduan [Contributing Guide](docs/CONTRIBUTING.md).

**Cara cepat:**
1. Fork repo
2. Buat branch: `git checkout -b feature/nama-fitur`
3. Commit: `git commit -m 'feat: tambah fitur X'`
4. Push: `git push origin feature/nama-fitur`
5. Buat Pull Request

**Konvensi commit:** [Conventional Commits](https://www.conventionalcommits.org/)

---

## 📄 Lisensi

Project ini dilisensikan under [MIT License](LICENSE).

---

## 📞 Kontak

**SD Negeri 105382 Galang**
- 📍 Jalan Kesatuan Gang Inpres, Desa Galang Kota, Kec. Galang, Kab. Deli Serdang, Sumatera Utara
- 📧 [sdn105382@gmail.com](mailto:sdn105382@gmail.com)

---

<div align="center">

Dibuat dengan ❤️ untuk pendidikan Indonesia

**[⬆ Kembali ke atas](#crm-sdn-105382-galang)**

</div>
