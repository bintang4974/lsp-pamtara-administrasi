<x-layouts.admin title="Ubah Sertifikasi">
    <div class="mx-auto max-w-xl">
        <h1 class="text-xl font-semibold text-gray-900">Ubah Sertifikasi</h1>

        <form method="POST" action="{{ route('admin.certification-types.update', $certificationType) }}" class="mt-6 rounded-lg border border-gray-200 bg-white p-6">
            @csrf
            @method('PUT')
            <x-admin.certification-types.form :certification-type="$certificationType" />

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.certification-types.index') }}" class="inline-flex min-h-[44px] items-center rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-5 text-sm font-medium text-white hover:bg-blue-800">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
