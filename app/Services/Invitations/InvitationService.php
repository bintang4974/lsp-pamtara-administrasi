<?php

namespace App\Services\Invitations;

use App\Enums\RegistrationStatus;
use App\Models\InvitationDocument;
use App\Models\Registration;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use App\Services\Notifications\NotificationService;
use App\Services\Registration\RegistrationStatusService;
use App\Support\NumberGenerator;
use Barryvdh\DomPDF\Facade\Pdf;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Hanya untuk pendaftaran dengan pembayaran sudah dikonfirmasi. Seluruh data dari database,
 * simpan snapshot jadwal, versi baru menandai versi lama is_current=false (lihat PRD.md §3.7).
 */
class InvitationService
{
    public function __construct(
        private NumberGenerator $numbers,
        private RegistrationStatusService $statusService,
        private AuditLogger $auditLogger,
        private NotificationService $notifications,
    ) {}

    /**
     * @throws DomainException Jika prasyarat pembayaran atau data wajib belum lengkap.
     */
    public function issue(Registration $registration, User $actor): InvitationDocument
    {
        if (! in_array($registration->status, [RegistrationStatus::Paid, RegistrationStatus::Scheduled], true)) {
            throw new DomainException('Undangan hanya bisa diterbitkan setelah pembayaran dikonfirmasi.');
        }

        $registration->loadMissing(['user', 'certificationType', 'assessmentSchedule.tuk', 'assessmentSchedule.assessors']);
        $schedule = $registration->assessmentSchedule;
        $assessors = $schedule->assessors;

        if ($assessors->isEmpty()) {
            throw new DomainException('Jadwal belum memiliki asesor. Tugaskan asesor sebelum menerbitkan undangan.');
        }

        if (! $registration->user->name || ! $registration->certificationType->name
            || ! $schedule->tuk->name || ! $schedule->tuk->address || ! $schedule->starts_at) {
            throw new DomainException('Data wajib (peserta/sertifikasi/TUK/jadwal) belum lengkap untuk menerbitkan undangan.');
        }

        return DB::transaction(function () use ($registration, $actor, $schedule, $assessors) {
            $version = ((int) $registration->invitationDocuments()->max('version_number')) + 1;
            $documentNumber = $this->numbers->next('invitation', 'UND');

            $snapshot = [
                'participant_name' => $registration->user->name,
                'registration_number' => $registration->registration_number,
                'certification_name' => $registration->certificationType->name,
                'tuk_name' => $schedule->tuk->name,
                'tuk_address' => $schedule->tuk->address,
                'starts_at' => $schedule->starts_at->toISOString(),
                'ends_at' => $schedule->ends_at->toISOString(),
                'assessors' => $assessors->pluck('name')->all(),
            ];

            $pdf = Pdf::loadView('pdf.invitation', [
                'data' => $snapshot,
                'documentNumber' => $documentNumber,
                'issuedAt' => now(),
            ]);

            $path = 'registrations/'.$registration->id.'/invitations/'.$documentNumber.'.pdf';
            Storage::disk(config('pamtara.private_disk'))->put($path, $pdf->output());

            $registration->invitationDocuments()->update(['is_current' => false]);

            $invitation = InvitationDocument::query()->create([
                'registration_id' => $registration->id,
                'version_number' => $version,
                'document_number' => $documentNumber,
                'file_path' => $path,
                'schedule_snapshot' => $snapshot,
                'issued_by' => $actor->id,
                'issued_at' => now(),
                'is_current' => true,
            ]);

            $this->auditLogger->log(
                actor: $actor,
                action: 'invitation.issued',
                entityType: InvitationDocument::class,
                entityId: $invitation->id,
                newValues: ['document_number' => $documentNumber, 'version_number' => $version],
            );

            if ($registration->status === RegistrationStatus::Paid) {
                $this->statusService->transitionTo($registration, RegistrationStatus::Scheduled, $actor);
            }

            $this->notifications->notify(
                user: $registration->user,
                title: 'Undangan ujikom tersedia',
                message: "Undangan ujikom untuk pendaftaran {$registration->registration_number} telah terbit ({$documentNumber}).",
                type: 'invitation_issued',
                registration: $registration,
            );

            return $invitation;
        });
    }
}
