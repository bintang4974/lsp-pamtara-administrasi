<?php

namespace App\Exports;

use App\Services\Reports\ResultHistoryReportQuery;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ResultHistoryExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(private array $filters) {}

    public function query(): Builder
    {
        return (new ResultHistoryReportQuery)->query($this->filters);
    }

    public function headings(): array
    {
        return ['No. Pendaftaran', 'Nama Peserta', 'Hasil Lama', 'Hasil Baru', 'Alasan', 'Diubah Oleh', 'Waktu'];
    }

    public function map($history): array
    {
        return [
            $history->assessmentResult->registration->registration_number,
            $history->assessmentResult->registration->user->name,
            $history->old_result?->label() ?? '-',
            $history->new_result->label(),
            $history->reason,
            $history->changedBy?->name,
            $history->changed_at->format('d M Y H:i'),
        ];
    }
}
