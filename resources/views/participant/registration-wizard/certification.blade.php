<x-layouts.participant title="Pilih Sertifikasi">
    <x-stepper :current="1" />

    <h1 class="mt-4 text-xl font-semibold text-gray-900">Pilih Sertifikasi</h1>

    @if($certificationTypes->isEmpty())
        <div class="mt-6">
            <x-empty-state title="Belum ada sertifikasi aktif" description="Hubungi admin untuk informasi lebih lanjut." />
        </div>
    @else
        <form method="POST" action="{{ route('participant.registration-wizard.certification.store') }}" class="mt-6 grid gap-3 sm:grid-cols-3">
            @csrf
            @foreach($certificationTypes as $type)
                <label class="cursor-pointer rounded-lg border border-gray-200 bg-white p-5 hover:border-blue-400 has-[:checked]:border-blue-600 has-[:checked]:ring-2 has-[:checked]:ring-blue-600">
                    <input type="radio" name="certification_type_id" value="{{ $type->id }}" class="sr-only" required>
                    <x-cert-badge :certification-type="$type" />
                    @if($type->description)
                        <p class="mt-3 text-sm text-gray-600">{{ $type->description }}</p>
                    @endif
                </label>
            @endforeach

            <div class="sm:col-span-3">
                <button type="submit" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-5 text-sm font-medium text-white hover:bg-blue-800">
                    Lanjutkan
                </button>
            </div>
        </form>
    @endif
</x-layouts.participant>
