<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseMessages;
use App\Http\Requests\FeedbackRequest;
use App\Http\Resources\FeedbackResource;
use App\Models\Feedback;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class FeedbackController
{
    /**
     * @tags Dashboard
     */
    public function index(): AnonymousResourceCollection
    {
        return FeedbackResource::collection(Feedback::query()->latest()->paginate(request()->get('perPage')))
            ->additional(['message' => __(ResponseMessages::RETRIEVED->message())]);
    }

    /**
     * @tags API
     */
    public function store(FeedbackRequest $request): FeedbackResource
    {
        return FeedbackResource::make(Feedback::create($request->validated()))
            ->additional(['message' => __(ResponseMessages::CREATED->message())]);
    }

    /**
     * @tags Dashboard
     */
    public function destroy(Feedback $feedback): FeedbackResource
    {
        $feedback->delete();

        return FeedbackResource::make($feedback)
            ->additional(['message' => __(ResponseMessages::DELETED->message())]);
    }
}
