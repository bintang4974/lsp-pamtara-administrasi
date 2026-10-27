<x-layouts.public>
    <div class="rounded-xl bg-white p-8 shadow-sm sm:p-12">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
            Selamat datang di {{ $institutionName }}
        </h1>

        <p class="mt-4 max-w-2xl text-gray-600">
            Daftar sertifikasi kompetensi Satpam secara online: pilih jenis sertifikasi,
            tempat uji kompetensi (TUK), dan jadwal ujikom yang tersedia.
        </p>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('register') }}" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-5 text-sm font-medium text-white hover:bg-blue-800">
                Daftar Sekarang
            </a>
            <a href="{{ route('login') }}" class="inline-flex min-h-[44px] items-center rounded-md border border-gray-300 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Masuk
            </a>
        </div>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <x-status-badge label="Gada Pratama" tone="info" />
            <p class="mt-2 text-sm text-gray-600">Sertifikasi tingkat dasar.</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <x-status-badge label="Gada Madya" tone="warning" />
            <p class="mt-2 text-sm text-gray-600">Sertifikasi tingkat menengah.</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <x-status-badge label="Gada Utama" tone="danger" />
            <p class="mt-2 text-sm text-gray-600">Sertifikasi tingkat lanjut.</p>
        </div>
    </div>
</x-layouts.public>
