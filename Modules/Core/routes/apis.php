<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Controllers\Auth\RegisterController;
use Modules\Core\Middleware\ValidateHeaders;

Route::middleware(ValidateHeaders::class)->prefix('api/v1')->group(function(){

    Route::prefix('auth')->group(function(){

        Route::controller(RegisterController::class)->group(function(){
            Route::post('send-otp', 'sendOTP');
        });
    });

});