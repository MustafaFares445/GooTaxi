<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\TimeRange;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TimeRange
 */
final class TimeRangeResource extends JsonResource
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
            'days' => $this->days,
            'fromTime' => $this->from_time,
            'toTime' => $this->to_time,
            'pricePercentage' => $this->price_percentage,
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
