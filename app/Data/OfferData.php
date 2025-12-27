<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Offer;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

final class OfferData extends Data
{
    use HasModelAttributes;

    /** @var class-string<Offer> */
    protected static string $model = Offer::class;

    public function __construct(
        #[Max(255), Unique('offers', 'coupon_code')]
        public string $couponCode,
        #[Numeric]
        public int $discountRate,
        public int $numberOfTimesUsed,
        public int $uses,
        #[Max(255)]
        public string $status,
        #[Date]
        public string $startDate,
        #[Date]
        public string $endDate,
        #[Exists('bookings', 'id')]
        public ?int $offerId
    ) {}
}
