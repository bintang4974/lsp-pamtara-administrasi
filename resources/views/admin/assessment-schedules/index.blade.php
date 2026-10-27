<x-layouts.admin title="Jadwal Ujikom">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Jadwal Ujikom</h1>
        <a href="{{ route('admin.assessment-schedules.create') }}" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-4 text-sm font-medium text-white hover:bg-blue-800">
            Tambah Jadwal
        </a>
    </div>

    <form method="GET" class="mt-4 grid gap-3 sm:grid-cols-4">
        <select name="certification_type_id" class="min-h-[44px] rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Semua Sertifikasi</option>
            @foreach($certificationTypes as $type)
                <option value="{{ $type->id }}" {{ (string) ($filters['certification_type_id'] ?? '') === (string) $type->id ? 'selected' : '' }}>
                    {{ $type->name }}
                </option>
            @endforeach
        </select>

        <select name="tuk_id" class="min-h-[44px] rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Semua TUK</option>
            @foreach($tuks as $tuk)
                <option value="{{ $tuk->id }}" {{ (string) ($filters['tuk_id'] ?? '') === (string) $tuk->id ? 'selected' : '' }}>
                    {{ $tuk->name }}
                </option>
            @endforeach
        </select>

        <select name="status" class="min-h-[44px] rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Semua Status</option>
            @foreach(['draft' => 'Draft', 'open' => 'Dibuka', 'closed' => 'Ditutup', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $value => $label)
                <option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <button type="submit" class="min-h-[44px] rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Terapkan Filter
        </button>
    </form>

    @if($schedules->isEmpty())
        <div class="mt-6">
            <x-empty-state title="Belum ada jadwal" description="Tambahkan jadwal ujikom per sertifikasi dan TUK." />
        </div>
    @else
        <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Jadwal</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Sertifikasi</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">TUK</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Waktu</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Peserta</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($schedules as $schedule)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $schedule->schedule_code }}</td>
                            <td class="px-4 py-3"><x-cert-badge :certification-type="$schedule->certificationType" /></td>
                            <td class="px-4 py-3 text-gray-700">{{ $schedule->tuk->name }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $schedule->starts_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $schedule->registrations_count }} peserta</td>
                            <td class="px-4 py-3">
                                <x-status-badge :label="$schedule->status->label()" :tone="match($schedule->status->value) {
                                    'open' => 'success',
                                    'draft' => 'neutral',
                                    'closed' => 'warning',
                                    'cancelled' => 'danger',
                                    default => 'info',
                                }" />
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.assessment-schedules.edit', $schedule) }}" class="min-h-[44px] content-center rounded-md px-3 text-sm font-medium text-blue-700 hover:bg-blue-50">
                                    Ubah
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $schedules->links() }}</div>
    @endif
</x-layouts.admin>
