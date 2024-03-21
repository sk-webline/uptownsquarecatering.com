<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class IsUser
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
        if (Auth::check() && (Auth::user()->user_type == 'customer' || Auth::user()->user_type == 'seller') ) {
            return $next($request);
        } else if(Auth::check() && (Auth::user()->user_type == 'cashier') ) {

            if(auth()->user()->active==1){
                return redirect()->route('cashier.dashboard');
            }else{
                return redirect()->route('logout');
            }

        } else if(Auth::check() && (Auth::user()->user_type == 'admin') ) {
            if(auth()->user()->active==1){
                return redirect()->route('admin.dashboard');
            }else{
                return redirect()->route('logout');
            }

        }
        else{
            session(['link' => url()->current()]);
            return redirect()->route('user.login');
        }
    }
}
