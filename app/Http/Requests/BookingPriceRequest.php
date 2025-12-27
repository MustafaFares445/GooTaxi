<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
        ];
    }
}
