<?php

namespace App\Exports;

use App\Services\Reports\RegistrationReportQuery;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RegistrationsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(private array $filters) {}

    public function query(): Builder
    {
        return (new RegistrationReportQuery)->query($this->filters);
    }

    public function headings(): array
    {
        return ['No. Pendaftaran', 'Nama Peserta', 'Email', 'Sertifikasi', 'TUK', 'Jadwal', 'Status', 'Tanggal Daftar'];
    }

    public function map($registration): array
    {
        return [
            $registration->registration_number,
            $registration->user->name,
            $registration->user->email,
            $registration->certificationType->name,
            $registration->assessmentSchedule->tuk->name,
            $registration->assessmentSchedule->starts_at->format('d M Y H:i'),
            $registration->status->label(),
            $registration->created_at->format('d M Y H:i'),
        ];
    }
}
