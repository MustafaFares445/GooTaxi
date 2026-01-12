<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Actions\CalculateBookingPriceAction;
use App\Data\BookingData;
use App\Enums\ResponseMessages;
use App\Http\Requests\BookingPriceRequest;
use App\Http\Resources\BookingPriceResource;
use App\Models\Offer;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

final readonly class BookingPriceController
{
    public function __construct(private CalculateBookingPriceAction $calculateBookingPriceAction) {}

    /**
     * Calculate booking price
     *
     * This endpoint calculates the total price for a booking based on distance, locations,
     * date, time, passengers, and optional extras. It also applies any valid coupon codes
     * if provided. Returns a detailed price breakdown including base price, additional prices,
     * and final price after discounts.
     *
     * @operation calculatePrice
     *
     * @tags API
     *
     * @throws ValidationException 422 Invalid location coordinates, distance, date, time, or coupon code
     */
    public function __invoke(BookingPriceRequest $request)
    {
        $validated = $request->validated();
        $bookingDateTime = Carbon::parse($validated['date'].' '.$validated['time']);

        $offerId = Offer::query()
            ->where('coupon_code', $request->validated('couponCode'))
            ->where('start_date', '<=', $bookingDateTime)
            ->where('end_date', '>=', $bookingDateTime)
            ->first()?->value('id');

        $data = $this->calculateBookingPriceAction->handle(
            BookingData::from(
                $validated + ['offerId' => $offerId]
            )
        );

        return BookingPriceResource::make($data)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }
}
