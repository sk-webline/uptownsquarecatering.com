<?php
namespace App\Http\Controllers\Gateways;


use App\BusinessSetting;
use App\Gateways\VivaWallet\CardToken;
use App\Gateways\VivaWallet\Response as VivaResponse;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AppOrderController;
use App\Models\AppOrder;
use App\Models\AppOrderDetail;
use App\Models\AppRefundDetail;
use App\Models\CanteenProduct;
use App\Models\CanteenPurchase;
use App\Models\Card;
use App\Models\CreditCard;
use App\Models\Gateways\AppViva;
use App\Models\Gateways\Viva;
use App\Models\OrganisationBreak;
use Carbon\Carbon;
use App\Gateways\VivaWallet\Transaction;
use App\Http\Controllers\Controller;
use App\User;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
//use Session;
use App\Gateways\VivaWallet\Client as VivaClient;
use App\Gateways\VivaWallet\Order as VivaOrder;
use App\Gateways\VivaWallet\Transaction as VivaTransaction;
use App\Gateways\VivaWallet\Webhook as VivaWebhook;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AppVivaController extends Controller
{
    /**
     * @var VivaClient
     */
    protected $client;

    /**
     * @var VivaOrder
     */
    protected $order;

    /**
     * @var VivaTransaction
     */
    protected $transaction;

    /**
     * @var VivaResponse
     */
    protected $response;

    public function __construct(VivaClient $client, VivaOrder $order, VivaTransaction $transaction,  VivaResponse $response)
    {
        $this->client = $client;

        $this->order = $order;

        $this->transaction = $transaction;

        $this->response = $response;

    }

    public function pay_order(Request $request): array
    {

        //        check if hours are appropriate to order
            #5412315641165884

            $check_cart = ApplicationController::appCartRefresh();

            if(!$request->session()->has('app_cart') || !auth()->guard('application')->check() || !$check_cart){
                return array(
                    'RedirectUrl' => route('application.cart')
                );
            }

            $total_price = number_format(Session::get('app_total'), 2, '', '');
            $order_id = AppOrder::generateOrderId();

            $transaction_desc = [];
            $product_quantities = [];

            $canteen_user = auth()->guard('application')->user();
            $rfid_card = $canteen_user->card;
            $organisation = $rfid_card->organisation;
            $canteen_setting = $organisation->current_canteen_settings();
            $minimum_preorder_minutes = $canteen_setting->minimum_preorder_minutes;

            foreach ($request->session()->get('app_cart') as $item) {

                $break = OrganisationBreak::find($item['break_id']);

                if($break == null){
                    $break = OrganisationBreak::where('canteen_setting_id', $canteen_setting->id)->where('break_num', '=', $item['break_sort'])->first();
                }

                if (!preorder_availability($item['date'], $break, $minimum_preorder_minutes)) {
                    return view('application.view_cart');
                }

                if(isset($product_quantities[$item['product_id']])){
                    $product_quantities[$item['product_id']] += $item['quantity'];
                }else{
                    $product_quantities[$item['product_id']] = $item['quantity'];
                }
            }

            foreach ($product_quantities as $product_id => $quantity) {
                $product = CanteenProduct::find($product_id);
                if($product!=null){
                    $transaction_desc[] = $quantity . ' x ' . $product->getTranslation('name');
                }
            }


            $parent_user = User::find($canteen_user->user_id);

            $lang = App::getLocale();

            $shipping_info = [
                "email"        =>  $parent_user->email,
                "parent_fullName"     => $parent_user->name,
                "app_username" =>   $canteen_user->username,
                "requestLang"  => ($lang == 'gr' ? 'el-GR' : 'en'),
            ];

            $postFields = [
                "customerTrns" => translate('You have ordered the following items:') . " " . implode(", ", $transaction_desc),
                "customer" => $shipping_info,
                "paymentTimeout" => 1800,
                "preauth" => false,
                "allowRecurring" => false,
                "maxInstallments" => 0,
                "paymentNotification" => false,
                "tipAmount" => 0,
                "disableExactAmount" => false,
                "disableCash" => true,
                "disableWallet" => false,
                "sourceCode" => config('gateways.viva.app_source_code'),
                "merchantTrns" => $order_id,
                "tags" => ['dev.uptownsquarecatering.com']
            ];


            $orderCode = $this->order->create($total_price, $postFields);

            $viva_logs = new AppViva();
            $viva_logs->transaction_type = 'order';
            $viva_logs->user_id = $canteen_user->id;
            $viva_logs->parent_user_id = $parent_user->id;
            $viva_logs->OrderCode = $orderCode;
            $viva_logs->merchantTrns = $order_id;
            $viva_logs->sourceCode = $postFields['sourceCode'];
            $viva_logs->customer_details = json_encode($shipping_info);
            $viva_logs->customerTrns = $postFields['customerTrns'];
            $viva_logs->all_requests = json_encode($postFields);
            $viva_logs->Tags =  implode(",", $postFields['tags']);
            $viva_logs->cart_items =  json_encode(Session::get('app_cart'));
            $viva_logs->vat_percentage = Session::get('vat_percentage'); //getVatFromSession('percentage');
            $viva_logs->subtotal =  Session::get('app_subtotal');
            $viva_logs->vat =  Session::get('app_vat_amount');
            $viva_logs->total =  Session::get('app_total');
            $viva_logs->save();

            $request->session()->put('payment_type', 'viva_wallet');

            return array(
                'order_code' => $orderCode,
                'RedirectUrl' => $this->order->getCheckoutUrl($orderCode)->__toString(),
            );


    }

    public function preauth_pay_order(Request $request)
    {

//        check if hours are appropriate to order
        #5412315641165884
        $check_cart = ApplicationController::appCartRefresh();

        $total_price = number_format(Session::get('app_total'), 2, '', '');
        $order_id = AppOrder::generateOrderId();

        $transaction_desc = [];
        $product_quantities = [];

        if(!$request->session()->has('app_cart') || !$request->session()->has('payment_type') || $request->session()->get('payment_type') != 'current-card' || !$check_cart){
            flash('Sorry something went wrong')->error();
            return view('application.view_cart');
        }

        $canteen_user = auth()->guard('application')->user();
        $rfid_card = $canteen_user->card;
        $organisation = $rfid_card->organisation;
        $canteen_setting = $organisation->current_canteen_settings();


        foreach ($request->session()->get('app_cart') as $item) {
            if(isset($product_quantities[$item['product_id']])){
                $product_quantities[$item['product_id']] += $item['quantity'];
            }else{
                $product_quantities[$item['product_id']] = $item['quantity'];
            }
        }

        foreach ($product_quantities as $product_id => $quantity) {
            $product = CanteenProduct::find($product_id);
            if($product!=null){
                $transaction_desc[] = $quantity . ' x ' . $product->getTranslation('name');
            }
        }


        $parent_user = User::find($canteen_user->user_id);

        if($canteen_user->credit_card_token_id == null){

            flash('Sorry something went wrong')->error();
//            return back();
            return view('application.view_checkout');


        }

        $credit_card = CreditCard::find($canteen_user->credit_card_token_id);

        if($credit_card == null){
            flash('Sorry something went wrong')->error();
//            return back();
            return view('application.view_checkout');
        }

        $transaction_id = $credit_card->transaction_id;

        $lang = App::getLocale();

        $shipping_info = [
            "email"        =>  $parent_user->email,
            "parent_fullName"     => $parent_user->name,
            "app_username" =>   $canteen_user->username,
            "requestLang"  => ($lang == 'gr' ? 'el-GR' : 'en'),
        ];

//        $total_price = 9954;  // Trigger Expired card - 10054 - The payment gateway declined the transaction because the expiration date is expired or does not match

        $post_data = [
            "amount"=> $total_price,
            "customerTrns"=> translate('You have ordered the following items:') . " " . implode(", ", $transaction_desc),
            "sourceCode"=> config('gateways.viva.app_source_code')
        ];

        $basic_url = $this->client->getUrl();
        $url = $basic_url .'/api/transactions/' . $transaction_id;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($post_data),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode(config('gateways.viva.merchant_id').':'.config('gateways.viva.api_key')),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

//        mail('natalia.skwebline@gmail.com', "Pre auth response" . date('d-m-Y H:i:s'), 'response: ' . json_encode($response));

        $curl_getinfo = curl_getinfo($curl);
        $status_code = $curl_getinfo['http_code'];

        curl_close($curl);

        if($status_code == 200){

//            $transaction = $this->transaction;
//            $getTransaction = $transaction->get($response['TransactionId']);
//            $orderCode = $getTransaction->orderCode;

            $postFields = [
                    "customerTrns" => translate('You have ordered the following items:') . " " . implode(", ", $transaction_desc),
                    "customer" => $shipping_info,
                    "paymentTimeout" => 1800,
                    "preauth" => false,
                    "allowRecurring" => false,
                    "maxInstallments" => 0,
                    "paymentNotification" => false,
                    "tipAmount" => 0,
                    "disableExactAmount" => false,
                    "disableCash" => true,
                    "disableWallet" => false,
                    "sourceCode" => config('gateways.viva.app_source_code'),
                    "merchantTrns" => $order_id,
                    "tags" => ['dev.uptownsquarecatering.com']
            ];



            $orderCode = $this->order->create($total_price, $postFields);

            $viva_logs = new AppViva();
            $viva_logs->transaction_type = 'preauth_order';
            $viva_logs->user_id = $canteen_user->id;
            $viva_logs->parent_user_id = $parent_user->id;
            $viva_logs->OrderCode = $orderCode;
            $viva_logs->merchantTrns = $order_id;
            $viva_logs->sourceCode = config('gateways.viva.app_source_code');
            $viva_logs->customer_details = json_encode($shipping_info);
            $viva_logs->customerTrns = translate('You have ordered the following items:') . " " . implode(", ", $transaction_desc);
            $viva_logs->all_requests = json_encode($post_data);
            $viva_logs->Tags =  'dev.uptownsquarecatering.com';
            $viva_logs->cart_items =  json_encode(Session::get('app_cart'));
            $viva_logs->vat_percentage = Session::get('vat_percentage'); //getVatFromSession('percentage');
            $viva_logs->subtotal =  Session::get('app_subtotal');
            $viva_logs->vat =  Session::get('app_vat_amount');
            $viva_logs->total =  Session::get('app_total');
            $viva_logs->TransactionId =  $response['TransactionId'];
            $viva_logs->TransactionTypeId =  $response['TransactionTypeId'];

            $old_viva_log = Viva::where('TransactionId', $transaction_id)->first();

            if($old_viva_log!=null){
                $viva_logs->BankId = $old_viva_log->BankId;
                $viva_logs->CardCountryCode =  $old_viva_log->CardCountryCode;
            }

            $viva_logs->CardNumber =  $credit_card->credit_card_number;
            $viva_logs->SourceName = 'Application Uptown';

            /* statusId
            * A: The transaction is in progress (PAYMENT PENDING)
            * C: The transaction has been captured (the C status refers to the original pre-auth transaction which has now been captured; the capture will be a separate transaction with status F)
            * F: The transaction has been completed successfully (PAYMENT SUCCESSFUL)
            * E: Error
            * */

            if($response['StatusId'] == 'F'){
                try{


                    $viva_logs->ReferenceNumber = $response['ReferenceNumber'];
                    $viva_logs->RetrievalReferenceNumber = $response['RetrievalReferenceNumber'];
                    $viva_logs->TransactionStatusId = $response['StatusId'];
                    $viva_logs->save();

                    try{

                        $orderController = new AppOrderController();
                        $orderController->storeForVivaWallet($viva_logs->id, $response['StatusId']);

                        $viva_logs->run_script = 1;
                        $viva_logs->save();

                    } catch (\Exception $exception) {
//                        Log::error("Problem with app order . Message: " . $exception->getMessage());

                        dd('error in order storeForVivaWallet',$exception , $exception->getMessage());
                    }

                } catch (\Exception $exception) {
                    Log::error("Problem with app viva log  $viva_logs->id. Message: " . $exception->getMessage());

                    dd('error in order pay pre auth',$exception , $exception->getMessage());
                }

                Session::forget('app_cart');

                Session::forget('app_total');
                Session::forget('app_vat_amount');
                Session::forget('app_subtotal');
                Session::forget('total_items');

                return redirect()->route('application.order_success', $orderCode);


            }elseif ($response['StatusId'] == 'A' || $response['StatusId'] == 'C'){
                // PENDING
                #545625123232
                try{

                    $viva_logs->ReferenceNumber = $response['ReferenceNumber'];
                    $viva_logs->RetrievalReferenceNumber = $response['RetrievalReferenceNumber'];
                    $viva_logs->TransactionStatusId = $response['StatusId'];
                    $viva_logs->save();

                } catch (\Exception $exception) {
                    Log::error("Problem with app viva log  $viva_logs->id. Message: " . $exception->getMessage());
                }

                Session::forget('app_cart');

                Session::forget('app_total');
                Session::forget('app_vat_amount');
                Session::forget('app_subtotal');
                Session::forget('total_items');

                return redirect()->route('application.order_pending', $orderCode);

            }

            else{


                $viva_logs->ReferenceNumber =  $response['ReferenceNumber'];
                $viva_logs->RetrievalReferenceNumber =  $response['RetrievalReferenceNumber'];
                $viva_logs->TransactionStatusId = $response['StatusId'];
                $viva_logs->ErrorCode = $response['ErrorCode'];
                $viva_logs->ErrorMessage = $response['ErrorText'];
                $viva_logs->EventTypeId = $response['EventId'];

                $events = $this->response->event; //json_decode(json_encode($this->response->event), true);

                $error_message = null;
                if(isset($events->{$response['EventId']})){
                    $error_message = $events->{$response['EventId']}->explanation;
                    $viva_logs->ErrorMessage = $events->{$response['EventId']}->reason;
                }else{
                    $error_message = $response['ErrorText'];
                }

                $viva_logs->save();

                $error_message = 'Error on Completing the Order';
                return view('application.error', compact('error_message'));

            }



        }else{

            $error_message = 'Error on Completing the Order';
            return view('application.error', compact('error_message'));

//            flash('Sorry something went wrong')->error();
//            return view('application.view_checkout');

        }


    }

    public function success(Request $request, Transaction $transaction) {


        $order_code = $request->input('s');
        $transaction_id = $request->input('t');

        $viva_log = AppViva::where('OrderCode', $order_code)->first();

        if  ($viva_log == null) abort('404');

        if ($viva_log->run_script) return redirect()->route('application.home');

        $getTransaction = $transaction->get($transaction_id);

        $viva_log->SourceName = 'Application Uptown';
        $viva_log->BankId = $getTransaction->bankId;
        $viva_log->CardNumber =  $getTransaction->cardNumber;
        $viva_log->TransactionId = $transaction_id;
        $viva_log->TransactionStatusId = $getTransaction->statusId;
        $viva_log->TransactionTypeId =  $getTransaction->transactionTypeId;
        $viva_log->CardCountryCode =  $getTransaction->cardCountryCode;

        $viva_log->EventTypeId = '1796';
        $viva_log->save();

        /* statusId
         * A: The transaction is in progress (PAYMENT PENDING)
         * C: The transaction has been captured (the C status refers to the original pre-auth transaction which has now been captured; the capture will be a separate transaction with status F)
         * F: The transaction has been completed successfully (PAYMENT SUCCESSFUL)
         * */


        // TODO: #2348970239
        if (in_array($getTransaction->statusId, ["A", "C"])) {
            sleep(10);
            $getTransaction = $transaction->get($transaction_id);
            if (in_array($getTransaction->statusId, ["A", "C"])) {
                Session::forget('app_cart');

                Session::forget('app_total');
                Session::forget('app_vat_amount');
                Session::forget('app_subtotal');
                Session::forget('total_items');

                $viva_log->pending_page_seen = 1;
                $viva_log->save();

                return redirect()->route('order_pending', $order_code);
            }
        }
        else if ($getTransaction->statusId == "F") {

                $orderController = new AppOrderController();
                $orderController->storeForVivaWallet($viva_log->id, $getTransaction->statusId);

                $viva_log->run_script = 1;
                $viva_log->confirm_page_seen = 1;
                $viva_log->save();

                Session::forget('app_cart');

                Session::forget('app_total');
                Session::forget('app_vat_amount');
                Session::forget('app_subtotal');
                Session::forget('total_items');

                return redirect()->route('application.order_success', $order_code);

        }
        else {
            $message = "Paraggelia me order code \"$order_code\" exei transaction status id \"$getTransaction->statusId\"";
            mail('george@skwebline.net', "Urgent: VivaWallet success function with not valid status id ".date('d-m-Y'), $message);

            $error_message = 'Error on Completing the Order';
            return view('application.error', compact('error_message'));
            //            return redirect()->route('home');
        }

    }

    public function failed(Request $request, Response $response) {

        if ($_SERVER['REMOTE_ADDR'] == '82.102.76.201') {
            dd($request->all(),  json_encode($response));
        }

        flash(translate('Error on Completing the Order'));
        return redirect()->route('cart');

    }


    private function getVerifyKey() {
        $response = $this->client->get(
            $this->client->getUrl()->withPath('/api/messages/config/token'),
            array_merge_recursive(
                [RequestOptions::AUTH => [config('gateways.viva.merchant_id'), config('gateways.viva.api_key')]]
            )
        );
        return $response->Key;
    }


    public function auto_setup()
    {
        if (!BusinessSetting::where('type', 'viva_wallet')->first()) {
            $business_settings = new BusinessSetting;
            $business_settings->type = "viva_wallet";
            $business_settings->value = "1";
            $business_settings->save();
        }
        else {
            dd("The \"Viva Wallet\" method has been already exist!");
        }

    }

    public function verify_website_on_viva_wallet(Request $request)
    {
        $export_data = [url()->previous(), $request->getRequestUri(), $request->all()];
        $export_params = print_r($export_data, true);
        mail('george@skwebline.net', "VivaWallet verify_website_on_viva_wallet " . date('d-m-Y'), $request->EventTypeId . " " . $export_params);

        if ($request->ip() == '82.102.76.201') {
            dd($this->getVerifyKey());
        }
        else {
            return response()->json(['key' => $this->getVerifyKey()], '200');
        }
    }

    /*
     * TODO: To Be Deleted
     *
     * Δεν χρειάζεται η ποιο κάτω μέθοδος. Πρέπει να διαγραφεί και το route
    */
    public function getTransaction($transaction_id) {
        $getTransaction =  $this->transaction->get($transaction_id);
        dd("Get Transaction", $getTransaction);
    }

    /*
     *
     *  Cancel Transaction - refund order
     *
    */
    public function cancel_order(Request $request){


        if(!auth()->guard('application')->check() || !$request->has('user_id') || !$request->has('date') || !$request->has('product_id') || !$request->has('break_num') || !$request->has('quantity') || $request->quantity < 1){
            return response()->json(['status' => 0, 'msg' => 'Missing data']);
        }

        if(auth()->guard('application')->user()->id!=$request->user_id){
            return response()->json(['status' => 0, 'msg' => 'Something went wrong']);
        }

        $user = auth()->guard('application')->user();
        $parent_user = User::find($user->user_id);

        $rfid_card = $user->card;
        $organisation = $rfid_card->organisation;
        $canteen_setting = $organisation->current_canteen_settings();

        $break = OrganisationBreak::where('canteen_setting_id', $canteen_setting->id)->where('break_num', '=', $request->break_num)->first();

        if($break==null){
            return response()->json(['status' => 0, 'msg' => 'Something went wrong']);
        }

        $now = Carbon::now();
        $carbon_date = Carbon::create($request->date . ' ' . $break->hour_from);


//        find the purchase

        $purchases = CanteenPurchase::where('canteen_app_user_id', $user->id)
            ->where('canteen_setting_id', $canteen_setting->id)
            ->where('canteen_product_id', $request->product_id)
            ->where('break_num', $break->break_num)
            ->where('date', $request->date)
            ->get();

        $quantity_to_delete = (int)$request->quantity;
        $temp_quantity = 0;

        $shipping_info = [
            "email"        =>  $parent_user->email,
            "parent_fullName"     => $parent_user->name,
            "app_username" =>   $user->username
        ];

        foreach ($purchases as $key => $purchase){

            if($temp_quantity>=$quantity_to_delete){  break; }

            $order_detail = AppOrderDetail::find($purchase->canteen_order_detail_id);

            if($order_detail==null){ break; }

            $order = AppOrder::find($order_detail->app_order_id);

            if($order==null){ break; }

            $app_viva_log = AppViva::where('OrderCode', $order->code)->first();

            if($app_viva_log==null){ break; }

//            $transaction_id = $app_viva_log->TransactionId;
//            $merchantTrns = $app_viva_log->merchantTrns;

            $temp_var = 0;

            $refunded_items = 0;
            if($temp_quantity + $purchase->quantity  <= $quantity_to_delete){
                $amount = $purchase->price * $purchase->quantity;
                $temp_quantity += $purchase->quantity;
                $refunded_items = $purchase->quantity;
            }else{

                $q = $quantity_to_delete - $temp_quantity;
                $amount = $purchase->price * $q;
                $temp_quantity += $q;
                $refunded_items = $q;
            }

            $original_amount = $amount;
            $refund_amount = $amount;


            if($this->delete_transaction($app_viva_log, $refund_amount)){

                $refund_detail = new AppRefundDetail();
                $refund_detail->app_order_id = $order->id;
                $refund_detail->app_order_code = $order->code;
                $refund_detail->app_order_detail_id = $order_detail->id;
                $refund_detail->items_refunded_quantity = $refunded_items;
                $refund_detail->price = $purchase->price;
                $refund_detail->amount_refunded = $refund_amount;

                $refund_detail->save();

                //
                $order_detail->refunded = 1;
                $order_detail->refunded_items = $refunded_items;
                $order_detail->save();

                if($purchase->quantity == $refunded_items){
                    $purchase->delete();
                }else{
                    $purchase->quantity = $purchase->quantity - $refunded_items;
                    $purchase->save();
                }


            }else{
                $temp_quantity-= $temp_var;
            }


        }

        $user = auth()->guard('application')->user(); // canteen user
        $today = \Carbon\Carbon::today();
        $time = Carbon::now()->format('H:i:s');
        $upcoming_purchases_count = CanteenPurchase::where('canteen_app_user_id', $user->id )
            ->where('date', '>', $today->format('Y-m-d'))
            ->orWhere(function ($query) use ($today, $time) {
                $query->where('date', '=', $today->format('Y-m-d'))
                    ->where('break_hour_from', '>', $time);
            })
            ->orderBy('date')->count();

        return response()->json(
            [ 'status' => 1,
            'quantity_deleted' => $temp_quantity,
            'request' => $request->all(),
            'view' => view('application.partials.upcoming_meals_table')->render(),
            'upcoming_purchases_count' => $upcoming_purchases_count]
        );


    }


    /*
     *
     *  Cancel Transaction - refund order
     *
    */
    public function delete_transaction($app_viva_log, $refund_amount){

        $basic_url = $this->client->getUrl();

        $transaction_id = $app_viva_log->TransactionId;
        $merchantTrns = $app_viva_log->merchantTrns;

        $amount = number_format($refund_amount, 2, '', '');

        $url = $basic_url .'/api/transactions/' . $transaction_id . '?amount='. $amount .  '&sourceCode=' . config('gateways.viva.app_source_code') ;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode(config('gateways.viva.merchant_id').':'.config('gateways.viva.api_key'))
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);
        $curl_getinfo = curl_getinfo($curl);
        $status_code = $curl_getinfo['http_code'];

        curl_close($curl);

        $viva_log_delete = new AppViva();
        $viva_log_delete->transaction_type = 'delete_transaction';
        $viva_log_delete->user_id = auth()->guard('application')->user()->id;
        $viva_log_delete->OrderCode = $app_viva_log->OrderCode;
        $viva_log_delete->TransactionId = $app_viva_log->TransactionId;
        $viva_log_delete->customer_details =  $app_viva_log->customer_details;
        $viva_log_delete->sourceCode = config('gateways.viva.app_source_code');
        $viva_log_delete->total =  $refund_amount;
        $viva_log_delete->SourceName = 'Application Uptown';
        $viva_log_delete->Tags =  implode(",", ['dev.uptownsquarecatering.com']);

        if($status_code==200){

            /* statusId
             * A: The transaction is in progress (PAYMENT PENDING)
             * C: The transaction has been captured (the C status refers to the original pre-auth transaction which has now been captured; the capture will be a separate transaction with status F)
             * F: The transaction has been completed successfully (PAYMENT SUCCESSFUL)
             * */

            $viva_log_delete->TransactionStatusId = $response['StatusId'];
            $viva_log_delete->TransactionTypeId = $response['TransactionTypeId'];

            if($response['StatusId'] == 'E'){
                $viva_log_delete->ErrorCode = $response['ErrorCode'];
                $viva_log_delete->ErrorMessage = $response['ErrorText'];
                $viva_log_delete->save();
                return false;
            }

            $viva_log_delete->save();
            return true;

        }else{

            $viva_log_delete->TransactionStatusId = $status_code . ' Error code';
            $viva_log_delete->save();
            return false;

        }

//        $events = $this->response->event; //json_decode(json_encode($this->response->event), true);



        dd('delete transaction', $url, $status_code, $response, $refund_amount, $app_viva_log) ;

    }

    public function test_fast_refund(){


        $basic_url = $this->client->getUrl();

        $viva_log = AppViva::find(32);
        $transaction_id = $viva_log->TransactionId;
        $merchantTrns = $viva_log->merchantTrns;


        $url = $basic_url .'/acquiring/v1/transactions/' . $transaction_id . ':fastrefund';

        $data = [
            "amount" => 100,
            "sourceCode" => config('gateways.viva.app_source_code'),
            "merchantTrns "=> $merchantTrns,
            "idempotencyKey"=> "fast_refund"
        ];

//        dd($data);

//        'Authorization: Bearer ' . $this->client->getBearerToken()
//
//        dd($url, '{
//                        "amount": 100,
//                        "sourceCode": "Default",
//                        "merchantTrns": "The text the merchant sees",
//                        "idempotencyKey": "string"
//                        }', json_encode($data));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode(config('gateways.viva.merchant_id').':'.config('gateways.viva.api_key'))
//                'Authorization: Bearer ' . $this->client->getBearerToken()
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        dd($response);
    }

    public function test(){

//        $vivaController = new VivaController($this->client, $this->order, $this->transaction, $this->response);

//       $app_viva_log = AppViva::find(32);
//       $refund_amount = $app_viva_log->total;
//       $r = $this->delete_transaction($app_viva_log, $refund_amount);


//       $transaction = $this->transaction;
//       $getTransaction = $transaction->get('2f3ee22f-9f8c-4aa0-a5f7-4355c8a265a4');
//       dd($getTransaction);

        $app_viva = AppViva::find(121);

        $refund_amount = 2;
//        dd($app_viva);

        $r = $this->delete_transaction($app_viva, $refund_amount);

    }






}
