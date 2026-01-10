<?php

declare(strict_types=1);

it('returns no content when coupon code does not exist', function () {
    $response = $this->postJson('/api/check-offer', [
        'couponCode' => 'NONEXISTENT123',
    ]);

    $response->assertNoContent();
});

it('returns no content when coupon code exists but offer is inactive', function () {
    $inactiveOffer = App\Models\Offer::factory()->inactive()->create([
        'coupon_code' => 'INACTIVE123',
        'start_date' => now()->subDays(10),
        'end_date' => now()->addDays(10),
    ]);

    $response = $this->postJson('/api/check-offer', [
        'couponCode' => $inactiveOffer->coupon_code,
    ]);

    $response->assertNoContent();
});

it('returns no content when coupon code exists but offer is expired', function () {
    $expiredOffer = App\Models\Offer::factory()->active()->create([
        'coupon_code' => 'EXPIRED123',
        'start_date' => now()->subDays(30),
        'end_date' => now()->subDays(1),
    ]);

    $response = $this->postJson('/api/check-offer', [
        'couponCode' => $expiredOffer->coupon_code,
    ]);

    $response->assertNoContent();
});

it('returns no content when coupon code exists but offer has reached usage limit', function () {
    $maxedOutOffer = App\Models\Offer::factory()->active()->create([
        'coupon_code' => 'MAXED123',
        'uses' => 10,
        'number_of_times_used' => 10,
        'start_date' => now()->subDays(5),
        'end_date' => now()->addDays(30),
    ]);

    $response = $this->postJson('/api/check-offer', [
        'couponCode' => $maxedOutOffer->coupon_code,
    ]);

    $response->assertNoContent();
});

it('validates that coupon code is required', function () {
    $response = $this->postJson('/api/check-offer', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['couponCode']);
});

it('validates that coupon code is a string', function () {
    $response = $this->postJson('/api/check-offer', [
        'couponCode' => 12345,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['couponCode']);
});

it('validates that coupon code has minimum length', function () {
    $response = $this->postJson('/api/check-offer', [
        'couponCode' => '',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['couponCode']);
});

it('validates that coupon code has maximum length', function () {
    $response = $this->postJson('/api/check-offer', [
        'couponCode' => str_repeat('A', 101),
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['couponCode']);
});
