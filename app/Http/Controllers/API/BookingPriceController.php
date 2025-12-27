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

    public function __invoke(BookingPriceRequest $request)
    {
        $offerId = Offer::query()
            ->where('coupon_code', $request->validated('couponCode'))
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->first()?->value('id');

        $data = $this->calculateBookingPriceAction->handle(
            BookingData::from(
                $request->validated() + ['offerId' => $offerId]
            )
        );

        return BookingPriceResource::make($data->toArray())
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }
}
