<?php

namespace App\Http\Middleware;

use App\Http\Controllers\CartController;
use Closure;
use Auth;
use App\BusinessSetting;

class CheckoutMiddleware
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
        if (count(CartController::checkProductStock()) > 0) {
          return redirect()->route('cart');
        }

        if (BusinessSetting::where('type', 'guest_checkout_active')->first()->value != 1) {
            if(Auth::check()){
                return $next($request);
            }
            else {
                session(['link' => url()->current()]);
                return redirect()->route('user.login');
            }
        }
        else{
            return $next($request);
        }
    }
}
