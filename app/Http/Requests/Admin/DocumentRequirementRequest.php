<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentRequirementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('document_requirement')?->id;

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('document_requirements', 'code')->ignore($id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_required' => ['sometimes', 'boolean'],
            'allowed_mime_types' => ['required', 'array', 'min:1'],
            'allowed_mime_types.*' => ['string', 'max:100'],
            'max_file_size_kb' => ['required', 'integer', 'min:1', 'max:51200'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
