---
description: Konvensi Laravel 13 / PHP untuk proyek ini
paths:
  - "app/**/*.php"
  - "database/**/*.php"
  - "routes/**/*.php"
  - "config/**/*.php"
---

# Konvensi Laravel

- **Laravel 13, PHP ≥ 8.3.** Gunakan fitur PHP modern (typed properties, `readonly`, enum, match). Cek dokumentasi versi terpasang bila ragu; jangan memakai API versi lama dari ingatan tanpa memverifikasi.
- Struktur: controller tipis → Form Request (validasi) → Policy (otorisasi) → Service (logika) → Eloquent. Lihat `docs/ARCHITECTURE.md` §2–3.
- **Enum** (`app/Enums`) untuk role & semua status; kolom DB tetap string, di-cast di model.
- Migration:
  - Satu perubahan skema = satu migration baru; jangan edit migration yang sudah dijalankan di lingkungan bersama.
  - FK eksplisit, index untuk kolom filter/join, unique sesuai `docs/DATABASE.md`.
  - Tabel riwayat/transaksi: `restrictOnDelete`, jangan cascade.
  - Uang: `decimal(15,2)`.
- Model: `$fillable` eksplisit, `casts()` untuk enum/JSON/date, relasi bertipe return. Hindari logika berat di model.
- **Transaction:** `DB::transaction()` untuk operasi multi-tabel; pengiriman efek samping eksternal setelah commit.
- **Query:** hindari N+1 (`with()`), gunakan `paginate()` untuk daftar, `chunk`/`lazy` untuk ekspor.
- **Validasi:** Form Request dengan pesan Bahasa Indonesia (`lang/id`). Jangan validasi di Blade.
- **Konfigurasi:** nilai dari `.env` dibaca hanya di `config/*.php`, bukan `env()` langsung di kode aplikasi.
- **Route:** grup per area (`admin`, `peserta`, publik) dengan nama route konsisten (`admin.registrations.show`).
- **Lokal:** `APP_LOCALE=id`; tanggal tampilan WIB. Format tanggal/rupiah lewat helper tunggal.
- Format kode: Laravel Pint. Tidak ada kode mati, `dd()`, `dump()`, atau komentar TODO tanpa konteks yang ditinggalkan.
- Tambah dependensi hanya jika perlu dan kompatibel Laravel 13 (verifikasi lewat `composer require`; baca pesan konflik). Catat paket baru di README.
