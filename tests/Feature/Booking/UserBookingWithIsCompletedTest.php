<?php

declare(strict_types=1);

use App\Enums\BookingStatus;
use App\Models\BasePrice;
use App\Models\Booking;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;

beforeEach(function () {
    $user = User::factory()->create(['email' => 'testuser@example.com']);
    BasePrice::factory()->create();
    Sanctum::actingAs($user);
});

it('creates a booking with only required fields and isCompleted true', function () {
    $payload = [
        'fromLocation' => 'Test From Location',
        'toLocation' => ['Test To Location 1', 'Test To Location 2'],
        'startingLat' => 40.7128,
        'startingLng' => -74.0060,
        'endingLat' => 34.0522,
        'endingLng' => -118.2437,
        'isCompleted' => true,
    ];

    $response = $this->postJson('/api/booking', $payload);

    $response->assertCreated()
        ->assertJsonPath('message', ResponseMessages::CREATED->message());

    $bookingId = $response->json('data.id');

    $this->assertDatabaseHas('bookings', [
        'id' => $bookingId,
        'user_id' => auth()->id(),
        'is_completed' => true,
        'from_location' => 'Test From Location',
        'driver_id' => null,
        'offer_id' => null,
        'distance' => null,
        'going_distance' => null,
        'return_distance' => null,
        'notes' => null,
    ]);

    $booking = Booking::find($bookingId);
    expect($booking->is_completed)->toBeTrue();
    expect($booking->passengers)->toBe(1);
    expect($booking->extra_large_bags)->toBeFalse();
    expect($booking->status)->toBe(BookingStatus::Pending);
});

it('creates a booking with missing optional fields and isCompleted true', function () {
    $payload = [
        'fromLocation' => 'Another From Location',
        'toLocation' => ['Another To Location'],
        'startingLat' => 40.7580,
        'startingLng' => -73.9855,
        'endingLat' => 40.7489,
        'endingLng' => -73.9680,
        'isCompleted' => true,
    ];

    $response = $this->postJson('/api/booking', $payload);

    $response->assertCreated();

    $bookingId = $response->json('data.id');

    $this->assertDatabaseHas('bookings', [
        'id' => $bookingId,
        'is_completed' => true,
        'driver_id' => null,
        'offer_id' => null,
        'distance' => null,
        'going_distance' => null,
        'return_distance' => null,
        'notes' => null,
    ]);
});

it('creates a booking with isCompleted true and some optional fields provided', function () {
    $payload = [
        'fromLocation' => 'Partial Data Location',
        'toLocation' => ['Destination 1'],
        'startingLat' => 40.7128,
        'startingLng' => -74.0060,
        'endingLat' => 34.0522,
        'endingLng' => -118.2437,
        'passengers' => 3,
        'extraLargeBags' => true,
        'notes' => 'Test notes',
        'isCompleted' => true,
    ];

    $response = $this->postJson('/api/booking', $payload);

    $response->assertCreated();

    $bookingId = $response->json('data.id');

    $this->assertDatabaseHas('bookings', [
        'id' => $bookingId,
        'is_completed' => true,
        'passengers' => 3,
        'extra_large_bags' => true,
        'notes' => 'Test notes',
        'driver_id' => null,
        'offer_id' => null,
        'distance' => null,
    ]);
});

it('creates a booking with isCompleted true and nullable distance fields missing', function () {
    $payload = [
        'fromLocation' => 'No Distance Location',
        'toLocation' => ['No Distance Destination'],
        'startingLat' => 40.7128,
        'startingLng' => -74.0060,
        'endingLat' => 34.0522,
        'endingLng' => -118.2437,
        'isCompleted' => true,
    ];

    $response = $this->postJson('/api/booking', $payload);

    $response->assertCreated();

    $bookingId = $response->json('data.id');

    $booking = Booking::find($bookingId);
    expect($booking->is_completed)->toBeTrue();
    expect($booking->distance)->toBeNull();
    expect($booking->going_distance)->toBeNull();
    expect($booking->return_distance)->toBeNull();
});

it('creates a booking with isCompleted true and date/time auto-filled', function () {
    $payload = [
        'fromLocation' => 'Auto Date Location',
        'toLocation' => ['Auto Date Destination'],
        'startingLat' => 40.7128,
        'startingLng' => -74.0060,
        'endingLat' => 34.0522,
        'endingLng' => -118.2437,
        'isCompleted' => true,
    ];

    $response = $this->postJson('/api/booking', $payload);

    $response->assertCreated();

    $bookingId = $response->json('data.id');

    $booking = Booking::find($bookingId);
    expect($booking->is_completed)->toBeTrue();
    expect($booking->date)->not->toBeNull();
    expect($booking->time)->not->toBeNull();
});

it('creates a booking with isCompleted true and driverId null', function () {
    $payload = [
        'fromLocation' => 'No Driver Location',
        'toLocation' => ['No Driver Destination'],
        'startingLat' => 40.7128,
        'startingLng' => -74.0060,
        'endingLat' => 34.0522,
        'endingLng' => -118.2437,
        'driverId' => null,
        'isCompleted' => true,
    ];

    $response = $this->postJson('/api/booking', $payload);

    $response->assertCreated();

    $bookingId = $response->json('data.id');

    $this->assertDatabaseHas('bookings', [
        'id' => $bookingId,
        'is_completed' => true,
        'driver_id' => null,
    ]);
});

it('creates a booking with isCompleted true and offerId null', function () {
    $payload = [
        'fromLocation' => 'No Offer Location',
        'toLocation' => ['No Offer Destination'],
        'startingLat' => 40.7128,
        'startingLng' => -74.0060,
        'endingLat' => 34.0522,
        'endingLng' => -118.2437,
        'offerId' => null,
        'isCompleted' => true,
    ];

    $response = $this->postJson('/api/booking', $payload);

    $response->assertCreated();

    $bookingId = $response->json('data.id');

    $this->assertDatabaseHas('bookings', [
        'id' => $bookingId,
        'is_completed' => true,
        'offer_id' => null,
    ]);
});
