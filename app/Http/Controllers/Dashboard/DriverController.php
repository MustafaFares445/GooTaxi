<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use Throwable;
use App\Models\Driver;
use App\Data\DriverData;
use App\Services\DriverService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\DriverResource;
use App\Http\Requests\DriverStoreRequest;
use App\Http\Requests\DriverFilterRequest;
use App\Http\Requests\DriverUpdateRequest;
use App\Http\Resources\DriverSummaryResource;
use Symfony\Component\HttpFoundation\Response;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class DriverController
{
    public function __construct(private DriverService $driverService) {}

    /**
     * @tags Dashboard
     */
    public function index(DriverFilterRequest $request): AnonymousResourceCollection
    {
        $drivers = Driver::getQuery()
            ->paginate($request->get('perPage', 20));

        return DriverResource::collection($drivers)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @tags Dashboard
     */
    public function summary(DriverFilterRequest $request): AnonymousResourceCollection
    {
        $drivers = Driver::getQuery()
            ->select(['id' , 'name'])
            ->get();

        return DriverSummaryResource::collection($drivers)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @tags Dashboard
     *
     * @throws Throwable
     */
    public function store(DriverStoreRequest $request): JsonResponse
    {
        $driver = $this->driverService->store(DriverData::from($request->validated()));

        return DriverResource::make($driver->load('bookings'))
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @tags Dashboard
     */
    public function show(Driver $driver): DriverResource
    {

        return DriverResource::make($driver->load('bookings'))
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @tags Dashboard
     *
     * @throws Throwable
     */
    public function update(DriverUpdateRequest $request, Driver $driver): DriverResource
    {
        $updatedDriver = $this->driverService->update(DriverData::from($request->validated()), $driver);

        return DriverResource::make($updatedDriver->load('bookings'))
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    /**
     * @tags Dashboard
     */
    public function destroy(Driver $driver): DriverResource
    {
        $driver->delete();

        return DriverResource::make($driver)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}
