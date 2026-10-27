<?php

namespace Database\Factories;

use App\Enums\RegistrationStatus;
use App\Models\AssessmentSchedule;
use App\Models\CertificationType;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Registration>
 */
class RegistrationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'registration_number' => 'REG-'.now()->year.'-'.fake()->unique()->numerify('######'),
            'user_id' => User::factory(),
            'certification_type_id' => CertificationType::factory(),
            'assessment_schedule_id' => AssessmentSchedule::factory(),
            'status' => RegistrationStatus::Draft,
        ];
    }
}
