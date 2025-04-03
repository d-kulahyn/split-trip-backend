<?php

use Illuminate\Support\Facades\Route;
use App\Infrastrstructure\API\Controllers\NotificationController;

Route::prefix('notifications')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/debt-reminder/{debt}', [NotificationController::class, 'debtReminder']);
    });
});
