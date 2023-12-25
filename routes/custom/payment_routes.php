<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post("registerIPN", [PaymentController::class, "registerIPN"]);
    Route::get("listIPNS", [PaymentController::class, "listIPNS"]);
    Route::get("completePayment", [PaymentController::class, "completePayment"]);
    Route::post("processOrder", [PaymentController::class, "processOrder"]);

    Route::post("testSendingMessages", [PaymentController::class, "testSendingMessages"]);

    Route::post("checkTransactionStatus", [PaymentController::class, "checkTransactionStatus"]);
});
