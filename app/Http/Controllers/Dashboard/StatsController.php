<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\Dashboard\StatsAction;
use App\Enums\ResponseMessages;
use Illuminate\Http\JsonResponse;

final class StatsController
{
    public function __construct(private StatsAction $statsAction) {}

    public function __invoke(): JsonResponse
    {
        $stats = $this->statsAction->handle();

        return response()->json([
            'data' => $stats->toArray(),
            'message' => ResponseMessages::RETRIEVED->message(),
        ]);
    }
}
