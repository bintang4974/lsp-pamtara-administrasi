# DATABASE — Skema MySQL

Semua tabel: `utf8mb4`, `id` bigint unsigned PK, `created_at`/`updated_at` kecuali dinyatakan lain. Nama tabel boleh menyesuaikan konvensi repo, tetapi kebutuhan data harus terpenuhi.

**Aturan umum**
- Foreign key + index pada semua relasi yang dipakai join/filter.
- Nominal uang: `DECIMAL(15,2)`, **bukan float**.
- Tabel transaksi/audit tidak dihapus destruktif (gunakan status/pembatalan). Jangan `ON DELETE CASCADE` pada tabel riwayat/transaksi; pakai `restrict`.
- Status disimpan sebagai string dan dipetakan ke PHP backed Enum.
- Tidak ada kolom `capacity`/kuota di mana pun. Tidak ada `certification_type_id` di `document_requirements`.

---

## 1. Akun dan profil

### `users`
| Kolom | Tipe | Catatan |
|---|---|---|
| name | string | |
| email | string, **unique** | Identifier login (default: email — lihat OPEN_DECISIONS) |
| phone | string | |
| password | string | hash |
| role | string | `admin` \| `participant`. **Tidak mass-assignable dari request publik** |
| status | string | `active` \| `inactive` (default `active`) |
| email_verified_at | timestamp null | |
| last_login_at | timestamp null | |
| remember_token | string null | bawaan Laravel |

Index: `role`, `status`.

### `participant_profiles`
| Kolom | Tipe | Catatan |
|---|---|---|
| user_id | FK users, **unique** | satu profil per akun |
| nik | string null | dibatasi akses; unique ditentukan setelah kebijakan final |
| birth_place | string | |
| birth_date | date | |
| gender | string | |
| address | text | |
| city | string | |
| province | string | |
| postal_code | string null | |

Jangan simpan data yang tidak diperlukan. NIK bukan identifier publik.

---

## 2. Master data

### `certification_types`
`code` (unique), `name`, `description` null, `color_code`, `is_active`.
Seed: `GADA_PRATAMA` (biru), `GADA_MADYA` (kuning), `GADA_UTAMA` (merah). Warna dapat dikonfigurasi, teks harus terbaca.

### `tuks`
`code` (unique), `name`, `address`, `city`, `province`, `phone` null, `email` null, `is_active`.
**Jangan seed nama/alamat fiktif.** Boleh seeder kosong atau placeholder yang jelas bertanda belum diisi; data aktual lewat admin.

### `assessors`
`registration_number` null, `name`, `phone` null, `email` null, `is_active`. Tanpa login.

### `assessment_schedules`
| Kolom | Catatan |
|---|---|
| schedule_code | unique |
| certification_type_id | FK |
| tuk_id | FK |
| starts_at, ends_at | `ends_at > starts_at` (validasi) |
| registration_deadline | divalidasi terhadap waktu pelaksanaan |
| status | `draft`, `open`, `closed`, `completed`, `cancelled` |
| notes | null |
| created_by | FK users null |

Index: `(certification_type_id, tuk_id, status)`, `starts_at`, `registration_deadline`.
Dapat dipilih peserta hanya jika `status = open` **dan** `registration_deadline` belum lewat **dan** sertifikasi cocok **dan** TUK aktif.

### `schedule_assessors` (pivot)
`assessment_schedule_id`, `assessor_id`. **Unique `(assessment_schedule_id, assessor_id)`**, FK + index keduanya. Tidak ada batas jumlah.

### `document_requirements`
`code` (unique), `name`, `description` null, `is_required`, `allowed_mime_types` (JSON array), `max_file_size_kb`, `sort_order`, `is_active`.

### `application_settings`
`key` (unique), `value` (text/JSON), `type`, `description` null, `updated_by` FK null.
Contoh key: `institution.name`, `institution.logo`, `institution.contact`, `payment.bank_name`, `payment.account_number`, `payment.account_holder`, `payment.amount`, `payment.instructions`, `pdf.footer`. Pengaturan sensitif hanya admin berwenang; perubahan masuk audit log.

---

## 3. Pendaftaran dan dokumen

### `registrations`
| Kolom | Catatan |
|---|---|
| registration_number | unique; dari `NumberGenerator` |
| user_id | FK |
| certification_type_id | FK |
| assessment_schedule_id | FK |
| status | enum string (lihat bawah) |
| submitted_at, administrative_approved_at | null |
| administrative_approved_by | FK users null |
| completed_at, cancelled_at | null |
| cancellation_reason | text null |

Status: `draft`, `submitted`, `under_review`, `revision_required`, `administratively_approved`, `awaiting_payment`, `payment_under_review`, `payment_revision_required`, `paid`, `scheduled`, `completed`, `cancelled`.

Index: `status`, `user_id`, `assessment_schedule_id`, `certification_type_id`, `submitted_at`.
Unique/duplikasi pendaftaran: **default aman di `OPEN_DECISIONS.md` (D-02)** — dicek di service + dilindungi constraint bila memungkinkan. Peserta tidak boleh mengganti jadwal/sertifikasi setelah submit tanpa proses terotorisasi yang tercatat.

### `registration_document_requirements` (snapshot)
`registration_id`, `requirement_code`, `requirement_name`, `description_snapshot` null, `is_required`, `allowed_mime_types_snapshot` (JSON), `max_file_size_kb_snapshot`. **Unique `(registration_id, requirement_code)`**.
Dibuat otomatis saat pendaftaran dibuat dari semua `document_requirements.is_active = true`.

### `registration_documents`
`registration_id`, `registration_document_requirement_id`, `current_status` (`missing|under_review|approved|revision_required`), `latest_submission_id` null, `approved_at` null, `approved_by` FK null.
**Unique `(registration_id, registration_document_requirement_id)`**. Satu record untuk **setiap** snapshot persyaratan dibuat saat pendaftaran dibuat.

### `document_submissions` (semua versi unggahan)
`registration_document_id`, `version_number`, `file_path`, `original_filename`, `mime_type`, `file_size_bytes`, `status` (`submitted|under_review|approved|revision_required`), `review_note` null, `uploaded_by` FK, `reviewed_by` FK null, `submitted_at`, `reviewed_at` null.
**Unique `(registration_document_id, version_number)`**.

Aturan: unggah ulang = record baru versi+1; file lama tidak ditimpa; persetujuan terkait versi yang diperiksa; versi baru mengembalikan dokumen ke `under_review`; alasan revisi tetap tertelusuri.

---

## 4. Pembayaran

### `payments`
| Kolom | Catatan |
|---|---|
| registration_id | FK, **unique** (satu payment utama per pendaftaran) |
| expected_amount | DECIMAL(15,2) — diambil dari settings saat dibuat |
| confirmed_amount | DECIMAL(15,2) null |
| status | `unpaid`, `awaiting_payment`, `under_review`, `revision_required`, `paid`, `cancelled` |
| paid_at | null |
| confirmed_by | FK users null |
| confirmed_at | null |
| transaction_reference | null |
| admin_note | null |

`paid` hanya lewat konfirmasi admin. Perubahan nominal/pembatalan terotorisasi dan tercatat di audit.

### `payment_submissions` (semua versi bukti)
`payment_id`, `version_number`, `proof_path`, `original_filename`, `mime_type`, `file_size_bytes`, `status` (`submitted|under_review|approved|revision_required`), `review_note` null, `uploaded_by`, `reviewed_by` null, `submitted_at`, `reviewed_at` null.
**Unique `(payment_id, version_number)`**. Bukti baru tidak menimpa lama; revisi menyimpan alasan.

---

## 5. Undangan dan hasil

### `invitation_documents`
`registration_id`, `version_number`, `document_number`, `file_path`, `schedule_snapshot` (JSON: peserta, sertifikasi, TUK+alamat, jadwal, daftar asesor saat terbit), `issued_by`, `issued_at`, `is_current` (bool).
**Unique `(registration_id, version_number)`**; **unique `document_number`**. Hanya satu `is_current = true` per pendaftaran (dijaga di service dalam transaction; boleh tambah kolom generated/unique parsial bila MySQL mendukung pola yang dipilih).

### `assessment_results`
`registration_id` (FK, **unique**), `result` (`competent|not_competent`), `notes` null, `entered_by` FK, `entered_at`, `announced_at` null.
Belum ada record / `announced_at` null ⇒ peserta melihat "Belum diumumkan". Jangan pernah default ke `not_competent`.

### `result_histories`
`assessment_result_id`, `old_result` null, `new_result`, `reason`, `changed_by`, `changed_at`, `created_at`. Hanya `created_at` (append-only, tanpa update/delete di aplikasi).
`reason` wajib untuk koreksi; entri awal boleh `old_result = null` dengan alasan "Input awal". Update hasil + insert history dalam **satu transaction**.

---

## 6. Riwayat, audit, notifikasi

### `registration_histories`
`registration_id`, `old_status` null, `new_status`, `reason` null, `changed_by` FK null, `changed_at`, `metadata` JSON null, `created_at`. Append-only. Alasan wajib untuk penolakan/pembatalan/perubahan manual.

### `audit_logs`
`actor_user_id` null, `action`, `entity_type`, `entity_id`, `old_values` JSON null, `new_values` JSON null, `reason` null, `ip_address` null, `user_agent` null, `created_at`. Append-only.
Dicatat: konfigurasi pembayaran, jadwal, penugasan asesor, status pendaftaran, hasil, penerbitan ulang undangan, persyaratan dokumen, aktivasi/penonaktifan akun. **Jangan** mencatat password, token, isi file.
Index: `(entity_type, entity_id)`, `actor_user_id`, `created_at`.

### `app_notifications`
> **Penamaan:** PRD asli memakai `notifications`, tetapi Laravel sudah punya tabel bawaan bernama `notifications` (UUID, untuk Notification class). Agar tidak bentrok, tabel ini bernama **`app_notifications`**.

`user_id`, `registration_id` null, `title`, `message`, `type`, `read_at` null.
Index: `(user_id, read_at)`. Tidak boleh menyiratkan email/WA terkirim.

### `number_sequences` (tambahan teknis)
`key` (unique), `last_value`. Untuk nomor pendaftaran/dokumen aman race condition. Lihat `ARCHITECTURE.md` §5.

### (Opsional) `import_batches` dan `import_rows`
Untuk preview import dua tahap: batch milik admin, status (`previewed|committed|discarded`), ringkasan; baris dengan data mentah, status valid/invalid, pesan error.

---

## 7. Relasi (ringkas)

- users 1—N registrations; users 1—1 participant_profiles; users 1—N app_notifications
- certification_types 1—N registrations; 1—N assessment_schedules
- tuks 1—N assessment_schedules
- assessment_schedules 1—N registrations; N—N assessors via schedule_assessors
- registrations 1—N registration_document_requirements (snapshot); 1—N registration_documents
- registration_documents 1—N document_submissions
- registrations 1—1 payments; payments 1—N payment_submissions
- registrations 1—N invitation_documents (satu `is_current`)
- registrations 0..1 assessment_results; assessment_results 1—N result_histories
- registrations 1—N registration_histories
- Semua tindakan penting → audit_logs

Tidak perlu tabel `registration_assessors` — asesor dicatat di jadwal. Buat hanya jika kelak ada penugasan asesor per peserta.

## 8. Seeder
| Seeder | Isi | Lingkungan |
|---|---|---|
| `CertificationTypeSeeder` | 3 sertifikasi + warna | semua |
| `DocumentRequirementSeeder` | **opsional**; hanya jika daftar resmi tersedia. Jangan mengarang | semua (jika ada data) |
| `ApplicationSettingSeeder` | key default kosong/placeholder (tanpa nomor rekening palsu) | semua |
| `DevelopmentSeeder` | admin + peserta contoh + TUK **jelas bertanda DUMMY** | hanya `local`/`testing` |

Admin produksi: `php artisan app:create-admin`.
