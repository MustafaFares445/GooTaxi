<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class OfferUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'couponCode' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'string',
                '3' => 'max:255',
                '4' => Rule::unique('offers', 'coupon_code')->ignore($this->route('offer')),
            ],
            'discountRate' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'numeric',
                '3' => 'between:-999999999.99,999999999.99',
            ],
            'numberOfTimesUsed' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'integer',
                '3' => 'min:-2147483648',
                '4' => 'max:2147483647',
            ],
            'uses' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'integer',
                '3' => 'min:-2147483648',
                '4' => 'max:2147483647',
            ],
            'status' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'string',
                '3' => 'max:255',
            ],
            'startDate' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'date',
                '3' => 'date_format:Y-m-d',
            ],
            'endDate' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'date',
                '3' => 'date_format:Y-m-d',
            ],
        ];
    }
}
