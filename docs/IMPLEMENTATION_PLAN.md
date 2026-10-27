# IMPLEMENTATION_PLAN — Fase Kerja

Kerjakan **berurutan**. Setiap fase harus menghasilkan kode yang bisa diuji. Jangan hanya menulis rencana: implementasikan, jalankan, dan laporkan hasil nyata.

Centang `[x]` hanya setelah benar-benar selesai **dan** diverifikasi (migration/command dijalankan, atau smoke check manual).

## Laporan setelah setiap fase (wajib)
1. File dan fitur yang dibuat/diubah.
2. Migration dan command yang **benar-benar dijalankan** + hasilnya (pengujian fungsional dilakukan manual oleh pemilik produk; automated test tidak wajib).
3. **Daftar langkah uji manual** untuk fase itu: skenario → hasil yang diharapkan (acuan: Acceptance Criteria `PRD.md` §8).
4. Konfigurasi/dependency yang masih dibutuhkan.
5. Lanjut ke fase berikut kecuali ada blocker yang butuh keputusan manusia (lihat `OPEN_DECISIONS.md`).

---

## Phase 1 — Repository Assessment
- [ ] Periksa struktur proyek, versi PHP (`php -v`, harus ≥ 8.3), Composer, Node, MySQL.
- [ ] Jika repo kosong → setup sesuai `ARCHITECTURE.md` §1. Jika sudah ada aplikasi → pertahankan struktur & dependensi kompatibel; jangan buat ulang.
- [ ] Identifikasi fitur existing, `.env`, dan risiko perubahan file existing.
- [ ] Pasang Laravel 13 + Breeze (Blade) bila repo kosong; pastikan `php artisan migrate` dan halaman login/registrasi bawaan berjalan.
- [ ] Tulis ringkasan kondisi repo dan rencana berdasarkan temuan nyata.

## Phase 2 — Database Foundation
- [ ] Migration semua tabel di `DATABASE.md` (FK, unique, index).
- [ ] Model Eloquent + relasi + `$fillable` ketat + cast Enum.
- [ ] Enum: `UserRole`, `RegistrationStatus`, `DocumentStatus`, `PaymentStatus`, `ScheduleStatus`, `AssessmentResult`.
- [ ] Seeder sertifikasi; seeder dev (admin, peserta, TUK DUMMY) hanya `local`/`testing`.
- [ ] Command `app:create-admin` (interaktif, aman).
- [ ] Factory untuk model utama.
- [ ] `migrate:fresh --seed` berhasil di DB development.
- ❌ Jangan buat kolom kuota/capacity; jangan seed TUK fiktif sebagai data produksi.

## Phase 3 — Auth dan UI Foundation
- [ ] Modifikasi registrasi Breeze: field `phone`, role dipaksa `participant`, akun `inactive` ditolak login.
- [ ] Middleware `EnsureUserHasRole`; route group `admin` dan `peserta`.
- [ ] Layout `public`, `participant`, `admin`; navigasi responsive (drawer admin).
- [ ] Komponen Blade dasar (status-badge, cert-badge, empty-state, confirm-modal, alert).
- [ ] Halaman error (403/404/500), empty/loading state.
- [ ] Lokalisasi Indonesia (`APP_LOCALE=id`), timezone.
- [ ] Smoke check: login/logout, peserta tidak bisa akses `/admin`, registrasi tidak bisa jadi admin, akun nonaktif ditolak.

## Phase 4 — Master Data dan Jadwal
- [ ] CRUD sertifikasi, TUK, asesor (nonaktifkan, bukan hapus bila terpakai).
- [ ] CRUD jadwal + penugasan banyak asesor + validasi (`ends_at > starts_at`, deadline).
- [ ] CRUD persyaratan dokumen (wajib/opsional, MIME, ukuran, urutan, aktif).
- [ ] `ScheduleChangeService` (audit + penanda undangan usang + notifikasi).
- [ ] `AuditLogger`.
- [ ] Halaman publik info sertifikasi/TUK/persyaratan.
- [ ] Smoke check: tidak ada kuota; jadwal berpendaftar tidak terhapus.

## Phase 5 — Registration dan Document Workflow
- [ ] `NumberGenerator` + tabel `number_sequences` (aman race condition, pakai `lockForUpdate` dalam transaction).
- [ ] Form pendaftaran bertahap: sertifikasi → TUK → jadwal → data diri → dokumen.
- [ ] `RegistrationService` (transaction, **snapshot persyaratan**, record dokumen).
- [ ] Upload dokumen ke **disk privat**, versioning, validasi MIME/ukuran dari snapshot.
- [ ] Endpoint unduh/preview terotorisasi (Policy).
- [ ] Verifikasi dokumen admin: setujui / minta revisi (alasan) / lihat versi lama.
- [ ] `AdministrativeEvaluator` + `RegistrationStatusService` + histori.
- [ ] Notifikasi dalam aplikasi (`app_notifications`) + UI baca/tandai dibaca.
- [ ] Dashboard peserta (stepper dari data nyata via `JourneyPresenter`).
- [ ] Daftar uji manual: AC-02, AC-03, AC-04.

## Phase 6 — Payment Workflow
- [ ] Pengaturan rekening/VA bersama + nominal + instruksi (admin, diaudit).
- [ ] Payment dibuat otomatis saat lolos administrasi.
- [ ] Instruksi pembayaran hanya tampil setelah lolos administrasi.
- [ ] Upload bukti (privat, versioning), revisi, konfirmasi admin (nominal, referensi, waktu).
- [ ] Audit trail + notifikasi.
- [ ] Daftar uji manual: AC-05 (unggah ≠ lunas; hanya admin yang bisa `paid`).

## Phase 7 — Invitation dan Assessment Result
- [ ] `InvitationService`: cek `paid` + data lengkap; render PDF **dari DB**; snapshot jadwal; versi; `is_current`.
- [ ] Unduh undangan terotorisasi (hanya pemilik/admin).
- [ ] Penerbitan ulang saat jadwal berubah (versi lama ditandai usang).
- [ ] `ResultService`: input, koreksi (alasan wajib), histori dalam satu transaction, pengumuman.
- [ ] Tampilan hasil peserta ("Belum diumumkan" bila belum).
- [ ] Daftar uji manual: AC-06, AC-07.

## Phase 8 — Reports dan Import
- [ ] Laporan: pendaftaran (periode/sertifikasi/TUK), peserta per jadwal, kelengkapan dokumen, pembayaran, rekap penerimaan (terkonfirmasi), hasil, riwayat hasil, rekap total.
- [ ] Filter + pagination; ekspor Excel/CSV (+PDF bila relevan) memakai query yang sama.
- [ ] Dashboard admin dengan statistik + filter.
- [ ] Import Excel: template, validasi, deteksi duplikat, preview, error per baris, konfirmasi, commit batch, ringkasan, aktivasi akun aman.
- [ ] Audit log viewer admin.
- [ ] QR code pendaftaran (URL stabil + intended redirect).
- [ ] Daftar uji manual: AC-08.

## Phase 9 — Quality Assurance
- [ ] Susun **panduan uji manual lengkap** (`docs/MANUAL_TEST_GUIDE.md`) dari seluruh AC-01 s.d. AC-09 untuk dipakai pemilik produk.
- [ ] Uji manual alur end-to-end peserta → admin → hasil.
- [ ] Uji akses per role, uji dokumen privat (tebak URL → ditolak), uji data peserta lain (→ 403/404).
- [ ] Uji responsive 360 / 768 / 1280.
- [ ] Pastikan tidak ada mock data, tidak ada tombol tanpa backend.
- [ ] Jalankan checklist `.claude/skills/audit-business-rules`.
- [ ] README lengkap (instalasi, env, migrate, seed, admin pertama, build, deployment, backup).
- [ ] Laporan akhir: fitur selesai, cara menjalankan, env, cara membuat admin pertama, hasil verifikasi yang benar-benar dijalankan, pekerjaan tertunda.

---

## Definition of Done
Lihat `PRD.md` §9. Fitur tidak dianggap selesai bila acceptance criteria terkait belum diverifikasi oleh agent (smoke check) dan belum ada langkah uji manual untuk pemilik produk.
