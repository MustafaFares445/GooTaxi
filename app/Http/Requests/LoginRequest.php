<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'min:5', 'max:191', Rule::exists('users', 'email')],
            'password' => ['required', 'string', 'min:1', 'max:255'],
        ];
    }
}
