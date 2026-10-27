<x-layouts.participant title="Pilih TUK">
    <x-stepper :current="2" />

    <h1 class="mt-4 text-xl font-semibold text-gray-900">Pilih Tempat Uji Kompetensi (TUK)</h1>

    @if($tuks->isEmpty())
        <div class="mt-6">
            <x-empty-state title="Belum ada TUK dengan jadwal terbuka" description="Coba lagi nanti atau hubungi admin." />
        </div>
    @else
        <form method="POST" action="{{ route('participant.registration-wizard.tuk.store') }}" class="mt-6 grid gap-3 sm:grid-cols-2">
            @csrf
            @foreach($tuks as $tuk)
                <label class="cursor-pointer rounded-lg border border-gray-200 bg-white p-5 hover:border-blue-400 has-[:checked]:border-blue-600 has-[:checked]:ring-2 has-[:checked]:ring-blue-600">
                    <input type="radio" name="tuk_id" value="{{ $tuk->id }}" class="sr-only" required>
                    <p class="font-semibold text-gray-900">{{ $tuk->name }}</p>
                    <p class="mt-1 text-sm text-gray-600">{{ $tuk->city }}, {{ $tuk->province }}</p>
                </label>
            @endforeach

            <div class="sm:col-span-2">
                <button type="submit" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-5 text-sm font-medium text-white hover:bg-blue-800">
                    Lanjutkan
                </button>
            </div>
        </form>
    @endif
</x-layouts.participant>
