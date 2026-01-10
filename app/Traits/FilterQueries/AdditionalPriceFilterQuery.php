<?php

declare(strict_types=1);

namespace App\Traits\FilterQueries;

use App\Models\AdditionalPrice;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Mrmarchone\LaravelAutoCrud\Helpers\SearchTermEscaper;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

trait AdditionalPriceFilterQuery
{
    public static function getQuery(): QueryBuilder
    {
        return QueryBuilder::for(AdditionalPrice::class)
            ->allowedFilters([
                AllowedFilter::partial('startPrice', 'start_price'),
                AllowedFilter::partial('priceOfGoingPerKm', 'price_of_going_per_km'),
                AllowedFilter::partial('returnPricePerKm', 'return_price_per_km'),
                AllowedFilter::partial('address', 'address'),
                AllowedFilter::scope('createdAfter'),
                AllowedFilter::scope('createdBefore'),
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                AllowedSort::field('startPrice', 'start_price'),
                AllowedSort::field('priceOfGoingPerKm', 'price_of_going_per_km'),
                AllowedSort::field('returnPricePerKm', 'return_price_per_km'),
                AllowedSort::field('address', 'address'),
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
            $q->whereRaw("start_price LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("price_of_going_per_km LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("return_price_per_km LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("latitude LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("longitude LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("address LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("id LIKE ? ESCAPE '!'", [$likeTerm]);
        });
    }
}
