<?php

declare(strict_types=1);

namespace App\Traits\FilterQueries;

use App\Models\BasePrice;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Mrmarchone\LaravelAutoCrud\Helpers\SearchTermEscaper;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

trait BasePriceFilterQuery
{
    public static function getQuery(): QueryBuilder
    {
        return QueryBuilder::for(BasePrice::class)
            ->allowedFilters([
                AllowedFilter::partial('pricePerKm', 'price_per_km'),
                AllowedFilter::partial('vanPricePercentage', 'van_price_percentage'),
                AllowedFilter::scope('createdAfter'),
                AllowedFilter::scope('createdBefore'),
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                AllowedSort::field('pricePerKm', 'price_per_km'),
                AllowedSort::field('vanPricePercentage', 'van_price_percentage'),
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
            $q->whereRaw("price_per_km LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("van_price_percentage LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("id LIKE ? ESCAPE '!'", [$likeTerm]);
        });
    }
}
