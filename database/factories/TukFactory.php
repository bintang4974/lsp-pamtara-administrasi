<?php

namespace Database\Factories;

use App\Models\Tuk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tuk>
 */
class TukFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => 'TUK-DUMMY-'.fake()->unique()->numberBetween(1, 9999),
            'name' => 'TUK DUMMY '.fake()->city(),
            'address' => '(DUMMY) '.fake()->address(),
            'city' => fake()->city(),
            'province' => fake()->state(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'is_active' => true,
        ];
    }
}
