<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class StatsData extends Data
{
    public function __construct(
        public int $totalBookings,
        public int $totalCustomers,
        public float $totalRevenue,
        public int $pendingBookings,
    ) {}
}
