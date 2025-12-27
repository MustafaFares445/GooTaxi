<?php

declare(strict_types=1);

namespace App\Data\Auth;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

final class ResetPasswordData extends Data
{
    public function __construct(
        #[Email, Max(191)]
        public string $email,
        public string $token,
        #[Min(8), Max(191)]
        public string $password,
    ) {}
}
