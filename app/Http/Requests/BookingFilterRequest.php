<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class BookingFilterRequest extends FormRequest
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
            'filter.driverId' => 'sometimes|string|max:255',
            'filter.fromLocation' => 'sometimes|string|max:255',
            'filter.toLocation' => 'sometimes|array',
            'filter.date' => 'sometimes|date',
            'filter.time' => 'sometimes|string|max:255',
            'filter.distance' => 'sometimes|string|max:255',
            'filter.goingDistance' => 'sometimes|string|max:255',
            'filter.returnDistance' => 'sometimes|string|max:255',
            'filter.passengers' => 'sometimes|string|max:255',
            'filter.extraLargeBags' => 'sometimes|string|max:255',
            'filter.finalPrice' => 'sometimes|string|max:255',
            'filter.status' => 'sometimes|string|max:255',
            'filter.offerId' => 'sometimes|string|max:255',
            'filter.isCompleted' => 'sometimes|boolean',
            'filter.createdAfter' => 'sometimes|date',
            'filter.createdBefore' => 'sometimes|date|after_or_equal:filter.createdAfter',
            'filter.search' => 'sometimes|string|max:255',
            'filter.pendingAndUpcoming' => 'sometimes|boolean',
            'sort' => 'sometimes|string|in:userId,-userId,driverId,-driverId,fromLocation,-fromLocation,toLocation,-toLocation,date,-date,time,-time,distance,-distance,passengers,-passengers,extraLargeBags,-extraLargeBags,finalPrice,-finalPrice,status,-status,offerId,-offerId',
        ];
    }
}
