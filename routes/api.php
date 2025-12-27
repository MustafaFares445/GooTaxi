<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\CheckOfferController;
use App\Http\Controllers\API\BookingPriceController;
use App\Http\Controllers\API\ReturnNearestAdditionalPriceController;
use App\Http\Controllers\Dashboard\FeedbackController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

Route::get('/booking/price', BookingPriceController::class);
Route::apiResource('/booking', BookingController::class)->only(['index', 'show', 'store']);
Route::post('/nearest-additional-price', ReturnNearestAdditionalPriceController::class)->middleware('auth:sanctum');
Route::post('/check-offer', CheckOfferController::class)->middleware('auth:sanctum');
Route::post('/feedback', FeedbackController::class)->middleware('auth:sanctum');
