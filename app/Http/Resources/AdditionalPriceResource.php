<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\AdditionalPrice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AdditionalPrice
 */
final class AdditionalPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'startPrice' => $this->start_price,
            'priceOfGoingPerKm' => $this->price_of_going_per_km,
            'returnPricePerKm' => $this->return_price_per_km,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
