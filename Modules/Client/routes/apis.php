<?php

use Illuminate\Support\Facades\Route;
use Modules\Client\Controllers\AddressController;
use Modules\Client\Controllers\HomeController;
use Modules\Core\Middleware\ValidateHeaders;

Route::middleware(ValidateHeaders::class)->prefix('api/v1/client')->group(function(){

    Route::controller(HomeController::class)->group(function(){
        Route::get('start', 'AppStart');
    });


    Route::middleware(['auth:sanctum'])->group(function(){

        Route::controller(AddressController::class)->prefix("address")->group(function(){
            Route::get('list', 'list');
            Route::post('create', 'create');
            Route::put('update', 'update');
            Route::delete('delete', 'delete');
            Route::post('set-default', 'setDefault');
        });


        Route::controller(HomeController::class)->group(function(){
            Route::get('sliders', 'HomeSliders');
        });
    });

});