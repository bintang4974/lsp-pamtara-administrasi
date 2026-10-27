<?php

namespace App\Services\Registration;

use App\Enums\RegistrationStatus;
use App\Models\AssessmentSchedule;
use App\Models\DocumentRequirement;
use App\Models\Registration;
use App\Models\RegistrationDocument;
use App\Models\RegistrationDocumentRequirement;
use App\Models\User;
use App\Services\Notifications\NotificationService;
use App\Support\NumberGenerator;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Membuat pendaftaran baru dalam transaction: validasi jadwal terbuka, nomor aman race
 * condition, snapshot persyaratan dokumen, record dokumen awal, histori, notifikasi
 * (lihat ARCHITECTURE.md §3 dan PRD.md §3.4–3.5).
 */
class RegistrationService
{
    public function __construct(
        private NumberGenerator $numbers,
        private RegistrationStatusService $statusService,
        private NotificationService $notifications,
    ) {}

    /**
     * @throws DomainException Jika jadwal tidak valid atau peserta sudah punya pendaftaran aktif.
     */
    public function create(User $user, int $certificationTypeId, int $assessmentScheduleId): Registration
    {
        $existing = $this->findExistingActiveRegistration($user, $certificationTypeId);

        if ($existing) {
            throw new DomainException('EXISTING_ACTIVE_REGISTRATION');
        }

        $schedule = AssessmentSchedule::query()->with('tuk')->find($assessmentScheduleId);

        if (! $schedule
            || $schedule->certification_type_id !== $certificationTypeId
            || ! $schedule->isOpenForRegistration()
        ) {
            throw new DomainException('Jadwal yang dipilih tidak lagi tersedia untuk pendaftaran.');
        }

        return DB::transaction(function () use ($user, $certificationTypeId, $schedule) {
            $registration = Registration::query()->create([
                'registration_number' => $this->numbers->next('registration', 'REG'),
                'user_id' => $user->id,
                'certification_type_id' => $certificationTypeId,
                'assessment_schedule_id' => $schedule->id,
                'status' => RegistrationStatus::Draft,
            ]);

            DocumentRequirement::query()->where('is_active', true)->orderBy('sort_order')->get()
                ->each(function (DocumentRequirement $requirement) use ($registration) {
                    $snapshot = RegistrationDocumentRequirement::query()->create([
                        'registration_id' => $registration->id,
                        'requirement_code' => $requirement->code,
                        'requirement_name' => $requirement->name,
                        'description_snapshot' => $requirement->description,
                        'is_required' => $requirement->is_required,
                        'allowed_mime_types_snapshot' => $requirement->allowed_mime_types,
                        'max_file_size_kb_snapshot' => $requirement->max_file_size_kb,
                    ]);

                    RegistrationDocument::query()->create([
                        'registration_id' => $registration->id,
                        'registration_document_requirement_id' => $snapshot->id,
                        'current_status' => 'missing',
                    ]);
                });

            $this->statusService->recordInitial($registration, $user);

            $this->notifications->notify(
                user: $user,
                title: 'Pendaftaran dibuat',
                message: "Pendaftaran {$registration->registration_number} dibuat. Lengkapi dokumen untuk melanjutkan.",
                type: 'registration_created',
                registration: $registration,
            );

            return $registration;
        });
    }

    public function findExistingActiveRegistration(User $user, int $certificationTypeId): ?Registration
    {
        return Registration::query()
            ->where('user_id', $user->id)
            ->where('certification_type_id', $certificationTypeId)
            ->where('status', '!=', RegistrationStatus::Cancelled->value)
            ->first();
    }

    /**
     * @throws DomainException Jika ada dokumen wajib yang belum diunggah.
     */
    public function submit(Registration $registration, User $actor): Registration
    {
        $hasMissingRequiredDocument = $registration->documents()
            ->whereHas('requirement', fn ($q) => $q->where('is_required', true))
            ->where('current_status', 'missing')
            ->exists();

        if ($hasMissingRequiredDocument) {
            throw new DomainException('Masih ada dokumen wajib yang belum diunggah.');
        }

        return $this->statusService->transitionTo($registration, RegistrationStatus::Submitted, $actor);
    }
}
