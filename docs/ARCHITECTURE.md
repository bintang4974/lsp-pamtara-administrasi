# ARCHITECTURE — LSP Pamtara Satpam Certification

Dokumen teknis. Aturan bisnis ada di `PRD.md`, skema tabel di `DATABASE.md`.

---

## 1. Tech Stack (ditetapkan)

| Lapisan | Pilihan | Catatan |
|---|---|---|
| Framework | **Laravel 13** | Membutuhkan **PHP ≥ 8.3** (didukung 8.3–8.5) |
| Database | MySQL (`utf8mb4`) | Foreign key, index, unique constraint wajib |
| Frontend | **Blade + Tailwind CSS** + Alpine.js seperlunya | Tanpa SPA, tanpa Livewire/Inertia |
| Auth | **Laravel Breeze, stack Blade** | Lihat catatan Breeze di bawah |
| Build | Vite (bawaan Laravel) | |
| Testing | **Manual oleh pemilik produk** | Automated test tidak wajib; boleh ditambah bila diminta (Pest/PHPUnit) |
| PDF | `barryvdh/laravel-dompdf` (verifikasi kompatibilitas Laravel 13 via `composer require`) | |
| Excel | `maatwebsite/excel` (verifikasi kompatibilitas Laravel 13 via `composer require`) | |
| QR | `simplesoftwareio/simple-qrcode` atau sejenis (opsional, hanya untuk membuat QR flyer) | |
| Responsive | Tailwind mobile-first | Lihat §8 |

### Catatan Breeze di Laravel 13
- Breeze **tidak lagi menerima fitur baru** (Laravel menyatakan ini sejak Laravel 12; starter kit baru memakai Fortify). Namun paket `laravel/breeze` masih memperbolehkan Laravel 13 dan `breeze:install blade` masih bisa dijalankan.
- Pilihan stack ini **disengaja** (permintaan pemilik produk). Jangan migrasi ke starter kit lain tanpa persetujuan.
- Breeze menyalin kode ke aplikasi (controller, view, route `routes/auth.php`). Kode itu **milik proyek** dan boleh dimodifikasi.
- Setelah install, **periksa versi Tailwind yang dihasilkan** (`package.json`) dan ikuti versi itu. Jangan upgrade mayor Tailwind di tengah proyek kecuali ada alasan.
- Fitur 2FA/passkey tidak tersedia di Breeze; bukan kebutuhan versi awal.

### Setup repo kosong (urutan)
```bash
composer create-project laravel/laravel lsp-pamtara
cd lsp-pamtara
composer require laravel/breeze --dev
php artisan breeze:install blade          # pilih stack Blade; framework test terserah (tidak wajib)
# konfigurasi .env (MySQL), lalu:
php artisan migrate
npm install && npm run build
```
Jika repo sudah berisi aplikasi: **jangan** ulangi; periksa dulu (lihat Phase 1 di `IMPLEMENTATION_PLAN.md`).

---

## 2. Struktur Folder yang Dituju

```
app/
├── Enums/                      # status & role (backed enum)
│   ├── UserRole.php            # admin, participant
│   ├── RegistrationStatus.php
│   ├── DocumentStatus.php
│   ├── PaymentStatus.php
│   ├── ScheduleStatus.php
│   └── AssessmentResult.php    # competent, not_competent
├── Http/
│   ├── Controllers/
│   │   ├── Admin/              # semua controller admin
│   │   ├── Participant/        # semua controller peserta
│   │   ├── Public/             # landing, info publik
│   │   └── Auth/               # dari Breeze (dimodifikasi)
│   ├── Middleware/
│   │   └── EnsureUserHasRole.php
│   └── Requests/               # Form Request per aksi
├── Models/
├── Policies/                   # kepemilikan data peserta
├── Services/                   # business logic (lihat §3)
│   ├── Registration/
│   ├── Documents/
│   ├── Payments/
│   ├── Invitations/
│   ├── Results/
│   ├── Imports/
│   ├── Reports/
│   ├── Audit/
│   └── Notifications/
├── Exports/                    # class export Excel
├── Imports/                    # class import Excel
└── Support/                    # helper kecil (NumberGenerator, dll.)
database/
├── migrations/
├── seeders/                    # CertificationTypeSeeder, DocumentRequirementSeeder (opsional)
└── factories/
resources/views/
├── layouts/                    # public, participant, admin
├── components/                 # komponen Blade (status-badge, stepper, dll.)
├── public/ participant/ admin/ pdf/
routes/
├── web.php                     # publik
├── auth.php                    # Breeze
├── participant.php             # prefix /peserta, middleware role:participant
└── admin.php                   # prefix /admin, middleware role:admin
tests/                          # opsional (pengujian manual oleh pemilik produk)
docs/                           # dokumen ini
```

Jika repo existing berbeda konvensi, **ikuti repo**, jangan paksa struktur ini.

---

## 3. Service Layer (tempat business logic)

Controller tipis: validasi (Form Request) → otorisasi (policy) → panggil service → return response.

| Service | Tanggung jawab |
|---|---|
| `RegistrationService` | Buat pendaftaran dalam transaction: validasi jadwal terbuka, buat nomor, **snapshot persyaratan**, buat record `registration_documents`, history awal, notifikasi |
| `RegistrationStatusService` | **Satu-satunya** tempat mengubah `registrations.status`. Cek transisi valid, tulis `registration_histories`, panggil audit |
| `AdministrativeEvaluator` | Evaluasi kelulusan administrasi dari dokumen wajib snapshot; dipanggil setelah setiap keputusan dokumen |
| `DocumentSubmissionService` | Unggah versi baru (versi+1, file tidak menimpa), set status ke `under_review`, validasi MIME/ukuran dari snapshot |
| `DocumentReviewService` | Approve / minta revisi (alasan wajib) per versi yang diperiksa; lalu panggil `AdministrativeEvaluator` |
| `PaymentService` | Buat record payment saat lolos administrasi (ambil nominal dari settings), terima bukti, minta revisi, **konfirmasi** (admin, waktu, nominal diterima, referensi) |
| `InvitationService` | Cek prasyarat (paid + data lengkap), render PDF dari DB, simpan snapshot jadwal, versi baru, tandai versi lama `is_current=false` |
| `ScheduleChangeService` | Ubah jadwal yang sudah punya pendaftar: audit, tandai undangan lama usang, notifikasi |
| `ResultService` | Input hasil; koreksi wajib alasan; update + `result_histories` dalam satu transaction; pengumuman |
| `NumberGenerator` | Nomor pendaftaran dan nomor dokumen aman race condition (§5) |
| `AuditLogger` | Tulis `audit_logs` (filter field sensitif) |
| `NotificationService` | Tulis notifikasi dalam aplikasi; kanal eksternal opsional dan terpisah |
| `ParticipantImportService` | Validasi, preview, commit batch |
| `ReportQuery` classes | Query laporan **satu sumber** untuk tampilan dan ekspor (filter identik) |

### Aturan arsitektur
1. Perubahan status pendaftaran **hanya** lewat `RegistrationStatusService`.
2. Setiap service yang mengubah data utama + audit/history memakai `DB::transaction()`.
3. Notifikasi eksternal (email/WA) dikirim **setelah commit** dan kegagalannya tidak membatalkan transaksi.
4. Tidak ada business logic di Blade; Blade hanya menampilkan data yang sudah disiapkan.
5. Status dan role memakai Enum, bukan string literal tersebar.

---

## 4. Model Status

Empat dimensi **terpisah** — jangan digabung:

1. **Status dokumen** (`registration_documents.current_status`): `missing → under_review → approved`; `under_review → revision_required → (unggah baru) → under_review`.
2. **Status pembayaran** (`payments.status`): `unpaid → awaiting_payment → under_review → paid`; `under_review → revision_required → (unggah baru) → under_review`; `cancelled` melalui proses terotorisasi.
3. **Status pendaftaran** (`registrations.status`) — proses keseluruhan.
4. **Hasil ujikom** (`assessment_results`) — tidak ikut menentukan status lain.

### 4.1 Transisi status pendaftaran

| Dari | Ke | Pemicu | Aktor |
|---|---|---|---|
| — | `draft` | Peserta mulai mengisi | Peserta |
| `draft` | `submitted` | Peserta kirim (semua dokumen wajib sudah diunggah) | Peserta |
| `submitted` | `under_review` | Admin mulai periksa / dokumen pertama diperiksa | Admin/Sistem |
| `under_review` | `revision_required` | Ada dokumen wajib diminta revisi | Admin |
| `revision_required` | `under_review` | Peserta unggah versi baru | Peserta |
| `under_review` | `administratively_approved` | `AdministrativeEvaluator`: semua dokumen wajib `approved` | Sistem |
| `administratively_approved` | `awaiting_payment` | Payment record dibuat, instruksi tampil | Sistem |
| `awaiting_payment` | `payment_under_review` | Peserta unggah bukti | Peserta |
| `payment_under_review` | `payment_revision_required` | Admin minta revisi bukti | Admin |
| `payment_revision_required` | `payment_under_review` | Peserta unggah bukti baru | Peserta |
| `payment_under_review` | `paid` | Admin konfirmasi | Admin |
| `paid` | `scheduled` | Undangan PDF diterbitkan | Admin |
| `scheduled` | `completed` | Ujikom selesai / hasil dicatat | Admin |
| (sebagian besar) | `cancelled` | Pembatalan terotorisasi, **alasan wajib** | Admin |

Transisi di luar tabel **ditolak** (exception domain). Setiap transisi menulis `registration_histories`.

Jika dokumen yang sudah `approved` perlu diperiksa ulang secara sah (mis. admin menemukan masalah), gunakan aksi eksplisit "buka kembali pemeriksaan" yang tercatat + alasan; evaluator akan menghitung ulang.

### 4.2 Peta status dashboard peserta (diturunkan dari data)
Stepper dihitung dari `registrations.status`, status dokumen, `payments.status`, undangan `is_current`, dan `assessment_results.announced_at`. Gunakan satu class `JourneyPresenter` agar tidak ada logika tersebar di Blade.

---

## 5. Penomoran Aman Race Condition

Tabel bantu `number_sequences` (tambahan arsitektur, bukan dari PRD asli):

| Kolom | Keterangan |
|---|---|
| `key` | mis. `registration:2026`, `invitation:2026` |
| `last_value` | counter terakhir |

`NumberGenerator::next($key)`:
```php
DB::transaction(function () use ($key) {
    $row = DB::table('number_sequences')->where('key', $key)->lockForUpdate()->first();
    // jika belum ada: insert dengan last_value=1 (tangani duplicate-key dengan retry)
    // jika ada: increment last_value
});
```
Format default (dapat diubah, lihat `OPEN_DECISIONS.md`):
- Pendaftaran: `REG-{YYYY}-{000001}`
- Dokumen undangan: `UND-{YYYY}-{000001}`

Kolom `registration_number` / `document_number` tetap punya **unique constraint** sebagai pengaman terakhir.

---

## 6. File Storage dan Keamanan Dokumen

- Semua file peserta (KTP, ijazah, bukti bayar, dll.) di **disk privat** (`local`/`private`, root `storage/app/private`), **bukan** `public`. Jangan buat `storage:link` untuk folder ini.
- Nama file internal: acak (`Str::uuid()` + ekstensi tervalidasi). Simpan `file_path`, `original_filename`, `mime_type`, `file_size_bytes`, `version_number` di DB.
- Struktur path: `registrations/{registration_id}/documents/{requirement_code}/v{n}-{uuid}.{ext}`; `registrations/{id}/payments/v{n}-{uuid}.{ext}`; `registrations/{id}/invitations/{document_number}.pdf`.
- Unduh/preview **hanya** lewat controller yang memanggil Policy (admin, atau pemilik pendaftaran). Respons dengan `Storage::download` / `response()->file` + header aman (`Content-Disposition`, `X-Content-Type-Options: nosniff`).
- Validasi: ekstensi **dan** MIME terdeteksi server (`mimetypes`/`finfo`) terhadap allowlist dari **snapshot** persyaratan; ukuran dari `max_file_size_kb_snapshot`. Jangan percaya MIME dari browser.
- File tidak pernah dieksekusi; tidak disimpan di bawah `public/`.
- Versi lama **tidak** dihapus/ditimpa.
- Pencatatan akses sensitif (unduh dokumen oleh admin) ke audit log bila diputuskan perlu.
- Backup DB + file dengan perlindungan setara; kebijakan retensi mengikuti hukum (jangan hapus histori sembarangan).

---

## 7. Otorisasi dan Keamanan

- Route dikelompokkan: `/admin/*` (middleware `auth`, `role:admin`), `/peserta/*` (middleware `auth`, `role:participant`), publik.
- `EnsureUserHasRole` middleware + **Policy** (`RegistrationPolicy`, `DocumentSubmissionPolicy`, `PaymentSubmissionPolicy`, `InvitationDocumentPolicy`, `AssessmentResultPolicy`) untuk kepemilikan.
- Gunakan **route model binding yang dibatasi** (scoped) atau query `->where('user_id', auth()->id())`. ID dari request bukan bukti otorisasi.
- Registrasi publik: `role` **dipaksa** `participant` di server; `role` tidak masuk `$fillable` pada jalur publik / tidak diambil dari request.
- Akun admin pertama dibuat lewat **artisan command** (`php artisan app:create-admin`), password diminta interaktif / dari env sekali pakai. Seeder admin hanya untuk `local`/`testing` dan dijaga `app()->environment()`.
- Akun `status = nonaktif` tidak boleh login (cek di login + middleware).
- Rate limiting: login (bawaan Breeze), registrasi, unggah file, unduh dokumen.
- CSRF aktif; cookie session `secure`/`httpOnly`/`sameSite` di produksi; `APP_DEBUG=false` di produksi.
- NIK: dibatasi akses (hanya admin & pemilik); pertimbangkan `encrypted` cast + kolom hash untuk pencarian duplikat (lihat `OPEN_DECISIONS.md`); jangan jadi identifier publik/URL.
- `audit_logs` tidak memuat password, token, atau isi file.
- Mass assignment: gunakan `$fillable` ketat; jangan `$guarded = []`.

---

## 8. Frontend dan Responsive

- **Mobile-first**: tulis kelas dasar untuk layar kecil, tambah `sm:`/`md:`/`lg:` untuk layar besar.
- Tiga layout Blade: `layouts.public`, `layouts.participant`, `layouts.admin`. Admin: sidebar collapsible di mobile (drawer, Alpine.js). Peserta: navigasi sederhana (top bar / bottom nav di mobile).
- Tabel admin: di mobile gunakan scroll horizontal terkontrol atau tampilan kartu; jangan biarkan layout pecah.
- Komponen Blade yang dipakai ulang: `<x-status-badge>`, `<x-cert-badge>` (warna + **label**), `<x-stepper>`, `<x-empty-state>`, `<x-confirm-modal>`, `<x-file-upload>`, `<x-alert>`, `<x-pagination-wrapper>`.
- Warna sertifikasi dari `certification_types.color_code`; pastikan kontras teks (hitung warna teks terang/gelap otomatis).
- Bahasa UI: **Bahasa Indonesia**; semua string UI lewat `lang/id` bila memungkinkan; set `APP_LOCALE=id`, timezone `Asia/Jakarta` untuk tampilan (simpan UTC bila konfigurasi default Laravel).
- Aksesibilitas dasar: label form, fokus terlihat, ukuran target sentuh ≥ 44px, kontras cukup.
- Form panjang: bagi per langkah/section; validasi inline; state loading pada tombol submit (cegah double submit).
- Target uji manual responsive: 360px, 768px, 1280px.

---

## 9. Laporan dan Ekspor

- Satu class query per laporan menerima objek filter yang sama untuk **tampilan (paginate)** dan **ekspor (lazy/chunk)**.
- Ekspor Excel/CSV via `maatwebsite/excel` (gunakan `FromQuery` + chunk untuk data besar); PDF via DomPDF bila perlu.
- Otorisasi (role admin) sebelum ekspor; kolom hanya data yang perlu; tanpa file dokumen.
- Penerimaan pembayaran = `SUM(confirmed_amount)` dari `payments.status = paid`.
- Format tanggal `d M Y H:i` (WIB) dan angka rupiah konsisten lewat helper tunggal.

## 10. Import Excel

Dua tahap: **(1) unggah + validasi → simpan hasil preview sementara** (tabel `import_batches` + `import_rows`, atau cache/session dengan ID batch milik admin) → **(2) konfirmasi → commit** dalam transaction/batch.
- Template dengan header baku; header salah ditolak dengan pesan jelas.
- Deteksi duplikat (email; NIK jika ada) terhadap DB dan di dalam file.
- Pesan error per baris (nomor baris + kolom + alasan).
- Akun baru: `password` acak tidak ditampilkan; kirim tautan aktivasi/reset password (broker password Laravel) atau tandai "perlu aktivasi". **Tidak pernah** plaintext password di Excel/laporan.
- Import tidak menyentuh status dokumen/pembayaran.

## 11. Konfigurasi dan Deployment

- Semua rahasia di `.env` (tidak di-commit). Sediakan `.env.example` lengkap tanpa nilai rahasia.
- `config/pamtara.php` untuk konstanta aplikasi (format nomor, disk privat, batas default unggah).
- Produksi: `APP_ENV=production`, `APP_DEBUG=false`, `php artisan config:cache route:cache view:cache`, queue worker bila dipakai, scheduler cron bila ada tugas terjadwal, HTTPS wajib, izin folder `storage` benar.
- README wajib: instalasi, env, migration, seeding, pembuatan admin pertama, testing, build aset, deployment, backup.

## 12. Testing

- Pengujian fungsional dilakukan **manual oleh pemilik produk** berdasarkan Acceptance Criteria di `PRD.md` §8. Automated test tidak diwajibkan.
- Agent tetap wajib **memverifikasi sendiri** pekerjaannya (migration berjalan, route terdaftar, smoke check) dan memberi **daftar langkah uji manual** per fase.
- Bila kelak ditambahkan automated test: gunakan database terpisah dan jangan pernah mereset database non-test.
