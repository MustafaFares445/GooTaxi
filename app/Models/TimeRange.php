<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\TimeRangeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property array|null $days
 * @property string $from_time
 * @property string $to_time
 * @property float $price_percentage
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read BasePrice $basePrice
 */
final class TimeRange extends Model
{
    /** @use HasFactory<TimeRangeFactory> */
    use HasFactory, HasFactory;

    protected $fillable = [
        'days',
        'from_time',
        'to_time',
        'price_percentage',
        'start_price',
        'price_of_going_per_km',
        'return_price_per_km',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'days' => 'array',
            'from_time' => 'string',
            'to_time' => 'string',
            'price_percentage' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
