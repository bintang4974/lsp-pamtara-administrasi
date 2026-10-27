<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function show(Registration $registration): View
    {
        $this->authorize('view', $registration);

        $registration->load(['invitationDocuments' => fn ($q) => $q->orderByDesc('version_number')]);

        return view('participant.registrations.invitation', ['registration' => $registration]);
    }
}
