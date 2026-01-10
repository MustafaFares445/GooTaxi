<?php

declare(strict_types=1);

use App\Enums\OfferStatus;
use App\Enums\ResponseMessages;
use App\Models\AdditionalPrice;
use App\Models\BasePrice;
use App\Models\Offer;
use App\Models\TimeRange;

beforeEach(function () {
    BasePrice::factory()->create([
        'price_per_km' => 10.0,
        'van_price_percentage' => 20.0,
    ]);
});

it('calculates booking price successfully with required parameters', function () {
    $response = $this->getJson('/api/booking/price?distance=100&goingDistance=50&returnDistance=50&startingLat=48.8588897&startingLng=2.3200410&endingLat=45.7578137&endingLng=4.8320114&extraLargeBags=0');

    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message())
        ->assertJsonStructure([
            'data' => [
                'finalPrice',
                'pricePerKm',
                'vanPricePercentage',
                'startPrice',
                'priceOfGoingPerKm',
                'goingDistance',
                'returnDistance',
                'returnPricePerKm',
                'offerDiscountRate',
            ],
        ]);

    expect($response->json('data.finalPrice'))->toBeNumeric();
    expect($response->json('data.pricePerKm'))->toBe(10);
});

it('calculates booking price with valid offer coupon code', function () {
    $activeOffer = Offer::factory()->create([
        'coupon_code' => 'TEST2024',
        'discount_rate' => 15.0,
        'status' => OfferStatus::Active,
        'start_date' => now()->yesterday(),
        'end_date' => now()->tomorrow(),
    ]);

    $date = now()->format('Y-m-d');
    $time = now()->format('H:i:s');
    $response = $this->getJson("/api/booking/price?distance=100&goingDistance=50&returnDistance=50&startingLat=48.8588897&startingLng=2.3200410&endingLat=45.7578137&endingLng=4.8320114&extraLargeBags=0&couponCode=TEST2024&date={$date}&time={$time}");

    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message())
        ->assertJsonPath('data.offerDiscountRate', 15);
});

it('calculates booking price with van surcharge when extraLargeBags is true', function () {
    $response = $this->getJson('/api/booking/price?distance=100&goingDistance=50&returnDistance=50&startingLat=48.8588897&startingLng=2.3200410&endingLat=45.7578137&endingLng=4.8320114&extraLargeBags=1');

    $response->assertOk();
    $finalPrice = $response->json('data.finalPrice');
    $pricePerKm = $response->json('data.pricePerKm');
    $vanPricePercentage = $response->json('data.vanPricePercentage');

    expect($finalPrice)->toBeNumeric();
    expect($vanPricePercentage)->toBe(20);
    expect($finalPrice)->toBeGreaterThan($pricePerKm * 100);
});

it('calculates booking price with time range adjustments', function () {
    TimeRange::factory()->create([
        'days' => [now()->format('D')],
        'from_time' => '00:00:00',
        'to_time' => '23:59:59',
        'start_price' => 5.0,
        'price_of_going_per_km' => 2.0,
        'return_price_per_km' => 1.5,
        'price_percentage' => 10.0,
    ]);

    $date = now()->format('Y-m-d');
    $time = now()->format('H:i:s');
    $response = $this->getJson("/api/booking/price?distance=100&goingDistance=50&returnDistance=50&startingLat=48.8588897&startingLng=2.3200410&endingLat=45.7578137&endingLng=4.8320114&extraLargeBags=0&date={$date}&time={$time}");

    $response->assertOk();
    expect($response->json('data.startPrice'))->toBe(5);
    expect($response->json('data.priceOfGoingPerKm'))->toBe(2);
    expect($response->json('data.returnPricePerKm'))->toBe(1.5);
});

it('calculates booking price with additional prices from coordinates when no time range', function () {
    AdditionalPrice::factory()->create([
        'latitude' => 48.8588897,
        'longitude' => 2.3200410,
        'start_price' => 3.0,
        'price_of_going_per_km' => 1.5,
        'return_price_per_km' => 1.0,
    ]);

    AdditionalPrice::factory()->create([
        'latitude' => 45.7578137,
        'longitude' => 4.8320114,
        'start_price' => 4.0,
        'price_of_going_per_km' => 1.8,
        'return_price_per_km' => 1.2,
    ]);

    $response = $this->getJson('/api/booking/price?distance=100&goingDistance=50&returnDistance=50&startingLat=48.8588897&startingLng=2.3200410&endingLat=45.7578137&endingLng=4.8320114&extraLargeBags=0');

    $response->assertOk();
    expect($response->json('data.startPrice'))->toBe(3);
    expect($response->json('data.priceOfGoingPerKm'))->toBe(1.5);
    expect($response->json('data.returnPricePerKm'))->toBe(1.2);
});

it('validates required fields', function () {
    $response = $this->getJson('/api/booking/price');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors([
            'distance',
            'goingDistance',
            'returnDistance',
            'startingLat',
            'startingLng',
            'endingLat',
            'endingLng',
        ]);
});

it('validates numeric fields', function () {
    $response = $this->getJson('/api/booking/price?distance=invalid&goingDistance=invalid&returnDistance=invalid&startingLat=invalid&startingLng=invalid&endingLat=invalid&endingLng=invalid');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors([
            'distance',
            'goingDistance',
            'returnDistance',
            'startingLat',
            'startingLng',
            'endingLat',
            'endingLng',
        ]);
});

it('ignores invalid or expired offer coupon code', function () {
    $expiredOffer = Offer::factory()->create([
        'coupon_code' => 'EXPIRED2024',
        'discount_rate' => 15.0,
        'status' => OfferStatus::Active,
        'start_date' => now()->subDays(10),
        'end_date' => now()->subDays(5),
    ]);

    $date = now()->format('Y-m-d');
    $time = now()->format('H:i:s');
    $response = $this->getJson("/api/booking/price?distance=100&goingDistance=50&returnDistance=50&startingLat=48.8588897&startingLng=2.3200410&endingLat=45.7578137&endingLng=4.8320114&extraLargeBags=0&couponCode=EXPIRED2024&date={$date}&time={$time}");

    $response->assertOk();
    expect($response->json('data.offerDiscountRate'))->toBe(0);
});

it('ignores inactive offer coupon code', function () {
    $inactiveOffer = Offer::factory()->create([
        'coupon_code' => 'INACTIVE2024',
        'discount_rate' => 15.0,
        'status' => OfferStatus::Inactive,
        'start_date' => now()->yesterday(),
        'end_date' => now()->tomorrow(),
    ]);

    $date = now()->format('Y-m-d');
    $time = now()->format('H:i:s');
    $response = $this->getJson("/api/booking/price?distance=100&goingDistance=50&returnDistance=50&startingLat=48.8588897&startingLng=2.3200410&endingLat=45.7578137&endingLng=4.8320114&extraLargeBags=0&couponCode=INACTIVE2024&date={$date}&time={$time}");

    $response->assertOk();
    expect($response->json('data.offerDiscountRate'))->toBe(0);
});

it('uses default date and time when not provided', function () {
    $response = $this->getJson('/api/booking/price?distance=100&goingDistance=50&returnDistance=50&startingLat=48.8588897&startingLng=2.3200410&endingLat=45.7578137&endingLng=4.8320114&extraLargeBags=0');

    $response->assertOk();
    expect($response->json('data.finalPrice'))->toBeNumeric();
});
