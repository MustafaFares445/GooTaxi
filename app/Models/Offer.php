<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OfferStatus;
use App\Traits\FilterQueries\OfferFilterQuery;
use Carbon\CarbonInterface;
use Database\Factories\OfferFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property string $coupon_code
 * @property float $discount_rate
 * @property int $number_of_times_used
 * @property int $uses
 * @property OfferStatus $status
 * @property CarbonInterface $start_date
 * @property CarbonInterface $end_date
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class Offer extends Model
{
    /** @use HasFactory<OfferFactory> */
    use HasFactory, OfferFilterQuery;

    protected $fillable = [
        'coupon_code',
        'discount_rate',
        'number_of_times_used',
        'uses',
        'status',
        'start_date',
        'end_date',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'coupon_code' => 'string',
            'discount_rate' => 'decimal:2',
            'number_of_times_used' => 'integer',
            'uses' => 'integer',
            'status' => OfferStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
