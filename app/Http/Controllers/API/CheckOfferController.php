<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Actions\GetOfferFromCouponCode;
use App\Enums\ResponseMessages;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use Illuminate\Http\Request;

final class CheckOfferController
{
    public function __construct(private GetOfferFromCouponCode $getOfferFromCouponCode) {}

    /**
     * @tags API
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
