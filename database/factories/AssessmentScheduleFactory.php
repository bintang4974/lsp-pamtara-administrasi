<?php

namespace Database\Factories;

use App\Enums\ScheduleStatus;
use App\Models\AssessmentSchedule;
use App\Models\CertificationType;
use App\Models\Tuk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentSchedule>
 */
class AssessmentScheduleFactory extends Factory
{
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('+1 week', '+2 months');

        return [
            'schedule_code' => 'SCH-'.fake()->unique()->numerify('####-####'),
            'certification_type_id' => CertificationType::factory(),
            'tuk_id' => Tuk::factory(),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify('+2 hours'),
            'registration_deadline' => (clone $startsAt)->modify('-3 days'),
            'status' => ScheduleStatus::Open,
            'notes' => null,
            'created_by' => null,
        ];
    }
}
