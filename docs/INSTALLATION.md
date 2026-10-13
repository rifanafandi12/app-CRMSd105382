# Panduan Instalasi Detail

## Prasyarat

### Software yang Dibutuhkan

| Software | Versi Minimal | Kegunaan |
|----------|---------------|----------|
| PHP | 7.4 | Backend aplikasi |
| Composer | 2.x | Dependency PHP |
| Node.js | 18.x | Build assets & Socket.io |
| npm | 9.x | Package manager Node |
| MySQL | 5.7 | Database |
| Git | 2.x | Version control |

### Extension PHP yang Dibutuhkan

- `mysqli` — Database driver
- `gd` atau `imagick` — Image processing (WebP)
- `mbstring` — Multibyte string
- `intl` — Internationalization
- `json` — JSON parsing
- `openssl` — Encryption

Cek di `php -m` atau `phpinfo()`.

## Instalasi Langkah-demi-Langkah

### Langkah 1: Clone & Setup

```bash
git clone https://github.com/USERNAME/appCRM-SDN105382.git
cd appCRM-SDN105382
