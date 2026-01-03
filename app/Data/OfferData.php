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
        public ?string $couponCode = null,
        #[Numeric]
        public ?int $discountRate = null,
        public ?int $numberOfTimesUsed = null,
        public ?int $uses = null,
        #[Max(255)]
        public ?string $status = null,
        #[Date]
        public ?string $startDate = null,
        #[Date]
        public ?string $endDate = null,
        #[Exists('bookings', 'id')]
        public ?int $offerId = null
    ) {}
}
