<?php

declare(strict_types=1);

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Upcoming = 'upcoming';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
    case Rejected = 'rejected';
}
