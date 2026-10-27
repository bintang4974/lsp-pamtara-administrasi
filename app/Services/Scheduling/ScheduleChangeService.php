<?php

namespace App\Services\Scheduling;

use App\Models\AssessmentSchedule;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use App\Services\Notifications\NotificationService;
use Illuminate\Support\Facades\DB;

/**
 * Satu-satunya tempat mengubah jadwal yang sudah berjalan: mencatat audit, menandai undangan
 * yang sudah terbit sebagai usang, dan mengirim notifikasi ke peserta terdaftar bila ada
 * perubahan signifikan (lihat ARCHITECTURE.md §3 dan §4.1).
 */
class ScheduleChangeService
{
    private const SIGNIFICANT_FIELDS = ['starts_at', 'ends_at', 'tuk_id', 'registration_deadline'];

    public function __construct(
        private AuditLogger $auditLogger,
        private NotificationService $notifications,
    ) {}

    /**
     * @param  array<string, mixed>  $data  Atribut jadwal (tanpa assessor_ids).
     * @param  array<int>|null  $assessorIds  Jika diberikan, menggantikan seluruh penugasan asesor.
     */
    public function update(AssessmentSchedule $schedule, array $data, ?array $assessorIds, User $actor): AssessmentSchedule
    {
        return DB::transaction(function () use ($schedule, $data, $assessorIds, $actor) {
            $old = $schedule->only(['schedule_code', 'certification_type_id', 'tuk_id', 'starts_at', 'ends_at', 'registration_deadline', 'status', 'notes']);

            $schedule->fill($data);
            $significantlyChanged = $schedule->isDirty(self::SIGNIFICANT_FIELDS);
            $schedule->save();

            if ($assessorIds !== null) {
                $schedule->assessors()->sync($assessorIds);
            }

            $this->auditLogger->log(
                actor: $actor,
                action: 'schedule.updated',
                entityType: AssessmentSchedule::class,
                entityId: $schedule->id,
                oldValues: $old,
                newValues: $schedule->only(array_keys($old)),
            );

            if ($significantlyChanged) {
                $this->markCurrentInvitationsStale($schedule);
                $this->notifyRegistrants($schedule);
            }

            return $schedule;
        });
    }

    private function markCurrentInvitationsStale(AssessmentSchedule $schedule): void
    {
        $schedule->registrations()
            ->with('currentInvitation')
            ->get()
            ->each(function ($registration) {
                $registration->currentInvitation?->update(['is_current' => false]);
            });
    }

    private function notifyRegistrants(AssessmentSchedule $schedule): void
    {
        $schedule->registrations()->with('user')->get()->each(function ($registration) {
            $this->notifications->notify(
                user: $registration->user,
                title: 'Jadwal ujikom berubah',
                message: "Jadwal ujikom untuk pendaftaran {$registration->registration_number} telah diperbarui oleh admin. Mohon periksa detail jadwal terbaru.",
                type: 'schedule_changed',
                registration: $registration,
            );
        });
    }
}
