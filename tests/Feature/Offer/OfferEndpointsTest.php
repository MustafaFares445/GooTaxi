<?php

declare(strict_types=1);

use App\Enums\ResponseMessages;
use App\Models\Offer;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $user = User::factory()->create(['is_admin' => true]);
    Sanctum::actingAs($user);
});

it('lists offers', function () {
    Offer::factory()->count(3)->create();

    $response = $this->getJson('/api/dashboard/offers');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();
});

it('creates, shows, updates and deletes a offer', function () {
    $payload = [
        'couponCode' => 'Sample couponCode',
        'discountRate' => 10.5,
        'numberOfTimesUsed' => 5,
        'uses' => 10,
        'status' => 'active',
        'startDate' => '2025-01-01T00:00:00Z',
        'endDate' => '2025-01-01T00:00:00Z',
    ];

    $create = $this->postJson('/api/dashboard/offers', $payload);
    $create->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    $id = $create->json('data.id');
    $this->assertDatabaseHas('offers', ['id' => $id]);

    $show = $this->getJson("/api/dashboard/offers/{$id}");
    $show->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());

    $updatePayload = [
        'couponCode' => 'Sample couponCode updated',
        'discountRate' => 10.5,
        'numberOfTimesUsed' => 3,
        'uses' => 8,
        'status' => 'active',
        'startDate' => '2025-01-01',
        'endDate' => '2025-01-01',
    ];

    $update = $this->putJson("/api/dashboard/offers/{$id}", $updatePayload);
    $update->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());

    $delete = $this->deleteJson("/api/dashboard/offers/{$id}");
    $delete->assertNoContent();

    $this->assertDatabaseMissing('offers', ['id' => $id]);
});

it('forbids unauthorized access to offers CRUD operations', function () {
    $user = User::factory()->create();
    $model = Offer::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson('/api/dashboard/offers', [
        'couponCode' => 'Sample couponCode',
        'discountRate' => 10.5,
        'numberOfTimesUsed' => 5,
        'uses' => 10,
        'status' => 'active',
        'startDate' => '2025-01-01T00:00:00Z',
        'endDate' => '2025-01-01T00:00:00Z',
    ])->assertForbidden();

    $this->putJson('/api/dashboard/offers/'.$model->id, [
        'couponCode' => 'Sample couponCode',
        'discountRate' => 10.5,
        'numberOfTimesUsed' => 5,
        'uses' => 10,
        'status' => 'active',
        'startDate' => '2025-01-01T00:00:00Z',
        'endDate' => '2025-01-01T00:00:00Z',
    ])->assertForbidden();

    $this->deleteJson('/api/dashboard/offers/'.$model->id)->assertForbidden();
});
it('sorts offers', function () {
    Offer::factory()->create(['coupon_code' => 'A offer']);
    Offer::factory()->create(['coupon_code' => 'Z offer']);

    $response = $this->getJson('/api/dashboard/offers?sort=couponCode');
    $response->assertOk();

    $val1 = $response->json('data.0.couponCode');
    if (is_array($val1)) {
        $val1 = $val1['en'] ?? array_values($val1)[0];
    }

    expect((string) $val1)->toContain('A');

    $response = $this->getJson('/api/dashboard/offers?sort=-couponCode');
    $response->assertOk();

    $val2 = $response->json('data.0.couponCode');
    if (is_array($val2)) {
        $val2 = $val2['en'] ?? array_values($val2)[0];
    }

    expect((string) $val2)->toContain('Z');
});
