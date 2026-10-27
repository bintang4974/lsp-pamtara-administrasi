<x-layouts.participant title="Dokumen Pendaftaran">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $registration->registration_number }}</h1>
            <p class="text-sm text-gray-500">
                <x-cert-badge :certification-type="$registration->certificationType" /> &middot;
                {{ $registration->assessmentSchedule->tuk->name }} &middot;
                {{ $registration->assessmentSchedule->starts_at->format('d M Y H:i') }}
            </p>
        </div>
        <x-status-badge :label="$registration->status->label()" tone="info" />
    </div>

    <div class="mt-6 space-y-4">
        @foreach($registration->documents as $document)
            @php
                $requirement = $document->requirement;
                $tone = match($document->current_status->value) {
                    'approved' => 'success',
                    'under_review' => 'info',
                    'revision_required' => 'danger',
                    default => 'neutral',
                };
                $latestSubmission = $document->submissions->sortByDesc('version_number')->first();
            @endphp
            <div class="rounded-lg border border-gray-200 bg-white p-5">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <p class="font-medium text-gray-900">
                            {{ $requirement->requirement_name }}
                            <x-status-badge :label="$requirement->is_required ? 'Wajib' : 'Opsional'" :tone="$requirement->is_required ? 'warning' : 'neutral'" class="ms-2" />
                        </p>
                        @if($requirement->description_snapshot)
                            <p class="mt-1 text-xs text-gray-500">{{ $requirement->description_snapshot }}</p>
                        @endif
                        <p class="mt-1 text-xs text-gray-500">
                            Jenis: {{ implode(', ', $requirement->allowed_mime_types_snapshot) }} &middot;
                            Maks {{ number_format($requirement->max_file_size_kb_snapshot) }} KB
                        </p>
                    </div>
                    <x-status-badge :label="$document->current_status->label()" :tone="$tone" />
                </div>

                @if($document->current_status->value === 'revision_required' && $latestSubmission?->review_note)
                    <x-alert type="danger" class="mt-3">
                        <strong>Perlu revisi:</strong> {{ $latestSubmission->review_note }}
                    </x-alert>
                @endif

                @if($document->submissions->isNotEmpty())
                    <div class="mt-3 space-y-1 text-xs text-gray-500">
                        @foreach($document->submissions->sortByDesc('version_number') as $submission)
                            <div class="flex items-center gap-2">
                                <span>v{{ $submission->version_number }} &middot; {{ $submission->original_filename }} &middot; {{ $submission->submitted_at->format('d M Y H:i') }}</span>
                                <a href="{{ route('documents.download', $submission) }}" class="text-blue-700 hover:underline">Unduh</a>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($document->current_status->value !== 'approved')
                    <form method="POST" action="{{ route('participant.registrations.documents.upload', [$registration, $document]) }}" enctype="multipart/form-data" class="mt-4">
                        @csrf
                        <input type="file" name="file" required class="block w-full text-sm">
                        <button type="submit" class="mt-2 inline-flex min-h-[44px] items-center rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Unggah {{ $document->submissions->isNotEmpty() ? 'Versi Baru' : 'Dokumen' }}
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>

    @if($registration->status->value === 'draft')
        <div class="mt-6">
            <button type="button" x-data x-on:click="$dispatch('open-modal-submit-registration')"
                class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-5 text-sm font-medium text-white hover:bg-blue-800">
                Kirim Pendaftaran
            </button>

            <x-confirm-modal id="submit-registration" title="Kirim Pendaftaran"
                :form-action="route('participant.registrations.submit', $registration)" confirm-label="Kirim">
                Setelah dikirim, data diri tidak dapat diubah sendiri. Pastikan seluruh dokumen wajib sudah benar.
            </x-confirm-modal>
        </div>
    @endif
</x-layouts.participant>
