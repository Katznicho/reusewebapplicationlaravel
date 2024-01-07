<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    Route::post('createCommunity', [\App\Http\Controllers\CommunityController::class, 'createCommunity']);
    Route::get('getCommunityTotals', [\App\Http\Controllers\CommunityController::class, 'getCommunityTotals']);
});

Route::get('getCommunitysByPage', [\App\Http\Controllers\CommunityController::class, 'getCommunitysByPage']);
Route::get('getCommunityDetails', [\App\Http\Controllers\CommunityController::class, 'getCommunityDetails']);
