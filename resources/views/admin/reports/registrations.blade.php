<x-layouts.admin title="Laporan Pendaftaran">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Laporan Pendaftaran</h1>
        <a href="{{ route('admin.reports.registrations.export', $filters) }}" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-4 text-sm font-medium text-white hover:bg-blue-800">
            Ekspor Excel
        </a>
    </div>

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

    @if($registrations->isEmpty())
        <div class="mt-6"><x-empty-state title="Tidak ada data" /></div>
    @else
        <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">No. Pendaftaran</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Peserta</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Sertifikasi</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">TUK</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($registrations as $registration)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $registration->registration_number }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $registration->user->name }}</td>
                            <td class="px-4 py-3"><x-cert-badge :certification-type="$registration->certificationType" /></td>
                            <td class="px-4 py-3 text-gray-700">{{ $registration->assessmentSchedule->tuk->name }}</td>
                            <td class="px-4 py-3"><x-status-badge :label="$registration->status->label()" tone="info" /></td>
                            <td class="px-4 py-3 text-gray-700">{{ $registration->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $registrations->links() }}</div>
    @endif
</x-layouts.admin>
