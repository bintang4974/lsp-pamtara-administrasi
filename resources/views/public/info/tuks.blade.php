<x-layouts.public title="Info TUK">
    <h1 class="text-2xl font-bold text-gray-900">Tempat Uji Kompetensi (TUK)</h1>
    <p class="mt-2 text-gray-600">Daftar TUK yang saat ini aktif menerima pendaftaran.</p>

    @if($tuks->isEmpty())
        <div class="mt-6">
            <x-empty-state title="Belum ada TUK aktif" description="Data TUK akan ditambahkan oleh admin." />
        </div>
    @else
        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            @foreach($tuks as $tuk)
                <div class="rounded-lg border border-gray-200 bg-white p-5">
                    <p class="font-semibold text-gray-900">{{ $tuk->name }}</p>
                    <p class="mt-1 text-sm text-gray-600">{{ $tuk->address }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $tuk->city }}, {{ $tuk->province }}</p>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.public>
