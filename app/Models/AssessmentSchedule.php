<?php

namespace App\Models;

use App\Enums\ScheduleStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'schedule_code', 'certification_type_id', 'tuk_id', 'starts_at', 'ends_at',
    'registration_deadline', 'status', 'notes', 'created_by',
])]
class AssessmentSchedule extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'registration_deadline' => 'datetime',
            'status' => ScheduleStatus::class,
        ];
    }

    public function certificationType(): BelongsTo
    {
        return $this->belongsTo(CertificationType::class);
    }

    public function tuk(): BelongsTo
    {
        return $this->belongsTo(Tuk::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assessors(): BelongsToMany
    {
        return $this->belongsToMany(Assessor::class, 'schedule_assessors');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'assessment_schedule_id');
    }

    public function isOpenForRegistration(): bool
    {
        return $this->status === ScheduleStatus::Open
            && $this->registration_deadline->isFuture()
            && $this->tuk->is_active;
    }
}
