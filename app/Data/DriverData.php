<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Driver;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

final class DriverData extends Data
{
    use HasModelAttributes;

    /** @var class-string<Driver> */
    protected static string $model = Driver::class;

    public function __construct(
        #[Max(255)]
        public ?string $name = null,
        #[Exists('bookings', 'id')]
        public ?int $driverId = null
    ) {}
}
