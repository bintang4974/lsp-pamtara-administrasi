<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'name', 'description', 'is_required', 'allowed_mime_types', 'max_file_size_kb', 'sort_order', 'is_active'])]
class DocumentRequirement extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'allowed_mime_types' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
