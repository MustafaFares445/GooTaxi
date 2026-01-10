<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AdditionalPriceFilterRequest extends FormRequest
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
            'filter.startPrice' => 'sometimes|string|max:255',
            'filter.priceOfGoingPerKm' => 'sometimes|string|max:255',
            'filter.returnPricePerKm' => 'sometimes|string|max:255',
            'filter.latitude' => 'sometimes|numeric:|max:255',
            'filter.longitude' => 'sometimes|numeric|max:255',
            'filter.address' => 'sometimes|string|max:255',
            'filter.createdAfter' => 'sometimes|date',
            'filter.createdBefore' => 'sometimes|date|after_or_equal:filter.createdAfter',
            'filter.search' => 'sometimes|string|max:255',
            'sort' => 'sometimes|string|in:startPrice,-startPrice,priceOfGoingPerKm,-priceOfGoingPerKm,returnPricePerKm,-returnPricePerKm,latitude,-latitude,longitude,-longitude,address,-address',
        ];
    }
}
