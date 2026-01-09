<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\OfferStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

final class UserBookingRequest extends FormRequest
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
                '1' => 'sometimes',
                '2' => 'string',
                '3' => Rule::exists('offers', 'coupon_code')
                    ->where('status', OfferStatus::Active->value),
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
                '1' => 'string',
                '2' => 'max:255',
            ],
            'toLocation' => [
                '0' => 'bail',
                '1' => 'array',
            ],
            'date' => [
                '0' => 'bail',
                '1' => 'nullable',
                '2' => 'date',
                '3' => 'date_format:Y-m-d',
            ],
            'time' => [
                '0' => 'bail',
                '1' => 'nullable',
                '2' => 'date_format:H:i:s',
            ],
            'distance' => [
                '0' => 'bail',
                '1' => 'numeric',
                '2' => 'between:-999999999.99,999999999.99',
            ],
            'goingDistance' => [
                '0' => 'bail',
                '1' => 'numeric',
                '2' => 'between:-999999999.99,999999999.99',
            ],
            'returnDistance' => [
                '0' => 'bail',
                '1' => 'numeric',
                '2' => 'between:-999999999.99,999999999.99',
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
            'isCompleted' => [
                '0' => 'bail',
                '1' => 'boolean',
            ],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (empty($this->date)) {
            $this->merge(['date' => Carbon::now()->format('Y-m-d')]);
        }

        if (empty($this->time)) {
            $this->merge(['time' => Carbon::now()->format('H:i:s')]);
        }
    }
}
