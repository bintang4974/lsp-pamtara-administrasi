<?php

namespace Database\Seeders;

use App\Models\ApplicationSetting;
use Illuminate\Database\Seeder;

class ApplicationSettingSeeder extends Seeder
{
    /**
     * Key default kosong/placeholder. Nilai nyata (rekening, nominal, dll.) diisi admin lewat
     * halaman pengaturan — jangan mengarang data bisnis di sini (lihat CLAUDE.md aturan keras).
     */
    public function run(): void
    {
        $defaults = [
            'institution.name' => ['value' => 'LSP Pamtara', 'type' => 'string'],
            'institution.logo' => ['value' => null, 'type' => 'string'],
            'institution.contact' => ['value' => null, 'type' => 'string'],
            'payment.bank_name' => ['value' => null, 'type' => 'string'],
            'payment.account_number' => ['value' => null, 'type' => 'string'],
            'payment.account_holder' => ['value' => null, 'type' => 'string'],
            'payment.amount' => ['value' => null, 'type' => 'decimal'],
            'payment.instructions' => ['value' => null, 'type' => 'text'],
            'pdf.footer' => ['value' => null, 'type' => 'string'],
        ];

        foreach ($defaults as $key => $attributes) {
            ApplicationSetting::query()->firstOrCreate(
                ['key' => $key],
                ['value' => $attributes['value'], 'type' => $attributes['type']]
            );
        }
    }
}
