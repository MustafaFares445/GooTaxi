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
        public ?float $startPrice = null,
        #[Numeric]
        public ?float $priceOfGoingPerKm = null,
        #[Numeric]
        public ?float $returnPricePerKm = null,
        #[Numeric]
        public ?float $latitude = null,
        #[Numeric]
        public ?float $longitude = null,
        public ?string $address = null
    ) {}
}
