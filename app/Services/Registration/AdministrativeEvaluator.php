<?php

namespace App\Services\Registration;

use App\Enums\DocumentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Registration;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use App\Services\Payments\PaymentService;
use DomainException;

/**
 * Lolos administrasi hanya jika SEMUA dokumen wajib (menurut snapshot) berstatus approved
 * (lihat PRD.md §3.5). Dipanggil setelah setiap keputusan dokumen. Payment dibuat otomatis
 * setelah lolos administrasi (PRD.md §3.6) — jika nominal belum dikonfigurasi admin, lolos
 * administrasi tetap tercatat dan admin bisa membuat tagihan manual setelah mengatur nominal.
 */
class AdministrativeEvaluator
{
    public function __construct(
        private RegistrationStatusService $statusService,
        private PaymentService $payments,
        private AuditLogger $auditLogger,
    ) {}

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

        if (! $allApproved) {
            return;
        }

        $this->statusService->transitionTo($registration, RegistrationStatus::AdministrativelyApproved, $actor);

        try {
            $this->payments->createForRegistration($registration, $actor);
        } catch (DomainException $e) {
            $this->auditLogger->log(
                actor: $actor,
                action: 'payment.creation_skipped',
                entityType: Registration::class,
                entityId: $registration->id,
                reason: $e->getMessage(),
            );
        }
    }
}
