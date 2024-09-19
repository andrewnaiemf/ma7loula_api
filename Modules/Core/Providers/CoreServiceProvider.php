<?php

namespace Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Core\Exceptions\HttpErrorException;
class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(InterfaceServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/apis.php');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'Core');


        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(3, 1)->by($request->ip())->response(function (Request $request, array $headers) {
                $time = Carbon::now()->addSeconds($headers['Retry-After']??'5');
                throw new HttpErrorException(trans('Core::messages.auth.throttle', ['time' => $time->diffForHumans()]));
            });;
        });

        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinute(1, 1)->by($request->input('phone')??$request->ip())->response(function (Request $request, array $headers) {
                $time = Carbon::now()->addSeconds($headers['Retry-After']??'5');
                throw new HttpErrorException(trans('Core::messages.auth.throttle', ['time' => $time->diffForHumans()]));
            });;
        });
    }
}
