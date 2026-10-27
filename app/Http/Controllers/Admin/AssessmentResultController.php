<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AssessmentResult as AssessmentResultEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssessmentResultCorrectionRequest;
use App\Http\Requests\Admin\AssessmentResultRequest;
use App\Models\AssessmentSchedule;
use App\Models\Registration;
use App\Services\Results\ResultService;
use DomainException;
use Illuminate\Http\RedirectResponse;

class AssessmentResultController extends Controller
{
    public function __construct(private ResultService $results) {}

    public function store(AssessmentResultRequest $request, Registration $registration): RedirectResponse
    {
        try {
            $this->results->enter(
                $registration,
                AssessmentResultEnum::from($request->validated('result')),
                $request->validated('notes'),
                $request->user(),
            );
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', 'Hasil ujikom berhasil diinput.');
    }

    public function update(AssessmentResultCorrectionRequest $request, Registration $registration): RedirectResponse
    {
        $assessmentResult = $registration->assessmentResult;
        abort_unless($assessmentResult, 404);

        try {
            $this->results->correct(
                $assessmentResult,
                AssessmentResultEnum::from($request->validated('result')),
                $request->validated('reason'),
                $request->user(),
            );
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', 'Hasil ujikom berhasil dikoreksi.');
    }

    public function announce(Registration $registration): RedirectResponse
    {
        $assessmentResult = $registration->assessmentResult;
        abort_unless($assessmentResult, 404);

        $this->results->announce($assessmentResult, request()->user());

        return back()->with('status', 'Hasil ujikom berhasil diumumkan.');
    }

    public function announceSchedule(AssessmentSchedule $assessmentSchedule): RedirectResponse
    {
        $count = $this->results->announceForSchedule($assessmentSchedule, request()->user());

        return back()->with('status', "{$count} hasil ujikom berhasil diumumkan untuk jadwal ini.");
    }
}
