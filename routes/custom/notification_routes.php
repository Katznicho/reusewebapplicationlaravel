<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('getUserNotifications', [NotificationController::class, 'getUserNotifications']);

});
