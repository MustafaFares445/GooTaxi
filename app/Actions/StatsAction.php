<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\StatsData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;

final class StatsAction
{
    public function handle(): StatsData
    {
        $totalBookings = Booking::query()->count();

        $totalCustomers = User::query()->where('is_admin' , false)->count();

        $totalRevenue = (float) Booking::query()->sum('final_price');

        $pendingBookings = Booking::query()
            ->where('status', BookingStatus::Pending)
            ->count();

        return new StatsData(
            $totalBookings,
            $totalCustomers,
            $totalRevenue,
            $pendingBookings,
        );
    }
}
