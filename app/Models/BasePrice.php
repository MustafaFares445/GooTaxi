<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\BasePriceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property float $price_per_km
 * @property float $van_price_percentage
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class BasePrice extends Model
{
    /** @use HasFactory<BasePriceFactory> */
    use HasFactory;

    protected $fillable = [
        'price_per_km',
        'van_price_percentage',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'price_per_km' => 'decimal:2',
            'van_price_percentage' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
