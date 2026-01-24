<?php

declare(strict_types=1);

use App\Enums\ResponseMessages;
use App\Models\BasePrice;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $user = User::factory()->create(['is_admin' => true]);
    Sanctum::actingAs($user);
});

it('retrieve base price object', function () {
    BasePrice::factory()->count(3)->create();

    $response = $this->getJson('/api/dashboard/base_prices');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    $data = json_decode($response->getContent(), false)->data;
    expect($data)->toBeObject();
    expect($data->id)->toBeInt();
});

it('updates a basePrice', function () {
    $id = BasePrice::factory()->create()->id;
    $updatePayload = [
        'pricePerKm' => 10.5,
        'vanPricePercentage' => 10.5,
    ];

    $update = $this->putJson("/api/dashboard/base_prices/{$id}", $updatePayload);
    $update->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());
});

it('forbids unauthorized access to base prices CRUD operations', function () {
    $user = User::factory()->create();
    $model = BasePrice::factory()->create();
    Sanctum::actingAs($user);

    $this->putJson('/api/dashboard/base_prices/'.$model->id, [
        'pricePerKm' => 10.5,
        'vanPricePercentage' => 10.5,
    ])->assertForbidden();
});
