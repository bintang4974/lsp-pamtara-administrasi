<?php

namespace App\Services\Reports;

use App\Models\AssessmentResult;
use Illuminate\Database\Eloquent\Builder;

class ResultReportQuery
{
    /**
     * @param  array<string, mixed>  $filters  result, announced ('yes'|'no'), certification_type_id
     */
    public function query(array $filters): Builder
    {
        $query = AssessmentResult::query()->with(['registration.user', 'registration.certificationType']);

        if (! empty($filters['result'])) {
            $query->where('result', $filters['result']);
        }

        if (! empty($filters['announced'])) {
            $filters['announced'] === 'yes'
                ? $query->whereNotNull('announced_at')
                : $query->whereNull('announced_at');
        }

        if (! empty($filters['certification_type_id'])) {
            $query->whereHas('registration', fn ($q) => $q->where('certification_type_id', $filters['certification_type_id']));
        }

        return $query->latest('entered_at');
    }
}
