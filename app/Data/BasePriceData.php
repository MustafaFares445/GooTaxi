<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\BasePrice;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Data;

final class BasePriceData extends Data
{
    use HasModelAttributes;

    /** @var class-string<BasePrice> */
    protected static string $model = BasePrice::class;

    public function __construct(
        #[Numeric]
        public int $pricePerKm,
        #[Numeric]
        public int $vanPricePercentage,
        #[Exists('time_ranges', 'id')]
        public ?int $basePriceId
    ) {}
}
