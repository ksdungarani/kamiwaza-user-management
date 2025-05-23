<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;


Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'loginAuth']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class)->except(['store', 'loginAuth']);
    Route::post('/logout', [UserController::class, 'logout']);
});
