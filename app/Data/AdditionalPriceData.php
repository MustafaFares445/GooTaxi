<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\AdditionalPrice;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Data;

final class AdditionalPriceData extends Data
{
    use HasModelAttributes;

    /** @var class-string<AdditionalPrice> */
    protected static string $model = AdditionalPrice::class;

    public function __construct(
        #[Numeric]
        public int $startPrice,
        #[Numeric]
        public int $priceOfGoingPerKm,
        #[Numeric]
        public int $returnPricePerKm,
        #[Numeric]
        public int $latitude,
        #[Numeric]
        public int $longitude
    ) {}
}
