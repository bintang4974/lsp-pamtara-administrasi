<?php

namespace App\Services\Reports;

use App\Models\ResultHistory;
use Illuminate\Database\Eloquent\Builder;

class ResultHistoryReportQuery
{
    /**
     * @param  array<string, mixed>  $filters  date_from, date_to
     */
    public function query(array $filters): Builder
    {
        $query = ResultHistory::query()->with(['assessmentResult.registration.user', 'changedBy']);

        if (! empty($filters['date_from'])) {
            $query->whereDate('changed_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('changed_at', '<=', $filters['date_to']);
        }

        return $query->latest('changed_at');
    }
}
