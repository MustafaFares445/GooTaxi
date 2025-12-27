<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\DriverData;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;
use Throwable;

final class DriverService
{
    /**
     * Validate driver data.
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function store(DriverData $data): Driver
    {
        return DB::transaction(static function () use ($data) {
            $driver = Driver::create($data->onlyModelAttributes());

            return $driver;
        });
    }

    /**
     * Update driver data
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function update(DriverData $data, Driver $driver): Driver
    {
        return DB::transaction(static function () use ($data, $driver) {
            tap($driver)->update($data->onlyModelAttributes());

            return $driver;
        });
    }
}
