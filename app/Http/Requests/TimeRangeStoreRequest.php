<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class TimeRangeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'days' => [
                '0' => 'bail',
                '1' => 'nullable',
                '2' => 'array',
            ],
            'fromTime' => [
                '0' => 'bail',
                '1' => 'date_format:H:i:s',
            ],
            'toTime' => [
                '0' => 'bail',
                '1' => 'date_format:H:i:s',
            ],
            'pricePercentage' => [
                '0' => 'bail',
                '1' => 'numeric',
                '2' => 'between:-999999999.99,999999999.99',
            ],
        ];
    }
}
