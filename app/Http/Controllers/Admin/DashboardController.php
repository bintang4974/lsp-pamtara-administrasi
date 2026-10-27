<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'totalAdmins' => User::query()->where('role', UserRole::Admin)->count(),
            'totalParticipants' => User::query()->where('role', UserRole::Participant)->count(),
        ]);
    }
}
