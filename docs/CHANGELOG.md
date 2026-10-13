# Changelog

Semua perubahan penting project ini akan didokumentasikan dalam file ini.

Format berdasarkan [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
dan mengikuti [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Fitur-fitur yang akan datang

## [1.0.0] - 2026-10-13

### Added
- Autentikasi dengan role-based access control (admin, guru, orang tua)
- Dashboard terpisah per role
- Manajemen data siswa (CRUD + export CSV)
- Manajemen kelas & wali kelas
- Input nilai akademik (NH1, NH2, UTS, UAS) dengan auto-calculate
- Rapor digital dengan print view
- Pengumuman dengan target kelas & attachment
- Aspirasi dari orang tua dengan respon admin
- Galeri foto dengan konversi WebP otomatis
- Chat realtime dengan Socket.io
- Notifikasi sound untuk pesan baru
- Laporan statistik dengan Chart.js
- Export data (pesan, aspirasi, siswa, nilai) dalam format CSV
- Profil user dengan upload avatar
- Keamanan berlapis (CSRF, XSS, rate limit, security headers)

### Security
- Password hashing bcrypt cost 12
- Session regeneration
- Rate limiting pada login
- File upload MIME validation
- SQL injection prevention

### Fixed
- Query DATE_FORMAT untuk kompatibilitas MySQL 8
- Route conflict pada Scores module
- CSP blocking Socket.io CDN

### Changed
- Migrasi dari Bootstrap ke Tailwind CSS
- Refactor struktur project untuk maintainability
- Palet warna disesuaikan dengan logo Tut Wuri Handayani

---

## Format Versi

- **MAJOR.MINOR.PATCH**
- **MAJOR**: Perubahan breaking
- **MINOR**: Fitur baru backward-compatible
- **PATCH**: Bug fix backward-compatible

## Kategori

- **Added**: Fitur baru
- **Changed**: Perubahan pada fitur existing
- **Deprecated**: Fitur yang akan dihapus
- **Removed**: Fitur yang dihapus
- **Fixed**: Bug fix
- **Security**: Perbaikan keamanan
