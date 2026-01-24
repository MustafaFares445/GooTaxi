<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Actions\ReturnNearestAdditionalPriceAction;
use App\Enums\ResponseMessages;
use App\Http\Requests\ReturnNearestAdditionalPriceRequest;
use App\Http\Resources\AdditionalPriceMiniResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;

final readonly class ReturnNearestAdditionalPriceController
{
    public function __construct(
        private ReturnNearestAdditionalPriceAction $returnNearestAdditionalPriceAction
    ) {}

    /**
     * Get nearest additional price based on location
     *
     * This endpoint finds and returns the nearest additional price based on the provided
     * latitude and longitude coordinates. Returns null if no additional price is found
     * within the search radius.
     *
     * @operation getNearestAdditionalPrice
     *
     * @tags API
     *
     * @throws ValidationException 422 Invalid latitude or longitude coordinates
     */
    public function __invoke(ReturnNearestAdditionalPriceRequest $request): JsonResource|JsonResponse
    {
        $additionalPrice = $this->returnNearestAdditionalPriceAction->handle(
            (float) $request->validated('latitude'),
            (float) $request->validated('longitude')
        );

        if ($additionalPrice === null) {
            return response()->json([
                'data' => null,
                'message' => __('No additional price found.'),
            ]);
        }

        return AdditionalPriceMiniResource::make($additionalPrice)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }
}
