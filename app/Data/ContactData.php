<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Contact;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

final class ContactData extends Data
{
    use HasModelAttributes;

    /** @var class-string<Contact> */
    protected static string $model = Contact::class;

    public function __construct(
        public ?string $phone = null,
        public ?string $whatsapp = null,
        #[Email, Max(254)]
        public ?string $email = null,
        public ?string $adress = null,
    ) {}
}
