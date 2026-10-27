<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Participant\DocumentUploadRequest;
use App\Models\Registration;
use App\Models\RegistrationDocument;
use App\Services\Documents\DocumentSubmissionService;
use App\Services\Registration\RegistrationService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationDocumentController extends Controller
{
    public function __construct(
        private DocumentSubmissionService $submissions,
        private RegistrationService $registrations,
    ) {}

    public function show(Registration $registration): View
    {
        $this->authorize('view', $registration);

        $registration->load([
            'documents.requirement',
            'documents.submissions',
            'certificationType',
            'assessmentSchedule.tuk',
        ]);

        return view('participant.registrations.documents', ['registration' => $registration]);
    }

    public function upload(DocumentUploadRequest $request, Registration $registration, RegistrationDocument $document): RedirectResponse
    {
        $this->authorize('update', $registration);
        abort_unless($document->registration_id === $registration->id, 404);

        try {
            $this->submissions->submit($document, $request->file('file'), $request->user());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', 'Dokumen berhasil diunggah, menunggu pemeriksaan admin.');
    }

    public function submit(Request $request, Registration $registration): RedirectResponse
    {
        $this->authorize('update', $registration);

        try {
            $this->registrations->submit($registration, $request->user());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('participant.dashboard')->with('status', 'Pendaftaran berhasil dikirim dan menunggu pemeriksaan.');
    }
}
