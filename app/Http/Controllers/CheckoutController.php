<?php

namespace App\Http\Controllers;

use App\Country;
use App\Models\Gateways\Viva;
use App\Utility\PayfastUtility;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Auth;
use App\Category;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\InstamojoController;
use App\Http\Controllers\ClubPointController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\PublicSslCommerzPaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\PaytmController;
use App\Order;
use App\CommissionHistory;
use App\BusinessSetting;
use App\Coupon;
use App\CouponUsage;
use App\User;
use App\Address;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use Session;
use App\Utility\PayhereUtility;
use App\ShippingWeightRangeCost;
use App\ShippingWeightRangeAcsCost;

class CheckoutController extends Controller
{

  public function __construct()
  {
    //
  }

  //check the selected payment gateway and redirect to that controller accordingly
  public function checkout(Request $request)
  {
    if (count(CartController::checkProductStock()) > 0) {
      return redirect()->route('cart');
    }
    if ($request->payment_option != null) {

      $orderController = new OrderController;
      $orderController->store($request);

      $request->session()->put('payment_type', 'cart_payment');

      if ($request->session()->get('order_id') != null) {
        if ($request->payment_option == 'paypal') {
          $paypal = new PaypalController;
          return $paypal->getCheckout();
        } elseif ($request->payment_option == 'stripe') {
          $stripe = new StripePaymentController;
          return $stripe->stripe();
        } elseif ($request->payment_option == 'sslcommerz') {
          $sslcommerz = new PublicSslCommerzPaymentController;
          return $sslcommerz->index($request);
        } elseif ($request->payment_option == 'instamojo') {
          $instamojo = new InstamojoController;
          return $instamojo->pay($request);
        } elseif ($request->payment_option == 'razorpay') {
          $razorpay = new RazorpayController;
          return $razorpay->payWithRazorpay($request);
        } elseif ($request->payment_option == 'paystack') {
          $paystack = new PaystackController;
          return $paystack->redirectToGateway($request);
        } elseif ($request->payment_option == 'voguepay') {
          $voguePay = new VoguePayController;
          return $voguePay->customer_showForm();
        } elseif ($request->payment_option == 'payhere') {
          $order = Order::findOrFail($request->session()->get('order_id'));

          $order_id = $order->id;
          $amount = $order->grand_total;
          $first_name = json_decode($order->shipping_address)->name;
          $last_name = 'X';
          $phone = json_decode($order->shipping_address)->phone;
          $email = json_decode($order->shipping_address)->email;
          $address = json_decode($order->shipping_address)->address;
          $city = json_decode($order->shipping_address)->city;

          return PayhereUtility::create_checkout_form($order_id, $amount, $first_name, $last_name, $phone, $email, $address, $city);
        } elseif ($request->payment_option == 'payfast') {
          $order = Order::findOrFail($request->session()->get('order_id'));

          $order_id = $order->id;
          $amount = $order->grand_total;

          return PayfastUtility::create_checkout_form($order_id, $amount);
        } else if ($request->payment_option == 'ngenius') {
          $ngenius = new NgeniusController();
          return $ngenius->pay();
        } else if ($request->payment_option == 'iyzico') {
          $iyzico = new IyzicoController();
          return $iyzico->pay();
        } else if ($request->payment_option == 'nagad') {
          $nagad = new NagadController;
          return $nagad->getSession();
        } else if ($request->payment_option == 'bkash') {
          $bkash = new BkashController;
          return $bkash->pay();
        }
        else if ($request->payment_option == 'flutterwave') {
          $flutterwave = new FlutterwaveController();
          return $flutterwave->pay();
        } else if ($request->payment_option == 'mpesa') {
          $mpesa = new MpesaController();
          return $mpesa->pay();
        } elseif ($request->payment_option == 'paytm') {
          $paytm = new PaytmController;
          return $paytm->index();
        } elseif ($request->payment_option == 'cash_on_delivery' || $request->payment_option == 'pay_on_credit') {
          $request->session()->forget('cart');
          $request->session()->forget('owner_id');
          $request->session()->forget('delivery_info');
          $request->session()->forget('coupon_id');
          $request->session()->forget('coupon_discount');
          $request->session()->forget('club_point');
          $request->session()->forget('total');
          $request->session()->forget('subtotal');
          $request->session()->forget('vat_amount');
          $request->session()->forget('shipping');
          $request->session()->forget('shipping_method');
          $request->session()->forget('shipping_info');
          $request->session()->forget('vat');
          $request->session()->forget('total_weight_cart');
          $request->session()->forget('selected_shipping_country');
          $request->session()->forget('payment_type');

          flash(translate("Your order has been placed successfully"))->success();
          return redirect()->route('order_confirmed');
        } elseif ($request->payment_option == 'wallet') {
          $user = Auth::user();
          $order = Order::findOrFail($request->session()->get('order_id'));
          if ($user->balance >= $order->grand_total) {
            $user->balance -= $order->grand_total;
            $user->save();
            return $this->checkout_done($request->session()->get('order_id'), null);
          }
        } else {
          $order = Order::findOrFail($request->session()->get('order_id'));
          $order->manual_payment = 1;
          $order->save();

          $request->session()->put('cart', Session::get('cart')->where('owner_id', '!=', Session::get('owner_id')));
          $request->session()->forget('owner_id');
          $request->session()->forget('delivery_info');
          $request->session()->forget('coupon_id');
          $request->session()->forget('coupon_discount');
          $request->session()->forget('club_point');

          flash(translate('Your order has been placed successfully. Please submit payment information from purchase history'))->success();
          return redirect()->route('order_confirmed');
        }
      }
    } else {
      flash(translate('Select Payment Option.'))->warning();
      return back();
    }
  }

  //redirects to this method after a successfull checkout
  public function checkout_done($order_id, $payment)
  {
    $order = Order::findOrFail($order_id);
    $order->payment_status = 'paid';
    $order->payment_details = $payment;
    $order->save();

    if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
      $affiliateController = new AffiliateController;
      $affiliateController->processAffiliatePoints($order);
    }

    if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated) {
      if (Auth::check()) {
        $clubpointController = new ClubPointController;
        $clubpointController->processClubPoints($order);
      }
    }
    if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() == null ||
      !\App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {

      foreach ($order->orderDetails as $key => $orderDetail) {
        $orderDetail->payment_status = 'paid';
        $orderDetail->save();
        $commission_percentage = 0;

        if (get_setting('category_wise_commission') != 1) {
          $commission_percentage = get_setting('vendor_commission');
        } else if ($orderDetail->product->user->user_type == 'seller') {
          $commission_percentage = $orderDetail->product->category->commision_rate;
        }

        if ($orderDetail->product->user->user_type == 'seller') {
          $seller = $orderDetail->product->user->seller;
          $admin_commission = ($orderDetail->price * $commission_percentage)/100;

          if (get_setting('product_manage_by_admin') == 1) {
            $seller_earning = ($orderDetail->tax + $orderDetail->price) - $admin_commission;
            $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->tax + $orderDetail->price) - $admin_commission;
          } else {
            $seller_earning = $orderDetail->tax + $orderDetail->shipping_cost + $orderDetail->price - $admin_commission;
            $seller->admin_to_pay = $seller->admin_to_pay - $admin_commission;
          }
//                    $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
          $seller->save();

          $commission_history = new CommissionHistory;
          $commission_history->order_id = $order->id;
          $commission_history->order_detail_id = $orderDetail->id;
          $commission_history->seller_id = $orderDetail->seller_id;
          $commission_history->admin_commission = $admin_commission;
          $commission_history->seller_earning = $seller_earning;

          $commission_history->save();
        }

      }

//            if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
//                $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
//                foreach ($order->orderDetails as $key => $orderDetail) {
//
//                    if ($orderDetail->product->user->user_type == 'seller') {
//                        $seller = $orderDetail->product->user->seller;
//                        $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
//                        $seller->save();
//                    }
//                }
//            } else {
//                foreach ($order->orderDetails as $key => $orderDetail) {
//                    $orderDetail->payment_status = 'paid';
//                    $orderDetail->save();
//                    if ($orderDetail->product->user->user_type == 'seller') {
//                        $commission_percentage = $orderDetail->product->category->commision_rate;
//                        $seller = $orderDetail->product->user->seller;
//                        $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
//                        $seller->save();
//                    }
//                }
//            }
    } else {
      foreach ($order->orderDetails as $key => $orderDetail) {
        $orderDetail->payment_status = 'paid';
        $orderDetail->save();
        if ($orderDetail->product->user->user_type == 'seller') {
          $seller = $orderDetail->product->user->seller;
          $seller->admin_to_pay = $seller->admin_to_pay + $orderDetail->price + $orderDetail->tax + $orderDetail->shipping_cost;
          $seller->save();
        }
      }
    }

    $order->commission_calculated = 1;
    $order->save();

    if (Session::has('cart')) {
      Session::put('cart', Session::get('cart')->where('owner_id', '!=', Session::get('owner_id')));
    }
    Session::forget('owner_id');
    Session::forget('payment_type');
    Session::forget('delivery_info');
    Session::forget('coupon_id');
    Session::forget('coupon_discount');
    Session::forget('club_point');


    flash(translate('Payment completed'))->success();
    return view('frontend.order_confirmed', compact('order'));
  }

  public function get_shipping_info(Request $request)
  {
    return redirect()->route('checkout.store_shipping_infostore');

    /*if (Session::has('cart') && count(Session::get('cart')) > 0) {
      $categories = Category::all();
      return view('frontend.shipping_info', compact('categories'));
    }
    flash(translate('Your cart is empty'))->success();
    return back();*/
  }

  public function store_shipping_info(Request $request)
  {
    if (!Session::has('cart') || empty(Session::get('cart'))) {
      return redirect()->route('user.login');
    }
    if (Auth::check()) {
      /*if ($request->address_id == null) {
          flash(translate("Please add shipping address"))->warning();
          return back();
      }
      $address = Address::findOrFail($request->address_id);*/
      $data['name'] = Auth::user()->name;
      $data['email'] = Auth::user()->email;
      $data['address'] = Auth::user()->address;
      $data['country'] = Auth::user()->country;
      $data['city'] = Auth::user()->city;
      $data['postal_code'] = Auth::user()->postal_code;
      $data['phone_code'] = Auth::user()->phone_code;
      $data['phone'] = Auth::user()->phone;
      $data['checkout_type'] = $request->checkout_type;
    } else {
      $data['name'] = $request->name;
      $data['email'] = $request->email;
      $data['address'] = $request->address;
      $data['country'] = $request->country;
      $data['city'] = $request->city;
      $data['postal_code'] = $request->postal_code;
      $data['phone_code'] = $request->phone_code;
      $data['phone'] = $request->phone;
      $data['checkout_type'] = $request->checkout_type;
    }

    if (Session::has('shipping_info')) {
      $shipping_info = Session::get('shipping_info');
    }
    else {
      $shipping_info = $data;
      $request->session()->put('shipping_info', $shipping_info);
    }

    $subtotal = 0;
    $tax = 0;
    $shipping = 0;
    foreach (Session::get('cart') as $key => $cartItem) {
      $subtotal += $cartItem['quantity'] * $cartItem['price'];
      $tax += $cartItem['quantity'] * $cartItem['tax'];

      if(isset($cartItem['shipping']) && is_array(json_decode($cartItem['shipping'], true))) {
        foreach(json_decode($cartItem['shipping'], true) as $shipping_region => $val) {
          if($shipping_info['city'] == $shipping_region) {
            $shipping += (double)($val) * $cartItem['quantity'];
          }
        }
      } else {
        if (!$cartItem['shipping']) {
          $shipping += 0;
        }
//                $shipping += $cartItem['shipping'] * $cartItem['quantity'];

      }
    }
    $total = $subtotal + $tax + $shipping;
    Session::put('subtotal', $subtotal);
    Session::put('total', $total);

    if (Session::has('coupon_discount')) {
      $total -= Session::get('coupon_discount');
    }

    return view('frontend.delivery_info', compact('shipping_info'));
    // return view('frontend.payment_select', compact('total'));
  }

  public function store_delivery_info(Request $request)
  {
    if (!Session::has('cart') || empty(Session::get('cart'))) {
      return redirect()->route('cart');
    }
    if (Auth::check()) {
      if ($request->address_id == null) {
        flash(translate("Please add or select shipping address"))->error();
        return back();
      }
      $address = Address::findOrFail($request->address_id);

      $data['name'] = Auth::user()->name;
      $data['email'] = Auth::user()->email;
      $data['address'] = $address->address;
      $data['country'] = $address->country;
      $data['city'] = $address->city;
      $data['postal_code'] = $address->postal_code;
      $data['phone_code'] = $address->phone_code;
      $data['phone'] = $address->phone;
      $data['checkout_type'] = $request->checkout_type;
    } else {
      $rules = [
        'name' => 'required',
        'surname' => 'required',
        'email' => 'required|email',
        'phone_code' => 'required',
        'phone' => 'required|numeric',
        'address' => 'required',
        'postal_code' => 'required',
        'country' => 'required|numeric',
        'checkout_type' => 'required'
      ];

      if ($request->country == '54') {
        $rules['city'] = 'required|numeric';
      } else {
        $rules['city_name'] = 'required|string';
      }

      $request->validate($rules);


      $data['name'] = $request->name . ' ' . $request->surname;
      $data['email'] = $request->email;
      $data['address'] = $request->address;
      $data['country'] = $request->country;
      $data['city'] = $request->city ?? $request->city_name;
      $data['postal_code'] = $request->postal_code;
      $data['phone_code'] = $request->phone_code;
      $data['phone'] = $request->phone;
      $data['checkout_type'] = $request->checkout_type;
    }

    $shipping_info = $data;
    $request->session()->put('shipping_info', $shipping_info);

    $request->session()->put('owner_id', null);

    $country = Country::find($shipping_info['country']);
    setVatOnSession(((auth()->check() && auth()->user()->excluded_vat) ? 0 : $country->vat_included), $country);

    if (Session::has('cart') && count(Session::get('cart')) > 0) {
      $cart = $request->session()->get('cart', collect([]));
      $cart = $cart->map(function ($object, $key) use ($request) {
          if ($request['shipping_type'] == 'pickup_point') {
            $object['shipping_type'] = 'pickup_point';
            $object['pickup_point'] = $request['pickup_point_id'];
          } else {
            $object['shipping_type'] = $request['shipping_type'];
          }
        return $object;
      });

      $request->session()->put('cart', $cart);

      $cart = $cart->map(function ($object, $key) use ($request) {
          if ($object['shipping_type'] == 'home_delivery') {
            $object['shipping'] = getShippingCost($key);
          }
          else {
            $object['shipping'] = 0;
          }
        return $object;
      });

      $request->session()->put('cart', $cart);
      $shipping_info = $request->session()->get('shipping_info');
      $subtotal = 0;
      $tax = 0;
      $shipping = 0;
      $has_product_cyprus_only = false;

      foreach (Session::get('cart') as $key => $cartItem) {
        $subtotal += $cartItem['price'] * $cartItem['quantity'];
        $tax += $cartItem['tax'] * $cartItem['quantity'];
        if(isset($cartItem['shipping']) && is_array(json_decode($cartItem['shipping'], true))) {
          foreach(json_decode($cartItem['shipping'], true) as $shipping_region => $val) {
            if($shipping_info['city'] == $shipping_region) {
              $shipping += (double)($val) * $cartItem['quantity'];
            }
          }
        } else {
          if (!$cartItem['shipping']) {
            $shipping += 0;
          }
        }
        if(isset($cartItem['cyprus_shipping_only']) && $cartItem['cyprus_shipping_only']) {
          $has_product_cyprus_only = true;
        }
      }

      if($shipping_info['country'] != 54 && $has_product_cyprus_only) {
        return redirect()->route('cart')->with('cyprus_only_warning', 1);
      }

      $total = $subtotal + $tax + $shipping;

      if (Session::has('coupon_discount')) {
        $total -= Session::get('coupon_discount');
      }
      $shipping_option = $request['shipping_type'];
      $pickup_point = null;

      if($request['pickup_point_id'] && $shipping_option == "pickup_point"){
        $pickup_point = $request['pickup_point_id'];
      }
      CartController::updateTotals();
      return view('frontend.payment_select', compact('total', 'shipping_info', 'shipping_option', 'pickup_point'));
    } else {
      flash(translate('Your Cart was empty'))->warning();
      return redirect()->route('home');
    }
  }

  public function get_payment_info(Request $request)
  {
    if (!Session::has('cart') || empty(Session::get('cart'))) {
      return redirect()->route('cart');
    }
    $subtotal = 0;
    $tax = 0;
    $shipping = 0;
    $shipping_info = $request->session()->get('shipping_info');

    foreach (Session::get('cart') as $key => $cartItem) {
      $subtotal += $cartItem['price'] * $cartItem['quantity'];
      $tax += $cartItem['tax'] * $cartItem['quantity'];
      if(isset($cartItem['shipping']) && is_array(json_decode($cartItem['shipping'], true))) {
        foreach(json_decode($cartItem['shipping'], true) as $shipping_region => $val) {
          if($shipping_info['city'] == $shipping_region) {
            $shipping += (double)($val) * $cartItem['quantity'];
          }
        }
      } else {
        if (!$cartItem['shipping']) {
          $shipping += 0;
        }
      }
    }

    $total = $subtotal + $tax + $shipping;

    if (Session::has('coupon_discount')) {
      $total -= Session::get('coupon_discount');
    }
    CartController::updateTotals();
    return view('frontend.payment_select', compact('total', 'shipping_info'));
  }

  public function apply_coupon_code(Request $request)
  {
    //dd($request->all());
    $coupon = Coupon::where('code', $request->code)->first();

    if ($coupon != null) {
      if (strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date) {
        if (CouponUsage::where('user_id', Auth::user()->id)->where('coupon_id', $coupon->id)->first() == null) {
          $coupon_details = json_decode($coupon->details);

          if ($coupon->type == 'cart_base') {
            $subtotal = 0;
            $tax = 0;
            $shipping = 0;
            foreach (Session::get('cart') as $key => $cartItem) {
              $subtotal += $cartItem['price'] * $cartItem['quantity'];
              $tax += $cartItem['tax'] * $cartItem['quantity'];
              $shipping += $cartItem['shipping'] * $cartItem['quantity'];
            }
            $sum = $subtotal + $tax + $shipping;

            if ($sum >= $coupon_details->min_buy) {
              if ($coupon->discount_type == 'percent') {
                $coupon_discount = ($sum * $coupon->discount) / 100;
                if ($coupon_discount > $coupon_details->max_discount) {
                  $coupon_discount = $coupon_details->max_discount;
                }
              } elseif ($coupon->discount_type == 'amount') {
                $coupon_discount = $coupon->discount;
              }
              $request->session()->put('coupon_id', $coupon->id);
              $request->session()->put('coupon_discount', $coupon_discount);
              flash(translate('Coupon has been applied'))->success();
            }
          } elseif ($coupon->type == 'product_base') {
            $coupon_discount = 0;
            foreach (Session::get('cart') as $key => $cartItem) {
              foreach ($coupon_details as $key => $coupon_detail) {
                if ($coupon_detail->product_id == $cartItem['id']) {
                  if ($coupon->discount_type == 'percent') {
                    $coupon_discount += $cartItem['price'] * $coupon->discount / 100;
                  } elseif ($coupon->discount_type == 'amount') {
                    $coupon_discount += $coupon->discount;
                  }
                }
              }
            }
            $request->session()->put('coupon_id', $coupon->id);
            $request->session()->put('coupon_discount', $coupon_discount);
            flash(translate('Coupon has been applied'))->success();
          }
        } else {
          flash(translate('You already used this coupon!'))->warning();
        }
      } else {
        flash(translate('Coupon expired!'))->warning();
      }
    } else {
      flash(translate('Invalid coupon!'))->warning();
    }
    return back();
  }

  public function remove_coupon_code(Request $request)
  {
    $request->session()->forget('coupon_id');
    $request->session()->forget('coupon_discount');
    return back();
  }

  public function apply_club_point(Request $request) {
    if (\App\Addon::where('unique_identifier', 'club_point')->first() != null &&
      \App\Addon::where('unique_identifier', 'club_point')->first()->activated){

      $point = $request->point;

//            if(Auth::user()->club_point->points >= $point) {
      if(Auth::user()->point_balance >= $point) {
        $request->session()->put('club_point', $point);
        flash(translate('Point has been redeemed'))->success();
      }
      else {
        flash(translate('Invalid point!'))->warning();
      }
    }
    return back();
  }

  public function remove_club_point(Request $request) {
    $request->session()->forget('club_point');
    return back();
  }

  public function order_confirmed($order_code = false)
  {
    if ($order_code) {
      $order = Order::where('code', $order_code)->first();
    } else {
      $order = Order::findOrFail(Session::get('order_id'));
    }

    if ($order == null) {
      Log::warning(basename(__FILE__)." ".__FUNCTION__.":".__LINE__." => Not found order with order code $order_code.");
      $viva_payment_log = Viva::where('OrderCode', $order_code)->first();
      if ($viva_payment_log != null) {
        if (!$viva_payment_log->run_script && !$viva_payment_log->callback) {
          Log::error(basename(__FILE__)." ".__FUNCTION__.":".__LINE__." => Viva Wallet response code not executed. Order code $order_code.");
        }
      }
    }

    if ($order->confirm_page_seen) return redirect()->route('home');

    $order->confirm_page_seen = 1;
    $order->save();
    return view('frontend.order_confirmed', compact('order'));
  }

  public function order_pending($order_code = false)
  {
    $viva_log = Viva::where('OrderCode', $order_code)->latest()->first();
    if ($viva_log == null || $viva_log->pending_page_seen) return redirect()->route('home');

    $viva_log->pending_page_seen = 1;
    $viva_log->save();
    return view('frontend.order_pending', compact('viva_log'));
  }

  public function order_failed()
  {
    return view('frontend.order_failed');
  }

  public function get_shipping_methods(Request $request) {
    $request->validate([
      'country' => 'required|numeric'
    ]);

    calcWeightCart();

    $country_id = $request->country;
    updateVatOnSession($country_id);
    $selected_shipping_method = Session::get('shipping_method')['method'] ?? null;
    $selected_pickup_point = Session::get('shipping_method')['pickup_point'] ?? null;

    Session::put('selected_shipping_country', $country_id);
    $country = Country::findOrFail($country_id);
    $country_name = toUpper($country->post_name_en);
    $total_weight = $request->session()->get('total_weight_cart');

    $parcels_epg_cost = 0;
    $parcels_epg_status = 0;
    $parcels_epg_msg = "";
    $ems_datapost_cost = 0;
    $ems_datapost_status = 0;
    $ems_datapost_msg = "";

    if ($country_id != '54') {
        $response_parcels_epg = calculateCyprusPostShipping('epg_parcels', $total_weight, $country_id);
        $response_ems_datapost = calculateCyprusPostShipping('ems_datapost', $total_weight, $country_id);

//        dd($response_parcels_epg, $response_ems_datapost);

        $parcels_epg_cost = $response_parcels_epg->cost;
        $parcels_epg_status = $response_parcels_epg->status;
        $parcels_epg_msg = $response_parcels_epg->message;

        $ems_datapost_cost = $response_ems_datapost->cost;
        $ems_datapost_status = $response_ems_datapost->status;
        $ems_datapost_msg = $response_ems_datapost->message;
    }

    return response()->json([
        'country'=> $country_id,
        'shipping_methods' => view('frontend.shipping_methods', compact(
            'country_id',
            'selected_shipping_method',
                     'selected_pickup_point',
                     'ems_datapost_cost',
                     'ems_datapost_status',
                     'ems_datapost_msg',
                     'parcels_epg_cost',
                     'parcels_epg_status',
                     'parcels_epg_msg',
        ))->render()
    ],'200');
  }

  public function select_shipping_method(Request $request) {
    $request->validate([
      'shipping_method' => 'required|string',
      'pickup_point' => 'numeric|nullable'
    ]);

    $shipping_method = $request->shipping_method;
    $pickup_point = $request->pickup_point;

    $country_id = Session::get('selected_shipping_country');

    calcWeightCart();
    $total_weight = $request->session()->get('total_weight_cart');

    $shipping_amount = 0;
    $shipping_vat = 0;

    if (in_array($shipping_method, ['epg_parcels', 'ems_datapost'])) {
        $response = calculateCyprusPostShipping($shipping_method, $total_weight, $country_id);
        $shipping_amount = $response->cost;
        $shipping_vat = calcVatPrice($response->cost) - $response->cost;
    } elseif($shipping_method == 'home_delivery') {
        $courier_cost = ShippingWeightRangeCost::where('from', '<=', $total_weight)->where('to', '>=', $total_weight)->first();
        if($courier_cost!=null) {
            $shipping_amount = $courier_cost->price;
            $shipping_vat = calcVatPrice($courier_cost->price) - $courier_cost->price;
        }
    } elseif($shipping_method == 'acs_delivery') {
        $acs_cost = ShippingWeightRangeAcsCost::where('from', '<=', $total_weight)->where('to', '>=', $total_weight)->first();
        if($acs_cost!=null) {
            $shipping_amount = $acs_cost->price;
            $shipping_vat = calcVatPrice($acs_cost->price) - $acs_cost->price;
        }
    }
    /*else {
        $shipping_amount = config("shipping_methods.$shipping_method");
    }*/

    Session::put('shipping_method', array(
      'amount' => $shipping_amount,
      'vat' => round($shipping_vat, 2),
      'method' => $shipping_method,
      'pickup_point' => $pickup_point
    ));

    return response()->json(['shipping_method'=> $shipping_method, 'pickup_point'=> $pickup_point, 'shipping_amount'=> $shipping_amount, 'shipping_vat'=> $shipping_vat],'200');

  }
}
