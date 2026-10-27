<x-layouts.participant title="Dashboard">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Dashboard Peserta</h1>
        <a href="{{ route('participant.registration-wizard.certification') }}" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-4 text-sm font-medium text-white hover:bg-blue-800">
            Daftar Sertifikasi Baru
        </a>
    </div>

    @if($registrations->isEmpty())
        <div class="mt-6">
            <x-empty-state
                title="Belum ada pendaftaran"
                description="Mulai pendaftaran sertifikasi Gada Pratama, Gada Madya, atau Gada Utama." />
        </div>
    @else
        <div class="mt-6 space-y-4">
            @foreach($registrations as $registration)
                @php($journey = $journeys[$registration->id])
                <div class="rounded-lg border border-gray-200 bg-white p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $registration->registration_number }}</p>
                            <p class="text-sm text-gray-500">
                                <x-cert-badge :certification-type="$registration->certificationType" /> &middot;
                                {{ $registration->assessmentSchedule->tuk->name }}
                            </p>
                        </div>
                        <x-status-badge :label="$journey->currentStatusLabel()" :tone="$journey->isCancelled() ? 'danger' : 'info'" />
                    </div>

                    @if($journey->isCancelled())
                        <x-alert type="danger" class="mt-4">
                            Pendaftaran ini dibatalkan. Alasan: {{ $registration->cancellation_reason ?? '-' }}
                        </x-alert>
                    @else
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($journey->steps() as $step)
                                <span class="rounded-full px-3 py-1 text-xs font-medium {{ match($step['state']) {
                                    'done' => 'bg-green-100 text-green-800',
                                    'current' => 'bg-blue-100 text-blue-800',
                                    default => 'bg-gray-100 text-gray-500',
                                } }}">
                                    {{ $step['number'] }}. {{ $step['label'] }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('participant.registrations.documents', $registration) }}" class="inline-flex min-h-[44px] items-center rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.participant>
