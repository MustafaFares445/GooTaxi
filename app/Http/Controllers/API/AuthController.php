<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\Auth\LoginData;
use App\Data\Auth\ResetPasswordData;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Resources\AuthResource;
use App\Services\AuthService;
use App\Traits\MessageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function register(RegisterRequest $request): AuthResource
    {
        $authResult = $this->authService->register($request->validated());

        return AuthResource::make($authResult)
            ->additional(['message' => __('Registration successful. Please verify your email.')]);
    }

    /**
     * @tags API
     */
    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        $responseData = $this->authService->verifyEmail($request->validated());

        return $this->successMessage(message: __($responseData['status']), status: $responseData['httpStatus']);
    }
}
