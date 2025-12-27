<?php

declare(strict_types=1);

namespace App\Traits\FilterQueries;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Mrmarchone\LaravelAutoCrud\Helpers\SearchTermEscaper;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

trait UserFilterQuery
{
    public static function getQuery(): QueryBuilder
    {
        return QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::partial('phoneNumber', 'phone_number'),
                AllowedFilter::partial('isAdmin', 'is_admin'),
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                AllowedSort::field('name'),
                AllowedSort::field('phoneNumber', 'phone_number'),
            ]);
    }

    public function scopeCreatedAfter($query, $date)
    {
        if (empty($date) || ! is_string($date)) {
            return $query;
        }

        try {
            $parsedDate = Carbon::parse($date);

            return $query->where('created_at', '>=', $parsedDate);
        } catch (Exception $e) {
            return $query;
        }
    }

    public function scopeCreatedBefore($query, $date)
    {
        if (empty($date) || ! is_string($date)) {
            return $query;
        }

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
            $q->whereRaw("email LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("name LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("phone_number LIKE ? ESCAPE '!'", [$likeTerm]);
        });
    }
}
