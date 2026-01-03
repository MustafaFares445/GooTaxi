<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\ContactData;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Throwable;

final class ContactService
{
    /**
     * Update contact data
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function update(ContactData $data, Contact $contact): Contact
    {
        return DB::transaction(static function () use ($data, $contact) {
            tap($contact)->update($data->onlyModelAttributes());

            return $contact;
        });
    }
}
