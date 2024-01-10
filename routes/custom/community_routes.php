<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    Route::post('createCommunity', [\App\Http\Controllers\CommunityController::class, 'createCommunity']);
    Route::post('getCommunityTotals', [\App\Http\Controllers\CommunityController::class, 'getCommunityTotals']);
    Route::get("getCommunityDeliveries", [\App\Http\Controllers\CommunityController::class, 'getCommunityDeliveries']);
});

Route::get('getCommunitysByPage', [\App\Http\Controllers\CommunityController::class, 'getCommunitysByPage']);
Route::get('getCommunityDetails', [\App\Http\Controllers\CommunityController::class, 'getCommunityDetails']);
Route::get('getAllAvailableCommunities', [\App\Http\Controllers\CommunityController::class, 'getAllAvailableCommunities']);
