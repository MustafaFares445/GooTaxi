<?php

declare(strict_types=1);

namespace App\Traits\FilterQueries;

use App\Models\TimeRange;
use Carbon\Carbon;
use Exception;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

trait TimeRangeFilterQuery
{
    public static function getQuery(): QueryBuilder
    {
        return QueryBuilder::for(TimeRange::class)
            ->allowedFilters([
                AllowedFilter::partial('days'),
                AllowedFilter::partial('fromTime', 'from_time'),
                AllowedFilter::partial('toTime', 'to_time'),
                AllowedFilter::partial('pricePercentage', 'price_percentage'),
                AllowedFilter::scope('createdAfter'),
                AllowedFilter::scope('createdBefore'),
            ])
            ->allowedSorts([
                AllowedSort::field('days'),
                AllowedSort::field('fromTime', 'from_time'),
                AllowedSort::field('toTime', 'to_time'),
                AllowedSort::field('pricePercentage', 'price_percentage'),
            ])
            ->defaultSort('-created_at');
    }

    public function scopeCreatedAfter($query, $date)
    {
        try {
            $parsedDate = Carbon::parse($date);

            return $query->where('created_at', '>=', $parsedDate);
        } catch (Exception $e) {
            return $query;
        }
    }

    public function scopeCreatedBefore($query, $date)
    {
        try {
            $parsedDate = Carbon::parse($date);

            return $query->where('created_at', '<=', $parsedDate);
        } catch (Exception $e) {
            return $query;
        }
    }
}
