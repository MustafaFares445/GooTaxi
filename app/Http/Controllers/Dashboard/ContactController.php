<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Data\ContactData;
use App\Enums\ResponseMessages;
use App\Http\Requests\ContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Services\ContactService;

final readonly class ContactController
{
    public function __construct(private ContactService $contactService) {}

    /**
     * Get contact information
     *
     * This endpoint retrieves the contact information (phone, email, address, etc.)
     * that is displayed to users in the application.
     *
     * @operation getContacts
     *
     * @tags API
     */
    public function index(): ContactResource
    {
        return ContactResource::make(Contact::query()->first())
            ->additional(['message' => __(ResponseMessages::RETRIEVED->message())]);
    }

    /**
     * @tags Dashboard
     */
    public function update(ContactRequest $request, Contact $contact): ContactResource
    {
        $updatedContact = $this->contactService->update(ContactData::from($request->validated()), $contact);

        return ContactResource::make($updatedContact)
            ->additional(['message' => __(ResponseMessages::UPDATED->message())]);
    }
}
