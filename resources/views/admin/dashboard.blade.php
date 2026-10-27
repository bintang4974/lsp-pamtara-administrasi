<x-layouts.admin title="Dashboard">
    <h1 class="text-xl font-semibold text-gray-900">Dashboard Admin</h1>

    <div class="mt-6 grid gap-4 sm:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Total Admin</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $totalAdmins }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Total Peserta</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $totalParticipants }}</p>
        </div>
    </div>

    <div class="mt-6">
        <x-empty-state
            title="Modul manajemen belum tersedia"
            description="Master data, jadwal, pendaftaran, dan laporan akan tersedia pada fase berikutnya." />
    </div>
</x-layouts.admin>
