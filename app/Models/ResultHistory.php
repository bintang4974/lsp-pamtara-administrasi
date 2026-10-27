<?php

namespace App\Models;

use App\Enums\AssessmentResult as AssessmentResultEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['assessment_result_id', 'old_result', 'new_result', 'reason', 'changed_by', 'changed_at'])]
class ResultHistory extends Model
{
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'old_result' => AssessmentResultEnum::class,
            'new_result' => AssessmentResultEnum::class,
            'changed_at' => 'datetime',
        ];
    }

    public function assessmentResult(): BelongsTo
    {
        return $this->belongsTo(AssessmentResult::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
