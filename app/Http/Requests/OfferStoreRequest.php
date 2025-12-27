<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class OfferStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'couponCode' => [
                '0' => 'bail',
                '1' => 'string',
                '2' => 'max:255',
                '3' => Rule::unique('offers', 'coupon_code'),
            ],
            'discountRate' => [
                '0' => 'bail',
                '1' => 'numeric',
                '2' => 'between:-999999999.99,999999999.99',
            ],
            'numberOfTimesUsed' => [
                '0' => 'bail',
                '1' => 'integer',
                '2' => 'min:-2147483648',
                '3' => 'max:2147483647',
            ],
            'uses' => [
                '0' => 'bail',
                '1' => 'integer',
                '2' => 'min:-2147483648',
                '3' => 'max:2147483647',
            ],
            'status' => [
                '0' => 'bail',
                '1' => 'string',
                '2' => 'max:255',
            ],
            'startDate' => [
                '0' => 'bail',
                '1' => 'string',
            ],
            'endDate' => [
                '0' => 'bail',
                '1' => 'string',
            ],
        ];
    }
}
