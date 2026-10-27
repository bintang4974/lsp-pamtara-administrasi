<?php

namespace App\Services\Reports;

use App\Models\Registration;
use Illuminate\Database\Eloquent\Builder;

/**
 * Satu sumber query untuk tampilan (paginate) dan ekspor (chunk) — filter identik
 * (lihat ARCHITECTURE.md §3, §9).
 */
class RegistrationReportQuery
{
    /**
     * @param  array<string, mixed>  $filters  date_from, date_to, certification_type_id, tuk_id, assessment_schedule_id, status
     */
    public function query(array $filters): Builder
    {
        $query = Registration::query()->with(['user', 'certificationType', 'assessmentSchedule.tuk']);

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (! empty($filters['certification_type_id'])) {
            $query->where('certification_type_id', $filters['certification_type_id']);
        }

        if (! empty($filters['tuk_id'])) {
            $query->whereHas('assessmentSchedule', fn ($q) => $q->where('tuk_id', $filters['tuk_id']));
        }

        if (! empty($filters['assessment_schedule_id'])) {
            $query->where('assessment_schedule_id', $filters['assessment_schedule_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest();
    }
}
