<?php

use Illuminate\Support\Facades\Route;
use Modules\Client\Controllers\AddressController;
use Modules\Client\Controllers\CarController;
use Modules\Client\Controllers\HomeController;
use Modules\Client\Controllers\OrderController;
use Modules\Client\Controllers\ProductCategoryController;
use Modules\Client\Controllers\ProductController;
use Modules\Core\Middleware\ValidateHeaders;

Route::middleware(ValidateHeaders::class)->prefix('api/v1/client')->group(function () {

    Route::controller(HomeController::class)->group(function () {
        Route::get('start', 'AppStart');
    });


    Route::prefix("car-parts")->group(function () {
        Route::get('categories', [ProductCategoryController::class, 'list']);
        Route::get('products', [ProductController::class, 'list']);

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('list-orders', [OrderController::class, 'listCarPartsOrder']);
            Route::get('order-details', [OrderController::class, 'orderDetails']);
            Route::post('create-order', [OrderController::class, 'createCarPartsOrder']);
        });
    });


    Route::controller(HomeController::class)->group(function () {
        Route::get('sliders', 'HomeSliders');
    });


    Route::middleware(['auth:sanctum'])->group(function () {

        Route::controller(AddressController::class)->prefix("address")->group(function () {
            Route::get('list', 'list');
            Route::post('create', 'create');
            Route::put('update', 'update');
            Route::delete('delete', 'delete');
            Route::post('set-default', 'setDefault');
        });

        Route::controller(CarController::class)->prefix("car")->group(function () {
            Route::post('add', 'addCar');
            Route::get('list', 'listCars');
            Route::delete('delete', 'deleteCar');
            Route::post('set-default', 'setDefault');
        });


    });
});
