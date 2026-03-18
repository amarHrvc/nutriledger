<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => response()->json(['status' => 'ok']));
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/user/{id}', [UserController::class, 'show']);

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/test/admin-only', fn () => response()->json(['status' => 'ok']));
    });

    Route::middleware(['role:admin,doktor'])->group(function () {
        Route::get('/test/admin-doktor-only', fn () => response()->json(['status' => 'ok']));
        // Patients + Visits — 002-patients, 003-visits BE
    });

});
