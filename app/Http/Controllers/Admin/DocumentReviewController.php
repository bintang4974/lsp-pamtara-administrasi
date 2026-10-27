<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocumentRevisionRequest;
use App\Models\DocumentSubmission;
use App\Models\Registration;
use App\Services\Documents\DocumentReviewService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DocumentReviewController extends Controller
{
    public function __construct(private DocumentReviewService $reviews) {}

    public function approve(Request $request, Registration $registration, DocumentSubmission $submission): RedirectResponse
    {
        $this->ensureSubmissionBelongsToRegistration($registration, $submission);

        try {
            $this->reviews->approve($submission, $request->user());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', 'Dokumen disetujui.');
    }

    public function requestRevision(DocumentRevisionRequest $request, Registration $registration, DocumentSubmission $submission): RedirectResponse
    {
        $this->ensureSubmissionBelongsToRegistration($registration, $submission);

        try {
            $this->reviews->requestRevision($submission, $request->validated('reason'), $request->user());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', 'Permintaan revisi dikirim ke peserta.');
    }

    private function ensureSubmissionBelongsToRegistration(Registration $registration, DocumentSubmission $submission): void
    {
        abort_unless($submission->registrationDocument->registration_id === $registration->id, 404);
    }
}
