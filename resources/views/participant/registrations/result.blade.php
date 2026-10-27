<x-layouts.participant title="Hasil Ujikom">
    <h1 class="text-xl font-semibold text-gray-900">Hasil Ujikom — {{ $registration->registration_number }}</h1>

    @if(! $result || ! $result->isAnnounced())
        <div class="mt-6">
            <x-empty-state title="Belum diumumkan" description="Hasil ujikom Anda akan tampil di sini setelah diumumkan oleh admin." />
        </div>
    @else
        <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 text-center">
            <x-status-badge
                :label="$result->result->label()"
                :tone="$result->result->value === 'competent' ? 'success' : 'danger'"
                class="text-base" />
            <p class="mt-3 text-sm text-gray-500">Diumumkan {{ $result->announced_at->format('d M Y H:i') }}</p>
        </div>
    @endif
</x-layouts.participant>
