<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['registration_id', 'old_status', 'new_status', 'reason', 'changed_by', 'changed_at', 'metadata'])]
class RegistrationHistory extends Model
{
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'old_status' => RegistrationStatus::class,
            'new_status' => RegistrationStatus::class,
            'changed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
