<?php

declare(strict_types=1);

use App\Enums\ResponseMessages;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $user = User::factory()->create(['is_admin' => true]);
    Sanctum::actingAs($user);
});

it('lists users', function () {
    User::factory()->count(3)->create();

    $response = $this->getJson('/api/dashboard/users');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();
});

it('creates, shows, updates and deletes a user', function () {
    $payload = [
        'name' => 'Sample name',
        'email' => 'test@gmail.com',
        'phoneNumber' => 'Sample phoneNumber',
        'isAdmin' => 0,
        'emailVerifiedAt' => '2025-01-01T10:00:00Z',
        'password' => 'Sample password',
        'rememberToken' => 'Sample rememberToken',
    ];

    $create = $this->postJson('/api/dashboard/users', $payload);
    $create->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    $id = $create->json('data.id');
    $this->assertDatabaseHas('users', ['id' => $id]);

    $show = $this->getJson("/api/dashboard/users/{$id}");
    $show->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());

    $updatePayload = [
        'name' => 'Sample name updated',
        'email' => 'test2@gmail.com',
        'phoneNumber' => 'Sample phoneNumber updated',
        'isAdmin' => 1,
        'emailVerifiedAt' => '2025-01-01 10:00:00',
        'password' => 'Sample password updated',
        'rememberToken' => 'Sample rememberToken updated',
    ];

    $update = $this->putJson("/api/dashboard/users/{$id}", $updatePayload);
    $update->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());

    $delete = $this->deleteJson("/api/dashboard/users/{$id}");
    $delete->assertNoContent();

    $this->assertDatabaseMissing('users', ['id' => $id]);
});

it('forbids unauthorized access to users CRUD operations', function () {
    $user = User::factory()->create();
    $model = User::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson('/api/dashboard/users', [
        'name' => 'Sample name',
        'email' => 'test@gmail.com',
        'phoneNumber' => 'Sample phoneNumber',
        'isAdmin' => 0,
        'emailVerifiedAt' => '2025-01-01T10:00:00Z',
        'password' => 'Sample password',
        'rememberToken' => 'Sample rememberToken',
    ])->assertForbidden();

    $this->putJson('/api/dashboard/users/'.$model->id, [
        'name' => 'Sample name',
        'email' => 'test@gmail.com',
        'phoneNumber' => 'Sample phoneNumber',
        'isAdmin' => 0,
        'emailVerifiedAt' => '2025-01-01T10:00:00Z',
        'password' => 'Sample password',
        'rememberToken' => 'Sample rememberToken',
    ])->assertForbidden();

    $this->deleteJson('/api/dashboard/users/'.$model->id)->assertForbidden();
});
it('sorts users', function () {
    User::factory()->create(['name' => 'A user']);
    User::factory()->create(['name' => 'Z user']);

    $response = $this->getJson('/api/dashboard/users?sort=name');
    $response->assertOk();

    $val1 = $response->json('data.0.name');
    if (is_array($val1)) {
        $val1 = $val1['en'] ?? array_values($val1)[0];
    }

    expect((string) $val1)->toContain('A');

    $response = $this->getJson('/api/dashboard/users?sort=-name');
    $response->assertOk();

    $val2 = $response->json('data.0.name');
    if (is_array($val2)) {
        $val2 = $val2['en'] ?? array_values($val2)[0];
    }

    expect((string) $val2)->toContain('Z');
});
