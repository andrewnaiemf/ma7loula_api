<?php

use Illuminate\Support\Facades\Route;
use Modules\Client\Controllers\Home\HomeController;
use Modules\Core\Middleware\ValidateHeaders;

Route::middleware(ValidateHeaders::class)->prefix('api/v1/client')->group(function(){

    Route::controller(HomeController::class)->group(function(){
        Route::get('start', 'AppStart');
    });


    Route::middleware(['auth:sanctum'])->group(function(){
        Route::controller(HomeController::class)->group(function(){
            Route::get('sliders', 'HomeSliders');
        });
    });

});