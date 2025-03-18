<?php

use App\Http\Controllers\SocialMediaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('/social')->group(function () {
    Route::get('/', [SocialMediaController::class, 'list']);
    Route::put('/', [SocialMediaController::class, 'create']);
    Route::get('/{id}', [SocialMediaController::class, 'details']);
    Route::patch('/{id}', [SocialMediaController::class, 'update']);
    Route::delete('/{id}', [SocialMediaController::class, 'delete']);
    Route::get('/{id}/posts', [SocialMediaController::class, 'posts']);
});