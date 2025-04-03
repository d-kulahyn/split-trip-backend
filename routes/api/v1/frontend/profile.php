<?php

use Illuminate\Support\Facades\Route;
use App\Infrastrstructure\API\Controllers\ProfileController;

Route::prefix('profile')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::put('/', [ProfileController::class, 'update']);
        Route::put('/firebase-cloudMessaging-token', [ProfileController::class, 'setCloudMessagingToken']);
        Route::post('avatar', [ProfileController::class, 'uploadAvatar']);
        Route::put('email', [ProfileController::class, 'updateEmail']);
        Route::put('password', [ProfileController::class, 'updatePassword']);
        Route::put('telephone', [ProfileController::class, 'updateTelephone']);
        Route::get('/{user:login}/info', [ProfileController::class, 'info']);
    });
});
