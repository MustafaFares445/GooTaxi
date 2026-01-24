<?php

declare(strict_types=1);

use App\Enums\ResponseMessages;
use App\Models\Driver;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $user = User::factory()->create(['is_admin' => true]);
    Sanctum::actingAs($user);
});

it('lists drivers', function () {
    Driver::factory()->count(3)->create();

    $response = $this->getJson('/api/dashboard/drivers');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();
});

it('creates, shows, updates and deletes a driver', function () {
    $payload = [
        'name' => 'Sample name',
    ];

    $create = $this->postJson('/api/dashboard/drivers', $payload);
    $create->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    $id = $create->json('data.id');
    $this->assertDatabaseHas('drivers', ['id' => $id]);

    $show = $this->getJson("/api/dashboard/drivers/{$id}");
    $show->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());

    $updatePayload = [
        'name' => 'Sample name updated',
    ];

    $update = $this->putJson("/api/dashboard/drivers/{$id}", $updatePayload);
    $update->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());

    $delete = $this->deleteJson("/api/dashboard/drivers/{$id}");
    $delete->assertNoContent();

    $this->assertDatabaseMissing('drivers', ['id' => $id]);
});

it('forbids unauthorized access to drivers CRUD operations', function () {
    $user = User::factory()->create();
    $model = Driver::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson('/api/dashboard/drivers', [
        'name' => 'Sample name',
    ])->assertForbidden();

    $this->putJson('/api/dashboard/drivers/'.$model->id, [
        'name' => 'Sample name',
    ])->assertForbidden();

    $this->deleteJson('/api/dashboard/drivers/'.$model->id)->assertForbidden();
});
it('sorts drivers', function () {
    Driver::factory()->create(['name' => 'A driver']);
    Driver::factory()->create(['name' => 'Z driver']);

    $response = $this->getJson('/api/dashboard/drivers?sort=name');
    $response->assertOk();

    $val1 = $response->json('data.0.name');
    if (is_array($val1)) {
        $val1 = $val1['en'] ?? array_values($val1)[0];
    }

    expect((string) $val1)->toContain('A');

    $response = $this->getJson('/api/dashboard/drivers?sort=-name');
    $response->assertOk();

    $val2 = $response->json('data.0.name');
    if (is_array($val2)) {
        $val2 = $val2['en'] ?? array_values($val2)[0];
    }

    expect((string) $val2)->toContain('Z');
});
