<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseMessages;
use App\Http\Requests\FeedbackRequest;
use App\Http\Resources\FeedbackResource;
use App\Models\Feedback;

class FeedbackController
{
    public function index()
    {
        return FeedbackResource::collection(Feedback::paginate(request()->get('perPage')))
            ->additional(['message' => __(ResponseMessages::RETRIEVED->message())]);
    }

    public function store(FeedbackRequest $request)
    {
        return FeedbackResource::make(Feedback::create($request->validated()))
            ->additional(['message' => __(ResponseMessages::CREATED->message())]);
    }
}
