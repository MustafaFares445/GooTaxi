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

final readonly class BookingPriceController
{
    public function __construct(private CalculateBookingPriceAction $calculateBookingPriceAction) {}

    /**
     * @tags API
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
