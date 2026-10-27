# PRD — Website Pendaftaran Sertifikasi Kompetensi Satpam (LSP Pamtara)

**Versi:** 1.1 (hasil restrukturisasi dari PRD v1.0)
**Jenis aplikasi:** Web application
**Pengguna:** Admin LSP dan Peserta
**Dokumen terkait:** `ARCHITECTURE.md` (desain teknis), `DATABASE.md` (skema), `IMPLEMENTATION_PLAN.md` (fase kerja), `OPEN_DECISIONS.md` (keputusan default)

> Dokumen ini hanya menjawab **APA** yang harus dibuat dan **ATURAN BISNIS**-nya.
> Cara membuatnya ada di `ARCHITECTURE.md` dan `DATABASE.md`.

---

## 1. Ringkasan Produk

### 1.1 Latar belakang
LSP Pamtara membutuhkan website untuk mengelola pendaftaran dan pelaksanaan sertifikasi kompetensi Satpam secara terstruktur: pendaftaran peserta, pemilihan jadwal dan TUK, pengumpulan dokumen, verifikasi administrasi, pembayaran, penerbitan undangan ujikom, hingga pencatatan hasil. Aplikasi menggantikan proses manual yang rawan salah catat, dokumen hilang, jadwal keliru, dan sulit ditelusuri.

### 1.2 Tujuan
1. Peserta mendaftar sertifikasi secara online.
2. Admin mengelola seluruh proses pendaftaran dan ujikom di satu tempat.
3. Mengelola 12 TUK di berbagai wilayah Indonesia.
4. Verifikasi dokumen dan pembayaran terkontrol dan tercatat.
5. PDF undangan dihasilkan dari data aktual database.
6. Riwayat perubahan tersedia untuk dokumen, pembayaran, hasil, dan status.
7. Laporan dan ekspor data untuk kebutuhan operasional.
8. Mengurangi input berulang dan risiko ketidaksesuaian data.

### 1.3 Ruang lingkup
- Satu lembaga: **LSP Pamtara**. Bukan multi-tenant.
- Tiga sertifikasi: **Gada Pratama, Gada Madya, Gada Utama**.
- Tidak ada: login asesor, portal perusahaan, payment gateway/VA unik per peserta, integrasi bank (versi awal).

### 1.4 Tech stack (ditetapkan)
Laravel 13 (PHP ≥ 8.3), MySQL, Blade + Tailwind CSS, Alpine.js seperlunya, Laravel Breeze (stack Blade), desain **responsive**. Detail di `ARCHITECTURE.md`.

---

## 2. Role dan Hak Akses

### 2.1 Admin
Dashboard operasional; kelola akun peserta, sertifikasi, TUK, asesor, jadwal, persyaratan dokumen; verifikasi dokumen dan pembayaran; terbitkan undangan PDF; input dan koreksi hasil (dengan alasan + audit); pengumuman/notifikasi; import Excel; laporan dan ekspor; lihat riwayat dan audit log; pengaturan aplikasi.

### 2.2 Peserta
Registrasi/login; kelola profil sendiri; pilih sertifikasi, TUK, jadwal tersedia; isi formulir; unggah dokumen dan unggah ulang yang diminta revisi; lihat status verifikasi per dokumen; lihat instruksi pembayaran (setelah lolos administrasi); unggah/unggah ulang bukti pembayaran; lihat dan unduh undangan; lihat hasil setelah diumumkan; lihat notifikasi dan riwayat **miliknya sendiri**.

Peserta **tidak boleh** melihat data peserta lain.

### 2.3 Aturan keamanan role
- Role divalidasi di **backend** (middleware + policy), bukan sekadar menyembunyikan menu.
- Setiap query data peserta dibatasi kepemilikan; ID dari frontend bukan bukti otorisasi.
- Akun admin tidak bisa dibuat lewat registrasi publik.
- Password di-hash; terapkan CSRF, session security, rate limiting, validasi input.

---

## 3. Aturan Bisnis

### 3.1 Jenis sertifikasi
| Sertifikasi | Warna UI |
|---|---|
| Gada Pratama | Biru |
| Gada Madya | Kuning |
| Gada Utama | Merah |

- Ketiganya memakai **formulir yang sama** dan **daftar persyaratan dokumen yang sama**.
- Jenis sertifikasi tetap dicatat di setiap pendaftaran; dipakai pada filter, laporan, jadwal, dan PDF.
- Warna hanya identifikasi visual; selalu sertakan label teks. Pastikan kontras teks terbaca.

### 3.2 TUK
- Mendukung 12 TUK; admin bisa tambah/ubah/aktifkan/nonaktifkan.
- Atribut: nama, kode, alamat, wilayah (kota/provinsi), status aktif.
- **Jangan mengarang** nama/alamat TUK. Data diisi admin atau seeder dari data resmi.
- TUK nonaktif tidak ditawarkan untuk pendaftaran baru, tetapi tetap tampil pada riwayat/dokumen lama.

### 3.3 Jadwal ujikom
- Admin membuat jadwal per sertifikasi + TUK: tanggal, waktu mulai/selesai, batas pendaftaran, status (`draft`, `open`, `closed`, `completed`, `cancelled`).
- Satu atau beberapa asesor ditugaskan ke jadwal.
- Peserta hanya bisa memilih jadwal: status `open`, belum lewat batas pendaftaran, sesuai sertifikasi yang dipilih. Peserta tidak menentukan tanggal sendiri.
- **TIDAK ADA kuota** peserta per jadwal dan **TIDAK ADA batas** asesi per asesor per hari. Jangan buat kolom `capacity` atau validasi terkait.
- Jumlah peserta per jadwal hanya untuk statistik/laporan.
- Jadwal yang sudah punya pendaftar tidak dihapus destruktif; gunakan penonaktifan/pembatalan yang tercatat.

### 3.4 Persyaratan dokumen
- Satu daftar berlaku untuk ketiga sertifikasi (**tidak** dibedakan per jenis sertifikasi).
- Admin: tambah, ubah, urutkan, aktif/nonaktifkan.
- Atribut: nama, deskripsi, wajib/opsional, jenis file diizinkan, batas ukuran, urutan.
- Hanya dokumen **wajib** yang menentukan kelulusan administrasi; opsional tidak menghalangi.
- Perubahan master persyaratan **tidak boleh** mengubah kewajiban pendaftaran lama → gunakan **snapshot persyaratan** per pendaftaran.

### 3.5 Kelulusan administrasi
Pendaftaran lolos administrasi **hanya jika semua dokumen wajib (menurut snapshot) berstatus `approved`**.

| Kondisi dokumen wajib | Hasil |
|---|---|
| Semua disetujui | Lolos → lanjut pembayaran |
| Ada yang belum diunggah | Belum lolos |
| Ada yang sedang diperiksa | Belum lolos |
| Ada yang diminta revisi | Belum lolos |

Validasi di backend lewat satu service terpusat (lihat `ARCHITECTURE.md`).

### 3.6 Pembayaran
- Semua peserta membayar ke **satu rekening perusahaan / nomor VA bersama**. **Bukan** VA unik per peserta.
- Bank, nomor rekening/VA, nama penerima, nominal, dan instruksi dikelola admin (pengaturan aplikasi).
- Peserta baru boleh unggah bukti **setelah lolos administrasi**.
- Admin memeriksa dan mengonfirmasi **manual**. Bukti diunggah ≠ lunas.
- Bukti salah/tidak jelas → admin minta revisi dengan alasan. Bukti lama **tidak dihapus** saat bukti baru diunggah.
- Konfirmasi mencatat: admin, waktu, nominal diterima, referensi transaksi (jika ada).
- Rekap penerimaan = pembayaran yang **dikonfirmasi**, bukan nominal bukti.
- Integrasi bank / payment gateway **tidak** termasuk versi awal.

### 3.7 Undangan PDF
- Hanya untuk pendaftaran dengan pembayaran **sudah dikonfirmasi**.
- Seluruh data dari database; admin **tidak mengetik ulang** nama, sertifikasi, TUK, jadwal, atau asesor.
- Wajib ada: nomor pendaftaran, nama peserta, jenis sertifikasi, nama + alamat TUK, tanggal + waktu ujikom, daftar asesor.
- Data wajib belum lengkap → sistem menolak penerbitan dengan pesan jelas.
- PDF punya nomor dokumen dan waktu penerbitan; riwayat penerbitan/penerbitan ulang tersimpan.
- Jika jadwal berubah, PDF lama ditandai **versi lama**; versi baru diterbitkan lewat proses tercatat.
- Peserta hanya mengakses undangannya sendiri.

### 3.8 Hasil ujikom
- Nilai final hanya: **Kompeten** dan **Tidak Kompeten**.
- Belum ada hasil → tampil **"Belum diumumkan"**, bukan otomatis Tidak Kompeten.
- Admin menginput; tercatat pencatat dan waktu.
- Perubahan hasil **wajib alasan**; riwayat menyimpan hasil lama, hasil baru, alasan, admin, waktu. Riwayat tidak bisa ditimpa/dihapus dari aplikasi.
- Peserta hanya melihat hasil sendiri **setelah diumumkan**.

---

## 4. User Journey

### 4.1 Alur peserta
1. Buka halaman pendaftaran (tautan / QR code).
2. Login atau buat akun.
3. Pilih Gada Pratama / Madya / Utama.
4. Pilih TUK.
5. Pilih jadwal yang tersedia.
6. Isi data diri dan formulir pendaftaran.
7. Unggah seluruh dokumen persyaratan.
8. Kirim pendaftaran.
9. Pantau status pemeriksaan dokumen.
10. Jika ada revisi: lihat alasan pada dokumen terkait, unggah versi baru.
11. Semua dokumen wajib disetujui → terima instruksi pembayaran.
12. Bayar ke rekening/VA perusahaan, unggah bukti.
13. Admin memverifikasi pembayaran.
14. Jika bukti perlu revisi, unggah bukti baru.
15. Pembayaran dikonfirmasi → akses undangan PDF.
16. Ikuti ujikom langsung.
17. Admin menginput hasil.
18. Peserta melihat hasil setelah diumumkan.

### 4.2 Dashboard peserta (stepper / journey)
Tahap: **1 Pendaftaran → 2 Verifikasi dokumen → 3 Pembayaran → 4 Undangan ujikom → 5 Hasil ujikom**.

Tiap tahap menampilkan: status, keterangan singkat, tanggal/waktu penting, tombol aksi relevan, pesan revisi yang jelas, indikator tahap saat ini.

Contoh label status: Lengkapi pendaftaran · Menunggu pemeriksaan dokumen · Perlu revisi dokumen · Lolos administrasi · Menunggu pembayaran · Menunggu verifikasi pembayaran · Perlu revisi bukti pembayaran · Pembayaran dikonfirmasi · Undangan tersedia · Ujikom selesai · Hasil diumumkan.

Status **diturunkan dari data aktual**, tidak di-hardcode.

---

## 5. Modul Admin

### 5.1 Dashboard
Statistik: total peserta; pendaftaran baru; menunggu pemeriksaan dokumen; perlu revisi; lolos administrasi; pembayaran menunggu verifikasi; pembayaran terkonfirmasi; jadwal aktif; ujikom selesai; hasil Kompeten vs Tidak Kompeten. Filter: rentang tanggal, sertifikasi, TUK, status.

### 5.2 Master data
- **Sertifikasi:** CRUD (nama, kode, warna, deskripsi, aktif); tidak boleh dihapus destruktif bila sudah dipakai.
- **TUK:** CRUD (nama, kode, alamat, kota/provinsi, kontak, aktif); cari dan filter.
- **Asesor:** CRUD (nama, nomor registrasi bila diperlukan dan sah, kontak, aktif); daftar jadwal tugas. Tanpa akun login.
- **Jadwal:** buat/ubah; pilih sertifikasi, TUK, tanggal, waktu, batas pendaftaran, status; tugaskan banyak asesor; lihat peserta per jadwal; tampilkan jumlah peserta (tanpa kuota); filter TUK/sertifikasi/tanggal/status.
- **Persyaratan dokumen:** CRUD, wajib/opsional, format + ukuran, urutan, aktif/nonaktif, perlindungan terhadap pendaftaran lama.

### 5.3 Manajemen pendaftaran
Daftar semua pendaftaran; cari (nomor, nama, dll.); filter sertifikasi/TUK/jadwal/tanggal/status; detail: profil, dokumen + versi + riwayat pemeriksaan, riwayat pembayaran, riwayat status, asesor, hasil.

### 5.4 Verifikasi dokumen
Buka dokumen via akses terotorisasi; setujui; minta revisi dengan alasan; lihat versi sebelumnya; lihat siapa/kapan unggah dan periksa. Revisi satu dokumen **tidak** memaksa unggah ulang dokumen lain yang sudah disetujui.

### 5.5 Verifikasi pembayaran
Lihat bukti; lihat nominal diharapkan vs dikonfirmasi; minta revisi (alasan); konfirmasi; catat waktu + referensi transaksi; lihat semua versi bukti.

### 5.6 Undangan dan hasil
Terbitkan PDF; lihat versi sebelumnya; terbitkan ulang; input hasil; koreksi hasil (alasan wajib); lihat riwayat hasil; umumkan hasil.

### 5.7 Laporan
Minimum: pendaftaran per periode / sertifikasi / TUK; peserta per jadwal; kelengkapan dokumen; status pembayaran; rekap penerimaan (terkonfirmasi); peserta per hasil; riwayat perubahan hasil; rekap keseluruhan.
Filter: periode, sertifikasi, TUK, jadwal, status pendaftaran, status administrasi, status pembayaran, hasil. Pagination. Ekspor Excel/CSV (dan PDF bila relevan) **memakai filter yang sama** dengan tampilan; otorisasi sebelum ekspor; tanpa file dokumen sensitif.

### 5.8 Import Excel peserta
1. Unduh template → 2. Unggah → 3. Validasi kolom → 4. Cek data wajib → 5. Cek duplikasi → 6. Preview valid/tidak valid → 7. Pesan error per baris → 8. Konfirmasi admin → 9. Simpan dalam transaction/batch aman → 10. Ringkasan hasil.

Import **tidak** menandai dokumen disetujui atau pembayaran lunas. Akun hasil import butuh alur aktivasi aman (mis. tautan set password). **Jangan** kirim/menyimpan password plaintext di Excel atau laporan.

### 5.9 Notifikasi
Notifikasi **dalam aplikasi** wajib untuk: pendaftaran terkirim; dokumen perlu revisi; dokumen disetujui; lolos administrasi; instruksi pembayaran tersedia; bukti pembayaran perlu revisi; pembayaran dikonfirmasi; undangan tersedia; jadwal berubah; hasil diumumkan. Peserta bisa melihat yang belum dibaca dan menandai dibaca.

Email/WhatsApp opsional bila provider dan kredensial tersedia. **Jangan** mengklaim pesan terkirim tanpa hasil pengiriman valid; kegagalan layanan eksternal tidak boleh merusak transaksi.

### 5.10 QR code dan akses pendaftaran
URL pendaftaran stabil untuk QR di flyer; opsional mengarah ke halaman sertifikasi tertentu; belum login → arahkan ke login/registrasi lalu kembali ke tujuan (intended URL). QR **tidak** berisi password, token rahasia, NIK, atau data pribadi dan tidak memberi akses otomatis ke data siapa pun.

---

## 6. Halaman

**Publik:** landing LSP Pamtara; info sertifikasi; info TUK; alur pendaftaran; persyaratan dokumen; panduan pembayaran; login; registrasi.
**Peserta:** dashboard journey; profil; form pendaftaran (sertifikasi → TUK → jadwal); upload dokumen; detail revisi; instruksi & status pembayaran; upload bukti; undangan PDF; hasil; notifikasi; riwayat.
**Admin:** dashboard; sertifikasi; TUK; asesor; jadwal; persyaratan; pendaftaran; verifikasi dokumen; verifikasi pembayaran; undangan & hasil; import Excel; laporan; notifikasi; pengaturan aplikasi; audit log.

## 7. UI/UX
- Modern, profesional, bersih, **responsive** (desktop, tablet, mobile; mobile-first karena peserta banyak memakai ponsel).
- Bahasa utama **Bahasa Indonesia**.
- Label status/aksi/error yang jelas; hindari dashboard terlalu padat.
- Warna sertifikasi konsisten; ada loading, empty, error state, dan dialog konfirmasi.
- Form panjang dibagi per bagian; validasi tampil dekat field; status proses ditampilkan visual.

---

## 8. Acceptance Criteria

**AC-01 Registrasi peserta** — Akun bisa dibuat dengan data valid; login/logout berfungsi; registrasi tidak memberi akses admin; identifier unik tidak bisa didaftarkan ulang.

**AC-02 Pendaftaran sertifikasi** — Tiga sertifikasi tersedia; formulir dan persyaratan sama; TUK dan jadwal tersedia bisa dipilih; jadwal ditutup tidak bisa dipilih; pendaftaran menghasilkan nomor unik.

**AC-03 Tanpa kuota** — Tidak ada kolom kuota; peserta tambahan tidak ditolak karena jumlah; tidak ada batas asesi per asesor per hari; statistik jumlah pendaftar tetap benar.

**AC-04 Verifikasi dokumen** — Admin bisa setujui / minta revisi (alasan); peserta hanya unggah ulang dokumen yang diminta; versi lama tetap ada; dokumen wajib belum disetujui mencegah lolos administrasi; dokumen opsional kosong tidak menghalangi.

**AC-05 Pembayaran** — Instruksi muncul hanya setelah lolos administrasi; peserta bisa unggah bukti; admin bisa setujui/minta revisi; versi lama dan baru tersimpan; unggah bukti ≠ lunas; lunas hanya setelah konfirmasi admin.

**AC-06 PDF undangan** — Belum bayar tidak bisa akses; data dari database; memuat peserta, sertifikasi, TUK, jadwal, asesor; tidak bisa diakses peserta lain; perubahan jadwal dan penerbitan ulang tertelusuri.

**AC-07 Hasil ujikom** — Admin input Kompeten/Tidak Kompeten; peserta tidak bisa mengubah; belum diumumkan ≠ Tidak Kompeten; perubahan wajib alasan; riwayat menyimpan hasil lama, baru, alasan, actor, timestamp.

**AC-08 Import dan laporan** — Struktur Excel salah ditolak dengan pesan jelas; duplikat dikenali; ada preview sebelum simpan; import tidak menyetujui dokumen/pembayaran otomatis; ekspor sesuai filter.

**AC-09 Keamanan** — Peserta tidak bisa akses route admin atau data peserta lain; dokumen privat tidak bisa diakses dengan menebak URL; input tidak valid ditolak di backend; audit trail tersedia; password tidak plaintext.

## 9. Definition of Done (versi awal)
- Semua migration berjalan; auth + role access berfungsi.
- Admin kelola master data dan jadwal; peserta mendaftar dan unggah dokumen.
- Verifikasi dokumen; kelulusan administrasi sesuai dokumen wajib.
- Pembayaran rekening/VA bersama dengan verifikasi manual; revisi tidak menghapus versi lama.
- PDF undangan dari database; hasil dengan histori.
- Laporan, import/export sesuai spesifikasi; dokumen sensitif terlindungi.
- Seluruh Acceptance Criteria (§8) lolos **uji manual oleh pemilik produk**; agent menyediakan daftar langkah uji manual per fase dan tidak mengklaim "teruji" tanpa menjalankannya.
- README: instalasi, env, migration, seeding, deployment, cara membuat admin pertama, panduan uji manual singkat.
- Tidak ada fitur inti yang hanya berupa tombol/tampilan tanpa backend.

**Target akhir:** aplikasi berfungsi end-to-end, database terstruktur, aman untuk data peserta, siap UAT sebelum produksi.
