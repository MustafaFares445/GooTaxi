<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\FeedbackData;
use App\Models\Feedback;
use Illuminate\Support\Facades\DB;
use Throwable;

final class FeedbackService
{
    /**
     * @throws Throwable
     */
    public function store(FeedbackData $data): Feedback
    {
        return DB::transaction(static function () use ($data) {
            return Feedback::create($data->onlyModelAttributes());
        });
    }
}
