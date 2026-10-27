<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'registration_id', 'version_number', 'document_number', 'file_path',
    'schedule_snapshot', 'issued_by', 'issued_at', 'is_current',
])]
class InvitationDocument extends Model
{
    protected function casts(): array
    {
        return [
            'schedule_snapshot' => 'array',
            'issued_at' => 'datetime',
            'is_current' => 'boolean',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
