<?php

namespace App\Services\Reports;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;

/**
 * Rekap penerimaan = SUM(confirmed_amount) untuk status paid, bukan nominal bukti
 * (lihat DATABASE.md §4, PRD.md §3.6).
 */
class PaymentReportQuery
{
    /**
     * @param  array<string, mixed>  $filters  date_from, date_to, status, certification_type_id
     */
    public function query(array $filters): Builder
    {
        $query = Payment::query()->with(['registration.user', 'registration.certificationType']);

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['certification_type_id'])) {
            $query->whereHas('registration', fn ($q) => $q->where('certification_type_id', $filters['certification_type_id']));
        }

        return $query->latest();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function confirmedTotal(array $filters): float
    {
        $filters['status'] = 'paid';

        return (float) $this->query($filters)->sum('confirmed_amount');
    }
}
