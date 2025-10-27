<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PeopleController;
use App\Http\Controllers\Api\LikesController;

Route::prefix('v1')->group(function () {
    // People endpoints
    Route::get('/people', [PeopleController::class, 'index']);
    Route::get('/people/{person}', [PeopleController::class, 'show']);

    // Likes endpoints
    Route::post('/likes/like', [LikesController::class, 'like']);
    Route::post('/likes/dislike', [LikesController::class, 'dislike']);
    Route::get('/likes/liked-people', [LikesController::class, 'likedPeople']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
