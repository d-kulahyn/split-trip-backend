<?php


use App\Infrastrstructure\API\Controllers\DebtsController;
use App\Infrastrstructure\API\Controllers\GroupController;
use Illuminate\Support\Facades\Route;

Route::prefix('groups')->group(function () {
    Route::post('/', [GroupController::class, 'createGroup']);
    Route::put('/{group}', [GroupController::class, 'updateGroup'])->middleware('can:update,group');
    Route::delete('/{group}', [GroupController::class, 'delete'])->middleware('can:delete,group');
    Route::post('/{group}/avatar', [GroupController::class, 'uploadAvatar']);
    Route::put('/{group}/simplifyDebts', [GroupController::class, 'toggleSimplify'])->middleware('can:update,group');
    Route::get('/', [GroupController::class, 'list']);
    Route::get('/{group}/members', [GroupController::class, 'members']);
    Route::get('/{group}', [GroupController::class, 'view']);
    Route::post('/{group}/members', [GroupController::class, 'addMember']);
    Route::delete('/{group}/members', [GroupController::class, 'removeMember']);
    Route::post('/{group}/expenses', [GroupController::class, 'addExpense']);
});

Route::prefix('debts')->group(function () {
    Route::put('/{debt}', [DebtsController::class, 'update'])->middleware('can:update,debt');
});
