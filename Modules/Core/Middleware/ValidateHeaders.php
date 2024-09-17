<?php

namespace Modules\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Exceptions\HttpErrorException;
use Symfony\Component\HttpFoundation\Response;

class ValidateHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('accept', 'application/json');

        if ($request->header('app') == null) {
            throw new HttpErrorException('App header is required');
        }

        if ($request->header('lang') == null) {
            throw new HttpErrorException('Lang header is required');
        }else{
            app()->setLocale($request->header('lang'));
        }

        return $next($request);
    }
}
