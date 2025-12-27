<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class TimeRangeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'days' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'nullable',
                '3' => 'array',
            ],
            'fromTime' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'date_format:H:i:s',
            ],
            'toTime' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'date_format:H:i:s',
            ],
            'pricePercentage' => [
                '0' => 'sometimes',
                '1' => 'bail',
                '2' => 'numeric',
                '3' => 'between:-999999999.99,999999999.99',
            ],
        ];
    }
}
