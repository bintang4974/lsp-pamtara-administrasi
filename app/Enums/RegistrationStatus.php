<?php

namespace App\Enums;

enum RegistrationStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case RevisionRequired = 'revision_required';
    case AdministrativelyApproved = 'administratively_approved';
    case AwaitingPayment = 'awaiting_payment';
    case PaymentUnderReview = 'payment_under_review';
    case PaymentRevisionRequired = 'payment_revision_required';
    case Paid = 'paid';
    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Lengkapi pendaftaran',
            self::Submitted => 'Terkirim, menunggu pemeriksaan',
            self::UnderReview => 'Menunggu pemeriksaan dokumen',
            self::RevisionRequired => 'Perlu revisi dokumen',
            self::AdministrativelyApproved => 'Lolos administrasi',
            self::AwaitingPayment => 'Menunggu pembayaran',
            self::PaymentUnderReview => 'Menunggu verifikasi pembayaran',
            self::PaymentRevisionRequired => 'Perlu revisi bukti pembayaran',
            self::Paid => 'Pembayaran dikonfirmasi',
            self::Scheduled => 'Undangan tersedia',
            self::Completed => 'Ujikom selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    /**
     * Transisi status yang sah. Digunakan satu-satunya oleh RegistrationStatusService (Phase 5).
     *
     * @return array<string>
     */
    public function allowedNextStatuses(): array
    {
        return match ($this) {
            self::Draft => [self::Submitted],
            self::Submitted => [self::UnderReview, self::Cancelled],
            self::UnderReview => [self::RevisionRequired, self::AdministrativelyApproved, self::Cancelled],
            self::RevisionRequired => [self::UnderReview, self::Cancelled],
            self::AdministrativelyApproved => [self::AwaitingPayment, self::Cancelled],
            self::AwaitingPayment => [self::PaymentUnderReview, self::Cancelled],
            self::PaymentUnderReview => [self::PaymentRevisionRequired, self::Paid, self::Cancelled],
            self::PaymentRevisionRequired => [self::PaymentUnderReview, self::Cancelled],
            self::Paid => [self::Scheduled, self::Cancelled],
            self::Scheduled => [self::Completed, self::Cancelled],
            self::Completed => [],
            self::Cancelled => [],
        };
    }
}
