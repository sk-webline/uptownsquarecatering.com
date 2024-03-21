<?php

namespace App\Http\Controllers;

use App\Models\Btms\Utilities\BtmsOrder;
use \App\Http\Controllers\CateringPlanPurchaseController;
use App\Models\Card;
use App\Models\CateringPlan;
use App\Models\CateringPlanPurchase;
use App\Models\EmailForOrder;
use App\Models\Organisation;
use App\Models\OrganisationSetting;
use App\OrdersExport;
use App\OrdersRfidFilteredExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Product;
use App\ProductStock;
use App\CommissionHistory;
use App\OrderDetail;
use App\CouponUsage;
use App\User;
use App\BusinessSetting;
use Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use DB;
use Mail;
use App\Mail\InvoiceEmailManager;
use CoreComponentRepository;
use App\Models\Gateways\Viva as VivaWallet;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource to seller.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $orders = DB::table('orders')
            ->orderBy('code', 'desc')
//                    ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('seller_id', Auth::user()->id)
            ->select('orders.id')
            ->distinct();

        if ($request->payment_status != null) {
            $orders = $orders->where('payment_status', $request->payment_status);
            $payment_status = $request->payment_status;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');

        }

        $orders = $orders->paginate(15);

        foreach ($orders as $key => $value) {
            $order = \App\Order::find($value->id);
            $order->viewed = 1;
            $order->save();
        }

        return view('frontend.user.seller.orders', compact('orders', 'payment_status', 'delivery_status', 'sort_search'));
    }

    // All Orders
    public function all_orders(Request $request)
    {
//        return $request->organisation;
        $date = $request->date;
        $sort_search = null;
        $orders = Order::orderBy('created_at', 'desc');
        $selected_organisations = [];
        $org_settings = [];
        $catering_plans = [];

        if ($request->has('organisation')) {
            $selected_organisations = $request->organisation;
        }


        if ($request->has('catering_plan')) {
            $catering_plans = $request->catering_plan;//CateringPlan::whereIn('id', $request->catering_plan)->get();
        }

        if (count($selected_organisations) > 0 && !in_array('all', $selected_organisations)) {

            foreach ($selected_organisations as $selected_organisation) {
                $organisation = Organisation::find($selected_organisation);
                if ($organisation != null) {
                    $org_settings[] = $organisation->currentSettings()->id;
                }
            }
        } else {

            foreach (Organisation::all() as $organisation) {

                $set = $organisation->currentSettings();

                if (!empty($set)) {
                    $org_settings[] = $organisation->currentSettings()->id;
                }
            }
        }


        if ($request->has('catering_plan')) {

            $orders = DB::table('catering_plan_purchases')
                ->whereIn('organisation_settings_id', $org_settings)
                ->whereIn('catering_plan_id', $catering_plans)
                ->where('deleted_at', '=', null)
                ->join('order_details', 'order_details.type_id', '=', 'catering_plan_purchases.id')
                ->where('order_details.type', '=', 'catering_plan')
//                ->where('order_details.order_id', '=', '535')
                ->join('orders', 'orders.id', '=', 'order_details.order_id')
                ->orderBy('orders.created_at', 'desc');


        } else {
            $orders = DB::table('catering_plan_purchases')
                ->whereIn('organisation_settings_id', $org_settings)
                ->where('deleted_at', '=', null)
                ->join('order_details', 'order_details.type_id', '=', 'catering_plan_purchases.id')
                ->where('order_details.type', '=', 'catering_plan')
                ->join('orders', 'orders.id', '=', 'order_details.order_id')
                ->orderBy('orders.created_at', 'desc');
        }


        if ($request->has('search') && $request->search != null) {

            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');

            if ($orders->count() == 0) {

                $card = Card::where('rfid_no', $request->search)->first();

                if ($card != null) {

                    $orders = DB::table('catering_plan_purchases')->where('card_id', '=', $card->id)->where('deleted_at', '=', null)
                        ->join('order_details', 'order_details.type_id', '=', 'catering_plan_purchases.id')
                        ->where('order_details.type', '=', 'catering_plan')
                        ->join('orders', 'orders.id', '=', 'order_details.order_id');

                    if ($date != null) {

                        $start_date = Carbon::create(date('Y-m-d', strtotime(explode(" to ", $date)[0])))->format('Y-m-d H:i:s');

                        $end_date = Carbon::create(date('Y-m-d', strtotime(explode(" to ", $date)[1])))->endOfDay()->format('Y-m-d H:i:s');

                        $orders = $orders->where('orders.created_at', '>=', $start_date)->where('orders.created_at', '<=', $end_date);

                    }

                    $rfid_search = 1;

                    $total_plans = count($orders->groupBy('order_details.id')->get());

                    $orders = $orders->orderBy('orders.created_at', 'desc')->select('orders.*')->groupBy('orders.code');

                    $total_orders = count($orders->get());

                    $orders = $orders->paginate(15);

                    return view('backend.sales.all_orders.index', compact('orders', 'sort_search', 'total_plans', 'total_orders', 'date', 'rfid_search', 'card'));
                }

            }

        }

        if ($date != null) {

            $start_date = Carbon::create(date('Y-m-d', strtotime(explode(" to ", $date)[0])))->format('Y-m-d H:i:s');

            $end_date = Carbon::create(date('Y-m-d', strtotime(explode(" to ", $date)[1])))->endOfDay()->format('Y-m-d H:i:s');

            $orders = $orders->where('orders.created_at', '>=', $start_date)->where('orders.created_at', '<=', $end_date);
        }

        $total_plans = count($orders->distinct()->get(['type_id']));
        $total_orders = count($orders->distinct()->get(['code']));


        if (!$request->has('form_type') || $request->form_type == 'filter') {


            $orders = $orders->select('orders.*')->distinct()->groupBy('code');

            $orders = $orders->paginate(15);

            return view('backend.sales.all_orders.index', compact('orders', 'sort_search', 'date', 'selected_organisations', 'catering_plans', 'total_orders', 'total_plans'));

        } else if ($request->form_type == 'export') {


            $filename = 'Orders Export ' . Carbon::now();


            if ($request->has('rfid_filtered')) {

                $filename = $filename . ' - Total Plans - ' . $total_plans;

                $orders = $orders->select('orders.*', 'catering_plan_purchases.card_id', 'catering_plan_purchases.catering_plan_id', 'catering_plan_purchases.organisation_settings_id')->get();
                return Excel::download(new OrdersRfidFilteredExport($orders), $filename . '.xlsx');

            } else {

                $filename = $filename . ' - Total Orders - ' . $total_orders;

                $orders = $orders->select('orders.*', 'catering_plan_purchases.organisation_settings_id')->distinct()->groupBy('code');

                $orders = $orders->get();

                return Excel::download(new OrdersExport($orders), $filename . '.xlsx');
            }

        }
    }


    public function all_orders_show($id)
    {
        $order = Order::findOrFail(decrypt($id));

        return view('backend.sales.all_orders.show', compact('order'));
    }

    // Inhouse Orders
    public function admin_orders(Request $request)
    {
        $date = $request->date;
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $admin_user_id = User::where('user_type', 'admin')->first()->id;
        $orders = DB::table('orders')
            ->orderBy('code', 'desc')
//                    ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('seller_id', $admin_user_id)
            ->select('orders.id')
            ->distinct();

        if ($request->payment_type != null) {
            $orders = $orders->where('payment_status', $request->payment_type);
            $payment_status = $request->payment_type;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($date != null) {
            $orders = $orders->whereDate('orders.created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('orders.created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }

        $orders = $orders->paginate(15);
        return view('backend.sales.inhouse_orders.index', compact('orders', 'payment_status', 'delivery_status', 'sort_search', 'admin_user_id', 'date'));
    }

    public function show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $order->viewed = 1;
        $order->save();
        return view('backend.sales.inhouse_orders.show', compact('order'));
    }

    // Seller Orders
    public function seller_orders(Request $request)
    {
        CoreComponentRepository::instantiateShopRepository();

        $date = $request->date;
        $seller_id = $request->seller_id;
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $admin_user_id = User::where('user_type', 'admin')->first()->id;
        $orders = DB::table('orders')
            ->orderBy('code', 'desc')
//                    ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('orders.seller_id', '!=', $admin_user_id)
            ->select('orders.id')
            ->distinct();

        if ($request->payment_type != null) {
            $orders = $orders->where('orders.payment_status', $request->payment_type);
            $payment_status = $request->payment_type;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($date != null) {
            $orders = $orders->whereDate('orders.created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('orders.created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }
        if ($seller_id) {
            $orders = $orders->where('seller_id', $seller_id);
        }

        $orders = $orders->paginate(15);
        return view('backend.sales.seller_orders.index', compact('orders', 'payment_status', 'delivery_status', 'sort_search', 'admin_user_id', 'seller_id', 'date'));
    }

    public function seller_orders_show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $order->viewed = 1;
        $order->save();
        return view('backend.sales.seller_orders.show', compact('order'));
    }


    // Pickup point orders
    public function pickup_point_order_index(Request $request)
    {
        $date = $request->date;
        $sort_search = null;

        if (Auth::user()->user_type == 'staff' && Auth::user()->staff->pick_up_point != null) {
            //$orders = Order::where('pickup_point_id', Auth::user()->staff->pick_up_point->id)->get();
            $orders = DB::table('orders')
                ->orderBy('code', 'desc')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->where('order_details.pickup_point_id', Auth::user()->staff->pick_up_point->id)
                ->select('orders.id')
                ->distinct();

            if ($request->has('search')) {
                $sort_search = $request->search;
                $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
            }
            if ($date != null) {
                $orders = $orders->whereDate('orders.created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('orders.created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
            }

            $orders = $orders->paginate(15);

            return view('backend.sales.pickup_point_orders.index', compact('orders'));
        } else {
            //$orders = Order::where('shipping_type', 'Pick-up Point')->get();
            $orders = DB::table('orders')
                ->orderBy('code', 'desc')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->where('order_details.shipping_type', 'pickup_point')
                ->select('orders.id')
                ->distinct();

            if ($request->has('search')) {
                $sort_search = $request->search;
                $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
            }
            if ($date != null) {
                $orders = $orders->whereDate('orders.created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('orders.created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
            }

            $orders = $orders->paginate(15);

            return view('backend.sales.pickup_point_orders.index', compact('orders', 'sort_search', 'date'));
        }
    }

    public function pickup_point_order_sales_show($id)
    {
        if (Auth::user()->user_type == 'staff') {
            $order = Order::findOrFail(decrypt($id));
            return view('backend.sales.pickup_point_orders.show', compact('order'));
        } else {
            $order = Order::findOrFail(decrypt($id));
            return view('backend.sales.pickup_point_orders.show', compact('order'));
        }
    }

    /**
     * Display a single sale to admin.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $order = new Order;
        if (Auth::check()) {
            $order->user_id = Auth::user()->id;
        } else {
            $order->guest_id = mt_rand(100000, 999999);
        }
        $shipping_method = (object)$request->session()->get('shipping_method');

        $order->seller_id = Session::get('owner_id');
        $order->shipping_address = json_encode($request->session()->get('shipping_info'));
        $order->vat_percentage = getVatFromSession('percentage');
        $order->vat_btms_code = getVatFromSession('btms_code');
        $order->payment_type = $request->payment_option;
        $order->delivery_viewed = '0';
        $order->shipping_cost = $shipping_method->amount;
        $order->shipping_vat = $shipping_method->vat;
        $order->shipping_method = $shipping_method->method;
        $order->pickup_point = $shipping_method->pickup_point;
        $order->payment_status_viewed = '0';
        $order->code = Order::generateOrderId();
        $order->date = strtotime('now');
        $order->vat_amount = $request->session()->get('vat_amount');
        $order->subtotal = $request->session()->get('subtotal');
        $order->grand_total = $request->session()->get('total');

        if ($order->save()) {
            $subtotal = 0;
            $tax = 0;
            $shipping = 0;

            //calculate shipping is to get shipping costs of different types
            $admin_products = array();
            $seller_products = array();

            //Order Details Storing
            foreach (Session::get('cart') as $key => $cartItem) {
                $product = Product::find($cartItem['id']);

                if ($product->added_by == 'admin') {
                    array_push($admin_products, $cartItem['id']);
                } else {
                    $product_ids = array();
                    if (array_key_exists($product->user_id, $seller_products)) {
                        $product_ids = $seller_products[$product->user_id];
                    }
                    array_push($product_ids, $cartItem['id']);
                    $seller_products[$product->user_id] = $product_ids;
                }

                $subtotal += $cartItem['price'] * $cartItem['quantity'];
                $tax += $cartItem['tax'] * $cartItem['quantity'];

                $product_variation = $cartItem['variant'];

                if ($product_variation != null) {
                    $product_stock = $product->stocks->where('variant', $product_variation)->first();
                    if ($product->digital != 1 && $cartItem['quantity'] > $product_stock->qty) {
                        flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
                        $order->delete();
                        return redirect()->route('cart')->send();
                    } else {
                        $product_stock->qty -= $cartItem['quantity'];
                        $product_stock->save();
                    }
                } else {
                    if ($product->digital != 1 && $cartItem['quantity'] > $product->current_stock) {
                        flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
                        $order->delete();
                        return redirect()->route('cart')->send();
                    } else {
                        $product->current_stock -= $cartItem['quantity'];
                        $product->save();
//                        $product_stock->qty -= $cartItem['quantity'];
//                        $product_stock->save();
                    }
                }

                $order_detail = new OrderDetail;
                $order_detail->order_id = $order->id;
                $order_detail->seller_id = $product->user_id;
                $order_detail->product_id = $product->id;
                $order_detail->variation = $product_variation;
                $order_detail->price_type_id = $cartItem['price_type_id'];
                $order_detail->price = $cartItem['price'] * $cartItem['quantity'];
                $order_detail->vat_amount = ($cartItem['price'] * $order->vat_percentage) / 100;
                $order_detail->vat_perc = $order->vat_percentage;
                $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];
                $order_detail->shipping_type = Session::get('shipping_method')['method'];
                $order_detail->product_referral_code = $cartItem['product_referral_code'];
                $order_detail->disc_perc = $cartItem['disc_percentage'] ?? 0;
                $order_detail->disc_amount = isset($cartItem['disc_amount']) ? ($cartItem['quantity'] * $cartItem['disc_amount']) : 0;

                //Dividing Shipping Costs
                $shipping_info = $request->session()->get('shipping_info');

                if (Session::get('shipping_method')['method'] == 'home_delivery') {
                    $order_detail->shipping_cost = getShippingCost($key);

                    if (isset($cartItem['shipping']) && is_array(json_decode($cartItem['shipping'], true))) {
                        foreach (json_decode($cartItem['shipping'], true) as $shipping_region => $val) {
                            if ($shipping_info['city'] == $shipping_region) {
                                $order_detail->shipping_cost = (double)($val);
                            } else {
                                $order_detail->shipping_cost = 0;
                            }
                        }
                    } else {
                        if (!$cartItem['shipping']) {
                            $order_detail->shipping_cost = 0;
                        }
                    }
                } else {
                    $order_detail->shipping_cost = 0;
                }
                if ($product->is_quantity_multiplied == 1 && get_setting('shipping_type') == 'product_wise_shipping') {
                    $order_detail->shipping_cost = $order_detail->shipping_cost * $cartItem['quantity'];
                }
                $shipping += $order_detail->shipping_cost;

                if (Session::get('shipping_method')['pickup_point'] != null) {
                    $order_detail->pickup_point_id = Session::get('shipping_method')['pickup_point'];
                }
                //End of storing shipping cost

                $order_detail->quantity = $cartItem['quantity'];
                $order_detail->save();

                $product->num_of_sale++;
                $product->save();

                if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null &&
                    \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
                    if ($order_detail->product_referral_code) {
                        $referred_by_user = User::where('referral_code', $order_detail->product_referral_code)->first();

                        $affiliateController = new AffiliateController;
                        $affiliateController->processAffiliateStats($referred_by_user->id, 0, $order_detail->quantity, 0, 0);
                    }
                }
            }


//            $order->grand_total = $subtotal + $tax + $shipping;

            if (Session::has('club_point')) {
                $order->grand_total -= Session::get('club_point');
                $clubpointController = new ClubPointController;
                $clubpointController->deductClubPoints($order->user_id, Session::get('club_point'));

                $order->club_point = Session::get('club_point');
            }

            if (Session::has('coupon_discount')) {
                $order->grand_total -= Session::get('coupon_discount');
                $order->coupon_discount = Session::get('coupon_discount');

                $coupon_usage = new CouponUsage;
                $coupon_usage->user_id = Auth::user()->id;
                $coupon_usage->coupon_id = Session::get('coupon_id');
                $coupon_usage->save();
            }

            $order->save();
            if ($order->user != null) {
                $user = $order->user;
                $user->cart = null;
                $user->save();
            }

            try {
                BtmsOrder::add($order->id);
            } catch (\Exception $e) {
                Log::error('Import Order to BTMS: ' . $e->getMessage());
            }

            $array['view'] = 'emails.invoice';
            $array['subject'] = translate('Your order has been placed') . ' - ' . $order->code;
            $array['from'] = env('MAIL_USERNAME');
            $array['order'] = $order;

            foreach ($seller_products as $key => $seller_product) {
                try {
                    Mail::to(\App\User::find($key)->email)->queue(new InvoiceEmailManager($array));
                } catch (\Exception $e) {

                }
            }

            if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\OtpConfiguration::where('type', 'otp_for_order')->first()->value) {
                try {
                    $otpController = new OTPVerificationController;
                    $otpController->send_order_code($order);
                } catch (\Exception $e) {

                }
            }

            //sends email to customer with the invoice pdf attached
            if (env('MAIL_USERNAME') != null) {
                try {
                    Mail::to($request->session()->get('shipping_info')['email'])->queue(new InvoiceEmailManager($array));
                    $array['subject'] = translate('An order has been placed') . ' - ' . $order->code;
                    Mail::to(config('app.order_email'))->queue(new InvoiceEmailManager($array));
                } catch (\Exception $e) {

                }
            }

            $request->session()->put('order_id', $order->id);
        }
    }


    public function storeForVivaWallet($viva_wallet_log_id, $transactionStatusId)
    {

        sleep(1);
        $viva_wallet_log = VivaWallet::where('id', $viva_wallet_log_id)->where('run_script', 0)->where('start_process', '=', 0)->orderBy('created_at', 'desc')->first();

        if ($viva_wallet_log == null) {
            return response('', '406');
        }
        $viva_wallet_log->start_process = 1;
        $viva_wallet_log->save();


        $shipping_method = json_decode($viva_wallet_log->shipping_method);
        $customer_details = json_decode($viva_wallet_log->customer_details);

        $order = new Order;
        if ($viva_wallet_log->user_id) {
            $order->user_id = $viva_wallet_log->user_id;
        } elseif ($viva_wallet_log->guest_id) {
            $order->guest_id = $viva_wallet_log->guest_id;
        }

        $order->shipping_address = $viva_wallet_log->customer_details;

        $order->vat_percentage = $viva_wallet_log->vat_percentage;
        $order->vat_btms_code = $viva_wallet_log->vat_btms_code;

        $order->payment_type = "viva_wallet";
        $order->payment_status = ($transactionStatusId == 'F' ? "paid" : ($transactionStatusId == 'A' ? "pending" : 'unpaid'));
        $order->delivery_viewed = '0';
        $order->payment_status_viewed = '0';
        $order->code = $viva_wallet_log->OrderCode;
        $order->date = strtotime('now');
        $order->vat_amount = $viva_wallet_log->vat;
        $order->subtotal = $viva_wallet_log->subtotal;

        if ($order->save()) {
            $subtotal = $viva_wallet_log->subtotal;
            $tax = $viva_wallet_log->vat;
            $shipping = $viva_wallet_log->shipping;

            //calculate shipping is to get shipping costs of different types
            $admin_products = array();
            $seller_products = array();
            $emails = array();

            //Order Details Storing
            foreach (json_decode($viva_wallet_log->cart_items, true) as $key => $cartItem) {

                if ($cartItem['type'] == 'catering_plan') {

                    $subtotal += $cartItem['price'];
                    $tax += $cartItem['tax'];
                    $order_detail = new OrderDetail;
                    $order_detail->order_id = $order->id;

                    try {
                        $order_detail->type = $cartItem['type'];
                        $catering_plan_purchase = new CateringPlanPurchase;
                        $catering_plan_purchase->user_id = $viva_wallet_log->user_id;
                        $catering_plan_purchase->organisation_settings_id = $cartItem['organisation_setting_id'];
                        $catering_plan_purchase->catering_plan_id = $cartItem['type_id'];

                        $catering_plan_purchase->card_id = $cartItem['card_id'];

                        try {
                            $card = Card::find($catering_plan_purchase->card_id);
                            if ($card != null) {
                                $organisation = Organisation::find($card->organisation_id);

                                if ($organisation != null) {
//                                    if ($_SERVER['REMOTE_ADDR'] == '82.102.76.201') {
                                    if (($organisation->email_for_order_id == null || $organisation->email_for_order_id == 1)) {
                                        $emails[] = EmailForOrder::find(1)->email;
                                    } else {
                                        $emails[] = $organisation->email_for_order->email; // EmailForOrder::find($organisation->)->email;
                                    }
//                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            Log::error("Problem with order $order->id. Message: Card with id " . $cartItem['card_id'] . " could not be found.");
                        }

                        $catering_plan_purchase->from_date = $cartItem['from_date'];
                        $catering_plan_purchase->to_date = $cartItem['to_date'];
                        $catering_plan_purchase->snack_quantity = $cartItem['snack_num'];
                        $catering_plan_purchase->meal_quantity = $cartItem['meal_num'];
                        $catering_plan_purchase->price = $cartItem['total'];

                        $this->calculateCateringPlanOrderActiveDays($catering_plan_purchase, $cartItem);
                        $catering_plan_purchase->save();

                        $order_detail->type_id = $catering_plan_purchase->id;
                        $order_detail->price = $cartItem['price'];
                        $order_detail->total = $cartItem['total'];
                        $order_detail->vat_amount = $cartItem['tax']; //($cartItem['price'] * $order->vat_percentage) / 100;
                        $order_detail->vat_perc = $order->vat_percentage;
                        $order_detail->tax = $cartItem['tax'];
                        $order_detail->shipping_cost = 0;
                        $order_detail->disc_perc = 0; //$cartItem['disc_percentage'] ?? 0;

                        $order_detail->quantity = 1;

                        $order_detail->save();
                    } catch (\Exception $exception) {
                        Log::error("Problem with order $order->id. Message: " . $exception->getMessage());
                    }


                }

                if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null &&
                    \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
                    if ($order_detail->product_referral_code) {
                        $referred_by_user = User::where('referral_code', $order_detail->product_referral_code)->first();

                        $affiliateController = new AffiliateController;
                        $affiliateController->processAffiliateStats($referred_by_user->id, 0, $order_detail->quantity, 0, 0);
                    }
                }
            }

            $order->grand_total = $viva_wallet_log->total;

            if (Session::has('club_point')) {
                $order->grand_total -= Session::get('club_point');
                $clubpointController = new ClubPointController;
                $clubpointController->deductClubPoints($order->user_id, Session::get('club_point'));

                $order->club_point = Session::get('club_point');
            }

            if (Session::has('coupon_discount')) {
                $order->grand_total -= Session::get('coupon_discount');
                $order->coupon_discount = Session::get('coupon_discount');

                $coupon_usage = new CouponUsage;
                $coupon_usage->user_id = Auth::user()->id;
                $coupon_usage->coupon_id = Session::get('coupon_id');
                $coupon_usage->save();
            }

            $order->save();
            if ($order->user != null) {
                $user = $order->user;
                $user->cart = null;
                $user->save();
            }

            try {
                BtmsOrder::add($order->id);
            } catch (\Exception $e) {
                Log::error('Import Order to BTMS: ' . $e->getMessage());
            }

            $array['view'] = 'emails.invoice';
            $array['subject'] = translate('Your order has been placed') . ' - ' . $order->code;
            $array['from'] = env('MAIL_USERNAME');
            $array['order'] = $order;


            //sends email to customer with the invoice pdf attached
            if (env('MAIL_USERNAME') != null) {
                try {
                    Mail::to($customer_details->email)->queue(new InvoiceEmailManager($array));
                    $array['subject'] = translate('An order has been placed') . ' - ' . $order->code;

                    $emails = array_unique($emails);

                    if (count($emails) > 0) {
                        foreach ($emails as $email) {
                            Mail::to($email)->queue(new InvoiceEmailManager($array));
                        }
                    } else {
                        Mail::to(config('app.order_email'))->queue(new InvoiceEmailManager($array));
                    }


//                    Mail::to(config('app.order_email'))->queue(new InvoiceEmailManager($array));

                } catch (\Exception $e) {
                }
            }

            return $order;
        }
    }


    public function calculateCateringPlanOrderActiveDays($catering_plan_purchase, $cartItem)
    {

        // calculate assigned days of this plan
        $organisation_setting = OrganisationSetting::findorfail($cartItem['organisation_setting_id']);

        $working_week_days = json_decode($organisation_setting->working_week_days);

        $holidays = json_decode($organisation_setting->holidays);

        $this_date = Carbon::create($cartItem['from_date']);

        $last_date = Carbon::create($cartItem['to_date']);

        $working_days_january = array();
        $working_days_february = array();
        $working_days_march = array();
        $working_days_april = array();
        $working_days_may = array();
        $working_days_june = array();
        $working_days_july = array();
        $working_days_august = array();
        $working_days_september = array();
        $working_days_october = array();
        $working_days_november = array();
        $working_days_december = array();

        $num_of_days_calc = 0;

        while ($last_date->gte($this_date)) {

            $day = substr($this_date->format('l'), 0, 3);

            if (in_array($day, $working_week_days)) {

                if (!in_array($this_date->format('Y-m-d'), $holidays)) {


                    $month = $this_date->format('m'); // 07
                    $date = $this_date->format('Y-m-d');

                    if ($month == '01') {
                        $working_days_january[] = $date;

                    } else if ($month == '02') {
                        $working_days_february[] = $date;

                    } else if ($month == '03') {
                        $working_days_march[] = $date;

                    } else if ($month == '04') {
                        $working_days_april[] = $date;

                    } else if ($month == '05') {
                        $working_days_may[] = $date;

                    } else if ($month == '06') {
                        $working_days_june[] = $date;

                    } else if ($month == '07') {
                        $working_days_july[] = $date;

                    } else if ($month == '08') {
                        $working_days_august[] = $date;

                    } else if ($month == '09') {
                        $working_days_september[] = $date;

                    } else if ($month == '10') {
                        $working_days_october[] = $date;

                    } else if ($month == '11') {
                        $working_days_november[] = $date;

                    } else {
                        $working_days_december[] = $date;
                    }

                    $num_of_days_calc = $num_of_days_calc + 1;

                }
            } else {
                if ($organisation_setting->extra_days()->where('date', '=', $this_date->format('Y-m-d'))->count() > 0) {
                    $month = $this_date->format('m'); // 07
                    $date = $this_date->format('Y-m-d');

                    if ($month == '01') {
                        $working_days_january[] = $date;

                    } else if ($month == '02') {
                        $working_days_february[] = $date;

                    } else if ($month == '03') {
                        $working_days_march[] = $date;

                    } else if ($month == '04') {
                        $working_days_april[] = $date;

                    } else if ($month == '05') {
                        $working_days_may[] = $date;

                    } else if ($month == '06') {
                        $working_days_june[] = $date;

                    } else if ($month == '07') {
                        $working_days_july[] = $date;

                    } else if ($month == '08') {
                        $working_days_august[] = $date;

                    } else if ($month == '09') {
                        $working_days_september[] = $date;

                    } else if ($month == '10') {
                        $working_days_october[] = $date;

                    } else if ($month == '11') {
                        $working_days_november[] = $date;

                    } else {
                        $working_days_december[] = $date;
                    }

                    $num_of_days_calc = $num_of_days_calc + 1;
                }
            }
            $this_date->addDay();
        }

        $catering_plan_purchase->num_of_days = $num_of_days_calc;

        $catering_plan_purchase->active_days_january = json_encode($working_days_january);
        $catering_plan_purchase->active_days_february = json_encode($working_days_february);
        $catering_plan_purchase->active_days_march = json_encode($working_days_march);
        $catering_plan_purchase->active_days_april = json_encode($working_days_april);
        $catering_plan_purchase->active_days_may = json_encode($working_days_may);
        $catering_plan_purchase->active_days_june = json_encode($working_days_june);
        $catering_plan_purchase->active_days_july = json_encode($working_days_july);
        $catering_plan_purchase->active_days_august = json_encode($working_days_august);
        $catering_plan_purchase->active_days_september = json_encode($working_days_september);
        $catering_plan_purchase->active_days_october = json_encode($working_days_october);
        $catering_plan_purchase->active_days_november = json_encode($working_days_november);
        $catering_plan_purchase->active_days_december = json_encode($working_days_december);


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        if ($order != null) {
            foreach ($order->orderDetails as $key => $orderDetail) {
                try {
                    if ($orderDetail->variation != null) {
                        $product_stock = ProductStock::where('product_id', $orderDetail->product_id)->where('variant', $orderDetail->variation)->first();
                        if ($product_stock != null) {
                            $product_stock->qty += $orderDetail->quantity;
                            $product_stock->save();
                        }
                    } else {
                        $product = $orderDetail->product;
                        $product->current_stock += $orderDetail->quantity;
                        $product->save();
                    }
                } catch (\Exception $e) {

                }

                $orderDetail->delete();
            }
            $order->delete();
            flash(translate('Order has been deleted successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }
        return back();
    }

    public function order_details(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->save();
        return view('frontend.user.seller.order_details_seller', compact('order'));
    }

    public function update_delivery_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->delivery_viewed = '0';
        $order->delivery_status = $request->status;
        $order->save();

        if (Auth::user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
                $orderDetail->delivery_status = $request->status;
                $orderDetail->save();

                if ($request->status == 'cancelled') {
                    if ($orderDetail->variation != null) {
                        $product_stock = ProductStock::where('product_id', $orderDetail->product_id)
                            ->where('variant', $orderDetail->variation)
                            ->first();
                        if ($product_stock != null) {
                            $product_stock->qty += $orderDetail->quantity;
                            $product_stock->save();
                        }
                    } else {
                        $product = Product::find($orderDetail->product_id);
                        $product->current_stock += $orderDetail->quantity;
                        $product->save();
                    }
                }
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->delivery_status = $request->status;
                $orderDetail->save();

                if ($request->status == 'cancelled') {
                    if ($orderDetail->variation != null) {
                        $product_stock = ProductStock::where('product_id', $orderDetail->product_id)
                            ->where('variant', $orderDetail->variation)
                            ->first();
                        if ($product_stock != null) {
                            $product_stock->qty += $orderDetail->quantity;
                            $product_stock->save();
                        }
                    } else {
                        $product = Product::find($orderDetail->product_id);
                        $product->current_stock += $orderDetail->quantity;
                        $product->save();
                    }
                }

                if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
                    if (($request->status == 'delivered' || $request->status == 'cancelled') &&
                        $orderDetail->product_referral_code) {

                        $no_of_delivered = 0;
                        $no_of_canceled = 0;

                        if ($request->status == 'delivered') {
                            $no_of_delivered = $orderDetail->quantity;
                        }
                        if ($request->status == 'cancelled') {
                            $no_of_canceled = $orderDetail->quantity;
                        }

                        $referred_by_user = User::where('referral_code', $orderDetail->product_referral_code)->first();

                        $affiliateController = new AffiliateController;
                        $affiliateController->processAffiliateStats($referred_by_user->id, 0, 0, $no_of_delivered, $no_of_canceled);
                    }
                }
            }
        }

        if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\OtpConfiguration::where('type', 'otp_for_delivery_status')->first()->value) {
            try {
                $otpController = new OTPVerificationController;
                $otpController->send_delivery_status($order);
            } catch (\Exception $e) {
            }
        }

        return 1;
    }

    public function update_payment_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->payment_status_viewed = '0';
        $order->save();

        if (Auth::user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        }

        $status = 'paid';
        foreach ($order->orderDetails as $key => $orderDetail) {
            if ($orderDetail->payment_status != 'paid') {
                $status = 'unpaid';
            }
        }
        $order->payment_status = $status;
        $order->save();


        if ($order->payment_status == 'paid' && $order->commission_calculated == 0) {
            if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() == null ||
                !\App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {

                if ($order->payment_type == 'cash_on_delivery') {
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
                            $admin_commission = ($orderDetail->price * $commission_percentage) / 100;

                            if (get_setting('product_manage_by_admin') == 1) {
                                $seller_earning = ($orderDetail->tax + $orderDetail->price) - $admin_commission;
                                $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->tax + $orderDetail->price) - $admin_commission;
                            } else {
                                $seller_earning = $orderDetail->tax + $orderDetail->shipping_cost + $orderDetail->price - $admin_commission;
                                $seller->admin_to_pay = $seller->admin_to_pay - $admin_commission;
                            }

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
                } elseif ($order->manual_payment) {
                    foreach ($order->orderDetails as $key => $orderDetail) {
                        $orderDetail->payment_status = 'paid';
                        $orderDetail->save();
                        $commission_percentage = 0;
                        if (get_setting('category_wise_commission') != 1) {
                            $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                        } else if ($orderDetail->product->user->user_type == 'seller') {
                            $commission_percentage = $orderDetail->product->category->commision_rate;
                        }
                        if ($orderDetail->product->user->user_type == 'seller') {
                            $seller = $orderDetail->product->user->seller;
                            $admin_commission = ($orderDetail->price * $commission_percentage) / 100;

                            if (get_setting('product_manage_by_admin') == 1) {
                                $seller_earning = ($orderDetail->tax + $orderDetail->price) - $admin_commission;
                                $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax;
                            } else {
                                $seller_earning = $orderDetail->tax + $orderDetail->shipping_cost + $orderDetail->price - $admin_commission;
                                $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
                            }

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
                }
            }

            if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
                $affiliateController = new AffiliateController;
                $affiliateController->processAffiliatePoints($order);
            }

            if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated) {
                if ($order->user != null) {
                    $clubpointController = new ClubPointController;
                    $clubpointController->processClubPoints($order);
                }
            }

            $order->commission_calculated = 1;
            $order->save();
        }

        if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\OtpConfiguration::where('type', 'otp_for_paid_status')->first()->value) {
            try {
                $otpController = new OTPVerificationController;
                $otpController->send_payment_status($order);
            } catch (\Exception $e) {
            }
        }
        return 1;
    }

    public function update_tracking_code(Request $request)
    {
        if (!$request->has('order_id')) {
            abort('500');
        }
        $order_id = $request->order_id;
        $tracking_number = $request->tracking_number;

        $order = Order::findOrFail(decrypt($order_id));
        $order->tracking_number = $tracking_number;

        $order->save();
        return redirect()->route('all_orders.show', $order_id);
    }
}
