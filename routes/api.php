<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('login', [\App\Http\Controllers\API\AuthController::class, 'store'])->name('login.api');
Route::post('register', [\App\Http\Controllers\API\RegisterController::class, 'store'])->name('register.api');

Route::middleware('auth:api')->group(function (){
    Route::post('me', [\App\Http\Controllers\API\AuthController::class, 'me'])->name('me');

    Route::get('charts', [\App\Http\Controllers\API\ExchangeRateController::class, 'charts']);
    Route::get('rates', [\App\Http\Controllers\API\ExchangeRateController::class, 'index'])->name('rates.index');
    Route::get('rates/show', [\App\Http\Controllers\API\ExchangeRateController::class, 'show'])->name('rates.show');
    Route::post('logout', [\App\Http\Controllers\API\AuthController::class, 'destroy'])->name('logout.api');

});
