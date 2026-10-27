<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function show(Registration $registration): View
    {
        $this->authorize('view', $registration);

        return view('participant.registrations.result', [
            'registration' => $registration,
            'result' => $registration->assessmentResult,
        ]);
    }
}
