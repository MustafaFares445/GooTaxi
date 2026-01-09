<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use Throwable;
use App\Models\Booking;
use App\Data\BookingData;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BookingResource;
use App\Http\Requests\UserBookingRequest;
use App\Http\Requests\BookingFilterRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\UserBookingUpdateRequest;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final readonly class BookingController
{
    public function __construct(private BookingService $bookingService) {}

    /**
     * @tags API
     */
    public function index(BookingFilterRequest $request): AnonymousResourceCollection
    {
        $bookings = Booking::getQuery()
            ->where('user_id', Auth::id())
            ->paginate($request->get('perPage', 20));

        return BookingResource::collection($bookings)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @tags API
     *
     * @throws Throwable
     */
    public function store(UserBookingRequest $request): JsonResponse
    {
        $booking = $this->bookingService->store(
            BookingData::from($request->validated() + ['userId' => Auth::id()]),
            $request->validated('couponCode')
        );

        return BookingResource::make($booking->load('driver', 'offer'))
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @tags API
     */
    public function show(Booking $booking): BookingResource
    {
        return BookingResource::make($booking->load('driver', 'offer'))
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @tags API
     *
     * @throws Throwable
     */
    public function update(UserBookingUpdateRequest $request, Booking $booking): BookingResource
    {
        $booking = $this->bookingService->update(BookingData::from($request->validated()), $booking);

        return BookingResource::make($booking->load('driver', 'offer'))
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }
}
