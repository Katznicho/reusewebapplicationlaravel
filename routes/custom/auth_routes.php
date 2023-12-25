<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('verifyEmail', [AuthController::class, 'verifyEmail']);
    Route::post('resendOTP', [AuthController::class, 'resendOTP']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('requestPasswordReset', [AuthController::class, 'requestPasswordReset']);
    Route::post('resetPassword', [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('createUserProfile', [AuthController::class, 'createUserProfile']);
        Route::post('updateUserProfile', [AuthController::class, 'updateUserProfile']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('changePassword', [AuthController::class, 'changePassword']);
        Route::post('updateAvatar', [AuthController::class, 'updateAvatar']);
    });
});
