<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'string',
                '3' => 'max:255',
            ],
            'email' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'email',
                '3' => 'max:255',
                '4' => Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            'phoneNumber' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'nullable',
                '3' => 'string',
                '4' => 'max:255',
            ],
            'password' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'string',
                '3' => 'min:8',
                '4' => 'max:255',
            ],
        ];
    }
}
