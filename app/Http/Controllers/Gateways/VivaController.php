<?php
namespace App\Http\Controllers\Gateways;


use App\BusinessSetting;
use App\Gateways\VivaWallet\CardToken;
use App\Http\Controllers\AppOrderController;
use App\Models\CanteenAppUser;
use App\Models\Card;
use App\Models\CreditCard;
use App\Models\Gateways\AppViva;
use Carbon\Carbon;
use App\Country;
use App\Gateways\VivaWallet\Response;
use App\Gateways\VivaWallet\Transaction;
use App\Http\Controllers\CartController;
use App\Models\CateringPlan;
use App\Order;
use App\Http\Controllers\Controller;
use App\Models\Gateways\Viva;
use App\Product;
use App\User;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
//use Session;
use App\Gateways\VivaWallet\Client as VivaClient;
use App\Gateways\VivaWallet\Order as VivaOrder;
use App\Gateways\VivaWallet\Transaction as VivaTransaction;
use App\Gateways\VivaWallet\CardToken as VivaCardToken;
use App\Gateways\VivaWallet\Response as VivaResponse;
use App\Gateways\VivaWallet\Webhook as VivaWebhook;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Session;

class VivaController extends Controller
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
     * @var VivaCardToken
     */
//    protected $cardToken;

    /**
     * @var VivaResponse
     */
    protected $response;

    public function __construct(VivaClient $client, VivaOrder $order, VivaTransaction $transaction, VivaResponse $response)
    {
        $this->client = $client;

        $this->order = $order;

        $this->transaction = $transaction;

//        $this->cardToken = $cardToken;

        $this->response = $response;
    }

    public function pay_order(Request $request): array
    {
//        if (count(CartController::checkProductStock()) > 0) {
//          return array(
//            'order_code' => "",
//            'RedirectUrl' => route('cart'),
//          );
//        }

            $total_price = number_format(Session::get('total'), 2, '', '');
            $order_id = Order::generateOrderId();

            $shipping_info = $request->session()->get('shipping_info');
            $transaction_desc = [];
            foreach ($request->session()->get('cart') as $item) {

                if($item['type']=='catering_plan'){
                    $start = Carbon::create($item['from_date'])->format('d/m/Y');
                    $end = Carbon::create($item['to_date'])->format('d/m/Y');
                    $item_card = Card::findorfail($item['card_id']);
                    $transaction_desc[] = "1 x ".$item['name']." - " .format_price($item['total'])  ." - $start - $end - RFID No: $item_card->rfid_no";
                }

//            $product = Product::find($item['id']);
//            // TODO: George => The "variant" on the below line it must change to a size
//            $transaction_desc[] = "{$item['quantity']} x $product->name".($item['color'] != null ? " - {$item['color']}" : "").($item['variant'] != null ? " - {$item['variant']}" : "");
            }

            $lang = App::getLocale();
            $postFields  = [
                "customerTrns"        => translate('You have ordered the following items:')." ".implode(", ", $transaction_desc),
                "customer"            => [
                    "email"        =>  $shipping_info['email'],
                    "fullName"     => $shipping_info['name'],
                    "phone"        => $shipping_info['phone'],
//                "countryCode"  => $country->code,
                    "requestLang"  => ($lang == 'gr' ? 'el-GR' : 'en'),
                ],
                "paymentTimeout"      => 1800,
                "preauth"             => false,
                "allowRecurring"      => false,
                "maxInstallments"     => 0,
                "paymentNotification" => false,
                "tipAmount"           => 0,
                "disableExactAmount"  => false,
                "disableCash"         => true,
                "disableWallet"       => true,
                "sourceCode"          => config('gateways.viva.source_code'),
                "merchantTrns"        => $order_id,
                "tags"                => ['uptownsquarecatering.com']
            ];

            $orderCode = $this->order->create($total_price, $postFields);

            $viva_logs = new Viva;
            if(Auth::check()){
                $viva_logs->user_id = Auth::user()->id;
            }
            else{
                $viva_logs->guest_id = mt_rand(100000, 999999);
            }
            $viva_logs->OrderCode = $orderCode;
            $viva_logs->merchantTrns = $order_id;
            $viva_logs->sourceCode = $postFields['sourceCode'];
            $viva_logs->customer_details = json_encode(Session::get('shipping_info'));
            $viva_logs->customerTrns = $postFields['customerTrns'];
            $viva_logs->all_requests = json_encode($postFields);
            $viva_logs->Tags =  implode(",", $postFields['tags']);
            $viva_logs->cart_items =  json_encode(Session::get('cart'));
            $viva_logs->vat_percentage = Session::get('vat_percentage'); //getVatFromSession('percentage');

            $viva_logs->subtotal =  Session::get('subtotal');
            $viva_logs->vat =  Session::get('vat_amount');
            $viva_logs->total =  Session::get('total');
            $viva_logs->save();

            $request->session()->put('payment_type', 'viva_wallet');

            return array(
                'order_code' => $orderCode,
                'RedirectUrl' => $this->order->getCheckoutUrl($orderCode)->__toString(),
            );


    }

    public function success(Request $request, Transaction $transaction) {


        $order_code = $request->input('s');
        $transaction_id = $request->input('t');

        $viva_log = Viva::where('OrderCode', $order_code)->first();

        if  ($viva_log == null) abort('404');

        $request->session()->put('checked_full_dates_card_id', '-');

        if ($viva_log->run_script) return redirect()->route('dashboard');

        $getTransaction = $transaction->get($transaction_id);

        $viva_log->SourceName = 'Uptown';
        $viva_log->BankId = $getTransaction->bankId;
        $viva_log->CardNumber =  $getTransaction->cardNumber;
        $viva_log->TransactionId = $transaction_id;
        $viva_log->TransactionStatusId = $getTransaction->statusId;
        $viva_log->TransactionTypeId =  $getTransaction->transactionTypeId;
        $viva_log->CardCountryCode =  $getTransaction->cardCountryCode;
        $viva_log->EventTypeId = '1796';
        $viva_log->save();

        if($viva_log->transaction_type == 'save_card' || $viva_log->transaction_type == 'update_card'){
//            $cancel = $this->cancel_transaction($viva_log);
            $counter = 0;
            $counter2 = 0;

            do {

                $cancel = $this->cancel_transaction($viva_log);

//                DD($cancel);
                // Increment the counter
                $counter++;

                if($cancel['status_code'] == 200){

                    do {
                        $response = $cancel['response'];
                        // Increment the counter
                        $counter2++;

                        // The condition to continue the loop
                    } while ($response['StatusId'] == 'E' && $counter2 < 5);
                }

                // The condition to continue the loop
            } while ($cancel['status_code'] != 200 && $counter < 5);

        }


//        dd($viva_log, $getTransaction);

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


                if($viva_log->transaction_type == 'order' ) {
                    Session::forget('cart');

                    Session::forget('total');
                    Session::forget('subtotal');
                    Session::forget('vat_amount');
                    Session::forget('shipping');
                    Session::forget('shipping_method');

                    return redirect()->route('order_pending', $order_code);
                }
            }elseif ($viva_log->transaction_type == 'save_card'){

//                 send api to get the transaction token
                $credit_card = CreditCard::where('cardUniqueReference', $getTransaction->cardUniqueReference)->first();

                if($credit_card==null){
                    $credit_card = new CreditCard();
                    $credit_card->user_id = Auth::user()->id;
                    $credit_card->cardUniqueReference = $getTransaction->cardUniqueReference;
                    $credit_card->transaction_id = $transaction_id;
                    $credit_card->credit_card_number = $getTransaction->cardNumber;
                    $credit_card->nickname = $viva_log->nickname;
                    $credit_card->expiration_date = null;
                    $credit_card->save();
                }

                if($viva_log->canteen_user_id!=null){
                    $canteen_user = CanteenAppUser::find($viva_log->canteen_user_id);

                    if($canteen_user!=null){
                        $canteen_user->credit_card_token_id = $credit_card->id;
                        $canteen_user->save();
                    }

                    flash("Credit card assigned successfully")->success();
                    return redirect()->route('dashboard');
                }

                flash("Credit card added successfully")->success();
                return redirect()->route('credit_cards')->withInput(['added_credit_card' => true]);

            }elseif ($viva_log->transaction_type == 'update_card'){


                $old_credit_card = CreditCard::where('cardUniqueReference', $getTransaction->cardUniqueReference)->first();

                if($old_credit_card==null){
                    $credit_card = new CreditCard();
                    $credit_card->user_id = Auth::user()->id;
                    $credit_card->cardUniqueReference = $getTransaction->cardUniqueReference;
                    $credit_card->transaction_id = $transaction_id;
                    $credit_card->credit_card_number = $getTransaction->cardNumber;
                    $credit_card->nickname = $viva_log->nickname;
                    $credit_card->expiration_date = null;
                    $credit_card->save();
                }else{
                    $credit_card = new CreditCard();
                    $credit_card->user_id = Auth::user()->id;
                    $credit_card->cardUniqueReference = $getTransaction->cardUniqueReference;
                    $credit_card->transaction_id = $transaction_id;
                    $credit_card->credit_card_number = $getTransaction->cardNumber;
                    $credit_card->nickname = $viva_log->nickname;
                    $credit_card->expiration_date = null;
                    $credit_card->save();

                    CanteenAppUser::where('credit_card_token_id', $old_credit_card->id)->update(['credit_card_token_id' => $credit_card->id]);
                    $old_credit_card->delete();
                }

                flash("Credit card updated successfully")->success();
                return redirect()->route('dashboard');


            }

        }
        else if ($getTransaction->statusId == "F") {

            if($viva_log->transaction_type == 'order' ) {
                $orderController = new OrderController;
                $orderController->storeForVivaWallet($viva_log->id, $getTransaction->statusId);

                $viva_log->run_script = 1;
                $viva_log->save();

                Session::forget('cart');
                Session::forget('owner_id');
                Session::forget('total');
                Session::forget('subtotal');
                Session::forget('vat_amount');
                Session::forget('shipping');
                Session::forget('shipping_method');
                Session::forget('shipping_info');
                Session::forget('vat');
                Session::forget('total_weight_cart');
                Session::forget('selected_shipping_country');
                Session::forget('payment_type');

                return redirect()->route('order_confirmed', $order_code);

            }elseif ($viva_log->transaction_type == 'save_card'){

//                 send api to get the transaction token
                $credit_card = CreditCard::where('cardUniqueReference', $getTransaction->cardUniqueReference)->first();

                if($credit_card==null){
                    $credit_card = new CreditCard();
                    $credit_card->user_id = Auth::user()->id;
                    $credit_card->cardUniqueReference = $getTransaction->cardUniqueReference;
                    $credit_card->transaction_id = $transaction_id;
                    $credit_card->credit_card_number = $getTransaction->cardNumber;
                    $credit_card->nickname = $viva_log->nickname;
                    $credit_card->expiration_date = null;
                    $credit_card->save();
                }

                if($viva_log->canteen_user_id!=null){
                    $canteen_user = CanteenAppUser::find($viva_log->canteen_user_id);

                    if($canteen_user!=null){
                        $canteen_user->credit_card_token_id = $credit_card->id;
                        $canteen_user->save();
                    }

                    flash("Credit card assigned successfully")->success();
                    return redirect()->route('dashboard');
                }

                flash("Credit card added successfully")->success();
                return redirect()->route('credit_cards')->withInput(['added_credit_card' => true]);
//                return redirect()->route('credit_cards');

            }elseif ($viva_log->transaction_type == 'update_card'){


                $old_credit_card = CreditCard::where('cardUniqueReference', $getTransaction->cardUniqueReference)->first();

                if($old_credit_card==null){
                    $credit_card = new CreditCard();
                    $credit_card->user_id = Auth::user()->id;
                    $credit_card->cardUniqueReference = $getTransaction->cardUniqueReference;
                    $credit_card->transaction_id = $transaction_id;
                    $credit_card->credit_card_number = $getTransaction->cardNumber;
                    $credit_card->nickname = $viva_log->nickname;
                    $credit_card->expiration_date = null;
                    $credit_card->save();
                }else{
                    $credit_card = new CreditCard();
                    $credit_card->user_id = Auth::user()->id;
                    $credit_card->cardUniqueReference = $getTransaction->cardUniqueReference;
                    $credit_card->transaction_id = $transaction_id;
                    $credit_card->credit_card_number = $getTransaction->cardNumber;
                    $credit_card->nickname = $viva_log->nickname;
                    $credit_card->expiration_date = null;
                    $credit_card->save();

                    CanteenAppUser::where('credit_card_token_id', $old_credit_card->id)->update(['credit_card_token_id' => $credit_card->id]);
                    $old_credit_card->delete();
                }

                flash("Credit card updated successfully")->success();
                return redirect()->route('dashboard');


            }
        }
        else {
            $message = "Paraggelia me order code \"$order_code\" exei transaction status id \"$getTransaction->statusId\"";
            mail('george@skwebline.net', "Urgent: VivaWallet success function with not valid status id ".date('d-m-Y'), $message);
            return redirect()->route('home');
        }

    }

    public function failed(Request $request, Response $response) {

        if ($_SERVER['REMOTE_ADDR'] == '82.102.76.201') {
            dd($request->all(),  json_encode($response));
        }

        flash(translate('Error on Completing the Order'));
        return redirect()->route('cart');

        /*$order_code = $request->s;
        dd($response->event, $this->transaction->get($order_code));

        $event_id = $request->eventId;

        $viva_log = Viva::where('OrderCode', $order_code)->first();

        if  ($viva_log == null) abort('404');

        if ($viva_log->run_script) return redirect()->route('home');

        $response_event = $response->event->{$event_id};
        $reason = $response_event->reason;
        $explanation = $response_event->explanation;

        return redirect()->route('order_failed');*/
    }
    public function callback(Request $request) {

        $export_data = [url()->previous(), $request->getRequestUri(), $request->all()];
        $export_params = print_r($export_data, true);
        mail('natalia.skwebline@gmail.com', "VivaWallet callback Uptown Canteen " . date('d-m-Y H:i:s'), $request->EventTypeId . " " . $export_params);

        try {

            if  ($request->has('EventData')) {
                $event_data = (object)$request->EventData;

                if ($event_data->SourceCode == config('gateways.viva.source_code') && $request->method() == 'POST') {
//                    dd(__LINE__, $request->all());

                    mail('natalia.skwebline@gmail.com', "2 VivaWallet callback Uptown Canteen " . date('d-m-Y H:i:s'), $request->EventTypeId . " " . $export_params);

                    $this->callbackCatering($request);

                } else if ($event_data->SourceCode == config('gateways.viva.app_source_code') && $request->method() == 'POST') {


                    $this->callbackApplication($request);

                }
           }
            else {
           return response()->json(['key' => $this->getVerifyKey()], '200');
            }

        }catch (\Exception $e) {
                $error_data = [
                    'url' => url()->previous(),
                    'request_uri' => $request->getRequestUri(),
                    'error_message' => $e->getMessage(),
                    'request_data' => $request->all()
                ];
                create_custom_log_file('viva_wallet_webhook.log', $error_data);
                $export_params = print_r($error_data, true);
                mail('george@skwebline.net', "VivaWallet callback " . date('d-m-Y'), $request->EventTypeId . " " . $export_params);
            }
    }

    public function callbackCatering(Request $request) {
        $export_data = [url()->previous(), $request->getRequestUri(), $request->all()];
        $export_params = print_r($export_data, true);

        try {
          $event_data = (object)$request->EventData;

//            mail('natalia.skwebline@gmail.com', "3 VivaWallet Catering callback Uptown Dev " . date('d-m-Y H:i:s'), $request->EventTypeId . " " . $export_params . " " . $export_params . " " . $event_data->TransactionTypeId);

                if ($event_data->SourceCode == config('gateways.viva.source_code') && $request->method() == 'POST') {
                  /* Created Payment */
                  if ($request->EventTypeId == '1796' ) {


                    $viva_logs = Viva::where('OrderCode', $event_data->OrderCode)->where('transaction_type', '!=' , 'delete_transaction')->orderBy('created_at', 'desc')->first();

                    if ($viva_logs == null) {
                      return response('', 200);
                    }
                    else if ($viva_logs->callback==1 ) {
                        mail('natalia.skwebline@gmail.com', "2 stamata ??" . date('d-m-Y H:i:s'), "  - ");

                        if($viva_logs->transaction_type == 'save_card' || $viva_logs->transaction_type == 'update_card'){

                            $viva_logs = Viva::where('TransactionId', $event_data->TransactionId)->orderBy('created_at', 'desc')->first();

                            mail('natalia.skwebline@gmail.com',  __LINE__ ." ela mesa " . date('d-m-Y H:i:s'), "  - " . json_encode($viva_logs));

                            if ($viva_logs == null) {
                                mail('natalia.skwebline@gmail.com', "11 stamata ??" . date('d-m-Y H:i:s'), "  - ");
                                return response('', 200);
                            }
                            else if ($viva_logs->callback==1) {
                                mail('natalia.skwebline@gmail.com', "22 stamata ??" . date('d-m-Y H:i:s'), "  - ");
                                return response()->json(['message' => 'The order has been processed'], '200');
                            }
                        }else{
                            return response()->json(['message' => 'The order has been processed'], '200');
                        }

                    }


                    $viva_logs->SourceName = $event_data->SourceName;
                    $viva_logs->BankId = $event_data->BankId;
                    $viva_logs->CardNumber = $event_data->CardNumber;
                    $viva_logs->TransactionId = $event_data->TransactionId;
                    $viva_logs->ReferenceNumber = $event_data->ReferenceNumber;
                    $viva_logs->TransactionStatusId = $event_data->StatusId;
                    if($viva_logs->TransactionTypeId == null){
                        $viva_logs->TransactionTypeId = $event_data->TransactionTypeId;
                    }
                    $viva_logs->CardCountryCode = $event_data->CardCountryCode;
                    $viva_logs->RetrievalReferenceNumber = $event_data->RetrievalReferenceNumber;
                    $viva_logs->CorrelationId = $request->CorrelationId;
                    $viva_logs->EventTypeId = $request->EventTypeId;
                    $viva_logs->Delay = $request->Delay;
                    $viva_logs->MessageId = $request->MessageId;
                    $viva_logs->RecipientId = $request->RecipientId;
                    $viva_logs->MessageTypeId = $request->MessageTypeId;
                    $viva_logs->save();

                      mail('natalia.skwebline@gmail.com', "7.5 VivaWallet Catering callback Uptown Dev " . date('d-m-Y H:i:s'), $request->EventTypeId . " " . $export_params . " " . $export_params . " " . $event_data->TransactionTypeId);


                      if($viva_logs->transaction_type == 'save_card' || $viva_logs->transaction_type == 'update_card'){
                        sleep(5);
                        $credit_card = CreditCard::where('transaction_id', $event_data->TransactionId)
                            ->where('cardUniqueReference', $event_data->CardUniqueReference)->first();

                        if($credit_card != null){
                            $credit_card->expiration_date = explode('T', $event_data->CardExpirationDate)[0];
                            $credit_card->save();
                        }
                    }

                    // store order for catering plan
                    if ($viva_logs->transaction_type == 'order' && $viva_logs->run_script==0) {
                      $orderController = new OrderController;
                      $orderController->storeForVivaWallet($viva_logs->id, $event_data->StatusId);
                      $viva_logs->run_script = 1;
                    }

                    $viva_logs->callback = 1;
                    $viva_logs->save();

                    if (!empty($viva_logs->user_id)) {
                      $user = User::find($viva_logs->user_id);
                      $user->cart = null;
                      $user->save();
                    }

                  }

                  return response()->json(['message' => 'ok'], '200');
                }

            // Using the below code for verify endpoint from viva wallet
            return response()->json(['key' => $this->getVerifyKey()], '200');
          }
          catch (\Exception $e) {
            $error_data = [
              'url' => url()->previous(),
              'request_uri' => $request->getRequestUri(),
              'error_message' => $e->getMessage(),
              'request_data' => $request->all()
            ];
            create_custom_log_file('viva_wallet_webhook.log', $error_data);
            $export_params = print_r($error_data, true);
            mail('george@skwebline.net', "VivaWallet callback " . date('d-m-Y'), $request->EventTypeId . " " . $export_params);
          }
    }

    public function callbackApplication(Request $request) {
//        mail('george@skwebline.net', "VivaWallet callback " . date('d-m-Y'), $request->ip() .' '.config('gateways.viva.source_code'));

        $export_data = [url()->previous(), $request->getRequestUri(), $request->all()];
        $export_params = print_r($export_data, true);
         try {

            $event_data = (object)$request->EventData;

             mail('natalia.skwebline@gmail.com', __LINE__." Del VivaWallet Application callback Uptown Canteen " . date('d-m-Y H:i:s'), json_encode($request->all()) . ' - '. $event_data .' - '. $export_params);


             if ($event_data->SourceCode == config('gateways.viva.app_source_code') && $request->method() == 'POST') {

                /* Created Payment */
                if ($request->EventTypeId == '1796') {

                    $viva_logs = AppViva::where('OrderCode', $event_data->OrderCode)->orderBy('created_at', 'desc')->first();

                    if ($viva_logs == null) {

                        $viva_logs = AppViva::where('TransactionId', $event_data->TransactionId)->orderBy('created_at', 'desc')->first();

                        if ($viva_logs == null) {
                            mail('natalia.skwebline@gmail.com', "Application 1 stamata ??" . date('d-m-Y H:i:s'), "  - ");
                            return response('', 200);
                        }

                    }else if($viva_logs->transaction_type == 'preauth_order'){
                        $viva_logs = AppViva::where('TransactionId', $event_data->TransactionId)->orderBy('created_at', 'desc')->first();
                    }

                    if ($viva_logs->callback==1) {
//                        mail('natalia.skwebline@gmail.com', __LINE__." VivaWallet Application callback Uptown Canteen " . date('d-m-Y H:i:s'), ' - '. $export_params . ' ' . json_encode($viva_logs));
//                        mail('natalia.skwebline@gmail.com', "Application 2 stamata ??" . date('d-m-Y H:i:s'), "  - ");
                        return response()->json(['message' => 'The order has been processed'], '200');
                    }

                    $viva_logs->SourceName = $event_data->SourceName;
                    $viva_logs->BankId = $event_data->BankId;
                    $viva_logs->CardNumber = $event_data->CardNumber;
                    $viva_logs->TransactionId = $event_data->TransactionId;
                    $viva_logs->ReferenceNumber = $event_data->ReferenceNumber;
                    $viva_logs->TransactionStatusId = $event_data->StatusId;
                    $viva_logs->TransactionTypeId = $event_data->TransactionTypeId;
                    $viva_logs->CardCountryCode = $event_data->CardCountryCode;
                    $viva_logs->RetrievalReferenceNumber = $event_data->RetrievalReferenceNumber;
                    $viva_logs->CorrelationId = $request->CorrelationId;
                    $viva_logs->EventTypeId = $request->EventTypeId;
                    $viva_logs->Delay = $request->Delay;
                    $viva_logs->MessageId = $request->MessageId;
                    $viva_logs->RecipientId = $request->RecipientId;
                    $viva_logs->MessageTypeId = $request->MessageTypeId;
                    $viva_logs->save();

//                    mail('natalia.skwebline@gmail.com', __LINE__." VivaWallet Application callback Uptown Canteen " . date('d-m-Y H:i:s'), ' - '. $export_params);

                    if ($viva_logs->run_script==0) {
                        $orderController = new AppOrderController();
                        $orderController->storeForVivaWallet($viva_logs->id, $event_data->StatusId);
                        $viva_logs->run_script = 1;
                    }

                    $viva_logs->callback = 1;
                    $viva_logs->save();

                    if (!empty($viva_logs->user_id)) {
                        $user = CanteenAppUser::find($viva_logs->user_id);
                        $user->cart = null;
                        $user->save();
                    }


                }


                return response()->json(['message' => 'ok'], '200');
            }
            // Using the below code for verify endpoint from viva wallet
            return response()->json(['key' => $this->getVerifyKey()], '200');
        }
        catch (\Exception $e) {
            $error_data = [
                'url' => url()->previous(),
                'request_uri' => $request->getRequestUri(),
                'error_message' => $e->getMessage(),
                'request_data' => $request->all()
            ];
            create_custom_log_file('viva_wallet_webhook.log', $error_data);
            $export_params = print_r($error_data, true);
            mail('george@skwebline.net', "VivaWallet Application callback " . date('d-m-Y'), $request->EventTypeId . " " . $export_params);
        }
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

    public function save_card_for_future_use(Request $request) {


        if(!Auth::check() || !$request->has('nickname') || $request->nickname == null || !$request->has('type')){
            return array(
                'status' => 0
            );
        }

        if($request->type == 'assign'){
//            Session::put('card_api_type', 'assign');
            if(!$request->has('card_id')){
                return array(
                    'status' => 0
                );
            }else{
                $rfid_card = Card::find($request->card_id);
                if($rfid_card==null){
                    return array(
                        'status' => 0
                    );
                }
            }

        }

        $total_price = 100;
        $order_id = Order::generateOrderId();

        $lang = App::getLocale();
        $postFields  = [
            "customerTrns"        => translate('Save card for future use'),
            "customer"            => [
                "email"        =>  Auth::user()->email,
                "fullName"     => Auth::user()-> name,
                "phone"        => Auth::user()->phone,
                "requestLang"  => ($lang == 'gr' ? 'el-GR' : 'en'),
            ],
            "paymentTimeout"      => 1800,
            "preauth"             => false,
            "allowRecurring"      => true,
            "maxInstallments"     => 0,
            "paymentNotification" => false,
            "tipAmount"           => 0,
            "disableExactAmount"  => false,
            "disableCash"         => true,
            "disableWallet"       => false,
            "sourceCode"          => config('gateways.viva.source_code'),
            "merchantTrns"        => $order_id,
            "tags"                => ['uptownsquarecatering.com'],
        ];

        $orderCode = $this->order->create($total_price, $postFields);

        $viva_logs = new Viva;

        if(Auth::check()){
            $viva_logs->user_id = Auth::user()->id;
        }else{
            return array(
                'status' => 0
            );
        }

        $viva_logs->transaction_type = 'save_card';

        if($request->has('canteen_user_id')){
            $viva_logs->canteen_user_id = $request->canteen_user_id;
        }
        $viva_logs->nickname = $request->nickname;
        $viva_logs->OrderCode = $orderCode;
        $viva_logs->merchantTrns = $order_id;
        $viva_logs->sourceCode = $postFields['sourceCode'];
        $viva_logs->customer_details = json_encode(["email"        =>  Auth::user()->email,
                                                    "fullName"     => Auth::user()-> name,
                                                    "phone"        => Auth::user()->phone,
                                                    "requestLang"  => ($lang == 'gr' ? 'el-GR' : 'en'),
                                                ]);
        $viva_logs->customerTrns = $postFields['customerTrns'];
        $viva_logs->all_requests = json_encode($postFields);
        $viva_logs->Tags =  implode(",", $postFields['tags']);
        $viva_logs->cart_items =  json_encode([]);
        $viva_logs->vat_percentage = 0; //getVatFromSession('percentage');

        $viva_logs->subtotal =  0;
        $viva_logs->vat =  0;
        $viva_logs->total =  1;
        $viva_logs->save();

        return array(
            'status' => 1,
            'order_code' => $orderCode,
            'RedirectUrl' => $this->order->getCheckoutUrl($orderCode)->__toString(),
        );


    }

    public function save_card_for_future_use_card_verification(Request $request) {

//       $array = explode('/', \Illuminate\Support\Facades\Session::get('_previous')['url']);
//       $last_page = end($array);

        if(!Auth::check() || !$request->has('nickname') || $request->nickname == null || !$request->has('type')){
            return array(
                'status' => 0
            );
        }

        if($request->type == 'add'){
            Session::put('card_api_type', 'add');
        }else if($request->type == 'assign'){
            Session::put('card_api_type', 'assign');
            if(!$request->has('card_id')){
                return array(
                    'status' => 0
                );
            }else{
                $rfid_card = Card::find($request->card_id);
                if($rfid_card==null){
                    return array(
                        'status' => 0
                    );
                }else{
                    Session::put('assign_to_card', $rfid_card->id);
                }
            }

        }

        $total_price = 1;
        $total_price = 0;
        $order_id = Order::generateOrderId();

        $lang = App::getLocale();
        $postFields  = [
            "customerTrns"        => translate('Save card for future use'),
            "customer"            => [
                "email"        =>  Auth::user()->email,
                "fullName"     => Auth::user()-> name,
                "phone"        => Auth::user()->phone,
                "requestLang"  => ($lang == 'gr' ? 'el-GR' : 'en'),
            ],
            "paymentTimeout"      => 1800,
            "preauth"             => false,
            "allowRecurring"      => true,
            "maxInstallments"     => 0,
            "paymentNotification" => false,
            "tipAmount"           => 0,
            "disableExactAmount"  => false,
            "disableCash"         => true,
            "disableWallet"       => false,
            "sourceCode"          => config('gateways.viva.source_code'),
            "merchantTrns"        => $order_id,
            "tags"                => ['uptownsquarecatering.com'],
            "isCardVerification" => true
        ];

        $orderCode = $this->order->create($total_price, $postFields);

        $viva_logs = new Viva;

        if(Auth::check()){
            $viva_logs->user_id = Auth::user()->id;
        }else{
            return array(
                'status' => 0
            );
        }

        $viva_logs->transaction_type = 'save_card';
        $viva_logs->nickname = $request->nickname;
        $viva_logs->OrderCode = $orderCode;
        $viva_logs->merchantTrns = $order_id;
        $viva_logs->sourceCode = $postFields['sourceCode'];
        $viva_logs->customer_details = json_encode(["email"        =>  Auth::user()->email,
            "fullName"     => Auth::user()-> name,
            "phone"        => Auth::user()->phone,
            "requestLang"  => ($lang == 'gr' ? 'el-GR' : 'en'),
        ]);
        $viva_logs->customerTrns = $postFields['customerTrns'];
        $viva_logs->all_requests = json_encode($postFields);
        $viva_logs->Tags =  implode(",", $postFields['tags']);
        $viva_logs->cart_items =  json_encode([]);
        $viva_logs->vat_percentage = 0; //getVatFromSession('percentage');

        $viva_logs->subtotal =  0;
        $viva_logs->vat =  0;
        $viva_logs->total =  0;
        $viva_logs->save();

        return array(
            'status' => 1,
            'order_code' => $orderCode,
//            'RedirectUrl' => $this->order->getCheckoutUrl($orderCode)->__toString(),
        );

    }

    public function test(Request $request) {

//        $vivaResponse
        dd($this->response->event);


        $order_code = 3248720446868113;
        $order_viva = $this->order->get($order_code);

        dd($order_viva);


    }


    public function cancel_transaction($viva_log) {

        $basic_url = $this->client->getUrl();

//        $viva_log = AppViva::find(32);
        $transaction_id = $viva_log->TransactionId;
        $merchantTrns = $viva_log->merchantTrns;

        $amount = number_format($viva_log->total, 2, '', '');

        $url = $basic_url .'/api/transactions/' . $transaction_id . '?amount=' . $amount . '&sourceCode=' . config('gateways.viva.source_code');

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

        if($status_code==200){

            /* statusId
             * A: The transaction is in progress (PAYMENT PENDING)
             * C: The transaction has been captured (the C status refers to the original pre-auth transaction which has now been captured; the capture will be a separate transaction with status F)
             * F: The transaction has been completed successfully (PAYMENT SUCCESSFUL)
             * */

            $viva_log_delete = new Viva();
            $viva_log_delete->transaction_type = 'delete_transaction';
            $viva_log_delete->user_id = Auth::user()->id;
            $viva_log_delete->OrderCode = $viva_log->OrderCode;
            $viva_log_delete->TransactionId = $response['TransactionId'];
            $viva_log_delete->TransactionStatusId = $response['StatusId'];
            $viva_log_delete->RetrievalReferenceNumber = $response['RetrievalReferenceNumber'];
            $viva_log_delete->ReferenceNumber = $response['ReferenceNumber'];
            $viva_log_delete->customer_details =  $viva_log->customer_details;
            $viva_log_delete->sourceCode = config('gateways.viva.source_code');
            $viva_log_delete->TransactionTypeId = $response['TransactionTypeId'];
            $viva_log_delete->Tags =  implode(",", ['dev.uptownsquarecatering.com']);
            $viva_log_delete->total =  $viva_log->total;
            $viva_log_delete->SourceName = 'Uptown';
            $viva_log_delete->EventTypeId = '1796';


            if($response['StatusId'] == 'E'){
                $viva_log_delete->ErrorCode = $response['ErrorCode'];
                $viva_log_delete->ErrorMessage = $response['ErrorText'];
            }

            $viva_log_delete->save();
            return array('status_code' => $status_code, 'response' => $response);

        }else{

            $viva_log_delete = new Viva();
            $viva_log_delete->transaction_type = 'delete_transaction';
            $viva_log_delete->user_id = Auth::user()->id;
            $viva_log_delete->OrderCode = $viva_log->OrderCode;
            $viva_log_delete->TransactionId = $viva_log->TransactionId;
            $viva_log_delete->customer_details =  $viva_log->customer_details;
            $viva_log_delete->sourceCode = config('gateways.viva.source_code');
            $viva_log_delete->TransactionStatusId = $status_code . ' Error code';
            $viva_log_delete->total =  $viva_log->total;
            $viva_log_delete->SourceName = 'Uptown';
            $viva_log_delete->EventTypeId = '1796';
            $viva_log_delete->save();

            return array('status_code' => $status_code, 'response' => $response);
        }

        $response = [];

        return array('status_code' => $status_code, 'response' => $response);

    }


    public function edit_credit_card(Request $request) {


        if(!Auth::check() || !$request->has('credit_card_id') || $request->credit_card_id == null ){
            return array(
                'status' => 0,
                'msg' => 'Missing data',
                $request->all()
            );
        }

        $credit_card = CreditCard::find($request->credit_card_id);

        if($credit_card==null || $credit_card->user_id != Auth::user()->id ){
            return array(
                'status' => 0,
                'msg' => 'No credit card found'
            );
        }

        $total_price = 100;
        $order_id = Order::generateOrderId();

        $lang = App::getLocale();
        $postFields  = [
            "customerTrns"        => translate('Update & Save card for future use'),
            "customer"            => [
                "email"        =>  Auth::user()->email,
                "fullName"     => Auth::user()-> name,
                "phone"        => Auth::user()->phone,
                "requestLang"  => ($lang == 'gr' ? 'el-GR' : 'en'),
            ],
            "paymentTimeout"      => 1800,
            "preauth"             => false,
            "allowRecurring"      => true,
            "maxInstallments"     => 0,
            "paymentNotification" => false,
            "tipAmount"           => 0,
            "disableExactAmount"  => false,
            "disableCash"         => true,
            "disableWallet"       => false,
            "sourceCode"          => config('gateways.viva.source_code'),
            "merchantTrns"        => $order_id,
            "tags"                => ['uptownsquarecatering.com'],
        ];

        $orderCode = $this->order->create($total_price, $postFields);

        $viva_logs = new Viva;

        if(Auth::check()){
            $viva_logs->user_id = Auth::user()->id;
        }else{
            return array(
                'status' => 0
            );
        }

        $viva_logs->transaction_type = 'update_card';

        if($request->has('canteen_user_id')){
            $viva_logs->canteen_user_id = $request->canteen_user_id;
        }

        $viva_logs->nickname = $credit_card->nickname;
        $viva_logs->OrderCode = $orderCode;
        $viva_logs->merchantTrns = $order_id;
        $viva_logs->sourceCode = $postFields['sourceCode'];
        $viva_logs->customer_details = json_encode(["email"        =>  Auth::user()->email,
            "fullName"     => Auth::user()-> name,
            "phone"        => Auth::user()->phone,
            "requestLang"  => ($lang == 'gr' ? 'el-GR' : 'en'),
        ]);
        $viva_logs->customerTrns = $postFields['customerTrns'];
        $viva_logs->all_requests = json_encode($postFields);
        $viva_logs->Tags =  implode(",", $postFields['tags']);
        $viva_logs->cart_items =  json_encode([]);
        $viva_logs->vat_percentage = 0; //getVatFromSession('percentage');
        $viva_logs->subtotal =  0;
        $viva_logs->vat =  0;
        $viva_logs->total =  1;
        $viva_logs->save();

//        CanteenAppUser::where('credit_card_token_id', $credit_card->id)->update(['credit_card_token_id' => null]);
//        $credit_card->delete();

        return array(
            'status' => 1,
            'order_code' => $orderCode,
            'RedirectUrl' => $this->order->getCheckoutUrl($orderCode)->__toString(),
        );


    }




}
