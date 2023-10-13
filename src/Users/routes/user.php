<?php

use Esca7a\Verification\Users\Controllers\AuthController;

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/phone_request', [AuthController::class, 'phoneRequest'])->name('phone_request');
    Route::post('/phone_confirm', [AuthController::class, 'phoneConfirm'])->name('phone_request_confirm');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum'])->name('logout');
});