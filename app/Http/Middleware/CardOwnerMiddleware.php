<?php

namespace App\Http\Middleware;

use App\Models\Card;
use Closure;
use Auth;

class CardOwnerMiddleware
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

//        $card = Card::findorfail(decrypt($card_id));
        if (Auth::check() && (Auth::user()->id == 0 )) {
            return $next($request);
        }
        else{
            abort(404);
        }
    }
}
