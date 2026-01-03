<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

final class UserData extends Data
{
    use HasModelAttributes;

    /** @var class-string<User> */
    protected static string $model = User::class;

    public function __construct(
        #[Max(255)]
        public ?string $name = null,
        #[Max(255), Unique('users', 'email')]
        public ?string $email = null,
        #[Max(255)]
        public ?string $phoneNumber = null,
        public ?int $isAdmin = null,
        #[Date]
        public ?string $emailVerifiedAt = null,
        #[Max(255)]
        public ?string $password = null,
        #[Max(100)]
        public ?string $rememberToken = null,
        #[File]
        public ?UploadedFile $primaryImage = null,
        #[File]
        public ?array $images = null,
    ) {}
}
