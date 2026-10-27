<?php

namespace App\Services\Registration;

use App\Enums\DocumentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Registration;
use App\Models\User;

/**
 * Lolos administrasi hanya jika SEMUA dokumen wajib (menurut snapshot) berstatus approved
 * (lihat PRD.md §3.5). Dipanggil setelah setiap keputusan dokumen.
 */
class AdministrativeEvaluator
{
    public function __construct(private RegistrationStatusService $statusService) {}

    public function evaluate(Registration $registration, ?User $actor): void
    {
        if ($registration->status !== RegistrationStatus::UnderReview) {
            return;
        }

        $requiredDocuments = $registration->documents()
            ->whereHas('requirement', fn ($q) => $q->where('is_required', true))
            ->get();

        if ($requiredDocuments->isEmpty()) {
            return;
        }

        $allApproved = $requiredDocuments->every(
            fn ($document) => $document->current_status === DocumentStatus::Approved
        );

        if ($allApproved) {
            $this->statusService->transitionTo($registration, RegistrationStatus::AdministrativelyApproved, $actor);
        }
    }
}
