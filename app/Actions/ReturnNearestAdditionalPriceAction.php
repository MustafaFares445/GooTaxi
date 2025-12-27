<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\AdditionalPrice;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

final class ReturnNearestAdditionalPriceAction
{
    public function handle(float $latitude, float $longitude): ?AdditionalPrice
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            return $this->handleSqlite($latitude, $longitude);
        }

        return AdditionalPrice::query()
            ->select('*', $this->getDistanceExpression($latitude, $longitude))
            ->orderBy('distance')
            ->first();
    }

    private function handleSqlite(float $latitude, float $longitude): ?AdditionalPrice
    {
        $prices = AdditionalPrice::query()->get();

        if ($prices->isEmpty()) {
            return null;
        }

        return $prices->map(function ($price) use ($latitude, $longitude) {
            $price->distance = $this->calculateHaversineDistance(
                $latitude,
                $longitude,
                (float) $price->latitude,
                (float) $price->longitude
            );

            return $price;
        })->sortBy('distance')->first();
    }

    private function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * asin(sqrt($a));

        return round($earthRadius * $c, 2);
    }

    private function getDistanceExpression(float $userLat, float $userLon): Expression
    {
        return new Expression(
            "ROUND((6371 * 2 * ASIN(SQRT(
                POW(SIN(RADIANS(({$userLat} - latitude) / 2)), 2) +
                COS(RADIANS(latitude)) *
                COS(RADIANS({$userLat})) *
                POW(SIN(RADIANS(({$userLon} - longitude) / 2)), 2)
            ))), 2) as distance"
        );
    }
}
