<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\FilterQueries\AdditionalPriceFilterQuery;
use Carbon\CarbonInterface;
use Database\Factories\AdditionalPriceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property float $start_price
 * @property float $price_of_going_per_km
 * @property float $return_price_per_km
 * @property float $latitude
 * @property float $longitude
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class AdditionalPrice extends Model
{
    /** @use HasFactory<AdditionalPriceFactory> */
    use AdditionalPriceFilterQuery, HasFactory, HasFactory;

    protected $fillable = [
        'start_price',
        'price_of_going_per_km',
        'return_price_per_km',
        'latitude',
        'longitude',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'start_price' => 'decimal:2',
            'price_of_going_per_km' => 'decimal:2',
            'return_price_per_km' => 'decimal:2',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
