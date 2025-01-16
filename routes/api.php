<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('auth')
        ->controller(AuthController::class)
        ->group(function () {
            Route::post('register', 'register');
            Route::post('login', 'login');
            Route::post('logout', 'logout')->middleware('auth:sanctum');
        });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('posts')
            ->controller(PostController::class)
            ->group(function () {
                Route::post('/', 'index');
                Route::delete('{post}', 'destroy');
                Route::delete('{post}', 'show');
                Route::post('/', 'store');
            });

        Route::prefix('users')
            ->controller(UserController::class)
            ->group(function () {
                Route::post('/', 'index');
                Route::delete('{post}', 'show');
            });
    });
});
