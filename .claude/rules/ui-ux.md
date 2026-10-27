---
description: Aturan UI/UX — Blade, Tailwind, responsive, Bahasa Indonesia
paths:
  - "resources/views/**/*.blade.php"
  - "resources/css/**/*"
  - "resources/js/**/*"
  - "tailwind.config.*"
---

# UI/UX (Blade + Tailwind, responsive)

## Responsive (wajib)
- **Mobile-first:** kelas dasar untuk layar kecil, lalu `sm:` `md:` `lg:`. Uji di 360px, 768px, 1280px.
- Tidak ada scroll horizontal halaman. Tabel lebar: bungkus `overflow-x-auto` atau ubah jadi kartu di mobile.
- Target sentuh ≥ 44px; font dasar ≥ 16px pada input (cegah zoom iOS).
- Admin: sidebar menjadi drawer di mobile (Alpine.js). Peserta: navigasi sederhana.

## Bahasa dan teks
- Seluruh teks UI **Bahasa Indonesia**, jelas dan singkat. Pesan error spesifik dan dapat ditindaklanjuti.
- Pesan revisi dokumen/pembayaran tampil **di item terkait**, bukan hanya di halaman umum.

## Komponen
- Gunakan komponen Blade bersama (`<x-status-badge>`, `<x-cert-badge>`, `<x-stepper>`, `<x-empty-state>`, `<x-confirm-modal>`, `<x-file-upload>`, `<x-alert>`). Jangan menyalin markup yang sama berulang.
- Warna sertifikasi (Pratama biru, Madya kuning, Utama merah) dari `certification_types.color_code`, **selalu disertai label teks**; hitung warna teks agar kontras terbaca.
- Status badge: teks + warna (jangan hanya warna).

## State dan interaksi
- Sediakan loading (tombol submit disable saat proses), empty state, error state, dan dialog konfirmasi untuk aksi destruktif/penting (setujui, minta revisi, konfirmasi bayar, koreksi hasil).
- Form panjang dibagi per langkah/section. Validasi tampil dekat field (`@error`) dan nilai lama dipertahankan (`old()`).
- Stepper dashboard peserta dihitung dari data nyata (via `JourneyPresenter`), **bukan hardcoded**.

## Teknis
- Blade hanya menampilkan; logika di service/presenter.
- `{{ }}` untuk output; hindari `{!! !!}`.
- Alpine.js seperlunya; tidak menambah framework JS besar.
- Aksesibilitas: `<label>` untuk semua input, fokus terlihat, `alt` pada gambar, kontras cukup.
- Gunakan Vite; jangan memuat CSS/JS dari CDN di produksi.
