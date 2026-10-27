<?php

namespace App\Http\Requests\Admin;

use App\Models\Tuk;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AssessmentScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('assessment_schedule')?->id;

        return [
            'schedule_code' => ['required', 'string', 'max:100', Rule::unique('assessment_schedules', 'schedule_code')->ignore($id)],
            'certification_type_id' => ['required', 'exists:certification_types,id'],
            'tuk_id' => ['required', 'exists:tuks,id'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'registration_deadline' => ['required', 'date', 'before:starts_at'],
            'status' => ['required', Rule::in(['draft', 'open', 'closed', 'completed', 'cancelled'])],
            'notes' => ['nullable', 'string'],
            'assessor_ids' => ['nullable', 'array'],
            'assessor_ids.*' => ['exists:assessors,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'ends_at.after' => 'Waktu selesai harus setelah waktu mulai.',
            'registration_deadline.before' => 'Batas pendaftaran harus sebelum waktu mulai ujikom.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $tukId = $this->input('tuk_id');
            if ($tukId && ! Tuk::query()->where('id', $tukId)->where('is_active', true)->exists()) {
                $validator->errors()->add('tuk_id', 'TUK yang dipilih tidak aktif.');
            }
        });
    }
}
