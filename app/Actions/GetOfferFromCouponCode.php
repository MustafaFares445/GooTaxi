<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OfferStatus;
use App\Models\Offer;

final class GetOfferFromCouponCode
{
    public function handle(?string $couponsCode = null, ?int $offerId = null, bool $changeStatus = true): ?int
    {
        if ($couponsCode) {
            $offer = Offer::query()
                ->where('coupon_code', $couponsCode)
                ->where('status', OfferStatus::Active)
                ->whereColumn('uses', '<', 'number_of_times_used')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->first();

            if ($changeStatus) {
                $offer?->increment('uses');

                if ($offer?->uses >= $offer?->number_of_times_used) {
                    $offer?->update([
                        'status' => OfferStatus::Inactive,
                    ]);
                }
            }

            return $offer?->id;
        }

        return $offerId;
    }
}
