<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

final class BookingPriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'distance' => ['required', 'numeric'],
            'goingDistance' => ['required', 'numeric'],
            'returnDistance' => ['required', 'numeric'],
            'startingLat' => ['required', 'numeric'],
            'startingLng' => ['required', 'numeric'],
            'endingLat' => ['required', 'numeric'],
            'endingLng' => ['required', 'numeric'],
            'couponCode' => ['sometimes', 'string', 'min:1', 'max:100'],
            'extraLargeBags' => ['sometimes', 'boolean'],
            'date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'time' => ['nullable', 'date_format:H:i:s'],
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
