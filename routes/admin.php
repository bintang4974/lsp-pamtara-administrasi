<?php

use App\Http\Controllers\Admin\ApplicationSettingController;
use App\Http\Controllers\Admin\AssessmentScheduleController;
use App\Http\Controllers\Admin\AssessorController;
use App\Http\Controllers\Admin\CertificationTypeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DocumentRequirementController;
use App\Http\Controllers\Admin\DocumentReviewController;
use App\Http\Controllers\Admin\PaymentReviewController;
use App\Http\Controllers\Admin\RegistrationController;
use App\Http\Controllers\Admin\TukController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('certification-types', [CertificationTypeController::class, 'index'])->name('certification-types.index');
        Route::get('certification-types/create', [CertificationTypeController::class, 'create'])->name('certification-types.create');
        Route::post('certification-types', [CertificationTypeController::class, 'store'])->name('certification-types.store');
        Route::get('certification-types/{certification_type}/edit', [CertificationTypeController::class, 'edit'])->name('certification-types.edit');
        Route::put('certification-types/{certification_type}', [CertificationTypeController::class, 'update'])->name('certification-types.update');
        Route::patch('certification-types/{certification_type}/toggle', [CertificationTypeController::class, 'toggle'])->name('certification-types.toggle');

        Route::get('tuks', [TukController::class, 'index'])->name('tuks.index');
        Route::get('tuks/create', [TukController::class, 'create'])->name('tuks.create');
        Route::post('tuks', [TukController::class, 'store'])->name('tuks.store');
        Route::get('tuks/{tuk}/edit', [TukController::class, 'edit'])->name('tuks.edit');
        Route::put('tuks/{tuk}', [TukController::class, 'update'])->name('tuks.update');
        Route::patch('tuks/{tuk}/toggle', [TukController::class, 'toggle'])->name('tuks.toggle');

        Route::get('assessors', [AssessorController::class, 'index'])->name('assessors.index');
        Route::get('assessors/create', [AssessorController::class, 'create'])->name('assessors.create');
        Route::post('assessors', [AssessorController::class, 'store'])->name('assessors.store');
        Route::get('assessors/{assessor}/edit', [AssessorController::class, 'edit'])->name('assessors.edit');
        Route::put('assessors/{assessor}', [AssessorController::class, 'update'])->name('assessors.update');
        Route::patch('assessors/{assessor}/toggle', [AssessorController::class, 'toggle'])->name('assessors.toggle');

        Route::get('document-requirements', [DocumentRequirementController::class, 'index'])->name('document-requirements.index');
        Route::get('document-requirements/create', [DocumentRequirementController::class, 'create'])->name('document-requirements.create');
        Route::post('document-requirements', [DocumentRequirementController::class, 'store'])->name('document-requirements.store');
        Route::get('document-requirements/{document_requirement}/edit', [DocumentRequirementController::class, 'edit'])->name('document-requirements.edit');
        Route::put('document-requirements/{document_requirement}', [DocumentRequirementController::class, 'update'])->name('document-requirements.update');
        Route::patch('document-requirements/{document_requirement}/toggle', [DocumentRequirementController::class, 'toggle'])->name('document-requirements.toggle');
        Route::delete('document-requirements/{document_requirement}', [DocumentRequirementController::class, 'destroy'])->name('document-requirements.destroy');

        Route::get('assessment-schedules', [AssessmentScheduleController::class, 'index'])->name('assessment-schedules.index');
        Route::get('assessment-schedules/create', [AssessmentScheduleController::class, 'create'])->name('assessment-schedules.create');
        Route::post('assessment-schedules', [AssessmentScheduleController::class, 'store'])->name('assessment-schedules.store');
        Route::get('assessment-schedules/{assessment_schedule}/edit', [AssessmentScheduleController::class, 'edit'])->name('assessment-schedules.edit');
        Route::put('assessment-schedules/{assessment_schedule}', [AssessmentScheduleController::class, 'update'])->name('assessment-schedules.update');

        Route::get('registrations', [RegistrationController::class, 'index'])->name('registrations.index');
        Route::get('registrations/{registration}', [RegistrationController::class, 'show'])->name('registrations.show');
        Route::post('registrations/{registration}/documents/{submission}/approve', [DocumentReviewController::class, 'approve'])->name('registrations.documents.approve');
        Route::post('registrations/{registration}/documents/{submission}/request-revision', [DocumentReviewController::class, 'requestRevision'])->name('registrations.documents.request-revision');

        Route::post('registrations/{registration}/payment/create', [PaymentReviewController::class, 'create'])->name('registrations.payment.create');
        Route::post('registrations/{registration}/payment/confirm', [PaymentReviewController::class, 'confirm'])->name('registrations.payment.confirm');
        Route::post('registrations/{registration}/payment/{submission}/request-revision', [PaymentReviewController::class, 'requestRevision'])->name('registrations.payment.request-revision');

        Route::get('settings', [ApplicationSettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [ApplicationSettingController::class, 'update'])->name('settings.update');
    });
