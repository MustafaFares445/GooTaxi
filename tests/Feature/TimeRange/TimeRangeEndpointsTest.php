<?php

declare(strict_types=1);

use App\Models\TimeRange;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;

beforeEach(function () {
    $user = User::factory()->create(['is_admin' => true]);
    Sanctum::actingAs($user);
});

it('lists time ranges', function () {
    TimeRange::factory()->count(3)->create();

    $response = $this->getJson('/api/dashboard/time_ranges');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();
});

it('creates, shows, updates and deletes a timeRange', function () {
    $payload = [
        'days' => [1, 2, 3],
        'fromTime' => '08:00:00',
        'toTime' => '17:00:00',
        'pricePercentage' => 10.5,
    ];

    $create = $this->postJson('/api/dashboard/time_ranges', $payload);
    $create->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    $id = $create->json('data.id');
    $this->assertDatabaseHas('time_ranges', ['id' => $id]);

    $show = $this->getJson("/api/dashboard/time_ranges/{$id}");
    $show->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());

    $updatePayload = [
        'days' => [1, 2, 3],
        'fromTime' => '09:00:00',
        'toTime' => '18:00:00',
        'pricePercentage' => 10.5,
    ];

    $update = $this->putJson("/api/dashboard/time_ranges/{$id}", $updatePayload);
    $update->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());

    $delete = $this->deleteJson("/api/dashboard/time_ranges/{$id}");
    $delete->assertOk()
        ->assertJsonPath('message', ResponseMessages::DELETED->message());

    $this->assertDatabaseMissing('time_ranges', ['id' => $id]);
});

it('forbids unauthorized access to time ranges CRUD operations', function () {
    $user = User::factory()->create();
    $model = TimeRange::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson('/api/dashboard/time_ranges', [
        'days' => [1, 2, 3],
        'fromTime' => '08:00:00',
        'toTime' => '17:00:00',
        'pricePercentage' => 10.5,
    ])->assertForbidden();

    $this->putJson('/api/dashboard/time_ranges/'.$model->id, [
        'days' => [1, 2, 3],
        'fromTime' => '08:00:00',
        'toTime' => '17:00:00',
        'pricePercentage' => 10.5,
    ])->assertForbidden();

    $this->deleteJson('/api/dashboard/time_ranges/'.$model->id)->assertForbidden();
});
