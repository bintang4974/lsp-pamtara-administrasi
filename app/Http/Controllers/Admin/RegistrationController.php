<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificationType;
use App\Models\Registration;
use App\Models\Tuk;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Registration::query()->with(['user', 'certificationType', 'assessmentSchedule.tuk']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($certificationTypeId = $request->input('certification_type_id')) {
            $query->where('certification_type_id', $certificationTypeId);
        }

        if ($tukId = $request->input('tuk_id')) {
            $query->whereHas('assessmentSchedule', fn ($q) => $q->where('tuk_id', $tukId));
        }

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        return view('admin.registrations.index', [
            'registrations' => $query->latest()->paginate(15)->withQueryString(),
            'certificationTypes' => CertificationType::query()->orderBy('name')->get(),
            'tuks' => Tuk::query()->orderBy('name')->get(),
            'filters' => $request->only(['status', 'certification_type_id', 'tuk_id', 'q']),
        ]);
    }

    public function show(Registration $registration): View
    {
        $registration->load([
            'user.participantProfile',
            'certificationType',
            'assessmentSchedule.tuk',
            'documents.requirement',
            'documents.submissions.uploader',
            'documents.submissions.reviewer',
            'payment.submissions.uploader',
            'payment.submissions.reviewer',
            'histories.changedBy',
        ]);

        return view('admin.registrations.show', ['registration' => $registration]);
    }
}
