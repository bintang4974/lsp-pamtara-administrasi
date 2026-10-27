<x-layouts.admin title="TUK">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Tempat Uji Kompetensi (TUK)</h1>
        <a href="{{ route('admin.tuks.create') }}" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-4 text-sm font-medium text-white hover:bg-blue-800">
            Tambah TUK
        </a>
    </div>

    <form method="GET" class="mt-4">
        <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, kode, atau kota..."
            class="min-h-[44px] w-full max-w-sm rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </form>

    @if($tuks->isEmpty())
        <div class="mt-6">
            <x-empty-state title="Belum ada TUK" description="Tambahkan data TUK resmi. Jangan mengarang nama/alamat TUK." />
        </div>
    @else
        <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Nama</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Kota/Provinsi</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($tuks as $tuk)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ $tuk->name }}</p>
                                <p class="text-xs text-gray-500">{{ $tuk->code }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $tuk->city }}, {{ $tuk->province }}</td>
                            <td class="px-4 py-3">
                                <x-status-badge :label="$tuk->is_active ? 'Aktif' : 'Nonaktif'" :tone="$tuk->is_active ? 'success' : 'neutral'" />
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.tuks.edit', $tuk) }}" class="min-h-[44px] content-center rounded-md px-3 text-sm font-medium text-blue-700 hover:bg-blue-50">
                                        Ubah
                                    </a>
                                    <button type="button" x-data x-on:click="$dispatch('open-modal-toggle-tuk-{{ $tuk->id }}')"
                                        class="min-h-[44px] rounded-md px-3 text-sm font-medium text-gray-700 hover:bg-gray-100">
                                        {{ $tuk->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>

                                    <x-confirm-modal :id="'toggle-tuk-'.$tuk->id" title="Konfirmasi"
                                        :form-action="route('admin.tuks.toggle', $tuk)" form-method="PATCH"
                                        :confirm-label="$tuk->is_active ? 'Nonaktifkan' : 'Aktifkan'">
                                        Yakin ingin {{ $tuk->is_active ? 'menonaktifkan' : 'mengaktifkan' }} TUK
                                        <strong>{{ $tuk->name }}</strong>?
                                        @if($tuk->is_active)
                                            TUK nonaktif tidak ditawarkan untuk jadwal baru, tetapi tetap tampil pada riwayat/dokumen lama.
                                        @endif
                                    </x-confirm-modal>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $tuks->links() }}</div>
    @endif
</x-layouts.admin>
