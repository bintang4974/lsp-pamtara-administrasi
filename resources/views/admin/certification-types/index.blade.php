<x-layouts.admin title="Sertifikasi">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900">Sertifikasi</h1>
        <a href="{{ route('admin.certification-types.create') }}" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-4 text-sm font-medium text-white hover:bg-blue-800">
            Tambah Sertifikasi
        </a>
    </div>

    @if($certificationTypes->isEmpty())
        <div class="mt-6">
            <x-empty-state title="Belum ada sertifikasi" description="Tambahkan Gada Pratama, Gada Madya, dan Gada Utama." />
        </div>
    @else
        <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Sertifikasi</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Kode</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($certificationTypes as $type)
                        <tr>
                            <td class="px-4 py-3"><x-cert-badge :certification-type="$type" /></td>
                            <td class="px-4 py-3 text-gray-700">{{ $type->code }}</td>
                            <td class="px-4 py-3">
                                <x-status-badge :label="$type->is_active ? 'Aktif' : 'Nonaktif'" :tone="$type->is_active ? 'success' : 'neutral'" />
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.certification-types.edit', $type) }}" class="min-h-[44px] content-center rounded-md px-3 text-sm font-medium text-blue-700 hover:bg-blue-50">
                                        Ubah
                                    </a>
                                    <button type="button" x-data x-on:click="$dispatch('open-modal-toggle-cert-{{ $type->id }}')"
                                        class="min-h-[44px] rounded-md px-3 text-sm font-medium text-gray-700 hover:bg-gray-100">
                                        {{ $type->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>

                                    <x-confirm-modal :id="'toggle-cert-'.$type->id" title="Konfirmasi"
                                        :form-action="route('admin.certification-types.toggle', $type)" form-method="PATCH"
                                        :confirm-label="$type->is_active ? 'Nonaktifkan' : 'Aktifkan'">
                                        Yakin ingin {{ $type->is_active ? 'menonaktifkan' : 'mengaktifkan' }} sertifikasi
                                        <strong>{{ $type->name }}</strong>?
                                        @if($type->is_active)
                                            Sertifikasi nonaktif tidak akan ditawarkan untuk pendaftaran baru, namun data lama tetap tersimpan.
                                        @endif
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
