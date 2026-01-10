<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AdditionalPriceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'startPrice' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'numeric',
                '3' => 'between:-999999999.99,999999999.99',
            ],
            'priceOfGoingPerKm' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'numeric',
                '3' => 'between:-999999999.99,999999999.99',
            ],
            'returnPricePerKm' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'numeric',
                '3' => 'between:-999999999.99,999999999.99',
            ],
            'latitude' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'numeric',
                '3' => 'between:-999999999.99,999999999.99',
            ],
            'longitude' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'numeric',
                '3' => 'between:-999999999.99,999999999.99',
            ],
            'address' => [
                '0' => 'sometimes',
                '1' => 'nullable',
                '2' => 'string',
                '3' => 'max:255',
            ],
        ];
    }
}
