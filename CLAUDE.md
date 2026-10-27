# CLAUDE.md — LSP Pamtara: Website Pendaftaran Sertifikasi Satpam

Aplikasi web satu lembaga (LSP Pamtara) untuk pendaftaran sertifikasi Satpam: **Gada Pratama, Gada Madya, Gada Utama**. Pengguna: **Admin** dan **Peserta**.

## Stack (ditetapkan, jangan diganti tanpa persetujuan)
Laravel 13 (PHP ≥ 8.3) · MySQL · Blade + Tailwind CSS · Alpine.js seperlunya · Laravel Breeze (stack Blade) · **responsive, mobile-first** · UI berbahasa Indonesia. Pengujian: manual oleh pemilik produk (tanpa kewajiban automated test).

## Dokumen sumber kebenaran
Baca sesuai kebutuhan; jangan menebak aturan bisnis.
- @docs/PRD.md — apa yang dibuat + aturan bisnis + acceptance criteria
- @docs/ARCHITECTURE.md — struktur kode, service, status, keamanan file, responsive
- @docs/DATABASE.md — skema tabel lengkap
- @docs/IMPLEMENTATION_PLAN.md — fase kerja + checklist
- @docs/OPEN_DECISIONS.md — default untuk hal yang belum diputuskan

Aturan terperinci ada di `.claude/rules/` (otomatis dimuat sesuai path). Skill di `.claude/skills/`.

## Cara bekerja
1. **Periksa repo dulu** (struktur, `composer.json`, versi PHP, migration yang ada). Jangan mengasumsikan sesuatu sudah dibuat. Jika repo sudah berisi aplikasi, pertahankan konvensinya.
2. Kerjakan **per fase** sesuai `docs/IMPLEMENTATION_PLAN.md`. Mulai dari fase pertama yang belum selesai.
3. Kerjakan fitur **end-to-end**: migration → model → service → request → policy → controller → view. Tidak boleh ada tombol/halaman tanpa backend.
4. Setelah tiap fase: laporkan file yang berubah, perintah yang **benar-benar dijalankan**, hasil nyata, daftar langkah uji manual, dan hal yang masih dibutuhkan. Lalu lanjut ke fase berikut kecuali ada blocker keputusan manusia.
5. **Pengujian fungsional dilakukan manual oleh pemilik produk.** Jangan menulis automated test kecuali diminta. Tetap verifikasi pekerjaanmu sendiri (migration jalan, `php artisan route:list`, smoke check lewat `tinker`/halaman). **Jangan klaim sesuatu sudah berjalan/teruji jika belum dijalankan**; jika tidak bisa dijalankan (mis. tidak ada DB), katakan apa adanya. Di akhir tiap fase, beri **daftar langkah uji manual** (skenario + hasil yang diharapkan) mengacu ke Acceptance Criteria di `docs/PRD.md`.
6. Detail kecil yang belum ditentukan → pakai default di `OPEN_DECISIONS.md`, catat sebagai asumsi. Jangan mengarang data bisnis.

## Aturan keras (jangan dilanggar)
- ❌ **Tidak ada kuota** peserta per jadwal dan **tidak ada batas** asesi per asesor per hari. Jangan buat kolom `capacity` atau validasi terkait.
- ❌ Persyaratan dokumen **sama** untuk ketiga sertifikasi (tanpa `certification_type_id` di `document_requirements`).
- ❌ Peserta tidak boleh mengubah status verifikasi dokumen/pembayaran maupun hasil ujikom.
- ❌ **Pembayaran lunas hanya** lewat konfirmasi admin. Unggah bukti ≠ lunas.
- ❌ Jangan menimpa/menghapus versi unggahan lama atau riwayat hasil/status/audit.
- ❌ PDF undangan **hanya dari data database**, bukan ketikan manual; hanya untuk pendaftaran `paid`.
- ❌ Belum ada hasil ≠ "Tidak Kompeten" (tampilkan "Belum diumumkan").
- ❌ Jangan mengarang nama/alamat 12 TUK, nomor rekening, atau daftar persyaratan resmi.
- ❌ Jangan simpan file sensitif di folder publik; jangan simpan/kirim password plaintext; jangan commit rahasia.
- ❌ Jangan hapus fitur/data existing tanpa alasan dan persetujuan. Jangan ganti stack.
- ✅ Perubahan status pendaftaran **hanya** lewat `RegistrationStatusService` (lihat arsitektur).
- ✅ Operasi multi-tabel + audit dalam satu `DB::transaction()`.
- ✅ Validasi **server-side** + otorisasi backend (middleware + policy) di setiap endpoint sensitif.
- ✅ Nominal uang `DECIMAL`, bukan float. Status/role memakai PHP Enum.

## Perintah umum
```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed          # hanya di DB development
php artisan serve                   # + npm run dev (Vite)
php artisan route:list              # cek route & middleware
./vendor/bin/pint                   # format kode (jika terpasang)
php artisan app:create-admin        # buat admin pertama (aman, interaktif)
```
Jangan jalankan `migrate:fresh` / `db:wipe` / reset pada database selain development.

## Konvensi kode (ringkas)
- PSR-12 / Laravel Pint. Nama class, method, tabel, kolom: **bahasa Inggris**. Teks UI, pesan validasi, notifikasi: **Bahasa Indonesia**.
- Controller tipis → Form Request → Policy → Service. Business logic di `app/Services`, bukan di controller/Blade.
- Satu migration per perubahan skema; jangan edit migration yang sudah dijalankan di environment bersama.
- Commit kecil dan bermakna; jangan commit `.env`, `storage/`, `vendor/`, `node_modules/`.

## Kapan berhenti dan bertanya
Hanya jika keputusan **tidak bisa dibatalkan** atau menyentuh kebijakan bisnis tanpa default aman (mis. menghapus data produksi, mengubah aturan pembayaran). Selain itu: pakai default, dokumentasikan, lanjut.
