<?php

namespace Database\Factories;

use App\Models\Assessor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assessor>
 */
class AssessorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'registration_number' => fake()->unique()->numerify('ASR-########'),
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'is_active' => true,
        ];
    }
}
