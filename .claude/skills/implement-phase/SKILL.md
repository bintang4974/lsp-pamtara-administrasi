---
name: implement-phase
description: Kerjakan satu fase dari docs/IMPLEMENTATION_PLAN.md secara end-to-end (migration sampai view) lalu laporkan hasil nyata dan daftar uji manual. Gunakan saat diminta "kerjakan fase N" atau "lanjutkan implementasi".
---

# implement-phase

Argumen: nomor fase (mis. `4`). Jika tidak diberikan, pilih **fase pertama yang masih punya checklist belum tercentang**.

## Langkah

1. **Baca konteks** (jangan menebak):
   - `docs/IMPLEMENTATION_PLAN.md` — bagian fase target + checklist.
   - `docs/PRD.md` bagian yang relevan, `docs/DATABASE.md` untuk tabel terkait, `docs/ARCHITECTURE.md` untuk service/status, `docs/OPEN_DECISIONS.md` untuk default.
   - `.claude/rules/*` yang relevan dimuat otomatis; patuhi.
2. **Periksa repo nyata**: `git status`, struktur folder, migration yang sudah ada, `php -v`, `composer.json`. Jangan membuat ulang sesuatu yang sudah ada; lanjutkan dan sesuaikan.
3. **Buat task list** dari checklist fase, urut dependensi (migration → model/enum → service → request/policy → controller/route → view → seed).
4. **Implementasikan end-to-end** per butir. Aturan keras di `CLAUDE.md` berlaku (tanpa kuota, snapshot persyaratan, pembayaran hanya via admin, file privat, riwayat append-only, perubahan status hanya via `RegistrationStatusService`, dst).
5. **Verifikasi sendiri** (nyata, bukan klaim):
   - `php artisan migrate` (atau `migrate:fresh --seed` **hanya** di DB development) berhasil.
   - `php artisan route:list` memuat route baru dengan middleware benar.
   - Smoke check lewat `php artisan tinker` / membuka halaman bila memungkinkan.
   - `./vendor/bin/pint` bila terpasang.
   - Jika sesuatu tidak bisa dijalankan, katakan jelas apa dan kenapa.
6. **Centang** hanya butir yang benar-benar selesai di `docs/IMPLEMENTATION_PLAN.md`.
7. **Laporan akhir fase** (ringkas, tanpa narasi proses):
   - File/fitur dibuat atau diubah.
   - Migration/command yang dijalankan + hasil.
   - **Asumsi** yang dipakai (rujuk ID di `OPEN_DECISIONS.md`).
   - **Langkah uji manual**: tabel `Skenario → Hasil diharapkan`, mengacu ke AC di `PRD.md` §8. Sertakan akun/seed yang dipakai.
   - Dependensi/konfigurasi `.env` yang masih dibutuhkan; pekerjaan tertunda.
8. Lanjut ke fase berikutnya **kecuali** ada blocker yang butuh keputusan manusia (kebijakan bisnis tanpa default aman, atau tindakan tak dapat dibatalkan).

## Larangan
- Tidak menambah kuota/capacity atau batas asesor.
- Tidak mengarang data TUK, rekening, atau daftar persyaratan resmi.
- Tidak menjalankan reset database selain development.
- Tidak mengganti stack atau menghapus fitur existing tanpa persetujuan.
