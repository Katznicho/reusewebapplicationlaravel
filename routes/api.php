<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    include_once __DIR__.'/custom/auth_routes.php';
    include_once __DIR__.'/custom/donator_routes.php';
    include_once __DIR__.'/custom/community_routes.php';
    include_once __DIR__.'/custom/payment_routes.php';
    include_once __DIR__.'/custom/product_routes.php';
});
