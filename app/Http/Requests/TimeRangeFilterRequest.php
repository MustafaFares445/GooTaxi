<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class TimeRangeFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'perPage' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'filter.basePriceId' => 'sometimes|string|max:255',
            'filter.days' => 'sometimes|string|max:255',
            'filter.fromTime' => 'sometimes|string|max:255',
            'filter.toTime' => 'sometimes|string|max:255',
            'filter.pricePercentage' => 'sometimes|string|max:255',
            'filter.createdAfter' => 'sometimes|date',
            'filter.createdBefore' => 'sometimes|date|after_or_equal:filter.createdAfter',
        ];
    }
}
