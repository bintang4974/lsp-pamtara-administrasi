<x-layouts.admin title="Dashboard">
    <h1 class="text-xl font-semibold text-gray-900">Dashboard Admin</h1>

    <form method="GET" class="mt-4 grid gap-3 sm:grid-cols-5">
        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="min-h-[44px] rounded-md border-gray-300 text-sm">
        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="min-h-[44px] rounded-md border-gray-300 text-sm">
        <select name="certification_type_id" class="min-h-[44px] rounded-md border-gray-300 text-sm">
            <option value="">Semua Sertifikasi</option>
            @foreach($certificationTypes as $type)
                <option value="{{ $type->id }}" {{ (string) ($filters['certification_type_id'] ?? '') === (string) $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
            @endforeach
        </select>
        <select name="tuk_id" class="min-h-[44px] rounded-md border-gray-300 text-sm">
            <option value="">Semua TUK</option>
            @foreach($tuks as $tuk)
                <option value="{{ $tuk->id }}" {{ (string) ($filters['tuk_id'] ?? '') === (string) $tuk->id ? 'selected' : '' }}>{{ $tuk->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="min-h-[44px] rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Filter</button>
    </form>

    <div class="mt-6 grid gap-4 sm:grid-cols-3 lg:grid-cols-4">
        @foreach([
            ['Total Admin', $totalAdmins],
            ['Total Peserta', $totalParticipants],
            ['Pendaftaran (filter)', $newRegistrations],
            ['Menunggu Pemeriksaan Dokumen', $awaitingDocumentReview],
            ['Perlu Revisi Dokumen', $needsRevision],
            ['Lolos Administrasi', $administrativelyApproved],
            ['Pembayaran Menunggu Verifikasi', $paymentUnderReview],
            ['Pembayaran Terkonfirmasi', $paymentConfirmed],
            ['Ujikom Selesai', $completed],
            ['Jadwal Aktif', $activeSchedules],
            ['Hasil Kompeten', $resultCompetent],
            ['Hasil Tidak Kompeten', $resultNotCompetent],
        ] as [$label, $value])
            <div class="rounded-lg border border-gray-200 bg-white p-5">
                <p class="text-sm text-gray-500">{{ $label }}</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $value }}</p>
            </div>
        @endforeach

        <div class="rounded-lg border border-blue-200 bg-blue-50 p-5">
            <p class="text-sm text-blue-700">Rekap Penerimaan (Terkonfirmasi)</p>
            <p class="mt-1 text-2xl font-bold text-blue-900">Rp {{ number_format($confirmedPaymentTotal, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('admin.reports.registrations') }}" class="inline-flex min-h-[44px] items-center rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Laporan Pendaftaran</a>
        <a href="{{ route('admin.reports.payments') }}" class="inline-flex min-h-[44px] items-center rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Laporan Pembayaran</a>
        <a href="{{ route('admin.reports.results') }}" class="inline-flex min-h-[44px] items-center rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Laporan Hasil</a>
    </div>
</x-layouts.admin>
