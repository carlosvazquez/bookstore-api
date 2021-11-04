<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\PingController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\UserController;
use CloudCreativity\LaravelJsonApi\Facades\JsonApi;
use Illuminate\Support\Facades\Route;

JsonApi::register('v1')->routes(function ($api) {
    Route::get('ping', [PingController::class, 'ping'])
        ->middleware('guest:sanctum')
        ->name('ping');

    $api->resource('books')->relationships(function ($api) {
        $api->hasOne('authors');
        $api->hasOne('categories');
    });

    $api->resource('authors')->only('index', 'read')->relationships(function ($api) {
        $api->hasMany('books')->except('replace', 'add', 'remove');
    });

    $api->resource('categories')->relationships(function ($api) {
        $api->hasMany('books')->except('replace', 'add', 'remove');
    });

    Route::post('login', [LoginController::class, 'login'])
        ->middleware('guest:sanctum')
        ->name('login');

    Route::post('logout', [LoginController::class, 'logout'])
        ->middleware('auth:sanctum')
        ->name('logout');

    Route::post('register', [RegisterController::class, 'register'])
        ->middleware('guest:sanctum')
        ->name('register');

    Route::get('user', UserController::class)
        ->middleware('auth:sanctum')
        ->name('user');
});
