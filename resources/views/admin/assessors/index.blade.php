<x-layouts.admin title="Asesor">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Asesor</h1>
        <a href="{{ route('admin.assessors.create') }}" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-4 text-sm font-medium text-white hover:bg-blue-800">
            Tambah Asesor
        </a>
    </div>

    @if($assessors->isEmpty())
        <div class="mt-6">
            <x-empty-state title="Belum ada asesor" description="Tambahkan asesor yang akan ditugaskan ke jadwal ujikom." />
        </div>
    @else
        <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Nama</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">No. Registrasi</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($assessors as $assessor)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $assessor->name }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $assessor->registration_number ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <x-status-badge :label="$assessor->is_active ? 'Aktif' : 'Nonaktif'" :tone="$assessor->is_active ? 'success' : 'neutral'" />
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.assessors.edit', $assessor) }}" class="min-h-[44px] content-center rounded-md px-3 text-sm font-medium text-blue-700 hover:bg-blue-50">
                                        Ubah
                                    </a>
                                    <button type="button" x-data x-on:click="$dispatch('open-modal-toggle-assessor-{{ $assessor->id }}')"
                                        class="min-h-[44px] rounded-md px-3 text-sm font-medium text-gray-700 hover:bg-gray-100">
                                        {{ $assessor->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>

                                    <x-confirm-modal :id="'toggle-assessor-'.$assessor->id" title="Konfirmasi"
                                        :form-action="route('admin.assessors.toggle', $assessor)" form-method="PATCH"
                                        :confirm-label="$assessor->is_active ? 'Nonaktifkan' : 'Aktifkan'">
                                        Yakin ingin {{ $assessor->is_active ? 'menonaktifkan' : 'mengaktifkan' }} asesor
                                        <strong>{{ $assessor->name }}</strong>?
                                    </x-confirm-modal>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $assessors->links() }}</div>
    @endif
</x-layouts.admin>
