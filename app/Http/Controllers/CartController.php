<?php

namespace App\Http\Controllers;

use App\Models\CateringPlan;
use App\Models\CateringPlanPurchase;
use App\Models\Organisation;
use App\Models\OrganisationSetting;
use App\Models\PlatformSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Product;
use App\Models\Cart;
use App\SubSubCategory;
use App\Category;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Color;
use Cookie;
use Illuminate\Support\Facades\Session as FacadesSession;

class CartController extends Controller
{
    public function index(Request $request)
    {
//        return view('frontend.order_confirmed' );

        if(Auth::check()){

            $vat= PlatformSetting::where('type', 'vat_percentage')->first()->value;

            $request->session()->put('vat_percentage', $vat);

            $data['name'] = Auth::user()->name;
            $data['email'] = Auth::user()->email;
            $data['phone'] = getUserPhone();

            $shipping_info = $data;
            $request->session()->put('shipping_info', $shipping_info);

            return view('frontend.view_cart' );

        }

    }

    public function showCartModal(Request $request)
    {
        $product = Product::find($request->id);
        return view('frontend.partials.addToCart', compact('product'));
    }

    public function updateNavCart(Request $request)
    {
        return view('frontend.partials.cart');
    }

    public function addCateringPlanToCart(Request $request)
    {

        $vat_percentage = PlatformSetting::where('type', 'vat_percentage')->first()->value;

        $organisation_setting = OrganisationSetting::findorfail($request->organisation_setting_id);
        $organisation = Organisation::findorfail($organisation_setting->organisation_id);


        $data = array();
        $data['type'] = "catering_plan";
        $data['card_id'] = $request->card_id;
        $data['organisation_setting_id'] = $request->organisation_setting_id;

        if($request->id == 'custom'){
            $new_start_date = Carbon::create($request->from);
            $new_end_date = Carbon::create($request->to);

            $data['type_id'] = $request->id;
            $data['name'] = 'Custom Subscription';
            $data['from_date'] = $request->from;
            $data['to_date'] = $request->to;

            $given_price = round($request->price, 2);
            $vat_temp = 1 + ($vat_percentage/100);
            $temp =round(($given_price / $vat_temp), 2);
            $vat_value = $request->price - $temp;
//            $calc_price = $request->price - $vat_value;

            $data['price'] = $request->price - $vat_value ;
            $data['name'] = 'Custom Subsciption';
            $data['tax'] = $vat_value;
            $data['total'] = round(($data['price'] +  $data['tax']),2) ;
            $data['snack_num'] = $request->snack_num;
            $data['meal_num'] = $request->meal_num;

            if($request->snack_num<0 || $request->snack_num> $organisation_setting->max_snack_quantity){
                return array('status' => 3);
            }else if($request->meal_num<0 || $request->meal_num> $organisation_setting->max_meal_quantity){
                return array('status' => 3);
            }

        }else{

            $added_plan = CateringPlan::findorfail($request->id);
            $new_start_date = Carbon::create($added_plan->from_date);
            $new_end_date = Carbon::create($added_plan->to_date);
            $plan_purchases = CateringPlanPurchase::where('card_id', $request->card_id)->where('catering_plan_id', $request->id)->get();

            if(count($plan_purchases)>0){
                return array('status' => 3, 'view' => view('frontend.partials.notAvailablePlan')->render());
            }

            $plan_purchases = CateringPlanPurchase::where('card_id', $request->card_id)->get();

            foreach ($plan_purchases as $purchase) {

                $check_start_date = Carbon::create($purchase->from_date);
                $check_end_date = Carbon::create($purchase->to_date);

                if ($new_start_date->gte($check_start_date) && $check_end_date->gte($new_start_date)) {
//                    return array('here 1');
                    return array('status' => 3, 'view' => view('frontend.partials.notAvailablePlan')->render());

                } else if ($check_end_date->gte($new_end_date) && $new_end_date->gte($check_start_date)) {
//                    return array('here 2');
                    return array('status' => 3, 'view' => view('frontend.partials.notAvailablePlan')->render());
                } else if ($check_start_date->gte($new_start_date) && $new_end_date->gte($check_end_date)) {
//                    return array('here 3', '$new_start_date'=>$new_start_date, '$new_end_date' =>$new_end_date, '$check_start_date'=>$check_start_date, '$check_end_date' => $check_end_date  );
                    return array('status' => 3, 'view' => view('frontend.partials.notAvailablePlan')->render());
                }



            }


            $catering_plan = CateringPlan::findorfail($request->id);

            $data['type_id'] = $catering_plan->id;
            $data['name'] = $catering_plan->name;
            $data['from_date'] = $catering_plan->from_date;
            $data['to_date'] = $catering_plan->to_date;
            $data['num_of_working_days'] = $catering_plan->num_of_working_days;

            $vat_temp = 1 + ($vat_percentage/100);
            $temp =round($catering_plan->price / $vat_temp, 2);
            $vat_value = $catering_plan->price - $temp;

            $data['price'] = round(($catering_plan->price - $vat_value), 2) ;
            $data['tax'] = $vat_value;
            $data['total'] = round(($data['price'] +  $data['tax']),2);
            $data['snack_num'] = $catering_plan->snack_num;
            $data['meal_num'] = $catering_plan->meal_num;

        }

        $catering_plan_id = $request->id;

        $foundInCart = false;


        if($request->id == 'custom'){

            if ($request->session()->has('cart')  && count($request->session()->get('cart')) > 0) {

                $request->session()->put('count', count($request->session()->get('cart')));

                $cart = collect();
                $foundInCart = true;

                $session_cart = $request->session()->get('cart');


                foreach ($session_cart as $cartItem) {

                    if ($cartItem['card_id']== $request->card_id){
                        if ($request->remove_prev == 1) {
                            continue;
                        } else {
                            return array('status' => 0, 'view' => view('frontend.partials.oneCateringPlanInCart', compact('catering_plan_id', 'data'))->render());
                        }
                    }

                    $cart->push($cartItem);
                }

                $cart->push($data);

            }else {
                $cart = collect([$data]);
            }


//            if (!$foundInCart) {
//                $cart->push($data);
//            }

            $request->session()->put('cart', $cart);

            Cart::refreshCartInDB();

            return array('status' => 1, 'view' => view('frontend.partials.cateringPlanAddedToCart', compact('data'))->render());

        }else {
            if ($request->session()->has('cart') && count($request->session()->get('cart')) > 0) {

                $cart = collect();

                $foundInCart = true;

                $session_cart = $request->session()->get('cart');

                foreach ($request->session()->get('cart') as $key => $cartItem) {
                    if ($cartItem['card_id'] == $request->card_id && $cartItem['type'] == $data['type']) {

                        if ($request->remove_prev == 1) {
                            continue;
                        } else {

                            return array('status' => 0, 'view' => view('frontend.partials.oneCateringPlanInCart', compact('catering_plan_id'))->render());
                        }
                    }
                    $cart->push($cartItem);
                }
                $cart->push($data);
            } else {
                $cart = collect([$data]);
            }

            $request->session()->put('cart', $cart);
            Cart::refreshCartInDB();

//            return array('status' => 1);
            return array('status' => 1, 'view' => view('frontend.partials.cateringPlanAddedToCart', compact('data'))->render());
        }
    }

    public function addToCart(Request $request)
    {
        $product = Product::find($request->id);

        $data = array();
        $data['id'] = $product->id;
        $data['owner_id'] = $product->user_id;
        $str = '';
        $tax = 0;
        $weight = 0;
        $total_quantity = 0;
        $total_weight = 0;
        $disc_amount = 0;
        $disc_percentage = 0;

        if($product->digital != 1 && $request->quantity < $product->min_qty) {
            return array('status' => 0, 'view' => view('frontend.partials.minQtyNotSatisfied', [
                'min_qty' => $product->min_qty
            ])->render());
        }


        //check the color enabled or disabled for the product
        if($request->has('color')){
            $str = $request['color'];
        }

        if ($product->digital != 1) {
            //Gets all the choice values of customer choice option and generate a string like Black-S-Cotton
            foreach (json_decode(Product::find($request->id)->choice_options) as $key => $choice) {
                if($str != null){
                    $str .= '-'.str_replace(' ', '', $request['attribute_id_'.$choice->attribute_id]);
                }
                else{
                    $str .= str_replace(' ', '', $request['attribute_id_'.$choice->attribute_id]);
                }
                if($choice->attribute_id == 1) {
                    $data['size'] = $request['attribute_id_'.$choice->attribute_id];
                }
            }
        }

        $data['variant'] = $str;

        $productInCart = false;
        if($request->session()->has('cart')){
            foreach ($request->session()->get('cart') as $key => $cartItem){
                if($cartItem['id'] == $request->id){
                    if($cartItem['variant'] == $str){
                        $productInCart = true;
                        $total_quantity = $cartItem['quantity'] + $request['quantity'];
                    }
                }
            }
        }

        if(!$productInCart) {
            $total_quantity = $request['quantity'];
        }

        if($str != null && $product->variant_product){
            $product_stock = $product->stocks->where('variant', $str)->first();
            $product_stock->updateStock();
            $price = $product_stock->getPriceForCurrentUser(true, false, false);
            $disc_percentage = $product_stock->getCurrentDiscount()->percentage;
            $price_list_id = $product_stock->getCurrentDiscount()->price_list_id;
            $disc_amount = $product_stock->getCurrentDiscount()->amount;
            $quantity = $product_stock->qty;
            $weight = $product_stock->weight;

            if($quantity < $request['quantity']){
                return array('status' => 0, 'view' => view('frontend.partials.outOfStockCart')->render());
            }
        }
        else{
            $price = $product->getPriceForCurrentUser(true, false, false);
            $disc_percentage = $product->getCurrentDiscount()->percentage;
            $price_list_id = $product->getCurrentDiscount()->price_list_id;
            $disc_amount = $product->getCurrentDiscount()->amount;
            $product_stock = $product->stocks->first();
            $weight = $product_stock->weight;
            $product->updateStock();
        }
        $tax = calcVatPrice($price) - $price;
        $total_weight = $weight * $total_quantity;

        $total_weight = weightConvertToKg($total_weight);

        //discount calculation based on flash deal and regular discount
        //calculation of taxes
//        if($product->discount_type == 'percent') {
//            $price -= ($price*$product->discount)/100;
//        }
//        elseif($product->discount_type == 'amount'){
//            $price -= $product->discount;
//        }
//
//        foreach ($product->taxes as $product_tax) {
//            if($product_tax->tax_type == 'percent'){
//                $tax += ($price * $product_tax->tax) / 100;
//            }
//            elseif($product_tax->tax_type == 'amount'){
//                $tax += $product_tax->tax;
//            }
//        }

        $data['color'] = $request['color'];
        $data['quantity'] = $request['quantity'];
        $data['price'] = round($price, 2);
        $data['price_type_id'] = $price_list_id; // TODO: George => Set Price list id from BTMS
        $data['tax'] = round($tax, 2);
        $data['shipping'] = 0;
        $data['disc_percentage'] = round($disc_percentage, 2);
        $data['disc_amount'] = round($disc_amount, 2);
        $data['product_referral_code'] = null;
        $data['cash_on_delivery'] = $product->cash_on_delivery;
        $data['digital'] = $product->digital;
        $data['weight'] = $weight ?? 0;
        $data['cyprus_shipping_only'] = $product->cyprus_shipping_only;

        if ($request['quantity'] == null){
            $data['quantity'] = 1;
        }

        if(Cookie::has('referred_product_id') && Cookie::get('referred_product_id') == $product->id) {
            $data['product_referral_code'] = Cookie::get('product_referral_code');
        }
        if($request->session()->has('cart')){
            $foundInCart = false;
            $cart = collect();

            foreach ($request->session()->get('cart') as $key => $cartItem){
                if($cartItem['id'] == $request->id  && (!$request->has('color') || $request->color == $cartItem['color'] ) && (!$request->has('attribute_id_1') || $request->attribute_id_1 == $cartItem['size'] )){
                    if($str != null && $cartItem['variant'] == $str){

                        $product_stock = $product->stocks->where('variant', $str)->first();
                        $quantity = $product_stock->qty;

                        if($quantity < $cartItem['quantity'] + $request['quantity']){
                            return array('status' => 0, 'view' => view('frontend.partials.outOfStockCart')->render());
                        }
                        else{
                            $foundInCart = true;
                            $cartItem['quantity'] += $request['quantity'];
                        }
                    }
                    elseif($product->current_stock < $cartItem['quantity'] + $request['quantity']){
                        return array('status' => 0, 'view' => view('frontend.partials.outOfStockCart')->render());
                    }
                    else{
                        $foundInCart = true;
                        $cartItem['quantity'] += $request['quantity'];
                    }
                }
                $cart->push($cartItem);
            }

            if (!$foundInCart) {
                $cart->push($data);
            }
            $request->session()->put('cart', $cart);
        }
        else{
            $cart = collect([$data]);
            $request->session()->put('cart', $cart);
        }
        calcWeightCart();
        Cart::refreshCartInDB();
        return array('status' => 1, 'view' => view('frontend.partials.addedToCart', compact('product', 'data', 'total_quantity', 'total_weight'))->render());
    }

    //removes from Cart
    public function removeFromCart(Request $request)
    {
        if($request->session()->has('cart')){
            $cart = $request->session()->get('cart', collect([]));
            $cart->forget($request->key);
            $request->session()->put('cart', $cart);
        }

//        calcWeightCart();
        Cart::refreshCartInDB();

//        return 'hi';
        return view('frontend.partials.cart_details');
        //redirect()->route('cart');
            //view('frontend.view_cart');
//        view('frontend.partials.cart_table');
//            redirect()->route('cart');
//        view('frontend.partials.cart_details');
    }

    //updated the quantity for a cart item
    public function updateQuantity(Request $request)
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if($key == $request->key){
                $product = \App\Product::find($object['id']);
                if($object['variant'] != null && $product->variant_product){
                    $product_stock = $product->stocks->where('variant', $object['variant'])->first();
                    $quantity = $product_stock->qty;
                    if($quantity >= $request->quantity){
                        if($request->quantity >= $product->min_qty){
                            $object['quantity'] = $request->quantity;
                        }
                    }
                }
                elseif ($product->current_stock >= $request->quantity) {
                    if($request->quantity >= $product->min_qty){
                        $object['quantity'] = $request->quantity;
                    }
                }
            }
            return $object;
        });
        $request->session()->put('cart', $cart);
        calcWeightCart();
        Cart::refreshCartInDB();
        return view('frontend.partials.cart_details');
    }

    public function mergeSessionAndDBProductsCart()
    {
       Cart::mergeSessionAndDBProductsCart();
//       dd("End");
    }

    public static function updateTotals() {
      $subtotal = 0;
      $tax = 0;
      $shipping = Session::get('shipping_method')['amount'] ?? 0;
      $tax += Session::get('shipping_method')['vat'] ?? 0;

      $session_cart = Session::get('cart', collect([]));

      $updated_session_cart = $session_cart->map(function ($object, $key){
        $object['tax'] = round(calcPriceBeforeAddVat($object['price'], "vat"), 2);
        return $object;
      });
      Session::put('cart', $updated_session_cart);

      foreach (Session::get('cart') as $key => $cartItem) {
        $subtotal += $cartItem['price'] * $cartItem['quantity'];
        $tax += $cartItem['tax'] * $cartItem['quantity'];
      }
      $total = $subtotal + $tax + $shipping;

      Session::put('subtotal', $subtotal);
      Session::put('vat_amount', $tax);
      Session::put('total', $total);
    }

    public static function checkProductStock($auto_remove_products = false): array
    {
      $products_with_changes = [];
      $remove_keys = [];
      $cart = Session::get('cart', collect([]));
      $cart = $cart->map(function ($object, $key) use (&$products_with_changes, $auto_remove_products, &$remove_keys) {
        $product = \App\Product::find($object['id']);
        if ($product->import_from_btms) {
          if ($product->variant_product) {
            $stock = $product->stocks->where('variant', $object['variant'])->first();
            if ($stock != null) {
              $stock->updateStock();
              if ($object['quantity'] > $stock->qty) {
                if ($auto_remove_products) {
                  $object['quantity'] = (int)$stock->qty;
                }
                $products_with_changes[$key] = [
                  'cart_key' => $key,
                  'available_stock' => $stock->qty,
                ];
              }
            }
          }
          else {
            $product->updateStock();
            if ($object['quantity'] > $product->current_stock) {
              if ($auto_remove_products) {
                $object['quantity'] = (int)$product->current_stock;
              }
              $products_with_changes[$key] = [
                'cart_key' => $key,
                'available_stock' => (int) $product->current_stock,
              ];
            }
          }
        }
        if ($object['quantity'] == 0) {
          $remove_keys[] = $key;
        }
        return $object;
      });
      if (count($remove_keys) > 0) {
        foreach ($remove_keys as $key) {
          $cart->forget($key);
        }
      }
      Session::put('cart', $cart);
      self::updateTotals();
      return $products_with_changes;
    }
}
