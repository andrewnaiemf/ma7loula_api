<?php

namespace Modules;

use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(\Modules\Core\Providers\CoreServiceProvider::class);
        $this->app->register(\Modules\Client\Providers\ClientServiceProvider::class);
        $this->app->register(\Modules\Vendor\Providers\VendorServiceProvider::class);
        $this->app->register(\Modules\Winch\Providers\WinchServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
