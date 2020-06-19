<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\URL;
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
        if (isset($_SERVER['HTTP_REFERER'])) {
            $response = $next($request);
            //히스토리 해더에 캐쉬불가능을 추가
            return $response->header('Cache-Control', 'no-cache, no-store') //1.1
                            ->header('Pragma', 'no-cache')  //1.0
                            ->header('Expires', '0'); //1.0
        } else {
            return redirect('/users');
        }
    }
}
