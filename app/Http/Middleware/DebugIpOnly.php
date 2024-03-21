<?php

namespace App\Http\Middleware;

use Closure;

class DebugIpOnly
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
        if (!in_array($request->ip(), ['82.102.76.201'])) {
            // here instead of checking a single ip address we can do collection of ips
            //address in constant file and check with in_array function
            abort(403);
        }
        return $next($request);
    }
}
