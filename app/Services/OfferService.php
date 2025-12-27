<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\OfferData;
use App\Models\Offer;
use Illuminate\Support\Facades\DB;
use Throwable;

final class OfferService
{
    /**
     * Validate offer data.
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function store(OfferData $data): Offer
    {
        return DB::transaction(static function () use ($data) {
            $offer = Offer::create($data->onlyModelAttributes());

            return $offer;
        });
    }

    /**
     * Update offer data
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function update(OfferData $data, Offer $offer): Offer
    {
        return DB::transaction(static function () use ($data, $offer) {
            tap($offer)->update($data->onlyModelAttributes());

            return $offer;
        });
    }
}
