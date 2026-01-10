<?php

declare(strict_types=1);

namespace Tests\Services;

use App\Actions\CalculateBookingPriceAction;
use App\Data\BookingData;
use App\Enums\BookingStatus;
use App\Models\AdditionalPrice;
use App\Models\BasePrice;
use App\Models\Driver;
use App\Models\Offer;
use App\Models\TimeRange;
use App\Models\User;
use Throwable;

/**
 * @throws Throwable
 */
it('calculate final price with time range', function () {
    $basePrice = BasePrice::factory()->create([
        'price_per_km' => 10,
        'van_price_percentage' => 20,
    ]);

    $bookingDate = '2025-01-01';
    $bookingDay = date('D', strtotime($bookingDate));

    TimeRange::factory()->create([
        'days' => [$bookingDay],
        'from_time' => '00:00:00',
        'to_time' => '23:59:59',
        'start_price' => 5,
        'price_of_going_per_km' => 2,
        'return_price_per_km' => 1.5,
        'price_percentage' => 10,
    ]);

    $offer = Offer::factory()->create([
        'discount_rate' => 10,
        'start_date' => '2024-12-01',
        'end_date' => '2025-12-31',
    ]);

    $payload = [
        'userId' => User::factory()->create()->id,
        'driverId' => Driver::factory()->create()->id,
        'fromLocation' => 'Sample fromLocation',
        'toLocation' => ['Sample toLocation 1', 'Sample toLocation 2'],
        'date' => $bookingDate,
        'time' => '10:00:00',
        'distance' => 10.5,
        'goingDistance' => 5,
        'returnDistance' => 3,
        'startingLat' => 40.7128,
        'startingLng' => -74.0060,
        'endingLat' => 34.0522,
        'endingLng' => -118.2437,
        'passengers' => 2,
        'extraLargeBags' => false,
        'status' => BookingStatus::Completed->value,
        'offerId' => $offer->id,
    ];

    $priceData = app(CalculateBookingPriceAction::class)->handle(BookingData::from($payload));

    expect($priceData->finalPrice)->toBeFloat();
    expect($priceData->pricePerKm)->toBe(10.0);
    expect($priceData->startPrice)->toBe(5.0);
});

/**
 * @throws Throwable
 */
it('calculate final price with additional price fallback when no time range', function () {
    $basePrice = BasePrice::factory()->create([
        'price_per_km' => 10,
        'van_price_percentage' => 20,
    ]);

    $startingAdditionalPrice = AdditionalPrice::factory()->create([
        'latitude' => 40.7128,
        'longitude' => -74.0060,
        'start_price' => 3,
        'price_of_going_per_km' => 1.5,
        'return_price_per_km' => 1,
    ]);

    $endingAdditionalPrice = AdditionalPrice::factory()->create([
        'latitude' => 34.0522,
        'longitude' => -118.2437,
        'start_price' => 4,
        'price_of_going_per_km' => 1.8,
        'return_price_per_km' => 1.2,
    ]);

    $payload = [
        'userId' => User::factory()->create()->id,
        'driverId' => Driver::factory()->create()->id,
        'fromLocation' => 'Sample fromLocation',
        'toLocation' => ['Sample toLocation 1', 'Sample toLocation 2'],
        'date' => '2025-01-01',
        'time' => '10:00:00',
        'distance' => 10.5,
        'goingDistance' => 5,
        'returnDistance' => 3,
        'startingLat' => 40.7128,
        'startingLng' => -74.0060,
        'endingLat' => 34.0522,
        'endingLng' => -118.2437,
        'passengers' => 2,
        'extraLargeBags' => false,
        'status' => BookingStatus::Completed->value,
        'offerId' => null,
    ];

    $priceData = app(CalculateBookingPriceAction::class)->handle(BookingData::from($payload));

    expect($priceData->finalPrice)->toBeFloat();
    expect($priceData->priceOfGoingPerKm)->toBe(1.5);
    expect($priceData->returnPricePerKm)->toBe(1.2);
    expect($priceData->startPrice)->toBe(3.0);
});

/**
 * @throws Throwable
 */
it('calculate final price with van surcharge', function () {
    BasePrice::factory()->create([
        'price_per_km' => 10,
        'van_price_percentage' => 20,
    ]);

    $bookingDate = '2025-01-01';
    $bookingDay = date('D', strtotime($bookingDate));

    TimeRange::factory()->create([
        'days' => [$bookingDay],
        'from_time' => '00:00:00',
        'to_time' => '23:59:59',
        'start_price' => 0,
        'price_of_going_per_km' => 0,
        'return_price_per_km' => 0,
        'price_percentage' => 0,
    ]);

    $payload = [
        'userId' => User::factory()->create()->id,
        'driverId' => Driver::factory()->create()->id,
        'fromLocation' => 'Sample fromLocation',
        'toLocation' => ['Sample toLocation 1', 'Sample toLocation 2'],
        'date' => $bookingDate,
        'time' => '10:00:00',
        'distance' => 10,
        'goingDistance' => 0,
        'returnDistance' => 0,
        'startingLat' => 40.7128,
        'startingLng' => -74.0060,
        'endingLat' => 34.0522,
        'endingLng' => -118.2437,
        'passengers' => 2,
        'extraLargeBags' => true,
        'status' => BookingStatus::Completed->value,
        'offerId' => null,
    ];

    $priceData = app(CalculateBookingPriceAction::class)->handle(BookingData::from($payload));

    // 10 km * 10 per km = 100
    // With 20% van surcharge = 120
    expect($priceData->finalPrice)->toBe(120.0);
});
