<?php

namespace App\Http\Middleware;

use Closure;

class History
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        return $response->header('Cache-Control', 'no-cache, no-store')
                        ->header('Pragma', 'no-cache')
                        ->header('Expires', '0');
    }
}
