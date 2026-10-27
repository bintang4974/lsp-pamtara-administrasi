# OPEN_DECISIONS — Keputusan Default dan Hal yang Perlu Dikonfirmasi

PRD asli meninggalkan beberapa hal "ditentukan kemudian". Agar AI agent tidak berhenti atau mengarang, berikut **default aman**. Pemilik produk boleh mengubahnya; jika diubah, perbarui dokumen ini dan `DATABASE.md`.

Agent: pakai default di bawah, **dokumentasikan** di README, dan **jangan** berhenti kecuali butuh keputusan yang tidak bisa dibatalkan.

| ID | Topik | Default yang dipakai | Perlu konfirmasi? |
|---|---|---|---|
| D-01 | Identifier login | **Email** (unique, tervalidasi). `phone` wajib diisi tapi bukan identifier login. | Ya, bila peserta banyak tidak punya email → pertimbangkan login via nomor HP |
| D-02 | Duplikasi pendaftaran | Satu peserta **tidak boleh punya >1 pendaftaran aktif** (status ≠ `cancelled`) untuk **jenis sertifikasi yang sama**. Peserta boleh mendaftar sertifikasi lain, atau mendaftar ulang setelah `cancelled`/hasil Tidak Kompeten. Dicek di `RegistrationService` (di dalam transaction). | **Ya** — kebijakan bisnis LSP |
| D-03 | Unique NIK | Validasi format 16 digit; **belum** unique constraint DB. Deteksi duplikat dilakukan di service (peringatan ke admin). Saran: simpan `nik` terenkripsi + `nik_hash` untuk pencarian. | **Ya** — setelah kebijakan identitas final |
| D-04 | Format nomor | Pendaftaran `REG-{YYYY}-{000001}`; undangan `UND-{YYYY}-{000001}`; counter per tahun. Kode jadwal diinput admin (unique). | Opsional |
| D-05 | Nominal biaya | **Satu nominal global** di `application_settings` (`payment.amount`), disalin ke `payments.expected_amount` saat dibuat. Belum dibedakan per sertifikasi. | **Ya** — apakah biaya berbeda per Pratama/Madya/Utama? |
| D-06 | Daftar persyaratan dokumen | Tidak di-seed (data resmi belum diberikan). Admin mengisi via UI. Jika daftar resmi tersedia, tambahkan seeder. | Ya — minta daftar resmi (KTP, ijazah, foto, dsb.) |
| D-07 | 12 TUK | Tidak di-seed. Admin menginput data resmi. Seeder dev memakai TUK **DUMMY** bertanda jelas. | Ya — minta daftar resmi 12 TUK |
| D-08 | Pengumuman hasil | Admin mencatat hasil, lalu menekan aksi "Umumkan" (set `announced_at`) secara individual atau massal per jadwal. | Opsional |
| D-09 | Tenggat unggah ulang revisi | Tidak ada tenggat otomatis. | Opsional |
| D-10 | Peserta ubah profil | Boleh ubah profil sebelum **submit**; setelah submit, data identitas kunci (nama, NIK, tempat/tanggal lahir) dikunci dan perubahan lewat admin dengan audit. | Ya |
| D-11 | Verifikasi email | Aktifkan `MustVerifyEmail` **hanya jika** email transaksional sudah tersedia di produksi; jika belum, nonaktif (dan jangan klaim email terkirim). | Ya |
| D-12 | Pembatalan oleh peserta | Peserta **tidak** bisa membatalkan sendiri; hanya admin (dengan alasan). | Opsional |
| D-13 | Ukuran/jenis file default | PDF/JPG/PNG, maks 2048 KB, bisa diubah per persyaratan oleh admin. | Opsional |
| D-14 | Peran admin | Satu role `admin` (semua admin setara). Pengaturan sensitif tetap untuk admin; tidak ada super-admin pada versi awal. | Opsional |
| D-15 | Penamaan tabel notifikasi | `app_notifications` (menghindari bentrok dengan tabel bawaan Laravel `notifications`). | Tidak |
| D-16 | Breeze | Dipakai sesuai permintaan walau sudah maintenance-only; tetap kompatibel dengan Laravel 13. | Tidak |
| D-17 | Timezone | Tampilan WIB (`Asia/Jakarta`); zona wilayah TUK lain (WITA/WIT) ditampilkan dengan label zona pada undangan bila diperlukan. | Ya — TUK tersebar di berbagai wilayah |

## Cara memakai
- Saat sebuah fase menyentuh topik di atas, agent menerapkan default lalu mencatat di bagian "Asumsi" pada laporan fase.
- Jika pemilik produk mengubah keputusan, edit baris terkait di sini — itu menjadi sumber kebenaran.
