<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Actions\GetOfferFromCouponCode;
use App\Enums\ResponseMessages;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class CheckOfferController
{
    public function __construct(private GetOfferFromCouponCode $getOfferFromCouponCode) {}

    /**
     * Check if coupon code is valid and get offer details
     *
     * This endpoint validates a coupon code and returns the associated offer details if valid.
     * Returns 204 No Content if the coupon code is invalid or the offer is not active.
     *
     * @operation checkOffer
     *
     * @tags API
     *
     * @throws ValidationException 422 Invalid coupon code format
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'couponCode' => 'required|string|min:1|max:100',
        ]);

        $offerId = $this->getOfferFromCouponCode->handle(
            $request->input('couponCode'),
        );

        if (! $offerId) {
            return response()->noContent();
        }

        return OfferResource::make(Offer::query()->find($offerId))
            ->additional(['message' => __(ResponseMessages::RETRIEVED->message())]);
    }
}
