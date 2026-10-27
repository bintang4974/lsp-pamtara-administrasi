<x-layouts.admin title="Audit Log">
    <h1 class="text-xl font-semibold text-gray-900">Audit Log</h1>

    <form method="GET" class="mt-4 grid gap-3 sm:grid-cols-5">
        <input type="text" name="action" value="{{ $filters['action'] ?? '' }}" placeholder="Cari aksi..." class="min-h-[44px] rounded-md border-gray-300 text-sm">
        <select name="entity_type" class="min-h-[44px] rounded-md border-gray-300 text-sm">
            <option value="">Semua Entitas</option>
            @foreach($entityTypes as $type)
                <option value="{{ $type }}" {{ ($filters['entity_type'] ?? '') === $type ? 'selected' : '' }}>{{ class_basename($type) }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="min-h-[44px] rounded-md border-gray-300 text-sm">
        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="min-h-[44px] rounded-md border-gray-300 text-sm">
        <button type="submit" class="min-h-[44px] rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Filter</button>
    </form>

    @if($logs->isEmpty())
        <div class="mt-6"><x-empty-state title="Tidak ada data" /></div>
    @else
        <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Waktu</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Aksi</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Entitas</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Aktor</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Alasan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($logs as $log)
                        <tr>
                            <td class="px-4 py-3 text-gray-700">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-900">{{ $log->action }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ class_basename($log->entity_type) }} #{{ $log->entity_id }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $log->actor?->name ?? 'Sistem' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $log->reason ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $logs->links() }}</div>
    @endif
</x-layouts.admin>
