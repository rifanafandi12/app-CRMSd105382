# Security Policy

## Versi yang Didukung

| Versi | Didukung |
|-------|----------|
| 1.x   | ✅ |

## Melaporkan Kerentanan

Jika Anda menemukan kerentanan keamanan, mohon **JANGAN** membuka issue publik.

Kirim laporan melalui email ke: **sdn105382@gmail.com**

### Informasi yang Perlu Disertakan

- Deskripsi kerentanan
- Langkah-langkah reproduksi
- Dampak potensial
- Saran perbaikan (jika ada)
- Bukti PoC (jika ada)

### Waktu Respons

- **Konfirmasi penerimaan**: 48 jam
- **Update status**: 7 hari
- **Fix/patch**: 30 hari (tergantung severity)

## Praktik Keamanan

### Authentication & Authorization
- Password hashing dengan bcrypt (cost 12)
- Session regeneration setelah login
- Session match IP (opsional)
- Role-based access control
- Rate limiting: max 5 login attempts per 5 menit

### Input Validation
- Sanitization semua input via `sanitize_input()`
- CSRF token verification pada setiap POST
- File upload MIME type validation
- Filename sanitization untuk cegah path traversal

### SQL Injection
- Query Builder CodeIgniter
- Prepared statements untuk dynamic queries
- No raw SQL dengan user input

### XSS Prevention
- Global XSS filtering
- Output escaping dengan `html_escape()`
- Content Security Policy (CSP)
- `X-XSS-Protection` header

### Session Security
- `cookie_httponly = TRUE`
- `cookie_samesite = Lax`
- `cookie_secure = TRUE` (production)
- Session regeneration berkala

### HTTP Headers
- `X-Frame-Options: SAMEORIGIN`
- `X-Content-Type-Options: nosniff`
- `Strict-Transport-Security` (production)
- `Referrer-Policy: strict-origin-when-cross-origin`

## Checklist Deployment Production

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] HTTPS dengan certificate valid
- [ ] `cookie_secure = TRUE`
- [ ] Encryption key 32 karakter random
- [ ] Database user bukan `root`
- [ ] Firewall aktif (hanya 80, 443, SSH)
- [ ] Backup otomatis
- [ ] Update dependencies berkala
- [ ] Monitoring log
