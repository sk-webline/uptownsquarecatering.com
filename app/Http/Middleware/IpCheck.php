<?php

namespace App\Http\Middleware;

use Closure;

class IpCheck
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
        // IP Uptown: 
        $viva_wallet_ips = ['51.138.37.238', '20.54.89.16', '13.80.70.181', '13.80.71.223', '13.79.28.70', '20.50.240.57', '40.74.20.78', '94.70.170.65', '94.70.174.36', '94.70.255.73', '94.70.248.18','83.235.24.226'];
        if (env('COMING_SOON') == '1' && !in_array($request->ip(), array_merge_recursive(['82.102.76.201'], $viva_wallet_ips))) {
            // here instead of checking a single ip address we can do collection of ips
            //address in constant file and check with in_array function
            return redirect(route('coming_soon'));
        }
        return $next($request);
    }
}
