<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Actions\ReturnNearestAdditionalPriceAction;
use App\Http\Requests\ReturnNearestAdditionalPriceRequest;
use App\Http\Resources\AdditionalPriceMiniResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

final readonly class ReturnNearestAdditionalPriceController
{
    public function __invoke(ReturnNearestAdditionalPriceRequest $request, ReturnNearestAdditionalPriceAction $action): JsonResource|JsonResponse
    {
        $additionalPrice = $action->handle((float) $request->latitude, (float) $request->longitude);

        if ($additionalPrice === null) {
            return response()->json([
                'data' => null,
                'message' => 'No additional price found',
            ]);
        }

        return AdditionalPriceMiniResource::make($additionalPrice)
            ->additional(['message' => 'Nearest additional price retrieved successfully']);
    }
}
