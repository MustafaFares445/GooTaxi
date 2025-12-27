<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\BasePrice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin BasePrice
 */
final class BasePriceResource extends JsonResource
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
            'pricePerKm' => $this->price_per_km,
            'vanPricePercentage' => $this->van_price_percentage,
            'timeRanges' => TimeRangeResource::collection($this->whenLoaded('timeRanges')),
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
