<?php

// Auth routes
use App\Infrastrstructure\API\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/confirm-email', [AuthController::class, 'confirmEmail']);
        Route::get('/user/me', [AuthController::class, 'me']);
        Route::post('/sendConfirmationCode', [AuthController::class, 'sendConfirmationCode']);
    });


//Unauthenticated auth routes
    Route::post('/social/{social}', [AuthController::class, 'social']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});
