<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Booking;
use Illuminate\Support\Optional;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Data;

final class BookingData extends Data
{
    use HasModelAttributes;

    /** @var class-string<Booking> */
    protected static string $model = Booking::class;

    public function __construct(
        public int $userId,
        public ?int $driverId,
        #[Max(255)]
        public string $fromLocation,
        #[Max(255)]
        public string $toLocation,
        #[Date]
        public string $date,
        public string $time,
        public float $distance,
        public float|Optional $goingDistance,
        public float $returnDistance,
        public float $startingLat,
        public float $startingLng,
        public float $endingLat,
        public float $endingLng,
        public int $passengers,
        public bool $extraLargeBags,
        #[Numeric]
        public ?float $finalPrice,
        #[Max(255)]
        public string $status,
        public ?int $offerId,
        public ?string $notes,
        public bool $isCompleted = true
    ) {}
}
