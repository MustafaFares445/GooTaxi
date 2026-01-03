<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BookingStatus;
use App\Traits\FilterQueries\BookingFilterQuery;
use Carbon\CarbonInterface;
use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property int $user_id
 * @property int|null $driver_id
 * @property string|null $from_location
 * @property array|null $to_location
 * @property CarbonInterface $date
 * @property string $time
 * @property float|null $distance
 * @property float|null $going_distance
 * @property float|null $return_distance
 * @property int $passengers
 * @property bool $extra_large_bags
 * @property float $final_price
 * @property BookingStatus $status
 * @property int|null $offer_id
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read User $user
 * @property-read Driver|null $driver
 * @property-read Offer|null $offer
 */
final class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use BookingFilterQuery, HasFactory, HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',
        'from_location',
        'to_location',
        'date',
        'time',
        'distance',
        'passengers',
        'extra_large_bags',
        'final_price',
        'status',
        'offer_id',
        'notes',
        'is_completed',
        'going_distance',
        'return_distance',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'driver_id' => 'integer',
            'from_location' => 'string',
            'to_location' => 'array',
            'date' => 'date',
            'time' => 'string',
            'distance' => 'decimal:2',
            'going_distance' => 'decimal:2',
            'return_distance' => 'decimal:2',
            'passengers' => 'integer',
            'extra_large_bags' => 'boolean',
            'final_price' => 'decimal:2',
            'status' => BookingStatus::class,
            'offer_id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'is_completed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
}
