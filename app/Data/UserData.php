<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
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
        public string $name,
        #[Max(255), Unique('users', 'email')]
        public string $email,
        #[Max(255)]
        public ?string $phoneNumber,
        public int $isAdmin,
        #[Date]
        public ?string $emailVerifiedAt,
        #[Max(255)]
        public string $password,
        #[Max(100)]
        public ?string $rememberToken,
        #[File]
        public ?UploadedFile $primaryImage,
        #[File]
        public ?array $images,
        #[Exists('bookings', 'id')]
        public ?int $userId,
        #[Exists('media', 'id')]
        public ?int $modelId,
        public ?string $modelType
    ) {}
}
