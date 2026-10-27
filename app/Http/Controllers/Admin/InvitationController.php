<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Services\Invitations\InvitationService;
use DomainException;
use Illuminate\Http\RedirectResponse;

class InvitationController extends Controller
{
    public function __construct(private InvitationService $invitations) {}

    public function store(Registration $registration): RedirectResponse
    {
        try {
            $this->invitations->issue($registration, request()->user());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', 'Undangan berhasil diterbitkan.');
    }
}
