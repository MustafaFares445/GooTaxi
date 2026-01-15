<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Actions\CalculateBookingPriceAction;
use App\Actions\GetOfferFromCouponCode;
use App\Data\BookingData;
use App\Enums\ResponseMessages;
use App\Http\Requests\BookingPriceRequest;
use App\Http\Resources\BookingPriceResource;
use Illuminate\Validation\ValidationException;

final readonly class BookingPriceController
{
    public function __construct(
        private CalculateBookingPriceAction $calculateBookingPriceAction,
        private GetOfferFromCouponCode $getOfferFromCouponCode
    ) {}

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

        $offerId = $this->getOfferFromCouponCode->handle(
            $request->validated('couponCode'),
            null,
            false
        );

        $data = $this->calculateBookingPriceAction->handle(
            BookingData::from(
                $validated + ['offerId' => $offerId]
            )
        );

        return BookingPriceResource::make($data)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }
}
