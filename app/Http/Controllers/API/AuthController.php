<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\Auth\LoginData;
use App\Traits\MessageTrait;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Data\Auth\VerifyEmailData;
use App\Http\Requests\LoginRequest;
use App\Data\Auth\ResetPasswordData;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResendVerificationRequest;

final class AuthController
{
    use MessageTrait;

    public function __construct(protected AuthService $authService) {}

    /**
     * @tags API
     */
    public function login(LoginRequest $request): AuthResource
    {
        $authResult = $this->authService->login(LoginData::from($request->validated()));

        return AuthResource::make($authResult)
            ->additional(['message' => __('Log in successfully')]);
    }

    /**
     * @tags API
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user()?->currentAccessToken());

        return $this->successMessage(message: __('Logged out successfully'));
    }

    /**
     * @tags API
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $responseData = $this->authService->sendResetLink($request->validated('email'));

        return $this->successMessage(message: __($responseData['status']), status: $responseData['httpStatus']);
    }

    /**
     * @tags API
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $responseData = $this->authService->resetPassword(ResetPasswordData::from($request->validated()));

        return $this->successMessage(message: __($responseData['status']), status: $responseData['httpStatus']);
    }

    /**
     * @tags API
     */
    public function register(RegisterRequest $request): UserResource
    {
        $user = $this->authService->register($request->validated());

        return UserResource::make($user)
            ->additional(['message' => __('Registration successful. Please verify your email.')]);
    }

    /**
     * @tags API
     */
    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        $responseData = $this->authService->verifyEmail(VerifyEmailData::from($request->validated()));

        return $this->successMessage(message: __($responseData['status']), status: $responseData['httpStatus']);
    }

    /**
     * @tags API
     */
    public function resendVerification(ResendVerificationRequest $request): JsonResponse
    {
        $responseData = $this->authService->resendVerification($request->validated('email'));

        return $this->successMessage(message: __($responseData['status']), status: $responseData['httpStatus']);
    }
}
