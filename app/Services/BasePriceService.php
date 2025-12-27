<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\BasePriceData;
use App\Models\BasePrice;
use Illuminate\Support\Facades\DB;
use Throwable;

final class BasePriceService
{
    /**
     * Update basePrice data
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function update(BasePriceData $data, BasePrice $basePrice): BasePrice
    {
        return DB::transaction(static function () use ($data, $basePrice) {
            tap($basePrice)->update($data->onlyModelAttributes());

            return $basePrice;
        });
    }
}
