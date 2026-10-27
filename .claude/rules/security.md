---
description: Aturan keamanan — auth, otorisasi, file privat, data sensitif
paths:
  - "app/Http/**/*.php"
  - "app/Policies/**/*.php"
  - "app/Services/**/*.php"
  - "routes/**/*.php"
  - "config/filesystems.php"
---

# Keamanan

## Otorisasi
- Setiap route admin: middleware `auth` + `role:admin`. Setiap route peserta: `auth` + `role:participant`.
- Data milik peserta **harus** dibatasi: Policy + query scoped (`where('user_id', $request->user()->id)`) atau scoped route binding. ID dari request bukan bukti otorisasi.
- Akses data peserta lain → 403/404, tidak boleh bocor informasi keberadaan.
- Jangan hanya menyembunyikan menu; selalu cek di backend.

## Registrasi dan akun
- Role dipaksa `participant` di server pada registrasi publik. `role` bukan input pengguna.
- Akun `inactive` tidak boleh login / mengakses apa pun.
- Admin dibuat lewat `php artisan app:create-admin`; seeder admin hanya `local`/`testing` (dijaga `app()->environment()`).
- Password selalu hash (`Hash`/cast `hashed`). Tidak pernah plaintext di DB, log, Excel, atau laporan.
- Rate limit: login, registrasi, unggah file, unduh dokumen.

## File
- Disk **privat** untuk semua unggahan dan PDF undangan. Tidak boleh di `public/` atau terjangkau `storage:link`.
- Nama file internal acak. Simpan metadata di DB.
- Validasi ekstensi **dan** MIME terdeteksi server terhadap allowlist dari snapshot persyaratan; ukuran dari snapshot. Jangan percaya MIME/nama dari browser.
- Unduh/preview hanya lewat controller ber-Policy. Header `X-Content-Type-Options: nosniff`.
- Jangan eksekusi atau `include` file unggahan. Jangan buat path dari input pengguna tanpa sanitasi (cegah path traversal).

## Data sensitif
- NIK: tidak di URL, tidak di log, tidak di QR, tidak di laporan kecuali perlu dan diotorisasi. Pertimbangkan `encrypted` cast.
- `audit_logs` tidak boleh memuat password, token, isi file.
- Tidak ada rahasia/API key di source; gunakan `.env`. Jangan commit `.env`.

## Web
- CSRF aktif di semua form POST/PUT/PATCH/DELETE. Output Blade memakai `{{ }}` (escape); `{!! !!}` hanya untuk HTML yang sudah disanitasi.
- Gunakan Eloquent/query builder berparameter; tidak ada SQL dari input mentah.
- Mass assignment: `$fillable` eksplisit; dilarang `$guarded = []`.
- Produksi: `APP_DEBUG=false`, HTTPS, cookie session aman.
