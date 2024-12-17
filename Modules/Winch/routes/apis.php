<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Middleware\ValidateHeaders;
use Modules\Winch\Controllers\WalletController;
use Modules\Winch\Controllers\WinchController;

Route::middleware(ValidateHeaders::class)->group(function () {

    Route::prefix('api/v1/winch')->group(function () {
        Route::controller(WinchController::class)->group(function () {
            Route::get('start', 'appStart');
            Route::get('sliders', 'homeSliders');
            Route::get('register-requirements', 'registerRequirements');
            Route::post('register', 'register');
            Route::post('login', 'login');

            Route::get('product/list-brands/{type}', 'listBrands');

            Route::middleware(['auth:sanctum'])->group(function () {
                Route::get('user-profile', 'userProfile');
                Route::post('update-location', 'updateLocation');

                Route::prefix('order')->group(function () {
                    Route::get('/', 'listOrders');
                    Route::get('/check', 'checkForOrders');
                    Route::get('/details', 'orderDetails');
                    Route::post('/send-offer', 'sendOffer');
                    Route::post('/reject-request', 'rejectRequest');
                    Route::post('/update-status', 'updateOrderStatus');
                });
            });
        });

        Route::prefix('wallet')
            ->controller(WalletController::class)
            ->middleware(['auth:sanctum'])
            ->group(function () {
                Route::get('/transactions', 'listTransactions');
                Route::get('/withdraw/methods', 'listWithdrawMethods');
                Route::get('/withdraw', 'listWithdrawRequests');
                Route::post('/withdraw', 'createWithdrawRequests');
            });
    });
});
