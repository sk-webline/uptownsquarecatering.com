<?php

namespace App\Http\Controllers\Api\V2;

use App\City;
use App\Country;
use App\Http\Resources\V2\AddressCollection;
use App\Address;
use App\Http\Resources\V2\CitiesCollection;
use App\Http\Resources\V2\CountriesCollection;
use App\Order;
use App\Wishlist;
use Illuminate\Http\Request;
use App\Models\Cart;

class ProfileController extends Controller
{
    public function counters($user_id)
    {
        return response()->json([
            'cart_item_count' => Cart::where('user_id', $user_id)->count(),
            'wishlist_item_count' => Wishlist::where('user_id', $user_id)->count(),
            'order_count' => Order::where('user_id', $user_id)->count(),
        ]);
    }
}
