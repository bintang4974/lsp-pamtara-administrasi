<?php

namespace App\Models;

use App\Enums\AssessmentResult as AssessmentResultEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['registration_id', 'result', 'notes', 'entered_by', 'entered_at', 'announced_at'])]
class AssessmentResult extends Model
{
    protected function casts(): array
    {
        return [
            'result' => AssessmentResultEnum::class,
            'entered_at' => 'datetime',
            'announced_at' => 'datetime',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ResultHistory::class, 'assessment_result_id');
    }

    public function isAnnounced(): bool
    {
        return $this->announced_at !== null;
    }
}
