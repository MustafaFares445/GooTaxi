<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class OfferFilterRequest extends FormRequest
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
            'filter.couponCode' => 'sometimes|string|max:255',
            'filter.discountRate' => 'sometimes|string|max:255',
            'filter.numberOfTimesUsed' => 'sometimes|string|max:255',
            'filter.uses' => 'sometimes|string|max:255',
            'filter.status' => 'sometimes|string|max:255',
            'filter.startDate' => 'sometimes|date',
            'filter.endDate' => 'sometimes|date',
            'filter.createdAfter' => 'sometimes|date',
            'filter.createdBefore' => 'sometimes|date|after_or_equal:filter.createdAfter',
            'filter.search' => 'sometimes|string|max:255',
            'sort' => 'sometimes|string|in:couponCode,-couponCode,discountRate,-discountRate,numberOfTimesUsed,-numberOfTimesUsed,uses,-uses,status,-status,startDate,-startDate,endDate,-endDate',
        ];
    }
}
