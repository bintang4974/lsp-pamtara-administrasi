---
description: Aturan bisnis inti LSP Pamtara — berlaku untuk logika domain
paths:
  - "app/**/*.php"
  - "database/**/*.php"
  - "routes/**/*.php"
---

# Aturan Bisnis (wajib dipatuhi di semua kode domain)

Sumber lengkap: `docs/PRD.md` §3 dan `docs/ARCHITECTURE.md` §4.

## Jadwal
- Tidak ada `capacity`, tidak ada validasi kuota, tidak ada batas asesi/asesor/hari.
- Jadwal dapat dipilih peserta jika: `status = open` **dan** `registration_deadline` belum lewat **dan** sertifikasi cocok **dan** TUK aktif.
- Jadwal yang sudah punya pendaftar tidak boleh dihapus destruktif; nonaktifkan/batalkan dengan audit.

## Persyaratan dokumen
- Satu daftar untuk 3 sertifikasi. Snapshot disalin ke `registration_document_requirements` saat pendaftaran dibuat.
- Evaluasi administrasi memakai **snapshot**, bukan master terkini.
- Dokumen opsional tidak menghalangi lolos administrasi.

## Dokumen & versi
- Unggah ulang = `version_number + 1`; file lama tetap. Dokumen kembali `under_review`.
- `approved` hanya untuk versi yang benar-benar ada dan diperiksa admin.
- Revisi selalu dengan `review_note` (alasan) tidak kosong.

## Administrasi
- `administratively_approved` hanya jika **semua** dokumen wajib `approved`, dievaluasi oleh `AdministrativeEvaluator` (satu tempat).

## Pembayaran
- Payment dibuat saat lolos administrasi. Peserta unggah bukti hanya setelah itu.
- `paid` hanya oleh admin via konfirmasi (catat `confirmed_by`, `confirmed_at`, `confirmed_amount`, `transaction_reference`).
- Rekap penerimaan = jumlah `confirmed_amount` untuk status `paid`.
- Rekening/VA bersama dari `application_settings`. Tidak ada VA per peserta; tidak ada integrasi gateway.

## Undangan PDF
- Prasyarat: `payments.status = paid` + data wajib lengkap (nomor pendaftaran, nama, sertifikasi, TUK nama+alamat, tanggal/waktu, asesor ≥ 1). Jika kurang → tolak dengan pesan jelas.
- Data dari DB; simpan `schedule_snapshot`; versi baru → versi lama `is_current = false`.
- Jadwal berubah → undangan lama ditandai usang, terbitkan versi baru lewat proses tercatat.

## Hasil
- Hanya `competent` / `not_competent`. Tanpa record atau `announced_at` null ⇒ "Belum diumumkan".
- Koreksi wajib `reason`; update hasil + `result_histories` dalam satu transaction; riwayat append-only.

## Status
- Empat dimensi terpisah (dokumen, pembayaran, pendaftaran, hasil). Jangan gabungkan.
- Ubah `registrations.status` hanya via `RegistrationStatusService`; transisi tidak sah ditolak; tulis `registration_histories`.

## Notifikasi
- Tabel `app_notifications`. Jangan menyiratkan email/WA terkirim bila belum terintegrasi; kegagalan kanal eksternal tidak membatalkan transaksi.
