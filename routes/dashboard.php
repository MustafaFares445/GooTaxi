<?php

declare(strict_types=1);

use App\Http\Controllers\API\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

Route::middleware(['auth:sanctum', 'admin-only'])->group(function () {
    Route::apiResource('/users', App\Http\Controllers\Dashboard\UserController::class);

    Route::apiResource('/additional_prices', App\Http\Controllers\Dashboard\AdditionalPriceController::class);

    Route::apiResource('/base_prices', App\Http\Controllers\Dashboard\BasePriceController::class);

    Route::apiResource('/contacts', App\Http\Controllers\Dashboard\ContactController::class)->only(['index' , 'show']);

    Route::apiResource('/bookings', App\Http\Controllers\Dashboard\BookingController::class)->except(['update' , 'destroy']);

    Route::apiResource('/drivers', App\Http\Controllers\Dashboard\DriverController::class);

    Route::apiResource('/offers', App\Http\Controllers\Dashboard\OfferController::class);

    Route::apiResource('/time_ranges', App\Http\Controllers\Dashboard\TimeRangeController::class);
    Route::get('/stats', App\Http\Controllers\Dashboard\StatsController::class);
});
