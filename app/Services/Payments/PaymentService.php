<?php

namespace App\Services\Payments;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\ApplicationSetting;
use App\Models\Payment;
use App\Models\PaymentSubmission;
use App\Models\Registration;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use App\Services\Notifications\NotificationService;
use App\Services\Registration\RegistrationStatusService;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Semua peserta membayar ke SATU rekening/VA bersama (bukan VA unik per peserta). Payment
 * dibuat sistem saat lolos administrasi; peserta hanya bisa unggah bukti setelah itu; lunas
 * HANYA lewat konfirmasi admin (lihat PRD.md §3.6, ARCHITECTURE.md §3).
 */
class PaymentService
{
    private const EXTENSION_BY_MIME = [
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];

    public function __construct(
        private RegistrationStatusService $statusService,
        private AuditLogger $auditLogger,
        private NotificationService $notifications,
    ) {}

    /**
     * @throws DomainException Jika nominal pembayaran belum dikonfigurasi admin.
     */
    public function createForRegistration(Registration $registration, ?User $actor): Payment
    {
        $amount = ApplicationSetting::getValue('payment.amount');

        if ($amount === null || $amount === '' || (float) $amount <= 0) {
            throw new DomainException('Nominal pembayaran belum diatur admin di Pengaturan Aplikasi.');
        }

        return DB::transaction(function () use ($registration, $actor, $amount) {
            $payment = Payment::query()->create([
                'registration_id' => $registration->id,
                'expected_amount' => $amount,
                'status' => PaymentStatus::AwaitingPayment,
            ]);

            $this->auditLogger->log(
                actor: $actor,
                action: 'payment.created',
                entityType: Payment::class,
                entityId: $payment->id,
                newValues: ['registration_id' => $registration->id, 'expected_amount' => $amount],
            );

            $this->statusService->transitionTo($registration, RegistrationStatus::AwaitingPayment, $actor);

            return $payment;
        });
    }

    /**
     * @throws DomainException Jika MIME/ukuran tidak sesuai ketentuan.
     */
    public function submitProof(Payment $payment, UploadedFile $file, User $uploader): PaymentSubmission
    {
        $rules = config('pamtara.uploads.payment_proof');
        $detectedMime = $file->getMimeType();

        if (! in_array($detectedMime, $rules['allowed_mime_types'], true)) {
            throw new DomainException('Jenis file bukti pembayaran tidak diizinkan.');
        }

        $extension = self::EXTENSION_BY_MIME[$detectedMime] ?? null;

        if ($extension === null) {
            throw new DomainException('Jenis file tidak dikenali.');
        }

        $sizeKb = (int) ceil($file->getSize() / 1024);

        if ($sizeKb > $rules['max_size_kb']) {
            throw new DomainException("Ukuran file maksimal {$rules['max_size_kb']} KB.");
        }

        return DB::transaction(function () use ($payment, $file, $uploader, $detectedMime, $extension) {
            $version = ((int) $payment->submissions()->max('version_number')) + 1;
            $directory = 'registrations/'.$payment->registration_id.'/payments';
            $filename = "v{$version}-".Str::uuid()->toString().'.'.$extension;

            $path = Storage::disk(config('pamtara.private_disk'))->putFileAs($directory, $file, $filename);

            $submission = PaymentSubmission::query()->create([
                'payment_id' => $payment->id,
                'version_number' => $version,
                'proof_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $detectedMime,
                'file_size_bytes' => $file->getSize(),
                'status' => 'submitted',
                'uploaded_by' => $uploader->id,
                'submitted_at' => now(),
            ]);

            $payment->update(['status' => PaymentStatus::UnderReview]);

            $registration = $payment->registration;

            if (in_array($registration->status, [RegistrationStatus::AwaitingPayment, RegistrationStatus::PaymentRevisionRequired], true)) {
                $this->statusService->transitionTo($registration, RegistrationStatus::PaymentUnderReview, $uploader);
            }

            return $submission;
        });
    }

    /**
     * @throws DomainException Jika alasan revisi kosong.
     */
    public function requestRevision(PaymentSubmission $submission, string $reason, User $admin): void
    {
        if (trim($reason) === '') {
            throw new DomainException('Alasan revisi wajib diisi.');
        }

        DB::transaction(function () use ($submission, $reason, $admin) {
            $submission->update([
                'status' => 'revision_required',
                'review_note' => $reason,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            $payment = $submission->payment;
            $payment->update(['status' => PaymentStatus::RevisionRequired]);

            $registration = $payment->registration;

            if ($registration->status === RegistrationStatus::PaymentUnderReview) {
                $this->statusService->transitionTo($registration, RegistrationStatus::PaymentRevisionRequired, $admin, $reason);
            }
        });
    }

    /**
     * Konfirmasi lunas. SATU-SATUNYA jalur untuk payments.status = paid (lihat PRD.md §3.6).
     */
    public function confirm(Payment $payment, User $admin, float $confirmedAmount, ?string $transactionReference, ?string $adminNote): Payment
    {
        return DB::transaction(function () use ($payment, $admin, $confirmedAmount, $transactionReference, $adminNote) {
            $latestSubmission = $payment->submissions()->latest('version_number')->first();
            $latestSubmission?->update([
                'status' => 'approved',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            $payment->update([
                'status' => PaymentStatus::Paid,
                'confirmed_amount' => $confirmedAmount,
                'confirmed_by' => $admin->id,
                'confirmed_at' => now(),
                'paid_at' => now(),
                'transaction_reference' => $transactionReference,
                'admin_note' => $adminNote,
            ]);

            $this->auditLogger->log(
                actor: $admin,
                action: 'payment.confirmed',
                entityType: Payment::class,
                entityId: $payment->id,
                newValues: [
                    'confirmed_amount' => $confirmedAmount,
                    'transaction_reference' => $transactionReference,
                ],
            );

            $this->statusService->transitionTo($payment->registration, RegistrationStatus::Paid, $admin);

            return $payment;
        });
    }
}
