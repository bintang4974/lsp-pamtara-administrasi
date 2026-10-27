<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PaymentConfirmationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'confirmed_amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_reference' => ['nullable', 'string', 'max:255'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
