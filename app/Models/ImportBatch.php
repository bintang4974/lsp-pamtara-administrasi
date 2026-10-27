<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['admin_user_id', 'original_filename', 'status', 'summary', 'committed_at'])]
class ImportBatch extends Model
{
    protected function casts(): array
    {
        return [
            'summary' => 'array',
            'committed_at' => 'datetime',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function rows(): HasMany
    {
        return $this->hasMany(ImportRow::class);
    }
}
