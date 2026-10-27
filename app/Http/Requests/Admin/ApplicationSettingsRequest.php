<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institution_name' => ['required', 'string', 'max:255'],
            'institution_contact' => ['nullable', 'string', 'max:255'],
            'payment_bank_name' => ['nullable', 'string', 'max:255'],
            'payment_account_number' => ['nullable', 'string', 'max:50'],
            'payment_account_holder' => ['nullable', 'string', 'max:255'],
            'payment_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_instructions' => ['nullable', 'string'],
            'pdf_footer' => ['nullable', 'string', 'max:255'],
        ];
    }
}
