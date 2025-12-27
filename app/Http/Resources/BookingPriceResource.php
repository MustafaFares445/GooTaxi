<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Data\BookingPriceData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**@mixin BookingPriceData*/
final class BookingPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'finalPrice' => $this->finalPrice,
            'pricePerKm' => $this->pricePerKm,
            'vanPricePercentage' => $this->vanPricePercentage,
            'startPrice' => $this->startPrice,
            'priceOfGoingPerKm' => $this->priceOfGoingPerKm,
            'goingDistance' => $this->goingDistance,
            'returnDistance' => $this->returnDistance,
            'returnPricePerKm' => $this->returnPricePerKm,
            'offerDiscountRate' => $this->offerDiscountRate,
        ];
    }
}
