<?php

use App\Infrastrstructure\API\Controllers\ActivityController;
use App\Infrastrstructure\API\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use App\Infrastrstructure\API\Controllers\CurrencyController;

require base_path('routes/api/v1/frontend/auth.php');
require base_path('routes/api/v1/frontend/profile.php');
require base_path('routes/api/v1/frontend/notifications.php');

Route::middleware(['auth:sanctum'])->group(function () {
    require base_path('routes/api/v1/frontend/group.php');

    Route::get('currencies/{currency}/rates', [CurrencyController::class, 'rates']);
    Route::get('currencies/codes', [CurrencyController::class, 'codes']);
    Route::get('activities', [ActivityController::class, 'index']);
    Route::post('activities/status/batch', [ActivityController::class, 'statusBatch']);
    Route::get('transactions', [TransactionController::class, 'index']);
    Route::patch('transactions/{transaction}/status', [TransactionController::class, 'status']);
});

require base_path('routes/api/v1/common.php');
