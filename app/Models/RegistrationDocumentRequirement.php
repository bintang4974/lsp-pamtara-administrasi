<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'registration_id', 'requirement_code', 'requirement_name', 'description_snapshot',
    'is_required', 'allowed_mime_types_snapshot', 'max_file_size_kb_snapshot',
])]
class RegistrationDocumentRequirement extends Model
{
    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'allowed_mime_types_snapshot' => 'array',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function document(): HasOne
    {
        return $this->hasOne(RegistrationDocument::class);
    }
}
