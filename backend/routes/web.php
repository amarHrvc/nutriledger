<?php

use App\Http\Controllers\VantageLoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/vantage-login', [VantageLoginController::class, 'show'])->name('vantage.login');
Route::post('/vantage-login', [VantageLoginController::class, 'login']);
Route::get('/vantage-logout', [VantageLoginController::class, 'logout'])->name('vantage.logout');
