<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

it('logs in and returns an access token', function () {
    $user = User::factory()->create(['password' => Hash::make('secret123')]);

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'secret123',
    ]);

    $response->assertOk()->assertJsonPath('message', __('Log in successfully'));
    expect($response->json('data.token'))->not()->toBeNull();
});

it('requires system admin role for dashboard login route', function () {
    $user = User::factory()->create(['password' => Hash::make('secret123')]);

    $response = $this->postJson('/api/dashboard/auth/login', [
        'email' => $user->email,
        'password' => 'secret123',
    ]);

    $response->assertUnauthorized();
});

it('allows system admin role to log into dashboard', closure: function () {
    $user = User::factory()->create(['password' => Hash::make('secret123'), 'is_admin' => true]);

    $response = $this->postJson('/api/dashboard/auth/login', [
        'email' => $user->email,
        'password' => 'secret123',
    ]);

    $response->assertOk()->assertJsonPath('message', __('Log in successfully'));
});

it('logs out the current token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api-token');

    $response = $this->withHeader('Authorization', 'Bearer '.$token->plainTextToken)
        ->postJson('/api/auth/logout');

    $response->assertOk()->assertJsonPath('message', __('Logged out successfully'));
    $this->assertDatabaseMissing('personal_access_tokens', ['id' => $token->accessToken->id]);
});

it('sends a password reset link', function () {
    Notification::fake();
    $user = User::factory()->create();

    $response = $this->postJson('/api/auth/forgot-password', ['email' => $user->email]);

    $response->assertOk();
    expect($response->json('message'))->not()->toBeEmpty();
    Notification::assertSentTo($user, ResetPassword::class);
});

it('resets a password with a valid token', function () {
    $user = User::factory()->create([]);
    $token = Password::createToken($user);

    $response = $this->postJson('/api/auth/reset-password', [
        'email' => $user->email,
        'token' => $token,
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertOk();
    expect($response->json('message'))->not()->toBeEmpty();
    expect(Hash::check('new-password-123', $user->fresh()->password))->toBeTrue();
});
