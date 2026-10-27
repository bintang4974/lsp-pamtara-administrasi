<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApplicationSettingsRequest;
use App\Models\ApplicationSetting;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Pengaturan rekening/VA bersama, nominal, instruksi pembayaran, dan info lembaga. Satu
 * rekening/VA untuk semua peserta (lihat PRD.md §3.6); setiap perubahan diaudit.
 */
class ApplicationSettingController extends Controller
{
    private const FIELD_TO_KEY = [
        'institution_name' => 'institution.name',
        'institution_contact' => 'institution.contact',
        'payment_bank_name' => 'payment.bank_name',
        'payment_account_number' => 'payment.account_number',
        'payment_account_holder' => 'payment.account_holder',
        'payment_amount' => 'payment.amount',
        'payment_instructions' => 'payment.instructions',
        'pdf_footer' => 'pdf.footer',
    ];

    public function __construct(private AuditLogger $auditLogger) {}

    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => ApplicationSetting::query()->get()->keyBy('key'),
        ]);
    }

    public function update(ApplicationSettingsRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            foreach (self::FIELD_TO_KEY as $field => $key) {
                $value = $request->validated()[$field] ?? null;
                $setting = ApplicationSetting::query()->firstOrNew(['key' => $key]);
                $oldValue = $setting->value;

                $setting->value = $value;
                $setting->updated_by = $request->user()->id;
                $setting->save();

                if ($oldValue !== $value) {
                    $this->auditLogger->log(
                        actor: $request->user(),
                        action: 'application_setting.updated',
                        entityType: ApplicationSetting::class,
                        entityId: $setting->id,
                        oldValues: ['key' => $key, 'value' => $oldValue],
                        newValues: ['key' => $key, 'value' => $value],
                    );
                }
            }
        });

        return redirect()->route('admin.settings.edit')->with('status', 'Pengaturan berhasil disimpan.');
    }
}
