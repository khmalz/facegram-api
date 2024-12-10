<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(["prefix" => "v1/auth"], function () {
    Route::post('/register', RegisteredUserController::class)
        ->middleware('guest')
        ->name('register');

    Route::post('/login', AuthenticatedSessionController::class)
        ->middleware('guest')
        ->name('login');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth:sanctum')
        ->name('logout');
});
