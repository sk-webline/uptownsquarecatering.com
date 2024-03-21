<?php

namespace App\Http\Controllers;

use App\Models\AppOrder;
use App\Models\AppOrderDetail;
use App\Models\AppRefundDetail;
use App\Models\Btms\Utilities\BtmsOrder;
use App\Models\CanteenAppUser;
use App\Models\CanteenPurchase;

use App\Models\EmailForOrder;
use App\Models\Organisation;
use App\Models\OrganisationBreak;
use App\CanteenOrdersExport;
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
use App\Models\Gateways\AppViva as AppVivaWallet;

class AppOrderController extends Controller
{
    /**
     * Display a listing of the resource to seller.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
//
    }

    // All Orders
    public function all_orders(Request $request)
    {

        $show_refunds = false;
        $date = $request->date;
        $sort_search = null;
        $orders = AppOrder::join('canteen_app_users', 'canteen_app_users.id', '=', 'app_orders.user_id')
                    ->join('cards', 'cards.id', '=', 'canteen_app_users.card_id')->orderBy('app_orders.created_at', 'desc');

        $selected_organisations = [];
        $break_nums = [];

        if ($request->has('organisation')) {
            $selected_organisations = $request->organisation;
        }

        if ($request->has('break_num')) {
            $break_nums = $request->break_num;

        }

        if($selected_organisations != []){
            $orders = $orders->whereIn('cards.organisation_id', $selected_organisations);
        }

        if($break_nums != []){
            $temp = $orders->join('app_order_details', 'app_order_details.app_order_id', '=', 'app_orders.id')
                ->join('canteen_purchases', 'canteen_purchases.canteen_order_detail_id', '=', 'app_order_details.id')
                ->whereIn('canteen_purchases.break_num', $break_nums)->pluck('app_orders.id');

            $orders = $orders->whereIn('app_orders.id', $temp);

        }

        if ($request->has('show_refunds')) {
            $show_refunds = true;
            $temp = $orders->join('app_order_details', 'app_order_details.app_order_id', '=', 'app_orders.id')
                ->where('app_order_details.refunded', 1)->pluck('app_orders.id');

            $orders = $orders->whereIn('app_orders.id', $temp);
        }

        if ($request->has('search') && $request->search != null) {

            $sort_search = $request->search;

            $orders = $orders
                ->where(function ($order) use ($sort_search) {
                    $order
                        ->where('canteen_app_users.username', 'like', '%' . $sort_search . '%')
                        ->orWhere('cards.rfid_no', 'like', '%' . $sort_search . '%')
                        ->orWhere('cards.rfid_no_dec', 'like', '%' . $sort_search . '%')
                        ->orWhere('app_orders.code', 'like', '%' . $sort_search . '%');
                });


        }


        if ($date != null) {

            $start_date = Carbon::create(date('Y-m-d', strtotime(explode(" to ", $date)[0])))->format('Y-m-d H:i:s');

            $end_date = Carbon::create(date('Y-m-d', strtotime(explode(" to ", $date)[1])))->endOfDay()->format('Y-m-d H:i:s');

            $orders = $orders->where('app_orders.created_at', '>=', $start_date)->where('app_orders.created_at', '<=', $end_date);
        }

        $total_orders = count($orders->distinct()->get(['code']));


        if (!$request->has('form_type') || $request->form_type == 'filter') {

            $orders = $orders->select('app_orders.*','canteen_app_users.username', 'canteen_app_users.card_id', 'cards.rfid_no', 'cards.organisation_id')->distinct()->groupBy('code');

            $orders = $orders->paginate(15);

            return view('backend.sales.canteen_orders.index', compact('orders', 'sort_search', 'date', 'selected_organisations', 'total_orders', 'break_nums',  'show_refunds'));

        } else if ($request->form_type == 'export') {

                $filename = 'Canteen Orders Export ' . Carbon::now();

                $orders = $orders->select('app_orders.*')->distinct()->groupBy('code')->get();

                return Excel::download(new CanteenOrdersExport($orders), $filename . '.xlsx');


        }


    }


    public function show($id)
    {

        $order = AppOrder::find(decrypt($id));

        if($order == null){
            flash('Canteen Order not found!')->error();
            return redirect()->route('canteen_orders.index');
        }


        return view('backend.sales.canteen_orders.show', compact('order'));

    }


    public function all_refunds(Request $request)
    {

        $refunds = AppRefundDetail::select('*')->paginate(15);
        $total_refunds = AppRefundDetail::all()->sum('items_refunded_quantity');
//        $orders = AppOrderDetail::join('app_orders', 'app_order_details.app_order_id', '=', 'app_orders.id')
//            ->join('canteen_app_users', 'canteen_app_users.id', '=', 'app_orders.user_id')
//            ->join('cards', 'cards.id', '=', 'canteen_app_users.card_id')
//            ->where('app_order_details.refunded', 1)
//            ->orderBy('app_orders.created_at', 'desc');
//
//
//        $total_refunds = count($orders->get());
////        dd($orders->get());
//
//        $orders = $orders->select('app_orders.code', 'app_order_details.app_order_id', 'app_orders.grand_total', 'app_order_details.total_quantity as items_refunded_quantity',
//             'canteen_app_users.username', 'canteen_app_users.card_id', 'cards.rfid_no', 'cards.organisation_id');
//        $orders = $orders->paginate(15);

        return view('backend.sales.canteen_refunds.index', compact('refunds',  'total_refunds'));


        dd('show canteen refunds');
    }


    public function storeForVivaWallet($viva_wallet_log_id, $transactionStatusId)
    {

        sleep(1);
        $viva_wallet_log = AppVivaWallet::where('id', $viva_wallet_log_id)->where('run_script', 0)->where('start_process', '=', 0)->orderBy('created_at', 'desc')->first();

        if ($viva_wallet_log == null) {
            return response('', '406');
        }

        $viva_wallet_log->start_process = 1;
        $viva_wallet_log->save();

//        $shipping_method = json_decode($viva_wallet_log->shipping_method);
        $customer_details = json_decode($viva_wallet_log->customer_details);

        $canteen_user = null;
        $order = new AppOrder();

        if ($viva_wallet_log->user_id != null) {
            $order->user_id = $viva_wallet_log->user_id;
            $canteen_user = CanteenAppUser::find($viva_wallet_log->user_id);
//            $rfid_card = $user->card;
//            $organisation = $rfid_card->organisation;
//            $canteen_setting = $organisation->current_canteen_settings();
        } elseif ($viva_wallet_log->guest_id != null) {
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
            $shipping = 0;

            //calculate shipping is to get shipping costs of different types

            $emails = array();

            //Order Details Storing
            foreach (json_decode($viva_wallet_log->cart_items, true) as $key => $cartItem) {

//                $subtotal += $cartItem['price'];
//                $tax += $cartItem['tax'];
                $order_detail = new AppOrderDetail();
                $order_detail->app_order_id = $order->id;

                try {


                    $order_detail->product_id = $cartItem['product_id'];
                    $order_detail->price = $cartItem['price'];
                    $order_detail->total = $cartItem['price'] * $cartItem['quantity'];
                    $order_detail->vat_amount = $cartItem['price']-remove_vat($cartItem['price'], 9);
                    $order_detail->vat_percentage = $order->vat_percentage;
                    $order_detail->total_quantity = $cartItem['quantity'];
                    $order_detail->payment_status = 'paid';

                    $order_detail->save();


                    $canteen_purchase = new CanteenPurchase();
                    $canteen_purchase->canteen_order_detail_id = $order_detail->id;
                    $canteen_purchase->canteen_app_user_id = $viva_wallet_log->user_id;
                    $canteen_purchase->canteen_product_id = $cartItem['product_id'];

                    $break = OrganisationBreak::find($cartItem['break_id']);
                    $canteen_purchase->break_num = $break->break_num;
                    $canteen_purchase->break_hour_from = $break->hour_from;
                    $canteen_purchase->break_hour_to = $break->hour_to;
                    $canteen_purchase->canteen_setting_id = $break->canteen_setting_id;
                    $canteen_purchase->date = $cartItem['date'];
                    $canteen_purchase->quantity = $cartItem['quantity'];
                    $canteen_purchase->price = $cartItem['price'];
                    $canteen_purchase->custom_price_status = $cartItem['custom_price_status'];


//                    meal code
                    $existing = CanteenPurchase::where('canteen_app_user_id', $canteen_user->id)->where('date', $cartItem['date'])->where('break_num', $break->break_num)->first();

                    if($existing != null){
                        $canteen_purchase->meal_code = $existing->meal_code;
                    }else{
                        $temp = CanteenPurchase::where('date', $cartItem['date'])->where('break_num', $break->break_num)->max('meal_code');
                        $meal_code = $canteen_purchase->extractNumberFromMealCode($temp)+1;

                        $canteen_purchase->meal_code = $canteen_purchase->formatMealCode($meal_code);
                    }


                    $canteen_purchase->save();

                    try {

                        $organisation = Organisation::find($break->organisation_id);

                        if ($organisation != null) {
                            if (($organisation->email_for_order_id == null || $organisation->email_for_order_id == 1)) {
                                $emails[] = EmailForOrder::find(1)->email;
                            } else {
                                $emails[] = $organisation->email_for_order->email; // EmailForOrder::find($organisation->)->email;
                            }
                        }

                    } catch (\Exception $e) {
                        Log::error("Problem with order $order->id. Message: $organisation with id " . $break->organisation_id . " could not be found.");
                    }

                } catch (\Exception $exception) {
                    Log::error("Problem with order $order->id. Message: " . $exception->getMessage());
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

            if ($canteen_user != null) {
                $canteen_user->cart = null;
                $canteen_user->save();
            }

            try {
                BtmsOrder::add($order->id);
            } catch (\Exception $e) {
                Log::error('Import Order to BTMS: ' . $e->getMessage());
            }

            $array['view'] = 'emails.application_invoice';
            $array['subject'] = translate('Your order has been placed') . ' - ' . $order->code;
            $array['from'] = env('MAIL_USERNAME');
            $array['order'] = $order;


            //sends email to customer with the invoice pdf attached
            if (env('MAIL_USERNAME') != null) {
                try {

//                    mail('natalia.skwebline@gmail.com', "Done storeForVivaWallet " . date('d-m-Y H:i:s'),  json_encode($order) . ' '. json_encode($order_detail) . ' '. $canteen_purchase);
                    Mail::to($customer_details->email)->queue(new InvoiceEmailManager($array));
                    $array['subject'] = translate('An order has been placed') . ' - ' . $order->code;
//
                    $emails = array_unique($emails);

//                    if (count($emails) > 0) {
//                        foreach ($emails as $email) {
////                            Mail::to($email)->queue(new InvoiceEmailManager($array));
//                        }
//                    } else {
////                        Mail::to(config('app.order_email'))->queue(new InvoiceEmailManager($array));
//                    }


                    Mail::to('natalia.skwebline@gmail.com')->queue(new InvoiceEmailManager($array));

                } catch (\Exception $e) {
                }
            }

            return $order;
        }
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

    }

    public function order_details(Request $request)
    {
//
    }

    public function update_delivery_status(Request $request)
    {

        dd('app order - update_delivery_status');
        $order = AppOrder::findOrFail($request->order_id);
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
        $order = AppOrder::findOrFail($request->order_id);
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


}
