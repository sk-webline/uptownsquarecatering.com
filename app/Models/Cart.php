<?php

namespace App\Models;

use App\User;
use App\Address;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
//use Session;

class Cart extends Model
{
    protected $guarded = [];
    protected $fillable = ['address_id','price','tax','shipping_cost','discount','coupon_code','coupon_applied','quantity','user_id','owner_id','product_id','variation'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public static function refreshCartInDB() {
        if (Session::has('cart') && Auth::check()) {
            $cart = Session::get('cart');
            Auth::user()->cart = json_encode(reset($cart));
            Auth::user()->save();
        }elseif (Session::has('app_cart') && auth()->guard('application')->check()){
            $cart = Session::get('app_cart');
            auth()->guard('application')->user()->cart = json_encode(reset($cart));
            auth()->guard('application')->user()->save();
        }
    }

    public static function mergeSessionAndDBProductsCart() {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        $user_db_cart = $user->cart;
        $final_cart = [];

        if  (Session::has('cart') && empty($user_db_cart)) {
            $user_session_cart = Session::get('cart');
            $user->cart = json_encode(reset($user_session_cart));
            $user->save();
        }
        elseif (!Session::has('cart') && !empty($user_db_cart)) {
            $cart = collect(json_decode($user_db_cart, true));
            Session::put('cart', $cart);
        }
        elseif (Session::has('cart') && !empty($user_db_cart)) {
            $user_db_cart = json_decode($user_db_cart, true);
            $user_session_cart = Session::get('cart');
            $user_session_cart = reset($user_session_cart);
            foreach ($user_session_cart as $session_key => $session_item) {
                $findInDB = false;
                foreach ($user_db_cart as $db_key => $db_cart) {
                    if ($session_item['id'] == $db_cart['id'] && $session_item['variant'] == $db_cart['variant']) {
                        $findInDB = true;
                        $db_cart['quantity'] += $session_item['quantity'];
                        $final_cart[] = $db_cart;
                        unset($user_db_cart[$db_key]);
                        break;
                    }
                }
                if  (!$findInDB) {
                    $final_cart[] = $session_item;
                }
            }

            $final_cart = array_merge($final_cart, $user_db_cart);

            $user->cart = json_encode($final_cart);
            $user->save();

            Session::forget('cart');
            $cart = collect($final_cart);
            Session::put('cart', $cart);
        }
    }

    public static function mergeApplicationSessionAndDBProductsCart() {
        if (!auth()->guard('application')->check()) {
            return;
        }

        $user = auth()->guard('application')->user();
        $user_db_cart = $user->cart;
        $final_cart = [];

        if  (Session::has('app_cart') && empty($user_db_cart)) {
            $user_session_cart = Session::get('app_cart');
            $user->cart = json_encode(reset($user_session_cart));
            $user->save();
        }
        elseif (!Session::has('app_cart') && !empty($user_db_cart)) {
            $cart = collect(json_decode($user_db_cart, true));
            Session::put('app_cart', $cart);
        }
        elseif (Session::has('app_cart') && !empty($user_db_cart)) {
            $user_db_cart = json_decode($user_db_cart, true);
            $user_session_cart = Session::get('app_cart');
            $user_session_cart = reset($user_session_cart);
            foreach ($user_session_cart as $session_key => $session_item) {
                $findInDB = false;
                foreach ($user_db_cart as $db_key => $db_cart) {
                    if ($session_item['product_id'] == $db_cart['product_id'] && $session_item['date'] == $db_cart['date'] && $session_item['break_sort'] == $db_cart['break_sort']) {
                        $findInDB = true;
                        $db_cart['quantity'] += $session_item['quantity'];
                        $final_cart[] = $db_cart;
                        unset($user_db_cart[$db_key]);
                        break;
                    }
                }
                if  (!$findInDB) {
                    $final_cart[] = $session_item;
                }
            }

            $final_cart = array_merge($final_cart, $user_db_cart);

            $user->cart = json_encode($final_cart);
            $user->save();

            Session::forget('app_cart');
            $cart = collect($final_cart);
            Session::put('app_cart', $cart);



        }
    }
}
