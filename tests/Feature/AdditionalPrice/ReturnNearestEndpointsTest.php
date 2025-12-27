<?php

declare(strict_types=1);

use App\Models\AdditionalPrice;
use App\Models\BasePrice;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
});

it('returns nearest additional price by longitude and latitude', function () {
    BasePrice::factory()->create();

    $nearbyPrice = AdditionalPrice::factory()->create([
        'latitude' => 40.7128,
        'longitude' => -74.0060,
        'start_price' => 5,
        'price_of_going_per_km' => 2,
        'return_price_per_km' => 1.5,
    ]);

    $farPrice = AdditionalPrice::factory()->create([
        'latitude' => 51.5074,
        'longitude' => -0.1278,
        'start_price' => 3,
        'price_of_going_per_km' => 1,
        'return_price_per_km' => 0.8,
    ]);

    $response = $this->postJson('/api/nearest-additional-price', [
        'latitude' => 40.7125,
        'longitude' => -74.0058,
    ]);

    $response->assertOk();
    expect($response->json('data.id'))->toBe($nearbyPrice->id);
    expect((float) $response->json('data.startPrice'))->toBe(5.0);
});

it('validates required coordinates', function () {
    $response = $this->postJson('/api/nearest-additional-price', [
        'latitude' => 40.7128,
    ]);

    $response->assertUnprocessable();
});

it('validates numeric coordinates', function () {
    $response = $this->postJson('/api/nearest-additional-price', [
        'latitude' => 'invalid',
        'longitude' => 'also-invalid',
    ]);

    $response->assertUnprocessable();
});

it('returns empty when no additional prices exist', function () {
    $response = $this->postJson('/api/nearest-additional-price', [
        'latitude' => 40.7128,
        'longitude' => -74.0060,
    ]);

    $response->assertOk();
    expect($response->json('data'))->toBeNull();
});
