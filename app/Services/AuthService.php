<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Auth\LoginData;
use App\Data\Auth\ResetPasswordData;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
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
     */
    public function sendResetLink(string $email): array
    {
        $status = Password::sendResetLink(['email' => $email]);

        return $this->passwordActionResponse($status, Password::RESET_LINK_SENT);
    }

    public function resetPassword(ResetPasswordData $data): array
    {
        $status = Password::reset([
            'email' => $data->email,
            'token' => $data->token,
            'password' => $data->password,
        ],
            static function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $this->passwordActionResponse($status, Password::PASSWORD_RESET);
    }

    /**
     * @return array{httpStatus: int, status: string}
     */
    private function passwordActionResponse(string $status, string $successStatus): array
    {
        $httpStatus = $status === $successStatus
            ? ResponseAlias::HTTP_OK
            : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;

        return [
            'httpStatus' => $httpStatus,
            'status' => $status,
        ];
    }
}
