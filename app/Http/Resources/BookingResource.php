<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Booking
 */
final class BookingResource extends JsonResource
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
            'userId' => $this->user_id,
            'driverId' => $this->driver_id,
            'fromLocation' => $this->from_location,
            'toLocation' => $this->to_location,
            'date' => $this->date,
            'time' => $this->time,
            'distance' => $this->distance,
            'passengers' => $this->passengers,
            'extraLargeBags' => $this->extra_large_bags,
            'goingDistance' => $this->going_distance,
            'returnDistance' => $this->return_distance,
            'startingLat' => $this->starting_lat,
            'startingLng' => $this->starting_lng,
            'endingLat' => $this->ending_lat,
            'endingLng' => $this->ending_lng,
            'finalPrice' => $this->final_price,
            'status' => $this->status,
            'offerId' => $this->offer_id,
            'isCompleted' => $this->is_completed,
            'user' => UserResource::make($this->whenLoaded('user')),
            'driver' => DriverResource::make($this->whenLoaded('driver')),
            'offer' => OfferResource::make($this->whenLoaded('offer')),
            'createdAt' => $this->created_at?->toDateTimeString(),
            'updatedAt' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
