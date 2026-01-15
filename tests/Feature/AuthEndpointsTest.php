<?php

declare(strict_types=1);

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

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

it('sends a password reset code', function () {
    Notification::fake();
    $user = User::factory()->create();

    $response = $this->postJson('/api/auth/forgot-password', ['email' => $user->email]);

    $response->assertOk();
    expect($response->json('message'))->toBe(__('Password reset code sent successfully'));

    $user->refresh();
    expect($user->password_reset_otp)->not()->toBeNull();
    expect($user->password_reset_otp_expires_at)->not()->toBeNull();
    expect(mb_strlen($user->password_reset_otp))->toBe(6);
    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

it('resets a password with a valid OTP', function () {
    Event::fake();
    $user = User::factory()->create([
        'password_reset_otp' => '123456',
        'password_reset_otp_expires_at' => Carbon::now()->addMinutes(15),
    ]);

    $response = $this->postJson('/api/auth/reset-password', [
        'email' => $user->email,
        'otp' => '123456',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertOk();
    expect($response->json('message'))->toBe(__('Password reset successfully'));
    expect(Hash::check('new-password-123', $user->fresh()->password))->toBeTrue();

    $user->refresh();
    expect($user->password_reset_otp)->toBeNull();
    expect($user->password_reset_otp_expires_at)->toBeNull();

    Event::assertDispatched(PasswordReset::class);
});

it('registers a new user and generates an OTP', function () {
    Notification::fake();

    $response = $this->postJson('/api/auth/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phoneNumber' => '1234567890',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated();
    expect($response->json('message'))->toBe(__('Registration successful. Please verify your email.'));

    $user = User::where('email', 'john@example.com')->first();
    expect($user)->not()->toBeNull();
    expect($user->email_verification_otp)->not()->toBeNull();
    expect($user->email_verification_otp_expires_at)->not()->toBeNull();
    expect(mb_strlen($user->email_verification_otp))->toBe(6);
    expect($user->hasVerifiedEmail())->toBeFalse();
});

it('verifies email with a valid OTP', function () {
    Event::fake();
    $user = User::factory()->create([
        'email_verification_otp' => '123456',
        'email_verification_otp_expires_at' => Carbon::now()->addMinutes(15),
        'email_verified_at' => null,
    ]);

    $response = $this->postJson('/api/auth/verify-email', [
        'email' => $user->email,
        'otp' => '123456',
    ]);

    $response->assertOk();
    expect($response->json('message'))->toBe(__('Email verified successfully'));

    $user->refresh();
    expect($user->hasVerifiedEmail())->toBeTrue();
    expect($user->email_verification_otp)->toBeNull();
    expect($user->email_verification_otp_expires_at)->toBeNull();

    Event::assertDispatched(Verified::class);
});

it('fails to verify email with an invalid OTP', function () {
    $user = User::factory()->create([
        'email_verification_otp' => '123456',
        'email_verification_otp_expires_at' => Carbon::now()->addMinutes(15),
        'email_verified_at' => null,
    ]);

    $response = $this->postJson('/api/auth/verify-email', [
        'email' => $user->email,
        'otp' => '000000',
    ]);

    $response->assertForbidden();
    expect($response->json('message'))->toBe(__('Invalid verification code'));

    $user->refresh();
    expect($user->hasVerifiedEmail())->toBeFalse();
});

it('fails to verify email with an expired OTP', function () {
    $user = User::factory()->create([
        'email_verification_otp' => '123456',
        'email_verification_otp_expires_at' => Carbon::now()->subMinutes(1),
        'email_verified_at' => null,
    ]);

    $response = $this->postJson('/api/auth/verify-email', [
        'email' => $user->email,
        'otp' => '123456',
    ]);

    $response->assertForbidden();
    expect($response->json('message'))->toBe(__('Verification code has expired'));

    $user->refresh();
    expect($user->hasVerifiedEmail())->toBeFalse();
});

it('returns success when verifying an already verified email', function () {
    $user = User::factory()->create([
        'email_verified_at' => Carbon::now(),
    ]);

    $response = $this->postJson('/api/auth/verify-email', [
        'email' => $user->email,
        'otp' => '123456',
    ]);

    $response->assertOk();
    expect($response->json('message'))->toBe(__('Email verified successfully'));
    expect($response->json('data.token'))->not()->toBeNull();
});

it('resends verification code', function () {
    Notification::fake();
    $user = User::factory()->create([
        'email_verification_otp' => '111111',
        'email_verification_otp_expires_at' => Carbon::now()->addMinutes(5),
        'email_verified_at' => null,
    ]);

    $oldOtp = $user->email_verification_otp;

    $response = $this->postJson('/api/auth/resend-verification', [
        'email' => $user->email,
    ]);

    $response->assertOk();
    expect($response->json('message'))->toBe(__('Verification code sent successfully'));

    $user->refresh();
    expect($user->email_verification_otp)->not()->toBe($oldOtp);
    expect($user->email_verification_otp)->not()->toBeNull();
    expect($user->email_verification_otp_expires_at)->not()->toBeNull();
});

it('returns success when resending verification for already verified email', function () {
    $user = User::factory()->create([
        'email_verified_at' => Carbon::now(),
    ]);

    $response = $this->postJson('/api/auth/resend-verification', [
        'email' => $user->email,
    ]);

    $response->assertOk();
    expect($response->json('message'))->toBe(__('Email already verified'));
});

it('validates email exists when verifying', function () {
    $response = $this->postJson('/api/auth/verify-email', [
        'email' => 'nonexistent@example.com',
        'otp' => '123456',
    ]);

    $response->assertUnprocessable();
});

it('validates OTP format when verifying', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/auth/verify-email', [
        'email' => $user->email,
        'otp' => '12345',
    ]);

    $response->assertUnprocessable();
});

it('validates email exists when resending verification', function () {
    $response = $this->postJson('/api/auth/resend-verification', [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertUnprocessable();
});

it('fails to reset password with an invalid OTP', function () {
    $user = User::factory()->create([
        'password_reset_otp' => '123456',
        'password_reset_otp_expires_at' => Carbon::now()->addMinutes(15),
    ]);

    $response = $this->postJson('/api/auth/reset-password', [
        'email' => $user->email,
        'otp' => '000000',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertForbidden();
    expect($response->json('message'))->toBe(__('Invalid reset code'));

    $user->refresh();
    expect(Hash::check('new-password-123', $user->password))->toBeFalse();
});

it('fails to reset password with an expired OTP', function () {
    $user = User::factory()->create([
        'password_reset_otp' => '123456',
        'password_reset_otp_expires_at' => Carbon::now()->subMinutes(1),
    ]);

    $response = $this->postJson('/api/auth/reset-password', [
        'email' => $user->email,
        'otp' => '123456',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertForbidden();
    expect($response->json('message'))->toBe(__('Reset code has expired'));

    $user->refresh();
    expect(Hash::check('new-password-123', $user->password))->toBeFalse();
});

it('validates email exists when requesting password reset', function () {
    $response = $this->postJson('/api/auth/forgot-password', [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertUnprocessable();
});

it('validates OTP format when resetting password', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/auth/reset-password', [
        'email' => $user->email,
        'otp' => '12345',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertUnprocessable();
});

it('validates email exists when resetting password', function () {
    $response = $this->postJson('/api/auth/reset-password', [
        'email' => 'nonexistent@example.com',
        'otp' => '123456',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertUnprocessable();
});
