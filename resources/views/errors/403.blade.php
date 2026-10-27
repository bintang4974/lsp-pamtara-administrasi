<x-layouts.public title="403 - Akses Ditolak">
    <div class="flex flex-col items-center justify-center rounded-xl bg-white p-12 text-center shadow-sm">
        <p class="text-5xl font-bold text-gray-300">403</p>
        <h1 class="mt-4 text-xl font-semibold text-gray-900">Akses Ditolak</h1>
        <p class="mt-2 max-w-md text-gray-600">
            Anda tidak memiliki izin untuk mengakses halaman ini.
        </p>
        <a href="{{ route('home') }}" class="mt-6 inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-5 text-sm font-medium text-white hover:bg-blue-800">
            Kembali ke Beranda
        </a>
    </div>
</x-layouts.public>
