<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Cookie;


class EnableDebugBarOnSelectedIPs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
//        if ($request->ip() != '82.102.76.201')
//        {
//            debugbar()->disable();
//        }
//        if(config('app.env') == 'local') {
            /*if (Cookie::has('debug_hash')) {
                if (Cookie::get('debug_hash') == '') {
                    debugbar()->enable();
                    return $next($request);
                }
            }
            else {
                Cookie::make('debug_hash', rand(00000001, 999999999999), 3600*24);
            }*/

            // if (Auth::user()->user_type == 'admin') {
            /*if ($request->ip() == '82.102.76.201') {
                debugbar()->enable();
                return $next($request);
            }*/
            /*elseif(Auth::check()){
                $user_id = Auth::user()->id ?? '';
                if ($user_id == 569) {
                    debugbar()->enable();
                    return $next($request);
                }
            }*/
//        }

        debugbar()->disable();
        return $next($request);
    }
}
