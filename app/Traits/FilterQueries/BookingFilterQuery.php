<?php

declare(strict_types=1);

namespace App\Traits\FilterQueries;

use App\Models\Booking;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Mrmarchone\LaravelAutoCrud\Helpers\SearchTermEscaper;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

trait BookingFilterQuery
{
    public static function getQuery(): QueryBuilder
    {
        return QueryBuilder::for(Booking::class)
            ->allowedFilters([
                AllowedFilter::partial('userId', 'user_id'),
                AllowedFilter::partial('driverId', 'driver_id'),
                AllowedFilter::partial('fromLocation', 'from_location'),
                AllowedFilter::partial('toLocation', 'to_location'),
                AllowedFilter::partial('date'),
                AllowedFilter::partial('time'),
                AllowedFilter::partial('distance'),
                AllowedFilter::partial('passengers'),
                AllowedFilter::partial('extraLargeBags', 'extra_large_bags'),
                AllowedFilter::partial('finalPrice', 'final_price'),
                AllowedFilter::partial('status'),
                AllowedFilter::partial('offerId', 'offer_id'),
                AllowedFilter::partial('isCompleted', 'is_completed'),
                AllowedFilter::scope('createdAfter'),
                AllowedFilter::scope('createdBefore'),
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                AllowedSort::field('userId', 'user_id'),
                AllowedSort::field('driverId', 'driver_id'),
                AllowedSort::field('fromLocation', 'from_location'),
                AllowedSort::field('toLocation', 'to_location'),
                AllowedSort::field('date'),
                AllowedSort::field('time'),
                AllowedSort::field('distance'),
                AllowedSort::field('passengers'),
                AllowedSort::field('extraLargeBags', 'extra_large_bags'),
                AllowedSort::field('finalPrice', 'final_price'),
                AllowedSort::field('status'),
                AllowedSort::field('offerId', 'offer_id'),
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

    public function scopeSearch($query, $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        $likeTerm = SearchTermEscaper::escape($term);

        return $query->where(function (Builder $q) use ($likeTerm) {
            $q->whereRaw("from_location LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("to_location LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("status LIKE ? ESCAPE '!'", [$likeTerm]);
        });
    }
}
