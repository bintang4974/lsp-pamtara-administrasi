<x-layouts.participant title="Undangan Ujikom">
    <h1 class="text-xl font-semibold text-gray-900">Undangan Ujikom — {{ $registration->registration_number }}</h1>

    @php($current = $registration->invitationDocuments->firstWhere('is_current', true))

    @if(! $current)
        <div class="mt-6">
            <x-empty-state
                title="Undangan belum tersedia"
                description="Undangan akan tersedia setelah pembayaran Anda dikonfirmasi admin." />
        </div>
    @else
        <div class="mt-6 rounded-lg border border-gray-200 bg-white p-5">
            <p class="font-medium text-gray-900">No. Dokumen: {{ $current->document_number }}</p>
            <p class="mt-1 text-sm text-gray-500">Diterbitkan {{ $current->issued_at->format('d M Y H:i') }}</p>
            <a href="{{ route('invitations.download', $current) }}" class="mt-4 inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-5 text-sm font-medium text-white hover:bg-blue-800">
                Unduh Undangan (PDF)
            </a>
        </div>

        @if($registration->invitationDocuments->count() > 1)
            <div class="mt-4 rounded-lg border border-gray-200 bg-white p-5">
                <h2 class="font-semibold text-gray-900">Versi Sebelumnya (usang)</h2>
                <div class="mt-2 space-y-1 text-sm text-gray-500">
                    @foreach($registration->invitationDocuments->where('is_current', false) as $old)
                        <div>
                            {{ $old->document_number }} &middot; diterbitkan {{ $old->issued_at->format('d M Y H:i') }}
                            <a href="{{ route('invitations.download', $old) }}" class="text-blue-700 hover:underline">Unduh</a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</x-layouts.participant>
