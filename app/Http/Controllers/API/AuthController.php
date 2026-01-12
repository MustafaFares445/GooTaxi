<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\Auth\LoginData;
use App\Data\Auth\ResetPasswordData;
use App\Data\Auth\VerifyEmailData;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResendVerificationRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Traits\MessageTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class AuthController
{
    use MessageTrait;

    public function __construct(protected AuthService $authService) {}

    /**
     * Authenticate user and generate access token
     *
     * This endpoint authenticates a user with their email and password. Upon successful authentication,
     * it returns a Bearer token that can be used for subsequent authenticated requests.
     *
     * @operation login
     *
     * @tags API
     *
     * @throws AuthenticationException 401 Invalid credentials provided
     * @throws AuthorizationException 403 Email address not verified (for non-admin users)
     */
    public function login(LoginRequest $request): AuthResource
    {
        $authResult = $this->authService->login(LoginData::from($request->validated()));

        return AuthResource::make($authResult)
            ->additional(['message' => __('Log in successfully')]);
    }

    /**
     * Logout authenticated user
     *
     * This endpoint invalidates the current access token, effectively logging out the user.
     * The user must be authenticated to access this endpoint.
     *
     * @operation logout
     *
     * @tags API
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user()?->currentAccessToken());

        return $this->successMessage(message: __('Logged out successfully'));
    }

    /**
     * Request password reset link
     *
     * This endpoint sends a password reset link to the user's email address.
     * The reset link can be used to reset the user's password.
     *
     * @operation forgotPassword
     *
     * @tags API
     *
     * @throws ValidationException 422 Invalid email address or email does not exist
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $responseData = $this->authService->sendResetLink($request->validated('email'));

        return $this->successMessage(message: __($responseData['status']), status: $responseData['httpStatus']);
    }

    /**
     * Reset user password
     *
     * This endpoint allows a user to reset their password using a valid reset token
     * that was sent to their email address via the forgot password endpoint.
     *
     * @operation resetPassword
     *
     * @tags API
     *
     * @throws ValidationException 422 Invalid token, email, or password validation failed
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $responseData = $this->authService->resetPassword(ResetPasswordData::from($request->validated()));

        return $this->successMessage(message: __($responseData['status']), status: $responseData['httpStatus']);
    }

    /**
     * Register a new user account
     *
     * This endpoint creates a new user account with the provided information.
     * Upon successful registration, an email verification code is sent to the user's email address.
     * The user must verify their email before they can log in (for non-admin users).
     *
     * @operation register
     *
     * @tags API
     *
     * @throws ValidationException 422 Invalid input data, email or phone number already exists
     */
    public function register(RegisterRequest $request): UserResource
    {
        $user = $this->authService->register($request->validated());

        return UserResource::make($user)
            ->additional(['message' => __('Registration successful. Please verify your email.')]);
    }

    /**
     * Verify user email address
     *
     * This endpoint verifies a user's email address using the OTP (One-Time Password) code
     * that was sent to their email during registration or resend verification.
     * Upon successful verification, the user receives an authentication token and can log in.
     *
     * @operation verifyEmail
     *
     * @tags API
     *
     * @throws AuthorizationException 403 Invalid verification code, verification code has expired, or failed to verify email
     * @throws ValidationException 422 Invalid email or OTP format
     */
    public function verifyEmail(VerifyEmailRequest $request): AuthResource
    {
        $authResult = $this->authService->verifyEmail(VerifyEmailData::from($request->validated()));

        return AuthResource::make($authResult)
            ->additional(['message' => __('Email verified successfully')]);
    }

    /**
     * Resend email verification code
     *
     * This endpoint resends a new email verification OTP code to the user's email address.
     * Useful when the original verification code has expired or was not received.
     *
     * @operation resendVerification
     *
     * @tags API
     *
     * @throws ValidationException 422 Invalid email address or email does not exist
     */
    public function resendVerification(ResendVerificationRequest $request): JsonResponse
    {
        $responseData = $this->authService->resendVerification($request->validated('email'));

        return $this->successMessage(message: __($responseData['status']), status: $responseData['httpStatus']);
    }
}
