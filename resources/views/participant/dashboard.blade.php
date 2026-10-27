<x-layouts.participant title="Dashboard">
    <h1 class="text-xl font-semibold text-gray-900">Dashboard Peserta</h1>

    @if($registrations->isEmpty())
        <div class="mt-6">
            <x-empty-state
                title="Belum ada pendaftaran"
                description="Fitur pendaftaran sertifikasi akan tersedia pada fase berikutnya." />
        </div>
    @else
        <div class="mt-6 space-y-3">
            @foreach($registrations as $registration)
                <div class="rounded-lg border border-gray-200 bg-white p-4">
                    <p class="font-medium text-gray-900">{{ $registration->registration_number }}</p>
                    <x-status-badge label="{{ $registration->status->label() }}" class="mt-2" />
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.participant>
