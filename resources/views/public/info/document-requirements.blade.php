<x-layouts.public title="Persyaratan Dokumen">
    <h1 class="text-2xl font-bold text-gray-900">Persyaratan Dokumen</h1>
    <p class="mt-2 text-gray-600">Berlaku sama untuk Gada Pratama, Gada Madya, dan Gada Utama.</p>

    @if($documentRequirements->isEmpty())
        <div class="mt-6">
            <x-empty-state title="Belum ada persyaratan aktif" />
        </div>
    @else
        <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Dokumen</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Wajib?</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Jenis File</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Maks. Ukuran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($documentRequirements as $requirement)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ $requirement->name }}</p>
                                @if($requirement->description)
                                    <p class="text-xs text-gray-500">{{ $requirement->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <x-status-badge :label="$requirement->is_required ? 'Wajib' : 'Opsional'" :tone="$requirement->is_required ? 'warning' : 'neutral'" />
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ implode(', ', $requirement->allowed_mime_types) }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ number_format($requirement->max_file_size_kb) }} KB</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.public>
