<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => response()->json(['status' => 'ok', 'message' => 'ping', 'data' => []]));
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/user', [AuthController::class, 'me'])->name('user.me');

    Route::apiResource('users', UserController::class);

    Route::middleware(['role:admin'])->group(function () {
        Route::post('/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::delete('/users/{user}/force', [UserController::class, 'forceDelete'])->name('users.forceDelete');
        Route::get('/test/admin-only', fn () => response()->json(['message' => 'ok', 'status' => 200, 'data' => null]));
    });

    Route::middleware(['role:admin,doktor'])->group(function () {
        Route::get('/test/admin-doktor-only', fn () => response()->json(['message' => 'ok', 'status' => 200, 'data' => null]));
        // Patients + Visits — 002-patients, 003-visits BE
    });

});
