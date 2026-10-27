<x-layouts.participant title="Pembayaran">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Pembayaran — {{ $registration->registration_number }}</h1>
        <x-status-badge :label="$payment->status->label()" tone="info" />
    </div>

    @php($settings = \App\Models\ApplicationSetting::query()->get()->keyBy('key'))

    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-5">
        <h2 class="font-semibold text-gray-900">Instruksi Pembayaran</h2>
        <dl class="mt-3 space-y-2 text-sm">
            <div><dt class="text-gray-500">Nominal</dt><dd class="font-semibold text-gray-900">Rp {{ number_format($payment->expected_amount, 0, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500">Bank</dt><dd class="text-gray-900">{{ $settings->get('payment.bank_name')?->value ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">No. Rekening/VA</dt><dd class="text-gray-900">{{ $settings->get('payment.account_number')?->value ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Atas Nama</dt><dd class="text-gray-900">{{ $settings->get('payment.account_holder')?->value ?? '-' }}</dd></div>
        </dl>
        @if($settings->get('payment.instructions')?->value)
            <p class="mt-3 text-sm text-gray-600">{{ $settings->get('payment.instructions')->value }}</p>
        @endif
        <p class="mt-3 text-xs text-gray-500">Mengunggah bukti bukan berarti lunas. Pembayaran dianggap lunas hanya setelah dikonfirmasi admin.</p>
    </div>

    @if($payment->status->value === 'paid')
        <x-alert type="success" class="mt-4">
            Pembayaran dikonfirmasi lunas pada {{ $payment->confirmed_at?->format('d M Y H:i') }}.
            @if($payment->transaction_reference) Referensi: {{ $payment->transaction_reference }}. @endif
        </x-alert>
    @else
        @if($payment->status->value === 'revision_required')
            @php($latestNote = $payment->submissions->sortByDesc('version_number')->first()?->review_note)
            @if($latestNote)
                <x-alert type="danger" class="mt-4"><strong>Perlu revisi:</strong> {{ $latestNote }}</x-alert>
            @endif
        @endif

        <div class="mt-4 rounded-lg border border-gray-200 bg-white p-5">
            <h2 class="font-semibold text-gray-900">Unggah Bukti Pembayaran</h2>

            @if($payment->submissions->isNotEmpty())
                <div class="mt-3 space-y-1 text-xs text-gray-500">
                    @foreach($payment->submissions->sortByDesc('version_number') as $submission)
                        <div class="flex items-center gap-2">
                            <span>v{{ $submission->version_number }} &middot; {{ $submission->original_filename }} &middot; {{ $submission->submitted_at->format('d M Y H:i') }}</span>
                            <a href="{{ route('payments.download', $submission) }}" class="text-blue-700 hover:underline">Unduh</a>
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('participant.registrations.payment.upload', $registration) }}" enctype="multipart/form-data" class="mt-4">
                @csrf
                <input type="file" name="file" required class="block w-full text-sm">
                <button type="submit" class="mt-2 inline-flex min-h-[44px] items-center rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Unggah {{ $payment->submissions->isNotEmpty() ? 'Bukti Baru' : 'Bukti' }}
                </button>
            </form>
        </div>
    @endif
</x-layouts.participant>
