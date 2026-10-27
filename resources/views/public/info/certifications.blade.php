<x-layouts.public title="Info Sertifikasi">
    <h1 class="text-2xl font-bold text-gray-900">Jenis Sertifikasi</h1>
    <p class="mt-2 text-gray-600">Ketiga sertifikasi memakai formulir pendaftaran dan persyaratan dokumen yang sama.</p>

    @if($certificationTypes->isEmpty())
        <div class="mt-6">
            <x-empty-state title="Belum ada sertifikasi aktif" />
        </div>
    @else
        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            @foreach($certificationTypes as $type)
                <div class="rounded-lg border border-gray-200 bg-white p-5">
                    <x-cert-badge :certification-type="$type" />
                    @if($type->description)
                        <p class="mt-3 text-sm text-gray-600">{{ $type->description }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.public>
