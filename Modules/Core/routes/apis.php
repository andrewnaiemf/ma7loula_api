<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Controllers\AddressController;
use Modules\Core\Controllers\Auth\AuthController;
use Modules\Core\Middleware\ValidateHeaders;

Route::middleware(ValidateHeaders::class)->prefix('api/v1')->group(function(){

    Route::prefix('auth')->group(function(){
        Route::controller(AuthController::class)->group(function(){
            Route::post('send-otp', 'sendOTP')->middleware(['throttle:otp']);
            Route::post('verify-otp', 'verifyOTP');
            Route::post('register', 'register');
            Route::post('login', 'login')->middleware(['throttle:login']);
            Route::post('reset-password', 'resetPassword');

            Route::middleware(['auth:sanctum'])->group(function(){
                Route::post('update-profile', 'updateProfile');
                Route::post('update-phone', 'updatePhone');
                Route::post('update-password', 'updatePassword');
            });
        });
    });

    Route::prefix('address')->group(function(){
        Route::controller(AddressController::class)->group(function(){
            Route::get('states', 'states');
            Route::get('cities', 'cities');
        });
    });
});