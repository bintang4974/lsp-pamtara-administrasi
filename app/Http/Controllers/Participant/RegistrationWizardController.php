<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Participant\RegistrationDataRequest;
use App\Models\AssessmentSchedule;
use App\Models\CertificationType;
use App\Models\Tuk;
use App\Services\Registration\RegistrationService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Form pendaftaran bertahap: sertifikasi -> TUK -> jadwal -> data diri (lihat PRD.md §4.1).
 * Pilihan antar-langkah disimpan di session sampai registrasi benar-benar dibuat di langkah
 * terakhir (data diri), lalu peserta lanjut ke unggah dokumen.
 */
class RegistrationWizardController extends Controller
{
    private const SESSION_KEY = 'registration_wizard';

    public function __construct(private RegistrationService $registrations) {}

    public function certification(): View
    {
        return view('participant.registration-wizard.certification', [
            'certificationTypes' => CertificationType::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function storeCertification(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'certification_type_id' => ['required', 'exists:certification_types,id'],
        ]);

        $existing = $this->registrations->findExistingActiveRegistration($request->user(), (int) $data['certification_type_id']);

        if ($existing) {
            return redirect()->route('participant.registrations.documents', $existing)
                ->with('status', 'Anda sudah memiliki pendaftaran aktif untuk sertifikasi ini.');
        }

        session([self::SESSION_KEY => ['certification_type_id' => (int) $data['certification_type_id']]]);

        return redirect()->route('participant.registration-wizard.tuk');
    }

    public function tuk(Request $request): View|RedirectResponse
    {
        $certificationTypeId = session(self::SESSION_KEY.'.certification_type_id');

        if (! $certificationTypeId) {
            return redirect()->route('participant.registration-wizard.certification');
        }

        $tuks = Tuk::query()
            ->where('is_active', true)
            ->whereHas('assessmentSchedules', function ($q) use ($certificationTypeId) {
                $q->where('certification_type_id', $certificationTypeId)
                    ->where('status', 'open')
                    ->where('registration_deadline', '>', now());
            })
            ->orderBy('name')
            ->get();

        return view('participant.registration-wizard.tuk', ['tuks' => $tuks]);
    }

    public function storeTuk(Request $request): RedirectResponse
    {
        if (! session(self::SESSION_KEY.'.certification_type_id')) {
            return redirect()->route('participant.registration-wizard.certification');
        }

        $data = $request->validate([
            'tuk_id' => ['required', 'exists:tuks,id'],
        ]);

        session([self::SESSION_KEY.'.tuk_id' => (int) $data['tuk_id']]);

        return redirect()->route('participant.registration-wizard.schedule');
    }

    public function schedule(): View|RedirectResponse
    {
        $certificationTypeId = session(self::SESSION_KEY.'.certification_type_id');
        $tukId = session(self::SESSION_KEY.'.tuk_id');

        if (! $certificationTypeId || ! $tukId) {
            return redirect()->route('participant.registration-wizard.certification');
        }

        $schedules = AssessmentSchedule::query()
            ->where('certification_type_id', $certificationTypeId)
            ->where('tuk_id', $tukId)
            ->where('status', 'open')
            ->where('registration_deadline', '>', now())
            ->orderBy('starts_at')
            ->get();

        return view('participant.registration-wizard.schedule', ['schedules' => $schedules]);
    }

    public function storeSchedule(Request $request): RedirectResponse
    {
        if (! session(self::SESSION_KEY.'.tuk_id')) {
            return redirect()->route('participant.registration-wizard.certification');
        }

        $data = $request->validate([
            'assessment_schedule_id' => ['required', 'exists:assessment_schedules,id'],
        ]);

        session([self::SESSION_KEY.'.assessment_schedule_id' => (int) $data['assessment_schedule_id']]);

        return redirect()->route('participant.registration-wizard.data');
    }

    public function data(Request $request): View|RedirectResponse
    {
        if (! session(self::SESSION_KEY.'.assessment_schedule_id')) {
            return redirect()->route('participant.registration-wizard.certification');
        }

        return view('participant.registration-wizard.data', [
            'user' => $request->user(),
            'profile' => $request->user()->participantProfile,
        ]);
    }

    public function storeData(RegistrationDataRequest $request): RedirectResponse
    {
        $wizard = session(self::SESSION_KEY);

        if (! $wizard || ! isset($wizard['assessment_schedule_id'])) {
            return redirect()->route('participant.registration-wizard.certification');
        }

        $user = $request->user();
        $validated = $request->validated();

        $user->forceFill(['name' => $validated['name'], 'phone' => $validated['phone']])->save();

        $user->participantProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'nik' => $validated['nik'] ?? null,
                'birth_place' => $validated['birth_place'],
                'birth_date' => $validated['birth_date'],
                'gender' => $validated['gender'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'province' => $validated['province'],
                'postal_code' => $validated['postal_code'] ?? null,
            ],
        );

        try {
            $registration = $this->registrations->create(
                $user,
                (int) $wizard['certification_type_id'],
                (int) $wizard['assessment_schedule_id'],
            );
        } catch (DomainException $e) {
            session()->forget(self::SESSION_KEY);

            return redirect()->route('participant.registration-wizard.certification')
                ->with('error', $e->getMessage() === 'EXISTING_ACTIVE_REGISTRATION'
                    ? 'Anda sudah memiliki pendaftaran aktif untuk sertifikasi ini.'
                    : $e->getMessage());
        }

        session()->forget(self::SESSION_KEY);

        return redirect()->route('participant.registrations.documents', $registration)
            ->with('status', 'Pendaftaran dibuat. Unggah dokumen persyaratan untuk melanjutkan.');
    }
}
