<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (class_exists(\Laravel\Telescope\TelescopeServiceProvider::class) && $this->app->environment('local')) {

            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);

            $this->app->register(TelescopeServiceProvider::class);

        }
    }

    public function boot(): void
    {
        $this->bootModelsDefaults();

        Gate::define('attempt-login', static function (User $user, string $password): bool {
            if (! Hash::check($password, $user->password)) {
                throw new AuthenticationException(__('Invalid credentials.'));
            }

            if (! $user->is_admin && ! $user->hasVerifiedEmail()) {
                throw new AuthorizationException(__('Please verify your email address before logging in.'));
            }

            if (! $user->is_admin && request()->is('api/dashboard/auth/login')) {
                throw new AuthenticationException(__('Invalid credentials.'));
            }

            return true;
        });

        Gate::define('verify-email', static function (User $user, string $otp): bool {
            if (! $user->email_verification_otp || $user->email_verification_otp !== $otp) {
                throw new AuthorizationException(__('Invalid verification code'));
            }

            if ($user->email_verification_otp_expires_at && $user->email_verification_otp_expires_at->isPast()) {
                throw new AuthorizationException(__('Verification code has expired'));
            }

            if (! $user->markEmailAsVerified()) {
                throw new AuthorizationException(__('Failed to verify email'));
            }

            return true;
        });
    }

    private function bootModelsDefaults(): void
    {
        Model::unguard();
    }
}
