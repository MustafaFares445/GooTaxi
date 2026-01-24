<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Feedback;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

final class FeedbackData extends Data
{
    use HasModelAttributes;

    /** @var class-string<Feedback> */
    protected static string $model = Feedback::class;

    public function __construct(
        #[Max(255)]
        public ?string $fullName = null,
        #[Email, Max(254)]
        public ?string $email = null,
        #[Max(255)]
        public ?string $phone = null,
        public ?string $message = null,
    ) {}
}
