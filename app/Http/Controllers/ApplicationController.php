<?php

namespace App\Http\Controllers;

use App\Models\AppOrder;
use App\Models\AppOrderDetail;
use App\Models\AppRefundDetail;
use App\Models\CanteenMenu;
use App\Models\CanteenProduct;
use App\Models\Cart;
use App\Models\Gateways\AppViva;
use App\Models\Gateways\Viva;
use App\Models\Organisation;
use App\Models\OrganisationBreak;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Log;
use Session;
use Auth;
use Hash;
use Cookie;
use Mail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\CanteenPurchase;



class ApplicationController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * Show the application frontend home.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->guard('application')->check()) {
            return redirect()->route('application.login');
        }
        $user = auth()->guard('application')->user(); // canteen user
        $rfid_card = $user->card;
        $organisation = $rfid_card->organisation;

        if($organisation->canteen != 1){
            return redirect()->route('application.logout');
        }

        return view('application.index', compact('user', 'rfid_card', 'organisation'));
    }

    public function login()
    {
        if (auth()->guard('application')->check()){
            return redirect()->route('application.home');
        }
        return view('application.user_login');
    }

    public function choose_snack(Request $request)
    {
        $date = decrypt($request->date);
        $break_id = decrypt($request->break_id);

        $break = OrganisationBreak::find($break_id);
        $user = auth()->guard('application')->user(); // canteen user

        if(!auth()->guard('application')->check() || $break==null){
            flash(translate('Sorry! Something went wrong.'))->error();
            return redirect()->route('application.home');
        }

        $organisation = Organisation::find($break->organisation_id);
        if($organisation==null){
            flash(translate('Sorry! Something went wrong.'))->error();
            return redirect()->route('application.home');
        }

        $canteen_setting = $organisation->current_canteen_settings();

        if($canteen_setting->id != $break->canteen_setting_id){
            flash(translate('Sorry! Something went wrong.'))->error();
            return redirect()->route('application.home');
        }

        $minimum_preorder_minutes = $canteen_setting->minimum_preorder_minutes;

        if(!preorder_availability($date, $break, $minimum_preorder_minutes)){
            #546525555
            flash(translate('Sorry! Something went wrong.'))->error();
            return redirect()->route('application.home');
        }

        return view('application.choose_snack', compact('date', 'break', 'organisation', 'user', 'canteen_setting'));
    }

    public function view_cart()
    {
//        $this->appCartRefresh();
        return view('application.view_cart');
    }

    public function view_checkout(Request $request)
    {

        $cart_check = $this->appCartRefresh();

        if(!$cart_check){
            return redirect()->route('application.cart');
        }

        if(!$request->has('payment')){
            return back()->withErrors(['Please select payment type']);
        }

        if($request->payment!='current-card' && $request->payment!='other-card'){
            return back();
        }

        Session::put('payment_type', $request->payment);

        return view('application.view_checkout');
    }

    public function order_success($order_code)
    {

        if ($order_code) {
            $order = AppOrder::where('code', $order_code)->first();
        } else {
            $order = AppOrder::findOrFail(Session::get('order_id'));
        }

        $viva_log = AppViva::where('OrderCode', $order_code)->first();
        if($viva_log!=null){
            $viva_log->confirm_page_seen = 1;
            $viva_log->save();
        }

        if ($order == null) {
            Log::warning(basename(__FILE__)." ".__FUNCTION__.":".__LINE__." => Not found order with order code $order_code.");
            $viva_payment_log = AppViva::where('OrderCode', $order_code)->first();
            if ($viva_payment_log != null) {
                if (!$viva_payment_log->run_script && !$viva_payment_log->callback) {
                    Log::error(basename(__FILE__)." ".__FUNCTION__.":".__LINE__." => Viva Wallet response code not executed. Order code $order_code.");
                }
            }
        }

        if ($order->confirm_page_seen) return redirect()->route('application.home');

        return view('application.order_success', compact('order'));
    }

    public function order_pending($order_code)
    {

        $app_viva_log= AppViva::where('OrderCode', $order_code)->first();

        if ($app_viva_log != null) {

            if ($app_viva_log->pending_page_seen) return redirect()->route('application.home');

            $app_viva_log->pending_page_seen = 1;
            $app_viva_log->save();

            return view('application.order_pending', compact('app_viva_log'));


        }else{
            Log::error(basename(__FILE__)." ".__FUNCTION__.":".__LINE__." => Viva Wallet response code not executed. Order code $order_code.");
            return redirect()->route('application_home');
        }



    }

    public function history()
    {
        return view('application.history');
    }

    public function upcoming_meals()
    {
        return view('application.upcoming_meals');
    }

    public function contact()
    {
        return view('application.contact');
    }
    public function account()
    {
        return view('application.user.account');
    }

    public function profile()
    {
        return view('application.user.profile');
    }

    public function available_balance()
    {
        return view('application.user.available_balance');
    }

    public function credit_card()
    {
        return view('application.user.credit_card');
    }

    public function get_week_calendar_view(Request $request)
    {

        $today = Carbon::today();
        $next_date_available_found = false;

        if($request->next_date_available_found){
            $next_date_available_found = $request->next_date_available_found;
        }

        if(!$request->has('type') || !$request->has('weeks') || !is_numeric($request->weeks) || $request->weeks < 0){
            return response()->json(['status' => 0, 'msg' => 'Specify type: next or prev week, and weeks: to add or remove']);
        }

        if($request->type == 'next'){

            $days_to_add = $request->weeks * 7;

            $this_week_start = Carbon::today()->addDays($days_to_add)->startOfWeek();
            $this_week_end = Carbon::today()->addDays($days_to_add)->endOfWeek();

        }elseif($request->type == 'prev'){

            $days_to_remove = $request->weeks * 7;

            $this_week_start = Carbon::today()->subDays($days_to_remove)->startOfWeek();
            $this_week_end = Carbon::today()->subDays($days_to_remove)->endOfWeek();

        }else{

            return response()->json(['status' => 0, 'msg' => 'Specify type: next or prev week']);

        }

        $type = $request->type;

        $user = auth()->guard('application')->user(); // canteen user
        $rfid_card = $user->card;
        $organisation = $rfid_card->organisation;
        $breaks = $organisation->breaks;
        $canteen_setting = $organisation->current_canteen_settings();
        $business_days = json_decode($canteen_setting->working_week_days);
        $holidays = json_decode($canteen_setting->holidays);
        $extra_days = $canteen_setting->extra_days->pluck('date')->toArray();

        $this_week_dates = [];  // $this_week_dates[11/12] = 'Mo'
        $this_week_full_dates = []; // $this_week_full_dates[11/12] = '2023-12-11'
        $dashboard_calendar = [];

        // for this week
        $canteen_purchases = CanteenPurchase::where('canteen_app_user_id', $user->id )->where('date', '>=', $this_week_start->format('Y-m-d'))
            ->where('date', '<=', $this_week_end->format('Y-m-d'))->groupBy('date')->orderBy('date')->get();

        foreach ($canteen_purchases as $purchase){

            if($purchase->date < $today->format('Y-m-d')){
                $dashboard_calendar[Carbon::create($purchase->date)->format('d/m')][$purchase->break_num] = 'old';
            }elseif ($purchase->date == $today->format('Y-m-d')){

                $temp = $purchase->date. ' '. $purchase->organisation_break->hour_from;

                if($temp<= Carbon::now()->format('Y-m-d H:i:s')){
                    $dashboard_calendar[$today->format('d/m')][$purchase->break_num] = 'old';
                }else{
                    $dashboard_calendar[$today->format('d/m')][$purchase->break_num] = 'preordered';
                }

            }else{
                $dashboard_calendar[Carbon::create($purchase->date)->format('d/m')][$purchase->break_num] = 'preordered';
            }

        }

        // Loop through each day of the week
        for ($date = $this_week_start; $date->lte($this_week_end); $date->addDay()) {

            $day = $date->format('D');

            if(in_array($day, $business_days) || in_array($date->format('Y-m-d'), $extra_days)){
                $this_week_dates[$date->format('d/m')] = substr($date->format('D'), 0,2);
                $this_week_full_dates[$date->format('d/m')] = $date->format('Y-m-d');
            }

        }

        $minimum_preorder_minutes = $canteen_setting->minimum_preorder_minutes;

        return response()->json(
            [   'status' => 1,
                'view' =>  view('application.partials.dashboard_calendar',
            compact('type','breaks', 'this_week_dates', 'this_week_full_dates', 'dashboard_calendar', 'holidays', 'next_date_available_found', 'minimum_preorder_minutes' ))->render() ,
               'dashboard_calendar' => $dashboard_calendar
            ]);

    }

    public function addToCart(Request $request)
    {

        $user = auth()->guard('application')->user(); // canteen user
        $rfid_card = $user->card;
        $organisation = $rfid_card->organisation;

        $date = $request->date;
        $break = OrganisationBreak::find($request->break_id);
        $day_name = Carbon::create($date)->format('l');

        if($date < Carbon::today()->format('Y-m-d') || $break==null || $break->organisation_id != $organisation->id){
            return response()->json(['status' => 0, 'msg' => 'Break or date info is not appropriate']);
        }

        $canteen_setting = $organisation->current_canteen_settings();
        $minimum_preorder_minutes = $canteen_setting->minimum_preorder_minutes;


        if(!preorder_availability($date, $break, $minimum_preorder_minutes)){
            #87987897897
            return response()->json(['status' => 0, 'msg' => 'Break or date info is not appropriate']);
        }

        $product = CanteenProduct::find($request->product_id);

        $product_available = $canteen_setting->canteen_menus->where('organisation_break_num', $break->break_num)->where('day',strtolower($day_name))->where('canteen_product_id',$product->id)->first();

        if($product == null || $product_available==null){
            return response()->json(['status' => 0, 'msg' => 'Product info is not appropriate']);
        }

        $purchases_price_for_today= CanteenPurchase::where('canteen_app_user_id', $user->id )->where('date', '=', $date)->sum('price');
        $cart_price_for_today = 0;

        if(Session::has('app_cart')){

            foreach (Session::get('app_cart') as $key => $cartItem) {

                if($cartItem['date'] == $date ){
                    if($cartItem['product_id'] != $product->id || $cartItem['break_id'] != $break->id){
                        $cart_price_for_today+=$cartItem['price']*$cartItem['quantity'];
                    }
                }

            }

        }

        $daily_limit = $user->daily_limit;
        $available_balance = $daily_limit-$purchases_price_for_today-$cart_price_for_today;

//        return response()->json(['status' => 0, '$cart_price_for_today' => $cart_price_for_today, '$purchases_price_for_today' => $purchases_price_for_today,
//            'available_balance' => single_price($available_balance), $temp, '$temp2' => $temp2]);


        $data = array();
        $data['product_id'] = $product->id;
        $data['date'] = $date;
        $data['day'] = $day_name;
        $data['break_id'] = $break->id;
        $data['break_sort'] = $request->break_sort;
        $data['custom_price_status'] = $product_available->custom_price_status;

        if($product_available->custom_price_status==1){
            $data['price'] = $product_available->custom_price;
        }else{
            $data['price'] = $product->price;
        }

        $data['quantity'] = $request->quantity;

        $quantity_in_cart = $request->quantity;

        if($request->session()->has('app_cart')) {
            $foundInCart = false;
            $cart = collect();

            foreach ($request->session()->get('app_cart') as $key => $cartItem){
                if($cartItem['product_id'] == $product->id && $cartItem['date'] == $date && $cartItem['break_id'] == $break->id){
                    $foundInCart = true;

                    $available_balance = $available_balance - $cartItem['price'] * $request->quantity;

                    if($available_balance<0){
                        return response()->json(['status' => 0, 'msg' => 'Not enough available balance 1',
                            'product_quantity_in_cart'=> $cartItem['quantity'], 'available_balance' => single_price($available_balance)]);
                    }

                    $cartItem['quantity'] = $request->quantity;
                    $quantity_in_cart = $cartItem['quantity'];
                }

                $cart->push($cartItem);

            }

            if (!$foundInCart) {

                $available_balance = $available_balance - $data['price'] * $request->quantity;

                if($available_balance<0){
                    return response()->json(['status' => 0, 'msg' => 'Not enough available balance 2', 'product_quantity_in_cart'=> 0 ]);
                }

                $cart->push($data);
            }

            $request->session()->put('app_cart', $cart);

        }else{

            $cart = collect([$data]);
            $request->session()->put('app_cart', $cart);
        }

        $this->updateTotals();
        $total = Session::get('app_total');
        $total_items = Session::get('total_items');

        //available limit for this date

        $purchases_price_for_today= CanteenPurchase::where('canteen_app_user_id', $user->id )->where('date', '=', $date)->sum('price');
        $cart_price_for_today = 0;

        if(Session::has('app_cart')){
            foreach (Session::get('app_cart') as $key => $cartItem) {
                if($cartItem['date'] == $date ){
                    $cart_price_for_today+=$cartItem['price']*$cartItem['quantity'];
                }
            }
        }

        $available_balance = $daily_limit-$purchases_price_for_today-$cart_price_for_today;

        Cart::refreshCartInDB();

        return response()->json(['status' => 1, 'total' => single_price($total), 'total_items' => $total_items, 'available_balance' => single_price($available_balance), 'available_balance_num' => $available_balance,'product_quantity_in_cart' => $quantity_in_cart]);
    }

    public function removeFromCart(Request $request){

        $user = auth()->guard('application')->user(); // canteen user
        $rfid_card = $user->card;
        $organisation = $rfid_card->organisation;

        $date = $request->date;
        $break = OrganisationBreak::find($request->break_id);
        $day_name = Carbon::create($date)->format('l');

        if($break==null || $break->organisation_id != $organisation->id){
            return response()->json(['status' => 0, 'msg' => 'Break or date info is not appropriate', $break]);
        }

        $product = CanteenProduct::find($request->product_id);
        $canteen_setting = $organisation->current_canteen_settings();
        $product_available = $canteen_setting->canteen_menus->where('organisation_break_num', $break->break_num)->where('day',strtolower($day_name))->where('canteen_product_id',$product->id)->first();

        if($product == null || $product_available==null){
            return response()->json(['status' => 0, 'msg' => 'Product info is not appropriate']);
        }

        $quantity_in_cart = 0;

        if($request->session()->has('app_cart')) {
            $cart = collect();

            foreach ($request->session()->get('app_cart') as $key => $cartItem){
                if($cartItem['product_id'] == $product->id && $cartItem['date'] == $date && $cartItem['break_id'] == $break->id){
                    if($cartItem['quantity'] > $request->quantity){
                        $cartItem['quantity'] = $request->quantity;
                    }else{
                        return response()->json(['status' => 0, 'msg' => 'The quantity of product in cart is less than the quantity given', 'quantity_given' => $request->quantity , 'quantity_of_item' =>  $cartItem['quantity']  ]);
                    }

                    if($cartItem['quantity']>0){
                        $quantity_in_cart = $cartItem['quantity'];
                        $cart->push($cartItem);
                    }else{
                        $quantity_in_cart=0;
                    }

                }else{
                    $cart->push($cartItem);
                }

            }

            $request->session()->put('app_cart', $cart);

        }

        $this->updateTotals();
        $total = Session::get('app_total');
        $total_items = Session::get('total_items');

        //available limit for this date

        $purchases_price_for_today= CanteenPurchase::where('canteen_app_user_id', $user->id )->where('date', '=', $date)->sum('price');
        $cart_price_for_today = 0;

        if(Session::has('app_cart')){
            foreach (Session::get('app_cart') as $key => $cartItem) {
                if($cartItem['date'] == $date ){
                    $cart_price_for_today+=$cartItem['price']*$cartItem['quantity'];
                }
            }
        }

        $daily_limit = $user->daily_limit;
        $available_balance = $daily_limit-$purchases_price_for_today-$cart_price_for_today;

        Cart::refreshCartInDB();

        if($request->has('type') && $request->type == 'delete'){
            return response()->json(['status' => 1, 'total' => single_price($total), 'total_items' => $total_items, 'available_balance' => single_price($available_balance), 'available_balance_num' => $available_balance,
                'product_quantity_in_cart' => $quantity_in_cart, 'cart_table_view' => view('application.partials.cart_results')->render()]);

        }

        return response()->json(['status' => 1, 'total' => single_price($total), 'total_items' => $total_items, 'available_balance' => single_price($available_balance), 'available_balance_num' => $available_balance, 'product_quantity_in_cart' => $quantity_in_cart, $request->all()]);


    }

    public static function updateTotals() {

        $total = 0;
        $total_items = 0;

        if(Session::has('app_cart')){
            foreach (Session::get('app_cart') as $key => $cartItem) {

                $total += $cartItem['price'] * $cartItem['quantity'];
                $total_items += $cartItem['quantity'];
            }
        }

        if(!Session::has('vat_percentage')){
            $vat_percentage= PlatformSetting::where('type', 'vat_percentage')->first()->value;
            Session::put('vat_percentage', $vat_percentage);
        }else{
            $vat_percentage = Session::get('vat_percentage');
        }

        $app_subtotal = $total - remove_vat($total, $vat_percentage);
        $app_vat_amount = $total - $app_subtotal;

        Session::put('app_subtotal', $app_vat_amount);
        Session::put('app_vat_amount', $app_subtotal);
        Session::put('app_total', $total);
        Session::put('total_items', $total_items);

    }

    public function nav_cart(Request $request)
    {
        return response()->json(['status' => 1, $request->all()]);
    }

    /*
     * Delete items for days that are no longer available for purchase
     */
    public static function appCartRefresh() {

        $response = true;
        if(Session::has('app_cart')){

            $cart = collect();

            $user = auth()->guard('application')->user(); // canteen user
            $rfid_card = $user->card;
            $organisation = $rfid_card->organisation;
            $canteen_setting = $organisation->current_canteen_settings();
            $minimum_preorder_minutes = $canteen_setting->minimum_preorder_minutes;

            foreach (Session::get('app_cart') as $key => $cartItem) {

                 $break = OrganisationBreak::find($cartItem['break_id']);

                 if($break == null){
                     $break = OrganisationBreak::where('canteen_setting_id', $canteen_setting->id)->where('break_num', '=', $cartItem['break_sort'])->first();
                 }

                if (array_key_exists('message', $cartItem)) {
                    unset($cartItem['message']);
                }

                if (array_key_exists('disabled', $cartItem)) {
                    unset($cartItem['disabled']);
                }

                 #87987897897
                 if(!preorder_availability($cartItem['date'], $break, $minimum_preorder_minutes)){
                     $cartItem['disabled'] = true;
                     $cartItem['message'] = translate('This product is no longer available');
                     $response = false;
                 }

                $canteen_menu = CanteenMenu::where('canteen_setting_id', $canteen_setting->id)
                    ->where('organisation_break_num', $break->break_num)->where('day', strtolower($cartItem['day']))
                    ->where('canteen_product_id', $cartItem['product_id'])->first();

                 if($canteen_menu == null){
                     $cartItem['disabled'] = true;
                     $cartItem['message'] = translate('This product is no longer available in the menu');
                     $response = false;
                 }

                $cart->push($cartItem);

            }
            Session::put('app_cart', $cart);
        }

        self::updateTotals();
        return $response;

    }

    public function get_order_details(Request $request)
    {

        $order = AppOrder::find($request->order_id);

        if($order==null || !auth()->guard('application')->check()){
            return response()->json(['status' => 0, 'msg' => translate('Order does not exists')]);
        }

        $user = auth()->guard('application')->user();

        if($order->user_id != $user->id){
            return response()->json(['status' => 0, 'msg' => translate('Something went wrong')]);
        }

        $order_details_ids = $order->orderDetails->pluck('id');
        $canteen_purchases = \App\Models\CanteenPurchase::whereIn('canteen_order_detail_id', $order_details_ids)->get();
//        $refunds = [];
        $refunds = AppRefundDetail::select('app_refund_details.app_order_code', 'app_refund_details.items_refunded_quantity as quantity ', 'canteen_purchases.date', 'canteen_purchases.break_num', 'canteen_purchases.price',
            'canteen_purchases.canteen_product_id as product_id', 'canteen_purchases.break_hour_from')
                ->join('app_order_details', 'app_order_details.id', '=', 'app_refund_details.app_order_detail_id')
                ->join('canteen_purchases', 'app_order_details.id', '=', 'canteen_purchases.canteen_order_detail_id')
                ->where('app_refund_details.app_order_id', $order->id)
                ->where(function ($refund) {
                    $refund
                        ->where('canteen_purchases.deleted_at', null)
                        ->orWhere('canteen_purchases.deleted_at', '!=', null);
                })->get();

        return response()->json(['status' => 1, 'view' => view('application.modals.history_modal', compact('order', 'canteen_purchases', 'refunds', 'order_details_ids'))->render()]);
    }

    public function update_password(Request $request)
    {

        if(!auth()->guard('application')->check()){
            return redirect()->back();
        }

        $request->validate([
            'password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6'
        ]);

        if($request->new_password != $request->confirm_password){
            return back()->withErrors(
                array(
                    'new_password' => 'The new password confirmation does not match.'
                )
            );
        }

        if(!Hash::check($request->password, auth()->guard('application')->user()->password)){
            return back()->withErrors(
                array(
                'password' => 'Incorrect current password.'
                )
            );
        }

        $app_user = auth()->guard('application')->user();
        $app_user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);

        if($app_user->save()){
            flash("Password updated successfully")->success();
        }else{
            flash("Sorry something went wrong")->error();
        }

        return redirect()->back();


    }

    public function offline(){
        return view('application.offline');
    }





}
