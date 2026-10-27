<?php

namespace App\Services\Reports;

use App\Models\Registration;
use Illuminate\Database\Eloquent\Builder;

class DocumentCompletenessReportQuery
{
    /**
     * @param  array<string, mixed>  $filters  date_from, date_to, certification_type_id, tuk_id
     */
    public function query(array $filters): Builder
    {
        $query = Registration::query()
            ->with(['user', 'certificationType', 'assessmentSchedule.tuk'])
            ->withCount([
                'documents as required_documents_count' => fn ($q) => $q->whereHas(
                    'requirement',
                    fn ($r) => $r->where('is_required', true)
                ),
                'documents as approved_required_documents_count' => fn ($q) => $q->whereHas(
                    'requirement',
                    fn ($r) => $r->where('is_required', true)
                )->where('current_status', 'approved'),
            ]);

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

        return $query->latest();
    }
}
