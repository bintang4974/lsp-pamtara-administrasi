<?php

namespace App\Services\Results;

use App\Enums\AssessmentResult as AssessmentResultEnum;
use App\Enums\RegistrationStatus;
use App\Models\AssessmentResult;
use App\Models\AssessmentSchedule;
use App\Models\Registration;
use App\Models\ResultHistory;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use App\Services\Notifications\NotificationService;
use App\Services\Registration\RegistrationStatusService;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Nilai final hanya Kompeten/Tidak Kompeten. Tanpa record atau announced_at null => "Belum
 * diumumkan" (jangan pernah default Tidak Kompeten). Koreksi wajib alasan, histori append-only
 * dalam satu transaction (lihat PRD.md §3.8, DATABASE.md §5).
 */
class ResultService
{
    public function __construct(
        private RegistrationStatusService $statusService,
        private AuditLogger $auditLogger,
        private NotificationService $notifications,
    ) {}

    /**
     * @throws DomainException Jika hasil sudah pernah diinput.
     */
    public function enter(Registration $registration, AssessmentResultEnum $result, ?string $notes, User $admin): AssessmentResult
    {
        if ($registration->assessmentResult) {
            throw new DomainException('Hasil untuk pendaftaran ini sudah pernah diinput. Gunakan koreksi.');
        }

        return DB::transaction(function () use ($registration, $result, $notes, $admin) {
            $assessmentResult = AssessmentResult::query()->create([
                'registration_id' => $registration->id,
                'result' => $result,
                'notes' => $notes,
                'entered_by' => $admin->id,
                'entered_at' => now(),
            ]);

            ResultHistory::query()->create([
                'assessment_result_id' => $assessmentResult->id,
                'old_result' => null,
                'new_result' => $result,
                'reason' => 'Input awal',
                'changed_by' => $admin->id,
                'changed_at' => now(),
            ]);

            $this->auditLogger->log(
                actor: $admin,
                action: 'result.entered',
                entityType: AssessmentResult::class,
                entityId: $assessmentResult->id,
                newValues: ['result' => $result->value],
            );

            if ($registration->status === RegistrationStatus::Scheduled) {
                $this->statusService->transitionTo($registration, RegistrationStatus::Completed, $admin);
            }

            return $assessmentResult;
        });
    }

    /**
     * @throws DomainException Jika alasan kosong.
     */
    public function correct(AssessmentResult $assessmentResult, AssessmentResultEnum $newResult, string $reason, User $admin): AssessmentResult
    {
        if (trim($reason) === '') {
            throw new DomainException('Alasan koreksi wajib diisi.');
        }

        return DB::transaction(function () use ($assessmentResult, $newResult, $reason, $admin) {
            $oldResult = $assessmentResult->result;

            $assessmentResult->update(['result' => $newResult]);

            ResultHistory::query()->create([
                'assessment_result_id' => $assessmentResult->id,
                'old_result' => $oldResult,
                'new_result' => $newResult,
                'reason' => $reason,
                'changed_by' => $admin->id,
                'changed_at' => now(),
            ]);

            $this->auditLogger->log(
                actor: $admin,
                action: 'result.corrected',
                entityType: AssessmentResult::class,
                entityId: $assessmentResult->id,
                oldValues: ['result' => $oldResult->value],
                newValues: ['result' => $newResult->value],
                reason: $reason,
            );

            if ($assessmentResult->isAnnounced()) {
                $this->notifications->notify(
                    user: $assessmentResult->registration->user,
                    title: 'Hasil ujikom diperbarui',
                    message: "Hasil ujikom untuk pendaftaran {$assessmentResult->registration->registration_number} telah dikoreksi.",
                    type: 'result_corrected',
                    registration: $assessmentResult->registration,
                );
            }

            return $assessmentResult;
        });
    }

    public function announce(AssessmentResult $assessmentResult, User $admin): AssessmentResult
    {
        if ($assessmentResult->isAnnounced()) {
            return $assessmentResult;
        }

        return DB::transaction(function () use ($assessmentResult, $admin) {
            $assessmentResult->update(['announced_at' => now()]);

            $this->auditLogger->log(
                actor: $admin,
                action: 'result.announced',
                entityType: AssessmentResult::class,
                entityId: $assessmentResult->id,
                newValues: ['announced_at' => now()->toISOString()],
            );

            $this->notifications->notify(
                user: $assessmentResult->registration->user,
                title: 'Hasil ujikom diumumkan',
                message: "Hasil ujikom untuk pendaftaran {$assessmentResult->registration->registration_number} telah diumumkan. Silakan periksa dashboard Anda.",
                type: 'result_announced',
                registration: $assessmentResult->registration,
            );

            return $assessmentResult;
        });
    }

    /**
     * Pengumuman massal per jadwal (D-08 di OPEN_DECISIONS.md). Mengembalikan jumlah yang diumumkan.
     */
    public function announceForSchedule(AssessmentSchedule $schedule, User $admin): int
    {
        $results = AssessmentResult::query()
            ->whereHas('registration', fn ($q) => $q->where('assessment_schedule_id', $schedule->id))
            ->whereNull('announced_at')
            ->get();

        foreach ($results as $result) {
            $this->announce($result, $admin);
        }

        return $results->count();
    }
}
