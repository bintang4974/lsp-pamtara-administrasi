<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Participant\DocumentUploadRequest;
use App\Models\Registration;
use App\Services\Payments\PaymentService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $payments) {}

    public function show(Registration $registration): View|RedirectResponse
    {
        $this->authorize('view', $registration);

        $payment = $registration->payment;

        if (! $payment) {
            return redirect()->route('participant.registrations.documents', $registration)
                ->with('error', 'Instruksi pembayaran hanya tersedia setelah pendaftaran lolos administrasi.');
        }

        $payment->load('submissions');

        return view('participant.registrations.payment', [
            'registration' => $registration,
            'payment' => $payment,
        ]);
    }

    public function uploadProof(DocumentUploadRequest $request, Registration $registration): RedirectResponse
    {
        $this->authorize('update', $registration);

        $payment = $registration->payment;

        if (! $payment) {
            abort(404);
        }

        try {
            $this->payments->submitProof($payment, $request->file('file'), $request->user());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', 'Bukti pembayaran berhasil diunggah, menunggu verifikasi admin.');
    }
}
