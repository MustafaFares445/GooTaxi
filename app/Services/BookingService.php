<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\CalculateBookingPriceAction;
use App\Actions\GetOfferFromCouponCode;
use App\Data\BookingData;
use App\Models\Booking;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

final class BookingService
{
    /**
     * Validate booking data.
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function store(BookingData $data, ?string $couponCode = null): Booking
    {
        return DB::transaction(function () use ($data, $couponCode) {
            $data->offerId = app(GetOfferFromCouponCode::class)->handle($couponCode, $data->offerId);

            $data->finalPrice = $this->calculateFinalPrice($data);

            return Booking::create($data->onlyModelAttributes());
        });
    }

    /**
     * Update booking data.
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function update(BookingData $data, Booking $booking): Booking
    {
        return DB::transaction(function () use ($data, $booking) {
            tap($booking)->update($data->onlyModelAttributes());

            return $booking;
        });
    }

    /**
     * @throws Exception
     */
    private function calculateFinalPrice(BookingData $data): float
    {
        return $data->finalPrice ?? app(CalculateBookingPriceAction::class)->handle($data)->finalPrice;
    }
}
