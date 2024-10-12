<?php

use Illuminate\Support\Facades\Route;
use Modules\Client\Controllers\AddressController;
use Modules\Client\Controllers\BatteryController;
use Modules\Client\Controllers\CarController;
use Modules\Client\Controllers\HomeController;
use Modules\Client\Controllers\OrderController;
use Modules\Client\Controllers\ProductCategoryController;
use Modules\Client\Controllers\ProductController;
use Modules\Client\Controllers\TireController;
use Modules\Core\Middleware\ValidateHeaders;

Route::middleware(ValidateHeaders::class)->prefix('api/v1/client')->group(function () {

    Route::controller(HomeController::class)->group(function () {
        Route::get('start', 'AppStart');
    });


    Route::prefix("car-parts")->group(function () {
        Route::get('categories', [ProductCategoryController::class, 'list']);
        Route::get('products', [ProductController::class, 'list']);
        Route::get('product-details', [ProductController::class, 'productDetails']);
        Route::post('available-slots', [OrderController::class, 'getAvailableSlots']);

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('order-details', [OrderController::class, 'orderDetails']);
            Route::get('list-orders', [OrderController::class, 'listCarPartsOrder']);
            Route::post('create-order', [OrderController::class, 'createCarPartsOrder']);
            Route::post('update-order-status', [OrderController::class, 'updateOrderStatus']);
            Route::post('rate-order', [OrderController::class, 'rateOrder']);
        });
    });


    Route::prefix("tires")->group(function () {
        Route::get('brands', [TireController::class, 'listBrands']);
        Route::get('types', [TireController::class, 'listTypes']);
        Route::get('sizes', [TireController::class, 'listSizes']);
        Route::get('products', [TireController::class, 'listTires']);
        Route::get('product-details', [ProductController::class, 'productDetails']);
        Route::post('available-slots', [OrderController::class, 'getAvailableSlots']);
        
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('order-details', [OrderController::class, 'orderDetails']);
            Route::get('list-orders', [OrderController::class, 'listTiresOrder']);
            Route::post('create-order', [OrderController::class, 'createTiresOrder']);
            Route::post('update-order-status', [OrderController::class, 'updateOrderStatus']);
            Route::post('rate-order', [OrderController::class, 'rateOrder']);
        });
    });


    Route::prefix("batteries")->group(function () {
        Route::get('volatages', [BatteryController::class, 'listVolts']);
        Route::get('brands', [BatteryController::class, 'listBrands']);
        Route::get('products', [BatteryController::class, 'listBatteries']);
        Route::get('product-details', [ProductController::class, 'productDetails']);
        Route::post('available-slots', [OrderController::class, 'getAvailableSlots']);
        
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('order-details', [OrderController::class, 'orderDetails']);
            Route::get('list-orders', [OrderController::class, 'lisBatteryOrder']);
            Route::post('create-order', [OrderController::class, 'createBatteryOrder']);
            Route::post('update-order-status', [OrderController::class, 'updateOrderStatus']);
            Route::post('rate-order', [OrderController::class, 'rateOrder']);
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
