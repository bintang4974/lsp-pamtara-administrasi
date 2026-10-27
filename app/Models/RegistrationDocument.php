<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'registration_id', 'registration_document_requirement_id', 'current_status',
    'latest_submission_id', 'approved_at', 'approved_by',
])]
class RegistrationDocument extends Model
{
    protected function casts(): array
    {
        return [
            'current_status' => DocumentStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(RegistrationDocumentRequirement::class, 'registration_document_requirement_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(DocumentSubmission::class);
    }

    public function latestSubmission(): BelongsTo
    {
        return $this->belongsTo(DocumentSubmission::class, 'latest_submission_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
