<?php

namespace App\Exports;

use App\Services\Imports\ParticipantImportService;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ParticipantImportTemplateExport implements FromArray, WithHeadings
{
    use Exportable;

    public function array(): array
    {
        return [
            [
                'Contoh Nama', 'contoh@email.com', '081234567890', '1234567890123456',
                'Jakarta', '1995-01-31', 'male', 'Jl. Contoh No. 1', 'Jakarta', 'DKI Jakarta', '12345',
            ],
        ];
    }

    public function headings(): array
    {
        return ParticipantImportService::REQUIRED_HEADERS;
    }
}
