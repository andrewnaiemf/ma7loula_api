<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Controllers\Auth\AuthController;
use Modules\Core\Controllers\Home\HomeController;
use Modules\Core\Middleware\ValidateHeaders;

Route::middleware(ValidateHeaders::class)->prefix('api/v1')->group(function(){

    Route::prefix('auth')->group(function(){
        Route::controller(AuthController::class)->group(function(){
            Route::post('send-otp', 'sendOTP')->middleware(['throttle:otp']);
            Route::post('register', 'register');
            Route::post('login', 'login')->middleware(['throttle:login']);
            Route::post('reset-password-otp', 'resetPasswordOTP')->middleware(['throttle:otp']);
            Route::post('reset-password', 'resetPassword');
        });
    });

});