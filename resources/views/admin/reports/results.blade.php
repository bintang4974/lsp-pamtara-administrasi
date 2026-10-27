<x-layouts.admin title="Laporan Hasil">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Laporan Hasil Ujikom</h1>
        <a href="{{ route('admin.reports.results.export', $filters) }}" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-4 text-sm font-medium text-white hover:bg-blue-800">
            Ekspor Excel
        </a>
    </div>

    <form method="GET" class="mt-4 grid gap-3 sm:grid-cols-4">
        <select name="result" class="min-h-[44px] rounded-md border-gray-300 text-sm">
            <option value="">Semua Hasil</option>
            <option value="competent" {{ ($filters['result'] ?? '') === 'competent' ? 'selected' : '' }}>Kompeten</option>
            <option value="not_competent" {{ ($filters['result'] ?? '') === 'not_competent' ? 'selected' : '' }}>Tidak Kompeten</option>
        </select>
        <select name="announced" class="min-h-[44px] rounded-md border-gray-300 text-sm">
            <option value="">Semua</option>
            <option value="yes" {{ ($filters['announced'] ?? '') === 'yes' ? 'selected' : '' }}>Sudah Diumumkan</option>
            <option value="no" {{ ($filters['announced'] ?? '') === 'no' ? 'selected' : '' }}>Belum Diumumkan</option>
        </select>
        <button type="submit" class="min-h-[44px] rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Filter</button>
    </form>

    @if($results->isEmpty())
        <div class="mt-6"><x-empty-state title="Tidak ada data" /></div>
    @else
        <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">No. Pendaftaran</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Peserta</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Sertifikasi</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Hasil</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Diumumkan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($results as $result)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $result->registration->registration_number }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $result->registration->user->name }}</td>
                            <td class="px-4 py-3"><x-cert-badge :certification-type="$result->registration->certificationType" /></td>
                            <td class="px-4 py-3"><x-status-badge :label="$result->result->label()" :tone="$result->result->value === 'competent' ? 'success' : 'danger'" /></td>
                            <td class="px-4 py-3 text-gray-700">{{ $result->announced_at?->format('d M Y H:i') ?? 'Belum' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $results->links() }}</div>
    @endif
</x-layouts.admin>
