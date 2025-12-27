<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class BasePriceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pricePerKm' => [
                '0' => 'bail',
                '1' => 'numeric',
                '2' => 'between:-999999999.99,999999999.99',
            ],
            'vanPricePercentage' => [
                '0' => 'bail',
                '1' => 'numeric',
                '2' => 'between:-999999999.99,999999999.99',
            ],
        ];
    }
}
