<?php

namespace Database\Factories;

use App\Models\CertificationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CertificationType>
 */
class CertificationTypeFactory extends Factory
{
    public function definition(): array
    {
        $code = fake()->unique()->bothify('CERT_####');

        return [
            'code' => $code,
            'name' => 'Sertifikasi '.$code,
            'description' => fake()->sentence(),
            'color_code' => fake()->safeHexColor(),
            'is_active' => true,
        ];
    }
}
