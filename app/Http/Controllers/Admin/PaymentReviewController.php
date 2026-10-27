<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocumentRevisionRequest;
use App\Http\Requests\Admin\PaymentConfirmationRequest;
use App\Models\PaymentSubmission;
use App\Models\Registration;
use App\Services\Payments\PaymentService;
use DomainException;
use Illuminate\Http\RedirectResponse;

class PaymentReviewController extends Controller
{
    public function __construct(private PaymentService $payments) {}

    public function requestRevision(DocumentRevisionRequest $request, Registration $registration, PaymentSubmission $submission): RedirectResponse
    {
        abort_unless($submission->payment->registration_id === $registration->id, 404);

        try {
            $this->payments->requestRevision($submission, $request->validated('reason'), $request->user());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', 'Permintaan revisi bukti pembayaran dikirim ke peserta.');
    }

    /**
     * Jalur manual bila pembuatan tagihan otomatis gagal sebelumnya (mis. nominal belum
     * dikonfigurasi saat registrasi lolos administrasi).
     */
    public function create(Registration $registration): RedirectResponse
    {
        if ($registration->payment) {
            return back()->with('status', 'Tagihan pembayaran sudah ada.');
        }

        try {
            $this->payments->createForRegistration($registration, request()->user());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', 'Tagihan pembayaran berhasil dibuat.');
    }

    public function confirm(PaymentConfirmationRequest $request, Registration $registration): RedirectResponse
    {
        $payment = $registration->payment;
        abort_unless($payment, 404);

        $validated = $request->validated();

        $this->payments->confirm(
            $payment,
            $request->user(),
            (float) $validated['confirmed_amount'],
            $validated['transaction_reference'] ?? null,
            $validated['admin_note'] ?? null,
        );

        return back()->with('status', 'Pembayaran dikonfirmasi lunas.');
    }
}
