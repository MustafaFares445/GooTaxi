<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class DriverUpdateRequest extends FormRequest
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
        ];
    }
}
