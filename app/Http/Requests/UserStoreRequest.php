<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                '0' => 'bail',
                '1' => 'string',
                '2' => 'max:255',
            ],
            'email' => [
                '0' => 'bail',
                '1' => 'email',
                '2' => 'max:255',
                '3' => Rule::unique('users', 'email'),
            ],
            'phoneNumber' => [
                '0' => 'bail',
                '1' => 'nullable',
                '2' => 'string',
                '3' => 'max:255',
            ],
            'isAdmin' => [
                '0' => 'bail',
                '1' => 'integer',
                '2' => 'min:-2147483648',
                '3' => 'max:255',
            ],
            'password' => [
                '0' => 'bail',
                '1' => 'string',
                '2' => 'min:8',
                '3' => 'max:255',
            ],
        ];
    }
}
