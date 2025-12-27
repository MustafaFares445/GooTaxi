<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\TimeRange;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Json;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Data;

final class TimeRangeData extends Data
{
    use HasModelAttributes;

    /** @var class-string<TimeRange> */
    protected static string $model = TimeRange::class;

    public function __construct(
        #[Json]
        public ?array $days,
        public string $fromTime,
        public string $toTime,
        #[Numeric]
        public int $pricePercentage
    ) {}
}
