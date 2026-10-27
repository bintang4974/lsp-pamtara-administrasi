<?php

namespace App\Http\Requests\Participant;

use Illuminate\Foundation\Http\FormRequest;

class DocumentUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Batas mutlak 50MB sebagai jaring pengaman; validasi MIME/ukuran sesuai snapshot
            // persyaratan dilakukan di DocumentSubmissionService (bisa lebih kecil dari ini).
            'file' => ['required', 'file', 'max:51200'],
        ];
    }
}
