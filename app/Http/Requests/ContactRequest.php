<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'phone' => ['nullable'],
            'whatsapp' => ['nullable'],
            'email' => ['nullable', 'email', 'max:254'],
            'adress' => ['nullable'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
