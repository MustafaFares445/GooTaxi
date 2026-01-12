<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\BookingData;
use App\Http\Requests\BookingFilterRequest;
use App\Http\Requests\UserBookingRequest;
use App\Http\Requests\UserBookingUpdateRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class BookingController
{
    public function __construct(private BookingService $bookingService) {}

    /**
     * Get paginated list of user bookings
     *
     * This endpoint retrieves a paginated list of bookings for the authenticated user.
     * Supports filtering by various criteria such as driver, location, date, time, status, and more.
     * Results can be sorted and paginated.
     *
     * @operation index
     *
     * @tags API
     *
     * @throws ValidationException 422 Invalid filter or pagination parameters
     */
    public function index(BookingFilterRequest $request): AnonymousResourceCollection
    {
        $bookings = Booking::getQuery()
            ->where('user_id', auth('sanctum')->id())
            ->paginate($request->get('perPage', 20));

        return BookingResource::collection($bookings)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Create a new booking
     *
     * This endpoint creates a new booking for the authenticated user. The booking includes
     * location details, date, time, distance, passengers, and optional coupon code for discounts.
     * The system calculates the final price based on base prices, additional prices, and applicable offers.
     *
     * @operation store
     *
     * @tags API
     *
     * @throws ValidationException 422 Invalid booking data, invalid coupon code, or validation failed
     * @throws Throwable 500 Internal server error during booking creation
     */
    public function store(UserBookingRequest $request): JsonResponse
    {
        $booking = $this->bookingService->store(
            BookingData::from($request->validated() + ['userId' => auth('sanctum')->id()]),
            $request->validated('couponCode')
        );

        return BookingResource::make($booking->load('driver', 'offer'))
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Get a specific booking details
     *
     * This endpoint retrieves detailed information about a specific booking.
     * The user can only access their own bookings.
     *
     * @operation show
     *
     * @tags API
     */
    public function show(Booking $booking): BookingResource
    {
        return BookingResource::make($booking->load('driver', 'offer'))
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Update an existing booking
     *
     * This endpoint allows the authenticated user to update their booking details.
     * Only the user who owns the booking can update it.
     *
     * @operation update
     *
     * @tags API
     *
     * @throws ValidationException 422 Invalid booking update data
     * @throws Throwable 500 Internal server error during booking update
     */
    public function update(UserBookingUpdateRequest $request, Booking $booking): BookingResource
    {
        $booking = $this->bookingService->update(BookingData::from($request->validated()), $booking);

        return BookingResource::make($booking->load('driver', 'offer'))
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }
}
