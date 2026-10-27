<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['import_batch_id', 'row_number', 'raw_data', 'is_valid', 'errors', 'committed'])]
class ImportRow extends Model
{
    protected function casts(): array
    {
        return [
            'raw_data' => 'array',
            'is_valid' => 'boolean',
            'errors' => 'array',
            'committed' => 'boolean',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class, 'import_batch_id');
    }
}
