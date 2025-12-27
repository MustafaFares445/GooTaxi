<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class BasePriceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pricePerKm' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'numeric',
                '3' => 'between:-999999999.99,999999999.99',
            ],
            'vanPricePercentage' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'numeric',
                '3' => 'between:-999999999.99,999999999.99',
            ],
        ];
    }
}
