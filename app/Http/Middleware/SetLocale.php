<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $available_locales = ['ar', 'en'];

        if ($request->hasHeader('lang') && in_array($request->header('lang'), $available_locales)) {
            $locale = $request->header('lang');
            app()->setLocale($locale);
        }else{
            app()->setLocale('ar');
        }


        return $next($request);
    }
}