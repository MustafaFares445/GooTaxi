<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Data\TimeRangeData;
use App\Enums\ResponseMessages;
use App\Http\Requests\TimeRangeStoreRequest;
use App\Http\Requests\TimeRangeUpdateRequest;
use App\Http\Resources\TimeRangeResource;
use App\Models\TimeRange;
use App\Services\TimeRangeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class TimeRangeController
{
    public function __construct(private TimeRangeService $timeRangeService) {}

    /**
     * @tags Dashboard
     */
    public function index(): AnonymousResourceCollection
    {
        $timeRanges = TimeRange::all();

        return TimeRangeResource::collection($timeRanges)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @tags Dashboard
     *
     * @throws Throwable
     */
    public function store(TimeRangeStoreRequest $request): JsonResponse
    {
        $timeRange = $this->timeRangeService->store(TimeRangeData::from($request->validated()));

        return TimeRangeResource::make($timeRange)
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @tags Dashboard
     */
    public function show(TimeRange $timeRange): TimeRangeResource
    {
        return TimeRangeResource::make($timeRange)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @tags Dashboard
     *
     * @throws Throwable
     */
    public function update(TimeRangeUpdateRequest $request, TimeRange $timeRange): TimeRangeResource
    {
        $updatedTimeRange = $this->timeRangeService->update(TimeRangeData::from($request->validated()), $timeRange);

        return TimeRangeResource::make($updatedTimeRange)
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    /**
     * @tags Dashboard
     */
    public function destroy(TimeRange $timeRange): HttpResponse
    {
        $timeRange->delete();

        return response()->noContent();
    }
}
