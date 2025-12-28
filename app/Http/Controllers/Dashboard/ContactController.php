<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;

final class ContactController
{
    /**
     * @tags Dashboard
     */
    public function index(): ContactResource
    {
        return ContactResource::make(Contact::query()->first())
            ->additional(['message' => __(ResponseMessages::RETRIEVED->message())]);
    }

    /**
     * @tags Dashboard
     */
    public function show(Contact $contact): ContactResource
    {
        return ContactResource::make($contact)
            ->additional(['message' => __(ResponseMessages::RETRIEVED->message())]);
    }
}
