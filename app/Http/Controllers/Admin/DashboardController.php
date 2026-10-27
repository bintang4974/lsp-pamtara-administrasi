<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AssessmentResult;
use App\Enums\RegistrationStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AssessmentSchedule;
use App\Models\CertificationType;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Tuk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $request->only(['date_from', 'date_to', 'certification_type_id', 'tuk_id']);

        $base = function () use ($filters) {
            $query = Registration::query();

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

            return $query;
        };

        return view('admin.dashboard', [
            'filters' => $filters,
            'certificationTypes' => CertificationType::query()->orderBy('name')->get(),
            'tuks' => Tuk::query()->orderBy('name')->get(),

            'totalAdmins' => User::query()->where('role', UserRole::Admin)->count(),
            'totalParticipants' => User::query()->where('role', UserRole::Participant)->count(),

            'newRegistrations' => $base()->count(),
            'awaitingDocumentReview' => $base()->whereIn('status', [RegistrationStatus::Submitted, RegistrationStatus::UnderReview])->count(),
            'needsRevision' => $base()->where('status', RegistrationStatus::RevisionRequired)->count(),
            'administrativelyApproved' => $base()->where('status', RegistrationStatus::AdministrativelyApproved)->count(),
            'paymentUnderReview' => $base()->where('status', RegistrationStatus::PaymentUnderReview)->count(),
            'paymentConfirmed' => $base()->where('status', RegistrationStatus::Paid)->count(),
            'completed' => $base()->where('status', RegistrationStatus::Completed)->count(),

            'activeSchedules' => AssessmentSchedule::query()->where('status', 'open')->count(),
            'confirmedPaymentTotal' => Payment::query()->where('status', 'paid')->sum('confirmed_amount'),

            'resultCompetent' => $base()->whereHas('assessmentResult', fn ($q) => $q->where('result', AssessmentResult::Competent))->count(),
            'resultNotCompetent' => $base()->whereHas('assessmentResult', fn ($q) => $q->where('result', AssessmentResult::NotCompetent))->count(),
        ]);
    }
}
