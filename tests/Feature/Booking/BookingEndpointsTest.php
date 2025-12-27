<?php

declare(strict_types=1);

use App\Enums\BookingStatus;
use App\Models\BasePrice;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;

beforeEach(function () {
    $user = User::factory()->create(['is_admin' => true]);
    BasePrice::factory()->create();
    Sanctum::actingAs($user);
});

it('lists bookings', function () {
    Booking::factory()->count(3)->create();

    $response = $this->getJson('/api/dashboard/bookings');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();
});

it('creates, shows a booking', function () {
    $payload = [
        'userId' => User::factory()->create()->id,
        'driverId' => Driver::factory()->create()->id,
        'fromLocation' => 'Sample fromLocation',
        'toLocation' => 'Sample toLocation',
        'date' => '2025-01-01',
        'time' => '10:00:00',
        'distance' => 10.5,
        'goingDistance' => 5.0,
        'returnDistance' => 3.0,
        'startingLat' => 40.7128,
        'startingLng' => -74.0060,
        'endingLat' => 34.0522,
        'endingLng' => -118.2437,
        'passengers' => 2,
        'extraLargeBags' => false,
        'finalPrice' => 10.5,
        'status' => BookingStatus::Completed->value,
        'offerId' => App\Models\Offer::factory()->create()->id,
    ];

    $create = $this->postJson('/api/dashboard/bookings', $payload);
    $create->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    $id = $create->json('data.id');
    $this->assertDatabaseHas('bookings', ['id' => $id]);

    $show = $this->getJson("/api/dashboard/bookings/{$id}");
    $show->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
});

it('forbids unauthorized access to bookings CRUD operations', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $model = Booking::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson('/api/dashboard/bookings', [
        'userId' => User::factory()->create()->id,
        'driverId' => Driver::factory()->create()->id,
        'fromLocation' => 'Sample fromLocation',
        'toLocation' => 'Sample toLocation',
        'date' => '2025-01-01',
        'time' => '10:00:00',
        'distance' => 10.5,
        'goingDistance' => 5.0,
        'returnDistance' => 3.0,
        'startingLat' => 40.7128,
        'startingLng' => -74.0060,
        'endingLat' => 34.0522,
        'endingLng' => -118.2437,
        'passengers' => 2,
        'extraLargeBags' => false,
        'finalPrice' => 10.5,
        'status' => BookingStatus::Completed->value,
        'offerId' => App\Models\Offer::factory()->create()->id,
    ])->assertForbidden();
});

it('sorts bookings', function () {
    Booking::factory()->create(['from_location' => 'A location']);
    Booking::factory()->create(['from_location' => 'Z location']);

    $response = $this->getJson('/api/dashboard/bookings?sort=fromLocation');
    $response->assertOk();

    $val1 = $response->json('data.0.fromLocation');
    if (is_array($val1)) {
        $val1 = $val1['en'] ?? array_values($val1)[0];
    }

    expect((string) $val1)->toContain('A');

    $response = $this->getJson('/api/dashboard/bookings?sort=-fromLocation');
    $response->assertOk();

    $val2 = $response->json('data.0.fromLocation');
    if (is_array($val2)) {
        $val2 = $val2['en'] ?? array_values($val2)[0];
    }

    expect((string) $val2)->toContain('Z');
});
