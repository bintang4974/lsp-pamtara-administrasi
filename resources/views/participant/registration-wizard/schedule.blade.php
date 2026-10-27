<x-layouts.participant title="Pilih Jadwal">
    <x-stepper :current="3" />

    <h1 class="mt-4 text-xl font-semibold text-gray-900">Pilih Jadwal Ujikom</h1>

    @if($schedules->isEmpty())
        <div class="mt-6">
            <x-empty-state title="Belum ada jadwal terbuka di TUK ini" description="Kembali dan pilih TUK lain, atau coba lagi nanti." />
        </div>
    @else
        <form method="POST" action="{{ route('participant.registration-wizard.schedule.store') }}" class="mt-6 space-y-3">
            @csrf
            @foreach($schedules as $schedule)
                <label class="flex cursor-pointer items-center justify-between rounded-lg border border-gray-200 bg-white p-4 hover:border-blue-400 has-[:checked]:border-blue-600 has-[:checked]:ring-2 has-[:checked]:ring-blue-600">
                    <div>
                        <p class="font-medium text-gray-900">{{ $schedule->starts_at->format('d M Y H:i') }} - {{ $schedule->ends_at->format('H:i') }}</p>
                        <p class="text-sm text-gray-500">Batas pendaftaran: {{ $schedule->registration_deadline->format('d M Y H:i') }}</p>
                    </div>
                    <input type="radio" name="assessment_schedule_id" value="{{ $schedule->id }}" class="h-5 w-5" required>
                </label>
            @endforeach

            <button type="submit" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-5 text-sm font-medium text-white hover:bg-blue-800">
                Lanjutkan
            </button>
        </form>
    @endif
</x-layouts.participant>
