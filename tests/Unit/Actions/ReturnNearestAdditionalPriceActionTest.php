<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\ReturnNearestAdditionalPriceAction;
use App\Models\AdditionalPrice;

it('returns nearest additional price by coordinates', function () {
    // Create multiple additional prices at different locations
    $nearbyPrice = AdditionalPrice::factory()->create([
        'latitude' => 40.7128,
        'longitude' => -74.0060,
        'start_price' => 5,
        'price_of_going_per_km' => 2,
        'return_price_per_km' => 1.5,
    ]);

    $farAwayPrice = AdditionalPrice::factory()->create([
        'latitude' => 51.5074,
        'longitude' => -0.1278,
        'start_price' => 3,
        'price_of_going_per_km' => 1,
        'return_price_per_km' => 0.8,
    ]);

    $action = app(ReturnNearestAdditionalPriceAction::class);

    // Query with coordinates close to the nearby price
    $result = $action->handle(40.7125, -74.0058);

    expect($result)->not->toBeNull();
    expect($result->id)->toBe($nearbyPrice->id);
    expect((float) $result->start_price)->toBe(5.0);
});

it('returns the only additional price when only one exists', function () {
    $additionalPrice = AdditionalPrice::factory()->create([
        'latitude' => 40.7128,
        'longitude' => -74.0060,
        'start_price' => 4,
        'price_of_going_per_km' => 1.5,
        'return_price_per_km' => 1,
    ]);

    $action = app(ReturnNearestAdditionalPriceAction::class);
    $result = $action->handle(34.0522, -118.2437);

    expect($result)->not->toBeNull();
    expect($result->id)->toBe($additionalPrice->id);
});

it('returns null when no additional prices exist', function () {
    $action = app(ReturnNearestAdditionalPriceAction::class);
    $result = $action->handle(40.7128, -74.0060);

    expect($result)->toBeNull();
});

it('correctly calculates haversine distance', function () {
    // Create prices at known locations
    AdditionalPrice::factory()->create([
        'latitude' => 0,
        'longitude' => 0,
        'start_price' => 1,
        'price_of_going_per_km' => 1,
        'return_price_per_km' => 1,
    ]);

    AdditionalPrice::factory()->create([
        'latitude' => 1,
        'longitude' => 1,
        'start_price' => 2,
        'price_of_going_per_km' => 2,
        'return_price_per_km' => 2,
    ]);

    $action = app(ReturnNearestAdditionalPriceAction::class);

    // Query close to first location (0, 0)
    $result = $action->handle(0.01, 0.01);

    expect((float) $result->start_price)->toBe(1.0);
});
