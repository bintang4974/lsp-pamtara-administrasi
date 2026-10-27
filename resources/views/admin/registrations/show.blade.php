<x-layouts.admin title="Detail Pendaftaran">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $registration->registration_number }}</h1>
            <p class="text-sm text-gray-500">{{ $registration->user->name }} &middot; {{ $registration->user->email }}</p>
        </div>
        <x-status-badge :label="$registration->status->label()" tone="info" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-lg border border-gray-200 bg-white p-5">
                <h2 class="font-semibold text-gray-900">Dokumen</h2>

                <div class="mt-4 space-y-4">
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
                        <div class="rounded-md border border-gray-100 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <p class="font-medium text-gray-900">
                                    {{ $requirement->requirement_name }}
                                    <x-status-badge :label="$requirement->is_required ? 'Wajib' : 'Opsional'" :tone="$requirement->is_required ? 'warning' : 'neutral'" class="ms-2" />
                                </p>
                                <x-status-badge :label="$document->current_status->label()" :tone="$tone" />
                            </div>

                            @if($document->submissions->isEmpty())
                                <p class="mt-2 text-sm text-gray-500">Belum diunggah peserta.</p>
                            @else
                                <div class="mt-3 space-y-2">
                                    @foreach($document->submissions->sortByDesc('version_number') as $submission)
                                        <div class="flex flex-wrap items-center justify-between gap-2 rounded bg-gray-50 p-2 text-sm">
                                            <div>
                                                <span class="font-medium">v{{ $submission->version_number }}</span>
                                                {{ $submission->original_filename }}
                                                <a href="{{ route('documents.download', $submission) }}" class="ms-2 text-blue-700 hover:underline">Unduh</a>
                                                <x-status-badge :label="$submission->status->label()" tone="neutral" class="ms-2" />
                                                @if($submission->review_note)
                                                    <p class="mt-1 text-xs text-red-600">Catatan: {{ $submission->review_note }}</p>
                                                @endif
                                            </div>

                                            @if($submission->id === $latestSubmission->id && $document->current_status->value === 'under_review')
                                                <div class="flex gap-2">
                                                    <button type="button" x-data x-on:click="$dispatch('open-modal-approve-{{ $submission->id }}')"
                                                        class="min-h-[44px] rounded-md bg-green-600 px-3 text-xs font-medium text-white hover:bg-green-700">
                                                        Setujui
                                                    </button>
                                                    <button type="button" x-data x-on:click="$dispatch('open-modal-revision-{{ $submission->id }}')"
                                                        class="min-h-[44px] rounded-md bg-red-600 px-3 text-xs font-medium text-white hover:bg-red-700">
                                                        Minta Revisi
                                                    </button>

                                                    <x-confirm-modal :id="'approve-'.$submission->id" title="Setujui Dokumen"
                                                        :form-action="route('admin.registrations.documents.approve', [$registration, $submission])"
                                                        confirm-label="Setujui">
                                                        Yakin ingin menyetujui dokumen <strong>{{ $requirement->requirement_name }}</strong> versi {{ $submission->version_number }}?
                                                    </x-confirm-modal>

                                                    <x-confirm-modal :id="'revision-'.$submission->id" title="Minta Revisi"
                                                        :form-action="route('admin.registrations.documents.request-revision', [$registration, $submission])"
                                                        confirm-label="Kirim Permintaan Revisi">
                                                        <label for="reason-{{ $submission->id }}" class="block text-sm font-medium text-gray-700">Alasan revisi (wajib)</label>
                                                        <textarea id="reason-{{ $submission->id }}" name="reason" rows="3" required
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                                                    </x-confirm-modal>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-5">
                <h2 class="font-semibold text-gray-900">Riwayat Status</h2>
                <ol class="mt-3 space-y-2 text-sm">
                    @foreach($registration->histories->sortByDesc('changed_at') as $history)
                        <li class="border-b border-gray-100 pb-2 last:border-0">
                            <span class="font-medium text-gray-900">{{ $history->new_status->label() }}</span>
                            <span class="text-gray-500">&middot; {{ $history->changed_at->format('d M Y H:i') }}</span>
                            @if($history->changedBy)
                                <span class="text-gray-500">&middot; {{ $history->changedBy->name }}</span>
                            @endif
                            @if($history->reason)
                                <p class="text-xs text-gray-500">Alasan: {{ $history->reason }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-lg border border-gray-200 bg-white p-5">
                <h2 class="font-semibold text-gray-900">Informasi Pendaftaran</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div><dt class="text-gray-500">Sertifikasi</dt><dd><x-cert-badge :certification-type="$registration->certificationType" /></dd></div>
                    <div><dt class="text-gray-500">TUK</dt><dd class="text-gray-900">{{ $registration->assessmentSchedule->tuk->name }}</dd></div>
                    <div><dt class="text-gray-500">Jadwal</dt><dd class="text-gray-900">{{ $registration->assessmentSchedule->starts_at->format('d M Y H:i') }}</dd></div>
                    <div><dt class="text-gray-500">Dikirim</dt><dd class="text-gray-900">{{ $registration->submitted_at?->format('d M Y H:i') ?? '-' }}</dd></div>
                </dl>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-5">
                <h2 class="font-semibold text-gray-900">Data Peserta</h2>
                @php($profile = $registration->user->participantProfile)
                <dl class="mt-3 space-y-2 text-sm">
                    <div><dt class="text-gray-500">Nama</dt><dd class="text-gray-900">{{ $registration->user->name }}</dd></div>
                    <div><dt class="text-gray-500">Telepon</dt><dd class="text-gray-900">{{ $registration->user->phone ?? '-' }}</dd></div>
                    <div><dt class="text-gray-500">NIK</dt><dd class="text-gray-900">{{ $profile?->nik ?? '-' }}</dd></div>
                    <div><dt class="text-gray-500">Tempat, Tgl Lahir</dt><dd class="text-gray-900">{{ $profile?->birth_place ?? '-' }}, {{ $profile?->birth_date?->format('d M Y') ?? '-' }}</dd></div>
                    <div><dt class="text-gray-500">Alamat</dt><dd class="text-gray-900">{{ $profile?->address ?? '-' }}, {{ $profile?->city ?? '-' }}, {{ $profile?->province ?? '-' }}</dd></div>
                </dl>
            </div>
        </div>
    </div>
</x-layouts.admin>
