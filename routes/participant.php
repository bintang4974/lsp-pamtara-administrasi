<?php

use App\Http\Controllers\Participant\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:participant'])
    ->prefix('peserta')
    ->name('participant.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
    });
