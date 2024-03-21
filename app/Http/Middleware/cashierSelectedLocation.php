<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Support\Facades\Session;

class cashierSelectedLocation
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
        if (Auth::check() && Auth::user()->user_type == 'cashier' && Session::has('location_id')) {
            return $next($request);
        }
        else{

            return redirect()->route('cashier.select_location');
//            abort(404);
        }
    }
}
