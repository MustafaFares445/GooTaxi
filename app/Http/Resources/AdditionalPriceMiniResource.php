<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AdditionalPriceMiniResource extends JsonResource
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
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'startPrice' => $this->start_price,
            'priceOfGoingPerKm' => $this->price_of_going_per_km,
            'returnPricePerKm' => $this->return_price_per_km,
            'address' => $this->address,
        ];
    }
}
