<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PeopleController;
use App\Http\Controllers\Api\LikesController;

Route::prefix('v1')->group(function () {
    // ============ Public Auth Routes ============
    Route::controller(AuthController::class)->group(function () {
        Route::post('/auth/register', 'register');
        Route::post('/auth/login', 'login');
    });

    // ============ Protected Routes (require authentication) ============
    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::controller(AuthController::class)->group(function () {
            Route::get('/auth/me', 'getCurrentUser');
            Route::post('/auth/logout', 'logout');
        });

        // People endpoints
        Route::get('/people', [PeopleController::class, 'index']);
        Route::get('/people/{person}', [PeopleController::class, 'show']);

        // Likes endpoints
        Route::post('/likes/like', [LikesController::class, 'like']);
        Route::post('/likes/dislike', [LikesController::class, 'dislike']);
        Route::get('/likes/liked-people', [LikesController::class, 'likedPeople']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
