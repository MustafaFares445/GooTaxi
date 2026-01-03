<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Data\ContactData;
use App\Http\Requests\ContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Services\ContactService;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;

final readonly class ContactController
{
    public function __construct(private ContactService $contactService) {}

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
    public function update(ContactRequest $request, Contact $contact): ContactResource
    {
        $updatedContact = $this->contactService->update(ContactData::from($request->validated()), $contact);

        return ContactResource::make($updatedContact)
            ->additional(['message' => __(ResponseMessages::RETRIEVED->message())]);
    }
}
