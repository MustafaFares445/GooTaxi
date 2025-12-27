<?php

declare(strict_types=1);

namespace App\Traits\FilterQueries;

use App\Models\Driver;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Mrmarchone\LaravelAutoCrud\Helpers\SearchTermEscaper;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

trait DriverFilterQuery
{
    public static function getQuery(): QueryBuilder
    {
        return QueryBuilder::for(Driver::class)
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::scope('createdAfter'),
                AllowedFilter::scope('createdBefore'),
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                AllowedSort::field('name'),
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
            $q->whereRaw("name LIKE ? ESCAPE '!'", [$likeTerm]);
        });
    }
}
