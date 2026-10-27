<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'registration_number', 'user_id', 'certification_type_id', 'assessment_schedule_id', 'status',
    'submitted_at', 'administrative_approved_at', 'administrative_approved_by',
    'completed_at', 'cancelled_at', 'cancellation_reason',
])]
class Registration extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => RegistrationStatus::class,
            'submitted_at' => 'datetime',
            'administrative_approved_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function certificationType(): BelongsTo
    {
        return $this->belongsTo(CertificationType::class);
    }

    public function assessmentSchedule(): BelongsTo
    {
        return $this->belongsTo(AssessmentSchedule::class);
    }

    public function administrativeApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administrative_approved_by');
    }

    public function documentRequirements(): HasMany
    {
        return $this->hasMany(RegistrationDocumentRequirement::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(RegistrationDocument::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function invitationDocuments(): HasMany
    {
        return $this->hasMany(InvitationDocument::class);
    }

    public function currentInvitation(): HasOne
    {
        return $this->hasOne(InvitationDocument::class)->where('is_current', true);
    }

    public function assessmentResult(): HasOne
    {
        return $this->hasOne(AssessmentResult::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(RegistrationHistory::class);
    }
}
