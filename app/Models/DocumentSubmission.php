<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'registration_document_id', 'version_number', 'file_path', 'original_filename', 'mime_type',
    'file_size_bytes', 'status', 'review_note', 'uploaded_by', 'reviewed_by', 'submitted_at', 'reviewed_at',
])]
class DocumentSubmission extends Model
{
    protected function casts(): array
    {
        return [
            'status' => DocumentStatus::class,
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function registrationDocument(): BelongsTo
    {
        return $this->belongsTo(RegistrationDocument::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
