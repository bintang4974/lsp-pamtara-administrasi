<?php

namespace App\Support;

use App\Enums\RegistrationStatus;
use App\Models\Registration;

/**
 * Menurunkan stepper dashboard peserta dari data aktual (lihat PRD.md §4.2, ARCHITECTURE.md
 * §4.2). Jangan hardcode status di Blade — selalu lewat class ini.
 */
class JourneyPresenter
{
    private const STAGE_LABELS = [
        1 => 'Pendaftaran',
        2 => 'Verifikasi Dokumen',
        3 => 'Pembayaran',
        4 => 'Undangan Ujikom',
        5 => 'Hasil Ujikom',
    ];

    private const STAGE_BY_STATUS = [
        'draft' => 1,
        'submitted' => 2,
        'under_review' => 2,
        'revision_required' => 2,
        'administratively_approved' => 2,
        'awaiting_payment' => 3,
        'payment_under_review' => 3,
        'payment_revision_required' => 3,
        'paid' => 3,
        'scheduled' => 4,
        'completed' => 5,
    ];

    public function __construct(private Registration $registration) {}

    public function currentStatusLabel(): string
    {
        return $this->registration->status->label();
    }

    public function isCancelled(): bool
    {
        return $this->registration->status === RegistrationStatus::Cancelled;
    }

    public function currentStageNumber(): int
    {
        return self::STAGE_BY_STATUS[$this->registration->status->value] ?? 1;
    }

    /**
     * @return array<int, array{number: int, label: string, state: string}>
     */
    public function steps(): array
    {
        $current = $this->currentStageNumber();

        return array_map(
            fn (int $number, string $label) => [
                'number' => $number,
                'label' => $label,
                'state' => match (true) {
                    $number < $current => 'done',
                    $number === $current => 'current',
                    default => 'upcoming',
                },
            ],
            array_keys(self::STAGE_LABELS),
            self::STAGE_LABELS,
        );
    }
}
