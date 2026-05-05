<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VisitController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => response()->json(['status' => 'ok', 'message' => 'ping', 'data' => []]));
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/user', [AuthController::class, 'me'])->name('user.me');

    Route::apiResource('users', UserController::class);

    Route::apiResource('patients', PatientController::class);

    Route::middleware(['role:admin'])->group(function () {
        Route::post('/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::delete('/users/{user}/force', [UserController::class, 'forceDelete'])->name('users.forceDelete');
        Route::get('/test/admin-only', fn () => response()->json(['message' => 'ok', 'status' => 200, 'data' => null]));
    });

    Route::middleware(['role:admin,doktor'])->group(function () {
        Route::apiResource('patients.visits', VisitController::class)
            ->only(['store', 'update']);
        Route::get('/test/admin-doktor-only', fn () => response()->json(['message' => 'ok', 'status' => 200, 'data' => null]));
        Route::get('/visits', [VisitController::class, 'globalIndex'])->name('visits.index');
    });

    Route::middleware('role:admin')->group(function () {
        Route::delete('/patients/{patient}/visits/{visit}', [VisitController::class, 'destroy'])
            ->where('visit', '[0-9]+')
            ->name('patients.visits.destroy');
    });

    // Patients can view their own visits (checked via policy)
    Route::get('/patients/{patient}/visits', [VisitController::class, 'index'])
        ->middleware('auth:sanctum')
        ->name('patients.visits.index');

    Route::get('/patients/{patient}/visits/{visit}', [VisitController::class, 'show'])
        ->middleware('auth:sanctum')
        ->where('visit', '[0-9]+')
        ->name('patients.visits.show');

});
