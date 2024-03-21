<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class isAppUser
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
        if (auth()->guard('application')->check()) {
            return $next($request);
        }else{
            session(['link' => url()->current()]);
            return redirect()->route('application.login');
        }
    }
}
