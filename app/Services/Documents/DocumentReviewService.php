<?php

namespace App\Services\Documents;

use App\Enums\DocumentStatus;
use App\Enums\RegistrationStatus;
use App\Models\DocumentSubmission;
use App\Models\Registration;
use App\Models\User;
use App\Services\Registration\AdministrativeEvaluator;
use App\Services\Registration\RegistrationStatusService;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Approve / minta revisi (alasan wajib) per versi dokumen yang diperiksa admin, lalu
 * memanggil AdministrativeEvaluator (lihat PRD.md §5.4, ARCHITECTURE.md §3).
 */
class DocumentReviewService
{
    public function __construct(
        private RegistrationStatusService $statusService,
        private AdministrativeEvaluator $evaluator,
    ) {}

    public function approve(DocumentSubmission $submission, User $admin): void
    {
        DB::transaction(function () use ($submission, $admin) {
            $submission->update([
                'status' => DocumentStatus::Approved,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            $document = $submission->registrationDocument;
            $document->update([
                'current_status' => DocumentStatus::Approved,
                'approved_at' => now(),
                'approved_by' => $admin->id,
            ]);

            $registration = $this->ensureUnderReview($document->registration, $admin);
            $this->evaluator->evaluate($registration, $admin);
        });
    }

    /**
     * @throws DomainException Jika alasan revisi kosong.
     */
    public function requestRevision(DocumentSubmission $submission, string $reason, User $admin): void
    {
        if (trim($reason) === '') {
            throw new DomainException('Alasan revisi wajib diisi.');
        }

        DB::transaction(function () use ($submission, $reason, $admin) {
            $submission->update([
                'status' => DocumentStatus::RevisionRequired,
                'review_note' => $reason,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            $document = $submission->registrationDocument;
            $document->update(['current_status' => DocumentStatus::RevisionRequired]);

            $registration = $this->ensureUnderReview($document->registration, $admin);

            if ($registration->status !== RegistrationStatus::RevisionRequired) {
                $this->statusService->transitionTo($registration, RegistrationStatus::RevisionRequired, $admin, $reason);
            }
        });
    }

    private function ensureUnderReview(Registration $registration, User $admin): Registration
    {
        if ($registration->status === RegistrationStatus::Submitted) {
            return $this->statusService->transitionTo($registration, RegistrationStatus::UnderReview, $admin);
        }

        return $registration;
    }
}
