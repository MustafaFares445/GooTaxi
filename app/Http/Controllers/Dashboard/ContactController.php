<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Contact;
use App\Http\Requests\ContactRequest;
use App\Http\Resources\ContactResource;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;

class ContactController
{
    public function index(): ContactResource
    {
        return ContactResource::make(Contact::query()->first())
          ->additional(['message' => __(ResponseMessages::RETRIEVED->message())]);;
    }

    public function update(ContactRequest $request, Contact $contact): ContactResource
    {
        $contact->update($request->validated());

        return ContactResource::make($contact)
              ->additional(['message' => __(ResponseMessages::UPDATED->message())]);;
    }
}
