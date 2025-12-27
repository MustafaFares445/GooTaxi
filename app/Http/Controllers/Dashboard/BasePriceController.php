<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Data\BasePriceData;
use App\Http\Requests\BasePriceUpdateRequest;
use App\Http\Resources\BasePriceResource;
use App\Models\BasePrice;
use App\Services\BasePriceService;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Throwable;

final readonly class BasePriceController
{
    public function __construct(private BasePriceService $basePriceService) {}

    public function index(): BasePriceResource
    {
        $basePrices = BasePrice::query()->first();

        return BasePriceResource::make($basePrices)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @throws Throwable
     */
    public function update(BasePriceUpdateRequest $request, BasePrice $basePrice): BasePriceResource
    {
        $updatedBasePrice = $this->basePriceService->update(BasePriceData::from($request->validated()), $basePrice);

        return BasePriceResource::make($updatedBasePrice)
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }
}
