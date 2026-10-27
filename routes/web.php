<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\InfoController;
use App\Models\ApplicationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('public.home', [
        'institutionName' => ApplicationSetting::getValue('institution.name', 'LSP Pamtara'),
    ]);
})->name('home');

Route::prefix('info')->name('info.')->group(function () {
    Route::get('sertifikasi', [InfoController::class, 'certifications'])->name('certifications');
    Route::get('tuk', [InfoController::class, 'tuks'])->name('tuks');
    Route::get('persyaratan', [InfoController::class, 'documentRequirements'])->name('document-requirements');
});

// Redirector peran: Breeze mengarahkan beberapa alur (registrasi, verifikasi, reset password)
// ke route('dashboard'); di sini diteruskan ke dashboard admin atau peserta sesuai role.
Route::get('/dashboard', function (Request $request) {
    return redirect($request->user()->isAdmin()
        ? route('admin.dashboard')
        : route('participant.dashboard'));
})->middleware(['auth', 'active'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/participant.php';
