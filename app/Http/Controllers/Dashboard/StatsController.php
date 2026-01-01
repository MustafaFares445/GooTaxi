<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\StatsAction;
use App\Enums\ResponseMessages;
use App\Http\Resources\StatsResource;
use Illuminate\Http\JsonResponse;

final readonly class StatsController
{
    public function __construct(private StatsAction $statsAction) {}

    /**
     * @tags Dashboard
     */
    public function __invoke() : StatsResource
    {
        $stats = $this->statsAction->handle();

        return StatsResource::make($stats)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }
}
