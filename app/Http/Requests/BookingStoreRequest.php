<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\BookingStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class BookingStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'userId' => [
                '0' => 'bail',
                '1' => 'integer',
                '2' => 'min:-2147483648',
                '3' => 'max:9223372036854775807',
            ],
            'driverId' => [
                '0' => 'bail',
                '1' => 'nullable',
                '2' => 'integer',
                '3' => 'min:-2147483648',
                '4' => 'max:9223372036854775807',
            ],
            'offerId' => [
                '0' => 'bail',
                '1' => 'nullable',
                '2' => 'integer',
                '3' => 'min:-2147483648',
                '4' => 'max:9223372036854775807',
            ],
            'fromLocation' => [
                '0' => 'bail',
                '1' => 'nullable',
                '2' => 'string',
                '3' => 'max:255',
            ],
            'toLocation' => [
                '0' => 'bail',
                '1' => 'nullable',
                '2' => 'string',
                '3' => 'max:255',
            ],
            'date' => [
                '0' => 'bail',
                '1' => 'date',
                '2' => 'date_format:Y-m-d',
            ],
            'time' => [
                '0' => 'bail',
                '1' => 'date_format:H:i:s',
            ],
            'distance' => [
                '0' => 'bail',
                '1' => 'nullable',
                '2' => 'numeric',
                '3' => 'between:-999999999.99,999999999.99',
            ],
            'isCompleted' => [
                '0' => 'bail',
                '1' => 'nullable',
                '2' => 'boolean',
            ],
            'goingDistance' => [
                '0' => 'bail',
                '1' => 'nullable',
                '2' => 'numeric',
                '3' => 'between:-999999999.99,999999999.99',
            ],
            'returnDistance' => [
                '0' => 'bail',
                '1' => 'nullable',
                '2' => 'numeric',
                '3' => 'between:-999999999.99,999999999.99',
            ],
            'startingLat' => [
                '0' => 'bail',
                '1' => 'required',
                '2' => 'numeric',
            ],
            'startingLng' => [
                '0' => 'bail',
                '1' => 'required',
                '2' => 'numeric',
            ],
            'endingLat' => [
                '0' => 'bail',
                '1' => 'required',
                '2' => 'numeric',
            ],
            'endingLng' => [
                '0' => 'bail',
                '1' => 'required',
                '2' => 'numeric',
            ],
            'passengers' => [
                '0' => 'bail',
                '1' => 'integer',
                '2' => 'min:-2147483648',
                '3' => 'max:2147483647',
            ],
            'extraLargeBags' => [
                '0' => 'bail',
                '1' => 'boolean',
            ],
            'status' => [
                '0' => 'bail',
                '1' => 'string',
                '2' => 'max:255',
                '4' => Rule::enum(BookingStatus::class),
            ],
            'notes' => ['nullable', 'string'],
        ];
    }
}
