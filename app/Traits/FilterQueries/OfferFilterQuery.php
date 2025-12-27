<?php

declare(strict_types=1);

namespace App\Traits\FilterQueries;

use App\Models\Offer;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Mrmarchone\LaravelAutoCrud\Helpers\SearchTermEscaper;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

trait OfferFilterQuery
{
    public static function getQuery(): QueryBuilder
    {
        return QueryBuilder::for(Offer::class)
            ->allowedFilters([
                AllowedFilter::partial('couponCode', 'coupon_code'),
                AllowedFilter::partial('discountRate', 'discount_rate'),
                AllowedFilter::partial('numberOfTimesUsed', 'number_of_times_used'),
                AllowedFilter::partial('uses'),
                AllowedFilter::partial('status'),
                AllowedFilter::partial('startDate', 'start_date'),
                AllowedFilter::partial('endDate', 'end_date'),
                AllowedFilter::scope('createdAfter'),
                AllowedFilter::scope('createdBefore'),
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                AllowedSort::field('couponCode', 'coupon_code'),
                AllowedSort::field('discountRate', 'discount_rate'),
                AllowedSort::field('numberOfTimesUsed', 'number_of_times_used'),
                AllowedSort::field('uses'),
                AllowedSort::field('status'),
                AllowedSort::field('startDate', 'start_date'),
                AllowedSort::field('endDate', 'end_date'),
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
            $q->whereRaw("coupon_code LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("status LIKE ? ESCAPE '!'", [$likeTerm]);
        });
    }
}
