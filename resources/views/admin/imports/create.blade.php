<x-layouts.admin title="Import Peserta">
    <h1 class="text-xl font-semibold text-gray-900">Import Peserta dari Excel</h1>

    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6">
        <p class="text-sm text-gray-600">
            Unduh template, isi data peserta, lalu unggah untuk divalidasi sebelum disimpan.
            Import hanya membuat akun peserta — tidak menyentuh status dokumen/pembayaran.
        </p>

        <a href="{{ route('admin.imports.template') }}" class="mt-3 inline-flex min-h-[44px] items-center rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Unduh Template
        </a>

        <form method="POST" action="{{ route('admin.imports.store') }}" enctype="multipart/form-data" class="mt-6">
            @csrf
            <x-input-label for="file" value="File Excel/CSV" />
            <input id="file" name="file" type="file" required accept=".xlsx,.xls,.csv" class="mt-1 block w-full text-sm">
            <x-input-error :messages="$errors->get('file')" class="mt-2" />

            <button type="submit" class="mt-4 inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-5 text-sm font-medium text-white hover:bg-blue-800">
                Unggah &amp; Validasi
            </button>
        </form>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6">
        <h2 class="font-semibold text-gray-900">Riwayat Import</h2>

        @if($batches->isEmpty())
            <p class="mt-2 text-sm text-gray-500">Belum ada import.</p>
        @else
            <div class="mt-3 space-y-2">
                @foreach($batches as $batch)
                    <a href="{{ route('admin.imports.show', $batch) }}" class="block rounded-md border border-gray-100 p-3 text-sm hover:bg-gray-50">
                        <span class="font-medium text-gray-900">{{ $batch->original_filename }}</span>
                        <x-status-badge :label="$batch->status" tone="neutral" class="ms-2" />
                        <span class="text-gray-500">&middot; {{ $batch->created_at->format('d M Y H:i') }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.admin>
