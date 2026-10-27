<?php

namespace App\Exports;

use App\Services\Reports\DocumentCompletenessReportQuery;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DocumentCompletenessExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(private array $filters) {}

    public function query(): Builder
    {
        return (new DocumentCompletenessReportQuery)->query($this->filters);
    }

    public function headings(): array
    {
        return ['No. Pendaftaran', 'Nama Peserta', 'Sertifikasi', 'Dokumen Wajib', 'Disetujui', 'Lengkap?'];
    }

    public function map($registration): array
    {
        $required = $registration->required_documents_count;
        $approved = $registration->approved_required_documents_count;

        return [
            $registration->registration_number,
            $registration->user->name,
            $registration->certificationType->name,
            $required,
            $approved,
            $required > 0 && $required === $approved ? 'Lengkap' : 'Belum Lengkap',
        ];
    }
}
