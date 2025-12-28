<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Data\AdditionalPriceData;
use App\Http\Requests\AdditionalPriceFilterRequest;
use App\Http\Requests\AdditionalPriceStoreRequest;
use App\Http\Requests\AdditionalPriceUpdateRequest;
use App\Http\Resources\AdditionalPriceResource;
use App\Models\AdditionalPrice;
use App\Services\AdditionalPriceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class AdditionalPriceController
{
    public function __construct(private AdditionalPriceService $additionalPriceService) {}

    /**
     * @tags Dashboard
     */
    public function index(AdditionalPriceFilterRequest $request): AnonymousResourceCollection
    {
        $additionalPrices = AdditionalPrice::getQuery()
            ->paginate($request->get('perPage', 20));

        return AdditionalPriceResource::collection($additionalPrices)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @tags Dashboard
     *
     * @throws Throwable
     */
    public function store(AdditionalPriceStoreRequest $request): JsonResponse
    {
        $additionalPrice = $this->additionalPriceService->store(AdditionalPriceData::from($request->validated()));

        return AdditionalPriceResource::make($additionalPrice)
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @tags Dashboard
     */
    public function show(AdditionalPrice $additionalPrice): AdditionalPriceResource
    {
        return AdditionalPriceResource::make($additionalPrice)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @tags Dashboard
     *
     * @throws Throwable
     */
    public function update(AdditionalPriceUpdateRequest $request, AdditionalPrice $additionalPrice): AdditionalPriceResource
    {
        $updatedAdditionalPrice = $this->additionalPriceService->update(AdditionalPriceData::from($request->validated()), $additionalPrice);

        return AdditionalPriceResource::make($updatedAdditionalPrice)
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    /**
     * @tags Dashboard
     */
    public function destroy(AdditionalPrice $additionalPrice): AdditionalPriceResource
    {
        $additionalPrice->delete();

        return AdditionalPriceResource::make($additionalPrice)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}
