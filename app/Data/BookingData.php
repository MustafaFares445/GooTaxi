<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Booking;
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
        public ?int $userId = null,
        public ?int $driverId = null,
        #[Max(255)]
        public ?string $fromLocation = null,
        public ?array $toLocation = null,
        #[Date]
        public ?string $date = null,
        public ?string $time = null,
        public ?float $distance = null,
        public ?float $goingDistance = null,
        public ?float $returnDistance = null,
        public ?float $startingLat = null,
        public ?float $startingLng = null,
        public ?float $endingLat = null,
        public ?float $endingLng = null,
        public ?int $passengers = null,
        public ?bool $extraLargeBags = null,
        #[Numeric]
        public ?float $finalPrice = null,
        #[Max(255)]
        public ?string $status = null,
        public ?int $offerId = null,
        public ?string $notes = null,
        public ?bool $isCompleted = null
    ) {}
}
