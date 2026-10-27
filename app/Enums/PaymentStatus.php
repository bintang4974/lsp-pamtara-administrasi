<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Unpaid = 'unpaid';
    case AwaitingPayment = 'awaiting_payment';
    case UnderReview = 'under_review';
    case RevisionRequired = 'revision_required';
    case Paid = 'paid';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Belum bayar',
            self::AwaitingPayment => 'Menunggu pembayaran',
            self::UnderReview => 'Menunggu verifikasi',
            self::RevisionRequired => 'Perlu revisi bukti',
            self::Paid => 'Lunas',
            self::Cancelled => 'Dibatalkan',
        };
    }
}
