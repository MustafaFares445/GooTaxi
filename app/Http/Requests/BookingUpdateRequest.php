<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\BookingStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class BookingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'userId' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'integer',
                '3' => 'min:-2147483648',
                '4' => 'max:9223372036854775807',
            ],
            'driverId' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'nullable',
                '3' => 'integer',
                '4' => 'min:-2147483648',
                '5' => 'max:9223372036854775807',
            ],
            'offerId' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'nullable',
                '3' => 'integer',
                '4' => 'min:-2147483648',
                '5' => 'max:9223372036854775807',
            ],
            'fromLocation' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'string',
                '3' => 'max:255',
            ],
            'toLocation' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'array',
            ],
            'date' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'date',
                '3' => 'date_format:Y-m-d',
            ],
            'time' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'date_format:H:i:s',
            ],
            'distance' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'numeric',
                '3' => 'between:-999999999.99,999999999.99',
            ],
            'passengers' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'integer',
                '3' => 'min:0',
                '4' => 'max:100',
            ],
            'extraLargeBags' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'boolean',
            ],
            'isCompleted' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'boolean',
            ],
            'finalPrice' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'numeric',
                '3' => 'between:-999999999.99,999999999.99',
            ],
            'status' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'string',
                '3' => 'max:255',
                '4' => Rule::enum(BookingStatus::class),
            ],
            'notes' => ['nullable', 'string'],
        ];
    }
}
