<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #111827; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        .subtitle { color: #6b7280; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        td { padding: 6px 4px; vertical-align: top; }
        td.label { width: 180px; color: #6b7280; }
        .footer { margin-top: 40px; font-size: 10px; color: #9ca3af; }
        .doc-number { text-align: right; font-size: 11px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="doc-number">No. Dokumen: {{ $documentNumber }} &middot; Diterbitkan: {{ $issuedAt->format('d M Y H:i') }}</div>

    <h1>Undangan Uji Kompetensi</h1>
    <p class="subtitle">LSP Pamtara — Sertifikasi Kompetensi Satpam</p>

    <table>
        <tr><td class="label">No. Pendaftaran</td><td>{{ $data['registration_number'] }}</td></tr>
        <tr><td class="label">Nama Peserta</td><td>{{ $data['participant_name'] }}</td></tr>
        <tr><td class="label">Jenis Sertifikasi</td><td>{{ $data['certification_name'] }}</td></tr>
        <tr><td class="label">Tempat Uji Kompetensi</td><td>{{ $data['tuk_name'] }}<br>{{ $data['tuk_address'] }}</td></tr>
        <tr><td class="label">Tanggal &amp; Waktu</td><td>
            {{ \Illuminate\Support\Carbon::parse($data['starts_at'])->format('d M Y H:i') }}
            s.d.
            {{ \Illuminate\Support\Carbon::parse($data['ends_at'])->format('H:i') }}
        </td></tr>
        <tr><td class="label">Asesor</td><td>{{ implode(', ', $data['assessors']) }}</td></tr>
    </table>

    <p style="margin-top: 24px;">
        Harap membawa dokumen identitas asli dan hadir 30 menit sebelum waktu ujikom dimulai.
    </p>

    <div class="footer">
        Dokumen ini diterbitkan otomatis oleh sistem berdasarkan data pendaftaran.
        {{ \App\Models\ApplicationSetting::getValue('pdf.footer') }}
    </div>
</body>
</html>
