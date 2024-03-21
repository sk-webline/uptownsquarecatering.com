<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Support\Facades\Session;

class IsCanteenCashier
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
        if (Auth::check() && Auth::user()->user_type == 'canteen_cashier') {
            return $next($request);
        } else if(Auth::check() && Auth::user()->user_type == 'customer'){
            return redirect()->route('dashboard');
        } else if(Auth::check() && Auth::user()->user_type == 'admin'){

            return redirect()->route('admin.dashboard');
        }
        else{
            return redirect()->route('cashier.login');
//            abort(404);
        }
    }
}
