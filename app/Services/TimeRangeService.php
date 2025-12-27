<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\TimeRangeData;
use App\Models\TimeRange;
use Illuminate\Support\Facades\DB;
use Throwable;

final class TimeRangeService
{
    /**
     * Validate timeRange data.
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function store(TimeRangeData $data): TimeRange
    {
        return DB::transaction(static function () use ($data) {
            $timeRange = TimeRange::create($data->onlyModelAttributes());

            return $timeRange;
        });
    }

    /**
     * Update timeRange data
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function update(TimeRangeData $data, TimeRange $timeRange): TimeRange
    {
        return DB::transaction(static function () use ($data, $timeRange) {
            tap($timeRange)->update($data->onlyModelAttributes());

            return $timeRange;
        });
    }
}
