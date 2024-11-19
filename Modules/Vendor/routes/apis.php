<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Middleware\ValidateHeaders;
use Modules\Vendor\Controllers\BTCarController;
use Modules\Vendor\Controllers\BTVendorController;
use Modules\Vendor\Controllers\CarPartsVendorController;

Route::middleware(ValidateHeaders::class)->group(function () {

    Route::prefix('api/v1/bt-vendor')->group(function () {
        Route::controller(BTVendorController::class)->group(function () {
            Route::get('start', 'appStart');
            Route::get('sliders', 'homeSliders');
            Route::get('register-requirements', 'registerRequirements');
            Route::post('register', 'register');
            Route::post('login', 'login');

            Route::get('product/list-brands/{type}', 'listBrands');

            Route::middleware(['auth:sanctum'])->group(function () {
                Route::get('user-profile', 'userProfile');


                Route::prefix('product')->group(function () {
                    Route::get('/', 'productDetails');
                    Route::get('/list/{type}/{status}', 'listProducts');
                    Route::post('/add-battery', 'addBattery');
                    Route::post('/add-tire', 'addTire');
                });

                Route::prefix('order')->group(function () {
                    Route::get('/details', 'orderDetails');
                    Route::get('/{status}', 'listOrders');
                    Route::post('/update-status', 'updateOrderStatus');
                });
            });
        });
    });

    Route::prefix('api/v1/car-parts-vendor')->group(function () {
        Route::controller(CarPartsVendorController::class)->group(function () {
            Route::get('start', 'appStart');
            Route::get('sliders', 'homeSliders');
            Route::get('register-requirements', 'registerRequirements');
            Route::post('register', 'register');
            Route::post('login', 'login');

            Route::get('product/list-brands/{type}', 'listBrands');

            Route::middleware(['auth:sanctum'])->group(function () {
                Route::get('user-profile', 'userProfile');


                Route::prefix('product')->group(function () {
                    Route::get('/', 'productDetails');
                    Route::get('/list/{status}', 'listProducts');
                    Route::post('/add-product', 'addProduct');
                });

                Route::prefix('order')->group(function () {
                    Route::get('/details', 'orderDetails');
                    Route::get('/{status}', 'listOrders');
                    Route::post('/update-status', 'updateOrderStatus');
                });
            });
        });
    });

    Route::prefix('api/v1/bt-car')->group(function () {
        Route::controller(BTCarController::class)->group(function () {
            Route::get('start', 'AppStart');
            Route::get('register-requirements', 'registerRequirements');
        });
    });
});
