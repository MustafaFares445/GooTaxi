<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\BookingStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
                '2' => 'array',
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
                '1' => 'nullable',
                '2' => 'numeric',
            ],
            'startingLng' => [
                '0' => 'bail',
                '1' => 'nullable',
                '2' => 'numeric',
            ],
            'endingLat' => [
                '0' => 'bail',
                '1' => 'nullable',
                '2' => 'numeric',
            ],
            'endingLng' => [
                '0' => 'bail',
                '1' => 'nullable',
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
            'finalPrice' => [
                '0' => 'bail',
                '1' => 'nullable',
                '2' => 'numeric',
            ],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $hasLocation = ! empty($this->startingLat) && ! empty($this->startingLng)
                && ! empty($this->endingLat) && ! empty($this->endingLng);
            $hasDistance = ! empty($this->goingDistance) || ! empty($this->returnDistance);

            if (! $hasLocation && ! $hasDistance && empty($this->finalPrice)) {
                $validator->errors()->add('finalPrice', 'The final price field is required when location coordinates or distance fields are not provided.');
            }
        });
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
