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
        //개인정보 페이지같은 곳은 히스토리접근이 안되게 해더에 캐쉬불가능을 추가
        return $response->header('Cache-Control', 'no-cache, no-store') //1.1
                        ->header('Pragma', 'no-cache')  //1.0
                        ->header('Expires', '0'); //1.0
    }
}
