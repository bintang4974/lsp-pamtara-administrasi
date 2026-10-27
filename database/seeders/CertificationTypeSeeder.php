<?php

namespace Database\Seeders;

use App\Models\CertificationType;
use Illuminate\Database\Seeder;

class CertificationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'GADA_PRATAMA', 'name' => 'Gada Pratama', 'color_code' => '#2563eb'],
            ['code' => 'GADA_MADYA', 'name' => 'Gada Madya', 'color_code' => '#eab308'],
            ['code' => 'GADA_UTAMA', 'name' => 'Gada Utama', 'color_code' => '#dc2626'],
        ];

        foreach ($types as $type) {
            CertificationType::query()->updateOrCreate(
                ['code' => $type['code']],
                [
                    'name' => $type['name'],
                    'color_code' => $type['color_code'],
                    'is_active' => true,
                ]
            );
        }
    }
}
