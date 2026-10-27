<x-layouts.admin title="Riwayat Perubahan Hasil">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Riwayat Perubahan Hasil</h1>
        <a href="{{ route('admin.reports.result-history.export', $filters) }}" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-4 text-sm font-medium text-white hover:bg-blue-800">
            Ekspor Excel
        </a>
    </div>

    <form method="GET" class="mt-4 grid gap-3 sm:grid-cols-3">
        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="min-h-[44px] rounded-md border-gray-300 text-sm">
        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="min-h-[44px] rounded-md border-gray-300 text-sm">
        <button type="submit" class="min-h-[44px] rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Filter</button>
    </form>

    @if($histories->isEmpty())
        <div class="mt-6"><x-empty-state title="Tidak ada data" /></div>
    @else
        <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">No. Pendaftaran</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Peserta</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Perubahan</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Alasan</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Oleh</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($histories as $history)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $history->assessmentResult->registration->registration_number }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $history->assessmentResult->registration->user->name }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $history->old_result?->label() ?? '-' }} &rarr; {{ $history->new_result->label() }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $history->reason }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $history->changedBy?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $history->changed_at->format('d M Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $histories->links() }}</div>
    @endif
</x-layouts.admin>
