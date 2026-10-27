<?php

namespace App\Services\Registration;

use App\Enums\RegistrationStatus;
use App\Models\Registration;
use App\Models\RegistrationHistory;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use App\Services\Notifications\NotificationService;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Satu-satunya tempat mengubah registrations.status (lihat ARCHITECTURE.md §3, §4.1).
 * Setiap transisi menulis registration_histories + audit_logs dalam satu transaction.
 */
class RegistrationStatusService
{
    public function __construct(
        private AuditLogger $auditLogger,
        private NotificationService $notifications,
    ) {}

    /**
     * Catat status awal (draft) saat pendaftaran dibuat. Bukan "transisi" dari status lain.
     */
    public function recordInitial(Registration $registration, ?User $actor): void
    {
        DB::transaction(function () use ($registration, $actor) {
            RegistrationHistory::create([
                'registration_id' => $registration->id,
                'old_status' => null,
                'new_status' => $registration->status,
                'reason' => null,
                'changed_by' => $actor?->id,
                'changed_at' => now(),
            ]);

            $this->auditLogger->log(
                actor: $actor,
                action: 'registration.created',
                entityType: Registration::class,
                entityId: $registration->id,
                newValues: ['status' => $registration->status->value],
            );
        });
    }

    /**
     * @param  array<string, mixed>  $metadata
     *
     * @throws DomainException Jika transisi tidak sah.
     */
    public function transitionTo(
        Registration $registration,
        RegistrationStatus $newStatus,
        ?User $actor,
        ?string $reason = null,
        array $metadata = [],
    ): Registration {
        $oldStatus = $registration->status;

        if (! in_array($newStatus, $oldStatus->allowedNextStatuses(), true)) {
            throw new DomainException("Transisi status dari {$oldStatus->value} ke {$newStatus->value} tidak diizinkan.");
        }

        if ($newStatus === RegistrationStatus::Cancelled && ! $reason) {
            throw new DomainException('Alasan wajib diisi untuk pembatalan pendaftaran.');
        }

        return DB::transaction(function () use ($registration, $oldStatus, $newStatus, $actor, $reason, $metadata) {
            $registration->status = $newStatus;

            match ($newStatus) {
                RegistrationStatus::Submitted => $registration->submitted_at = now(),
                RegistrationStatus::AdministrativelyApproved => $registration->fill([
                    'administrative_approved_at' => now(),
                    'administrative_approved_by' => $actor?->id,
                ]),
                RegistrationStatus::Completed => $registration->completed_at = now(),
                RegistrationStatus::Cancelled => $registration->fill([
                    'cancelled_at' => now(),
                    'cancellation_reason' => $reason,
                ]),
                default => null,
            };

            $registration->save();

            RegistrationHistory::create([
                'registration_id' => $registration->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'reason' => $reason,
                'changed_by' => $actor?->id,
                'changed_at' => now(),
                'metadata' => $metadata ?: null,
            ]);

            $this->auditLogger->log(
                actor: $actor,
                action: 'registration.status_changed',
                entityType: Registration::class,
                entityId: $registration->id,
                oldValues: ['status' => $oldStatus->value],
                newValues: ['status' => $newStatus->value],
                reason: $reason,
            );

            $this->notifications->notify(
                user: $registration->user,
                title: 'Status pendaftaran diperbarui',
                message: "Pendaftaran {$registration->registration_number}: {$newStatus->label()}".($reason ? " ({$reason})" : ''),
                type: 'registration_status_changed',
                registration: $registration,
            );

            return $registration;
        });
    }
}
