<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AdditionalPriceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'startPrice' => [
                '0' => 'bail',
                '1' => 'numeric',
                '2' => 'between:-999999999.99,999999999.99',
            ],
            'priceOfGoingPerKm' => [
                '0' => 'bail',
                '1' => 'numeric',
                '2' => 'between:-999999999.99,999999999.99',
            ],
            'returnPricePerKm' => [
                '0' => 'bail',
                '1' => 'numeric',
                '2' => 'between:-999999999.99,999999999.99',
            ],
            'latitude' => [
                '0' => 'bail',
                '1' => 'numeric',
                '2' => 'between:-999999999.99,999999999.99',
            ],
            'longitude' => [
                '0' => 'bail',
                '1' => 'numeric',
                '2' => 'between:-999999999.99,999999999.99',
            ],
            'address' => [
                '0' => 'nullable',
                '1' => 'string',
                '2' => 'max:255',
            ],
        ];
    }
}
