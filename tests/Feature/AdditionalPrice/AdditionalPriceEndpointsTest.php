<?php

declare(strict_types=1);

use App\Enums\ResponseMessages;
use App\Models\AdditionalPrice;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $user = User::factory()->create(['is_admin' => true]);
    Sanctum::actingAs($user);
});

it('lists additional prices', function () {
    AdditionalPrice::factory()->count(3)->create();

    $response = $this->getJson('/api/dashboard/additional_prices');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();
});

it('creates, shows, updates and deletes a additionalPrice', function () {
    $payload = [
        'startPrice' => 10.5,
        'priceOfGoingPerKm' => 10.5,
        'returnPricePerKm' => 10.5,
        'latitude' => 10.5,
        'longitude' => 10.5,
        'address' => '123 Main Street, City',
    ];

    $create = $this->postJson('/api/dashboard/additional_prices', $payload);
    $create->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    $id = $create->json('data.id');
    $this->assertDatabaseHas('additional_prices', ['id' => $id]);

    $show = $this->getJson("/api/dashboard/additional_prices/{$id}");
    $show->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());

    $updatePayload = [
        'startPrice' => 10.5,
        'priceOfGoingPerKm' => 10.5,
        'returnPricePerKm' => 10.5,
        'latitude' => 10.5,
        'longitude' => 10.5,
        'address' => '456 Oak Avenue, Town',
    ];

    $update = $this->putJson("/api/dashboard/additional_prices/{$id}", $updatePayload);
    $update->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());

    $delete = $this->deleteJson("/api/dashboard/additional_prices/{$id}");
    $delete->assertNoContent();

    $this->assertDatabaseMissing('additional_prices', ['id' => $id]);
});

it('forbids unauthorized access to additional prices CRUD operations', function () {
    $user = User::factory()->create();
    $model = AdditionalPrice::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson('/api/dashboard/additional_prices', [
        'startPrice' => 10.5,
        'priceOfGoingPerKm' => 10.5,
        'returnPricePerKm' => 10.5,
        'latitude' => 10.5,
        'longitude' => 10.5,
        'address' => '789 Pine Road, Village',
    ])->assertForbidden();

    $this->putJson('/api/dashboard/additional_prices/'.$model->id, [
        'startPrice' => 10.5,
        'priceOfGoingPerKm' => 10.5,
        'returnPricePerKm' => 10.5,
        'latitude' => 10.5,
        'longitude' => 10.5,
        'address' => '321 Elm Street, Hamlet',
    ])->assertForbidden();

    $this->deleteJson('/api/dashboard/additional_prices/'.$model->id)->assertForbidden();
});
it('filters additional prices by search term', function () {
    AdditionalPrice::factory()->create(['start_price' => 100.0]);
    AdditionalPrice::factory()->create(['start_price' => 200.0]);

    $response = $this->withHeader('Accept-Language', 'en')
        ->getJson('/api/dashboard/additional_prices?search=100');

    $response->assertOk();
    $data = $response->json('data');
    $found = false;
    foreach ($data as $item) {
        $val = (string) $item['startPrice'];
        if (str_contains($val, '100')) {
            $found = true;
            break;
        }
    }
    expect($found)->toBeTrue();
});

it('sorts additional prices', function () {
    AdditionalPrice::factory()->create(['start_price' => 10.0]);
    AdditionalPrice::factory()->create(['start_price' => 20.0]);

    $response = $this->getJson('/api/dashboard/additional_prices?sort=startPrice');
    $response->assertOk();

    $val1 = $response->json('data.0.startPrice');

    expect((float) $val1)->toBeLessThan(15.0);

    $response = $this->getJson('/api/dashboard/additional_prices?sort=-startPrice');
    $response->assertOk();

    $val2 = $response->json('data.0.startPrice');

    expect((float) $val2)->toBeGreaterThan(15.0);
});
