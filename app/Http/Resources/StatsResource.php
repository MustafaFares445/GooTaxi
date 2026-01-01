<?php

namespace App\Http\Resources;

use App\Data\StatsData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin StatsData*/
class StatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'totalBookings' => $this->totalBookings,
            'totalCustomers' => $this->totalCustomers,
            'totalRevenue' => $this->totalRevenue,
            'pendingBookings' => $this->pendingBookings,
        ];
    }
}
