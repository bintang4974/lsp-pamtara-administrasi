<x-layouts.admin title="QR Code Pendaftaran">
    <h1 class="text-xl font-semibold text-gray-900">QR Code untuk Flyer</h1>
    <p class="mt-1 text-sm text-gray-500">URL stabil, tidak mengandung data pribadi. Klik kanan gambar untuk menyimpan.</p>

    <div class="mt-6 grid gap-6 sm:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center">
            <h2 class="font-semibold text-gray-900">Halaman Utama</h2>
            <div class="mt-4 flex justify-center">{!! $homeSvg !!}</div>
            <p class="mt-3 break-all text-xs text-gray-500">{{ $homeUrl }}</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center">
            <h2 class="font-semibold text-gray-900">Mulai Pendaftaran</h2>
            <div class="mt-4 flex justify-center">{!! $registrationSvg !!}</div>
            <p class="mt-3 break-all text-xs text-gray-500">{{ $registrationUrl }}</p>
            <p class="mt-1 text-xs text-gray-400">Tamu yang belum login akan diarahkan ke login/registrasi, lalu kembali otomatis ke halaman ini.</p>
        </div>
    </div>
</x-layouts.admin>
