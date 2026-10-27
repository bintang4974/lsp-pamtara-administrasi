<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * URL pendaftaran stabil untuk QR di flyer. Tidak berisi password/token/NIK/data pribadi
 * (lihat PRD.md §5.10). Tamu yang belum login diarahkan login/registrasi lalu kembali ke
 * tujuan (intended URL) — sudah ditangani default Laravel di AuthenticatedSessionController
 * dan RegisteredUserController.
 */
class QrCodeController extends Controller
{
    public function index(): View
    {
        $homeUrl = route('home');
        $registrationUrl = route('participant.registration-wizard.certification');

        return view('admin.qrcode.index', [
            'homeUrl' => $homeUrl,
            'registrationUrl' => $registrationUrl,
            'homeSvg' => QrCode::size(280)->generate($homeUrl),
            'registrationSvg' => QrCode::size(280)->generate($registrationUrl),
        ]);
    }
}
