<?php

use App\Http\Controllers\Participant\DashboardController;
use App\Http\Controllers\Participant\NotificationController;
use App\Http\Controllers\Participant\PaymentController;
use App\Http\Controllers\Participant\RegistrationDocumentController;
use App\Http\Controllers\Participant\RegistrationWizardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:participant'])
    ->prefix('peserta')
    ->name('participant.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::prefix('daftar')->name('registration-wizard.')->group(function () {
            Route::get('sertifikasi', [RegistrationWizardController::class, 'certification'])->name('certification');
            Route::post('sertifikasi', [RegistrationWizardController::class, 'storeCertification'])->name('certification.store');
            Route::get('tuk', [RegistrationWizardController::class, 'tuk'])->name('tuk');
            Route::post('tuk', [RegistrationWizardController::class, 'storeTuk'])->name('tuk.store');
            Route::get('jadwal', [RegistrationWizardController::class, 'schedule'])->name('schedule');
            Route::post('jadwal', [RegistrationWizardController::class, 'storeSchedule'])->name('schedule.store');
            Route::get('data-diri', [RegistrationWizardController::class, 'data'])->name('data');
            Route::post('data-diri', [RegistrationWizardController::class, 'storeData'])->name('data.store');
        });

        Route::get('pendaftaran/{registration}/dokumen', [RegistrationDocumentController::class, 'show'])->name('registrations.documents');
        Route::post('pendaftaran/{registration}/dokumen/{document}', [RegistrationDocumentController::class, 'upload'])->name('registrations.documents.upload');
        Route::post('pendaftaran/{registration}/kirim', [RegistrationDocumentController::class, 'submit'])->name('registrations.submit');

        Route::get('pendaftaran/{registration}/pembayaran', [PaymentController::class, 'show'])->name('registrations.payment');
        Route::post('pendaftaran/{registration}/pembayaran/bukti', [PaymentController::class, 'uploadProof'])->name('registrations.payment.upload');

        Route::get('notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifikasi/{notification}/baca', [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('notifikasi/baca-semua', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    });
