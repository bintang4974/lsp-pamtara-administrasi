<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['registration_number', 'name', 'phone', 'email', 'is_active'])]
class Assessor extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function assessmentSchedules(): BelongsToMany
    {
        return $this->belongsToMany(AssessmentSchedule::class, 'schedule_assessors');
    }
}
