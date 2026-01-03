<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Data\BookingData;
use App\Http\Requests\BookingFilterRequest;
use App\Http\Requests\BookingStoreRequest;
use App\Http\Requests\BookingUpdateRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class BookingController
{
    public function __construct(private BookingService $bookingService) {}

    /**
     * @tags Dashboard
     */
    public function index(BookingFilterRequest $request): AnonymousResourceCollection
    {
        $bookings = Booking::getQuery()
            ->with(['user', 'driver'])
            ->paginate($request->get('perPage', 20));

        return BookingResource::collection($bookings)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @tags Dashboard
     *
     * @throws Throwable
     */
    public function store(BookingStoreRequest $request): JsonResponse
    {
        $booking = $this->bookingService->store(BookingData::from($request->validated()));

        return BookingResource::make($booking->load('user', 'driver', 'offer'))
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @tags Dashboard
     *
     * @throws Throwable
     */
    public function update(BookingUpdateRequest $request, Booking $booking): BookingResource
    {
        $updatedBooking = $this->bookingService->update(BookingData::from($request->validated()), $booking);

        return BookingResource::make($booking->load('user', 'driver', 'offer'))
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    /**
     * @tags Dashboard
     */
    public function show(Booking $booking): BookingResource
    {
        return BookingResource::make($booking->load('user', 'driver', 'offer'))
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }
}
