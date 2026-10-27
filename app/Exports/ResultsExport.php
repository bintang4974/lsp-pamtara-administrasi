<?php

namespace App\Exports;

use App\Services\Reports\ResultReportQuery;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ResultsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(private array $filters) {}

    public function query(): Builder
    {
        return (new ResultReportQuery)->query($this->filters);
    }

    public function headings(): array
    {
        return ['No. Pendaftaran', 'Nama Peserta', 'Sertifikasi', 'Hasil', 'Diumumkan'];
    }

    public function map($result): array
    {
        return [
            $result->registration->registration_number,
            $result->registration->user->name,
            $result->registration->certificationType->name,
            $result->result->label(),
            $result->announced_at?->format('d M Y H:i') ?? 'Belum diumumkan',
        ];
    }
}
