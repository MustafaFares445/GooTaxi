<?php

declare(strict_types=1);

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\BookingPriceController;
use App\Http\Controllers\API\CheckOfferController;
use App\Http\Controllers\API\ReturnNearestAdditionalPriceController;
use App\Http\Controllers\Dashboard\FeedbackController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('verify-email', [AuthController::class, 'verifyEmail']);
});

Route::get('/drivers/summary', [App\Http\Controllers\Dashboard\DriverController::class, 'summary']);
Route::get('contacts', [App\Http\Controllers\Dashboard\ContactController::class, 'index']);
Route::get('/booking/price', BookingPriceController::class);
Route::apiResource('/booking', BookingController::class)->only(['index', 'show', 'store']);
Route::post('/nearest-additional-price', ReturnNearestAdditionalPriceController::class);
Route::post('/check-offer', CheckOfferController::class);
Route::post('/feedback', [FeedbackController::class, 'store'])->middleware('auth:sanctum');
