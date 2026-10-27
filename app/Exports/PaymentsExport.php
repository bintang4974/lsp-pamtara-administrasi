<?php

namespace App\Exports;

use App\Services\Reports\PaymentReportQuery;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(private array $filters) {}

    public function query(): Builder
    {
        return (new PaymentReportQuery)->query($this->filters);
    }

    public function headings(): array
    {
        return ['No. Pendaftaran', 'Nama Peserta', 'Nominal Diharapkan', 'Nominal Diterima', 'Status', 'Referensi', 'Waktu Konfirmasi'];
    }

    public function map($payment): array
    {
        return [
            $payment->registration->registration_number,
            $payment->registration->user->name,
            $payment->expected_amount,
            $payment->confirmed_amount,
            $payment->status->label(),
            $payment->transaction_reference,
            $payment->confirmed_at?->format('d M Y H:i'),
        ];
    }
}
