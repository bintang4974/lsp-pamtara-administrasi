<x-layouts.admin title="Persyaratan Dokumen">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Persyaratan Dokumen</h1>
        <a href="{{ route('admin.document-requirements.create') }}" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-4 text-sm font-medium text-white hover:bg-blue-800">
            Tambah Persyaratan
        </a>
    </div>

    <p class="mt-2 text-sm text-gray-500">
        Satu daftar berlaku untuk ketiga sertifikasi. Perubahan di sini tidak memengaruhi pendaftaran yang sudah ada (snapshot).
    </p>

    @if($documentRequirements->isEmpty())
        <div class="mt-6">
            <x-empty-state title="Belum ada persyaratan dokumen" description="Tambahkan daftar persyaratan resmi (KTP, ijazah, dll.)." />
        </div>
    @else
        <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Urutan</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Dokumen</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Wajib?</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Maks. Ukuran</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($documentRequirements as $requirement)
                        <tr>
                            <td class="px-4 py-3 text-gray-700">{{ $requirement->sort_order }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ $requirement->name }}</p>
                                <p class="text-xs text-gray-500">{{ $requirement->code }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <x-status-badge :label="$requirement->is_required ? 'Wajib' : 'Opsional'" :tone="$requirement->is_required ? 'warning' : 'neutral'" />
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ number_format($requirement->max_file_size_kb) }} KB</td>
                            <td class="px-4 py-3">
                                <x-status-badge :label="$requirement->is_active ? 'Aktif' : 'Nonaktif'" :tone="$requirement->is_active ? 'success' : 'neutral'" />
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.document-requirements.edit', $requirement) }}" class="min-h-[44px] content-center rounded-md px-3 text-sm font-medium text-blue-700 hover:bg-blue-50">
                                        Ubah
                                    </a>
                                    <button type="button" x-data x-on:click="$dispatch('open-modal-toggle-req-{{ $requirement->id }}')"
                                        class="min-h-[44px] rounded-md px-3 text-sm font-medium text-gray-700 hover:bg-gray-100">
                                        {{ $requirement->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                    <button type="button" x-data x-on:click="$dispatch('open-modal-delete-req-{{ $requirement->id }}')"
                                        class="min-h-[44px] rounded-md px-3 text-sm font-medium text-red-600 hover:bg-red-50">
                                        Hapus
                                    </button>

                                    <x-confirm-modal :id="'toggle-req-'.$requirement->id" title="Konfirmasi"
                                        :form-action="route('admin.document-requirements.toggle', $requirement)" form-method="PATCH"
                                        :confirm-label="$requirement->is_active ? 'Nonaktifkan' : 'Aktifkan'">
                                        Yakin ingin {{ $requirement->is_active ? 'menonaktifkan' : 'mengaktifkan' }} persyaratan
                                        <strong>{{ $requirement->name }}</strong>?
                                    </x-confirm-modal>

                                    <x-confirm-modal :id="'delete-req-'.$requirement->id" title="Hapus Persyaratan"
                                        :form-action="route('admin.document-requirements.destroy', $requirement)" form-method="DELETE"
                                        confirm-label="Hapus">
                                        Yakin ingin menghapus persyaratan <strong>{{ $requirement->name }}</strong>?
                                        Pendaftaran lama tidak terpengaruh karena memakai snapshot persyaratan.
                                    </x-confirm-modal>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.admin>
