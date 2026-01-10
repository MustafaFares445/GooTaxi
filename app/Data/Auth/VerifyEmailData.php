<?php

declare(strict_types=1);

namespace App\Data\Auth;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

final class VerifyEmailData extends Data
{
    public function __construct(
        public int $id,
        #[Max(255)]
        public string $hash,
    ) {}
}
