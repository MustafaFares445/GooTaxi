<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\AdditionalPriceData;
use App\Models\AdditionalPrice;
use Illuminate\Support\Facades\DB;
use Throwable;

final class AdditionalPriceService
{
    /**
     * Validate additionalPrice data.
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function store(AdditionalPriceData $data): AdditionalPrice
    {
        return DB::transaction(static function () use ($data) {
            $additionalPrice = AdditionalPrice::create($data->onlyModelAttributes());

            return $additionalPrice;
        });
    }

    /**
     * Update additionalPrice data
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function update(AdditionalPriceData $data, AdditionalPrice $additionalPrice): AdditionalPrice
    {
        return DB::transaction(static function () use ($data, $additionalPrice) {
            tap($additionalPrice)->update($data->onlyModelAttributes());

            return $additionalPrice;
        });
    }
}
