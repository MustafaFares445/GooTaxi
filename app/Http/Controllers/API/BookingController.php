<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\BookingData;
use App\Http\Requests\BookingFilterRequest;
use App\Http\Requests\UserBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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
}
