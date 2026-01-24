<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Data\FeedbackData;
use App\Enums\ResponseMessages;
use App\Http\Requests\FeedbackFilterRequest;
use App\Http\Requests\FeedbackRequest;
use App\Http\Resources\FeedbackResource;
use App\Models\Feedback;
use App\Services\FeedbackService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Validation\ValidationException;
use Throwable;

final class FeedbackController
{
    public function __construct(private FeedbackService $feedbackService) {}

    /**
     * @tags Dashboard
     */
    public function index(FeedbackFilterRequest $request): AnonymousResourceCollection
    {
        return FeedbackResource::collection(
            Feedback::query()->latest()->paginate($request->get('perPage', 20))
        )
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Submit feedback
     *
     * This endpoint allows authenticated users to submit feedback about their experience.
     * The feedback is stored and can be viewed by administrators.
     *
     * @operation storeFeedback
     *
     * @tags API
     *
     * @throws ValidationException 422 Invalid feedback data
     * @throws Throwable
     */
    public function store(FeedbackRequest $request): FeedbackResource
    {
        $feedback = $this->feedbackService->store(FeedbackData::from($request->validated()));

        return FeedbackResource::make($feedback)
            ->additional(['message' => ResponseMessages::CREATED->message()]);
    }

    /**
     * @tags Dashboard
     */
    public function destroy(Feedback $feedback): HttpResponse
    {
        $feedback->delete();

        return response()->noContent();
    }
}
