<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Data\OfferData;
use App\Http\Requests\OfferFilterRequest;
use App\Http\Requests\OfferStoreRequest;
use App\Http\Requests\OfferUpdateRequest;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use App\Services\OfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class OfferController
{
    public function __construct(private OfferService $offerService) {}

    public function index(OfferFilterRequest $request): AnonymousResourceCollection
    {
        $offers = Offer::getQuery()
            ->paginate($request->get('perPage', 20));

        return OfferResource::collection($offers)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @throws Throwable
     */
    public function store(OfferStoreRequest $request): JsonResponse
    {
        $offer = $this->offerService->store(OfferData::from($request->validated()));

        return OfferResource::make($offer->load('bookings'))
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Offer $offer): OfferResource
    {

        return OfferResource::make($offer->load('bookings'))
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @throws Throwable
     */
    public function update(OfferUpdateRequest $request, Offer $offer): OfferResource
    {
        $updatedOffer = $this->offerService->update(OfferData::from($request->validated()), $offer);

        return OfferResource::make($updatedOffer->load('bookings'))
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    public function destroy(Offer $offer): OfferResource
    {
        $offer->delete();

        return OfferResource::make($offer)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}
