<?php

namespace Modules\Vendor\Providers;

use Illuminate\Support\ServiceProvider;

class VendorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app->rout
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__."/../routes/apis.php");
    }
}
