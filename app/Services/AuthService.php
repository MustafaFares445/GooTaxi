<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Auth\LoginData;
use App\Data\Auth\RegisterData;
use App\Data\Auth\ResetPasswordData;
use App\Data\Auth\VerifyEmailData;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Random\RandomException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

final class AuthService
{
    /**
     * @return array{user: User, token: string}
     */
    public function login(LoginData $data): array
    {
        $user = User::query()->where('email', $data->email)->firstOrFail();

        Gate::forUser($user)->authorize('attempt-login', [$data->password]);

        return [
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken,
        ];
    }

    public function logout(?PersonalAccessToken $token): void
    {
        $token?->delete();
    }

    /**
     * @return array{httpStatus: int, status: string}
     *
     * @throws RandomException
     */
    public function sendResetLink(string $email): array
    {
        $user = User::query()->where('email', $email)->firstOrFail();

        $otp = $this->generateOtp();

        $user->update([
            'password_reset_otp' => $otp,
            'password_reset_otp_expires_at' => Carbon::now()->addMinutes(15),
        ]);

        $user->sendPasswordResetNotification();

        return [
            'httpStatus' => ResponseAlias::HTTP_OK,
            'status' => __('Password reset code sent successfully'),
        ];
    }

    public function resetPassword(ResetPasswordData $data): array
    {
        $user = User::query()->where('email', $data->email)->firstOrFail();

        Gate::forUser($user)->authorize('reset-password', [$data->otp]);

        $user->forceFill([
            'password' => Hash::make($data->password),
            'remember_token' => Str::random(60),
            'password_reset_otp' => null,
            'password_reset_otp_expires_at' => null,
        ])->save();

        event(new PasswordReset($user));

        return [
            'httpStatus' => ResponseAlias::HTTP_OK,
            'status' => __('Password reset successfully'),
        ];
    }

    /**
     * @param  array{name: string, email: string, password: string, password_confirmation?: string}  $data
     *
     * @throws RandomException
     */
    public function register(array $data): User
    {
        $registerData = RegisterData::from($data);

        $otp = $this->generateOtp();

        $user = User::create([
            'name' => $registerData->name,
            'email' => $registerData->email,
            'phone_number' => $registerData->phoneNumber,
            'password' => $registerData->password,
            'email_verification_otp' => $otp,
            'email_verification_otp_expires_at' => Carbon::now()->addMinutes(15),
        ]);

        $user->sendEmailVerificationNotification();

        return $user;
    }

    /**
     * @return array{user: User, token: string}
     */
    public function verifyEmail(VerifyEmailData $data): array
    {
        $user = User::where('email', $data->email)->firstOrFail();

        if ($user->hasVerifiedEmail()) {
            return [
                'user' => $user,
                'token' => $user->createToken('api-token')->plainTextToken,
            ];
        }

        Gate::forUser($user)->authorize('verify-email', [$data->otp]);

        $user->update([
            'email_verification_otp' => null,
            'email_verification_otp_expires_at' => null,
        ]);

        event(new Verified($user));

        return [
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken,
        ];
    }

    /**
     * @return array{httpStatus: int, status: string}
     *
     * @throws RandomException
     */
    public function resendVerification(string $email): array
    {
        $user = User::where('email', $email)->firstOrFail();

        if ($user->hasVerifiedEmail()) {
            return [
                'httpStatus' => ResponseAlias::HTTP_OK,
                'status' => __('Email already verified'),
            ];
        }

        $otp = $this->generateOtp();

        $user->update([
            'email_verification_otp' => $otp,
            'email_verification_otp_expires_at' => Carbon::now()->addMinutes(15),
        ]);

        $user->sendEmailVerificationNotification();

        return [
            'httpStatus' => ResponseAlias::HTTP_OK,
            'status' => __('Verification code sent successfully'),
        ];
    }

    /**
     * @throws RandomException
     */
    private function generateOtp(): string
    {
        return mb_str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
