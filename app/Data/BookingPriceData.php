<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class BookingPriceData extends Data
{
    public function __construct(
        public float $finalPrice,
        public float $pricePerKm,
        public ?float $vanPricePercentage,
        public ?float $startPrice,
        public ?float $priceOfGoingPerKm,
        public ?float $goingDistance,
        public ?float $returnDistance,
        public ?float $returnPricePerKm,
        public ?float $offerDiscountRate,
    ) {}
}
