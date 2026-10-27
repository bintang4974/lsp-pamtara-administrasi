<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Support\JourneyPresenter;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $registrations = $request->user()->registrations()
            ->with(['certificationType', 'assessmentSchedule.tuk'])
            ->latest()
            ->get();

        return view('participant.dashboard', [
            'registrations' => $registrations,
            'journeys' => $registrations->mapWithKeys(fn ($registration) => [
                $registration->id => new JourneyPresenter($registration),
            ]),
            'unreadNotifications' => $request->user()->appNotifications()->whereNull('read_at')->count(),
        ]);
    }
}
