<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get("getUserProducts", [ProductController::class, "getUserProducts"]);
    Route::get("getUserDelivries", [ProductController::class, "getUserDelivries"]);
    Route::post("createProduct", [ProductController::class, "createProduct"]);
});
