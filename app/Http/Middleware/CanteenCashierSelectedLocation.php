<?php

namespace App\Http\Middleware;

use App\Models\CanteenLocation;
use App\Models\Organisation;
use Closure;
use Auth;
use Illuminate\Support\Facades\Session;

class CanteenCashierSelectedLocation
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
        if (Auth::check() && Auth::user()->user_type == 'canteen_cashier' && Session::has('organisation_id') && Session::has('location_id')) {

            $canteen_location = CanteenLocation::find(Session::get('location_id'));

//            dd($canteen_location);

            $organisation = Organisation::find($canteen_location->organisation_id);

            if($canteen_location!=null && $organisation!=null && $organisation->id == Session::get('organisation_id') ){
                return $next($request);
            }

        }

        return redirect()->route('canteen_cashier.select_location');

    }
}
