<x-layouts.admin title="Preview Import">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Preview Import — {{ $batch->original_filename }}</h1>
        <x-status-badge :label="$batch->status" tone="neutral" />
    </div>

    <div class="mt-4 grid gap-4 sm:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">Total Baris</p>
            <p class="text-2xl font-bold text-gray-900">{{ $batch->summary['total_rows'] ?? 0 }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">Valid</p>
            <p class="text-2xl font-bold text-green-700">{{ $batch->summary['valid_rows'] ?? 0 }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">Tidak Valid</p>
            <p class="text-2xl font-bold text-red-700">{{ $batch->summary['invalid_rows'] ?? 0 }}</p>
        </div>
    </div>

    @if($batch->status === 'previewed')
        <div class="mt-4 flex gap-3">
            <button type="button" x-data x-on:click="$dispatch('open-modal-commit-import')"
                class="min-h-[44px] rounded-md bg-blue-700 px-5 text-sm font-medium text-white hover:bg-blue-800">
                Konfirmasi &amp; Simpan ({{ $batch->summary['valid_rows'] ?? 0 }} baris valid)
            </button>
            <form method="POST" action="{{ route('admin.imports.discard', $batch) }}">
                @csrf
                <button type="submit" class="min-h-[44px] rounded-md border border-gray-300 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Buang
                </button>
            </form>

            <x-confirm-modal id="commit-import" title="Konfirmasi Import"
                :form-action="route('admin.imports.commit', $batch)" confirm-label="Simpan">
                Hanya baris valid yang akan disimpan sebagai akun peserta baru. Password acak dibuat dan tautan aktivasi diproses lewat sistem reset password. Lanjutkan?
            </x-confirm-modal>
        </div>
    @elseif($batch->status === 'committed')
        <x-alert type="success" class="mt-4">
            Import selesai {{ $batch->committed_at?->format('d M Y H:i') }} — {{ $batch->summary['created_accounts'] ?? 0 }} akun dibuat.
        </x-alert>
    @else
        <x-alert type="warning" class="mt-4">Batch ini telah dibuang.</x-alert>
    @endif

    <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Baris</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Nama</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Email</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Error</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($batch->rows as $row)
                    <tr>
                        <td class="px-4 py-3 text-gray-700">{{ $row->row_number }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $row->raw_data['name'] ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $row->raw_data['email'] ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <x-status-badge :label="$row->is_valid ? 'Valid' : 'Tidak Valid'" :tone="$row->is_valid ? 'success' : 'danger'" />
                        </td>
                        <td class="px-4 py-3 text-xs text-red-600">{{ $row->errors ? implode('; ', $row->errors) : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>
