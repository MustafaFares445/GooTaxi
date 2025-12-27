<?php

declare(strict_types=1);

namespace App\Data\Auth;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

final class LoginData extends Data
{
    public function __construct(
        #[Email, Max(191)]
        public string $email,
        #[Max(191)]
        public string $password,
    ) {}
}
