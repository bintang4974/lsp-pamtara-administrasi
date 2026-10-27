---
name: audit-business-rules
description: Audit kode terhadap aturan bisnis dan keamanan PRD LSP Pamtara (kuota, snapshot, pembayaran, file privat, riwayat, otorisasi). Gunakan sebelum menyatakan fase selesai, sebelum rilis, atau saat diminta review.
---

# audit-business-rules

Tujuan: menemukan pelanggaran aturan inti **dengan membaca dan mencari di kode nyata**, bukan berasumsi. Hanya baca/cari; jangan mengubah kode kecuali diminta memperbaiki.

## Cara
Jalankan pemeriksaan di bawah dengan `Grep`/`Glob`/`Read`, catat temuan dengan `file:baris`, lalu laporkan. Tandai tiap butir: ✅ sesuai · ❌ pelanggaran · ⚪ belum diimplementasi.

## Checklist

### Jadwal dan data
- [ ] Tidak ada kolom/validasi `capacity`, `quota`, `max_participants`, atau batas asesi per asesor (`grep -ri "capacity\|quota\|kuota\|max_participants"` di `app/ database/ resources/`).
- [ ] `document_requirements` tidak punya `certification_type_id`.
- [ ] Pemilihan jadwal peserta: `open` + deadline belum lewat + sertifikasi cocok + TUK aktif.
- [ ] Jadwal berpendaftar tidak dihapus destruktif.
- [ ] Tidak ada seeder produksi dengan data TUK/rekening/persyaratan fiktif.

### Dokumen
- [ ] Snapshot dibuat saat pendaftaran; evaluasi administrasi memakai snapshot.
- [ ] Unggah ulang membuat versi baru; tidak ada `Storage::delete`/overwrite pada file lama.
- [ ] Revisi mewajibkan `review_note`.
- [ ] Hanya satu tempat (`AdministrativeEvaluator`) yang menentukan lolos administrasi.
- [ ] `registrations.status` hanya diubah lewat `RegistrationStatusService` (cari `->update(['status'` / `->status =` di luar service).

### Pembayaran
- [ ] `paid` hanya di `PaymentService::confirm` yang dijaga role admin.
- [ ] Peserta tidak punya endpoint yang bisa mengubah status/`confirmed_amount`.
- [ ] Instruksi pembayaran dan unggah bukti hanya setelah `administratively_approved`.
- [ ] Uang memakai `decimal`; tidak ada `float`/`double` untuk nominal.
- [ ] Rekap penerimaan memakai `confirmed_amount` dari status `paid`.

### Undangan dan hasil
- [ ] Penerbitan menolak jika bukan `paid` atau data wajib kurang.
- [ ] PDF membaca data dari DB (tidak ada input teks bebas untuk nama/TUK/jadwal/asesor).
- [ ] Versi lama `is_current=false` saat versi baru terbit; `schedule_snapshot` tersimpan.
- [ ] Hasil: tidak ada default `not_competent`; "Belum diumumkan" ditampilkan bila kosong.
- [ ] Koreksi hasil wajib alasan; update + `result_histories` dalam satu `DB::transaction`.
- [ ] Tidak ada update/delete pada `result_histories`, `registration_histories`, `audit_logs`.

### Keamanan
- [ ] Semua route `/admin` ber-middleware `auth` + `role:admin`; `/peserta` ber-`role:participant` (`php artisan route:list`).
- [ ] Setiap akses data peserta dibatasi Policy/query kepemilikan (cari `find($request->id)` / `findOrFail` tanpa scope).
- [ ] `role` tidak bisa diisi dari request registrasi; registrasi memaksa `participant`.
- [ ] Akun `inactive` ditolak login.
- [ ] File di disk privat; tidak ada `Storage::disk('public')` untuk dokumen/bukti/PDF; unduh lewat controller ber-Policy.
- [ ] MIME + ekstensi + ukuran divalidasi dari snapshot; nama file internal acak.
- [ ] Tidak ada `$guarded = []`; tidak ada `{!! !!}` dengan data pengguna; tidak ada SQL mentah dari input.
- [ ] `audit_logs` tidak memuat password/token/isi file; tidak ada password plaintext di import/export.
- [ ] Tidak ada rahasia di source; `.env` tidak ter-commit.

### Konsistensi dan UI
- [ ] Penomoran lewat `NumberGenerator` (`lockForUpdate`) + unique constraint.
- [ ] Stepper peserta dari data nyata (`JourneyPresenter`), bukan hardcoded.
- [ ] Teks UI Bahasa Indonesia; layout tidak pecah di 360px (cek class responsive pada view utama).
- [ ] Tidak ada tombol/halaman tanpa backend; tidak ada data mock di jalur produksi.
- [ ] Notifikasi tidak menyiratkan email/WA terkirim.

## Format laporan
1. **Ringkasan**: n ✅ / n ❌ / n ⚪.
2. **Pelanggaran** (urut keparahan): butir, `file:baris`, mengapa melanggar, saran perbaikan singkat.
3. **Belum diimplementasi** yang diharapkan sudah ada pada fase saat ini.
4. Tanya apakah pelanggaran ingin langsung diperbaiki.

Jangan menyatakan ✅ tanpa bukti dari kode/route yang benar-benar diperiksa.
