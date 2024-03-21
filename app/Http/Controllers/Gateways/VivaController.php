<?php
namespace App\Http\Controllers\Gateways;


use App\BusinessSetting;
use App\Models\Card;
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

    public function __construct(VivaClient $client, VivaOrder $order, VivaTransaction $transaction)
    {
        $this->client = $client;

        $this->order = $order;

        $this->transaction = $transaction;
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
            "customerTrns"        => translate('You have ordered the following catering plans:')." ".implode(", ", $transaction_desc),
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
            "disableWallet"       => false,
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

//        if ($order_code == '7328519960254456') {
//            $getTransaction = (object) [
//                'statusId' => 'F',
//            ];
//        }
//        else {
//            $getTransaction = $transaction->get($transaction_id);
//        }

        $getTransaction = $transaction->get($transaction_id);

        if (in_array($getTransaction->statusId, ["A", "C"])) {
            sleep(10);
            $getTransaction = $transaction->get($transaction_id);
            if (in_array($getTransaction->statusId, ["A", "C"])) {
                Session::forget('cart');

                Session::forget('total');
                Session::forget('subtotal');
                Session::forget('vat_amount');
                Session::forget('shipping');
                Session::forget('shipping_method');

                return redirect()->route('order_pending', $order_code);
            }
        }
        else if ($getTransaction->statusId == "F") {
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
//        mail('george@skwebline.net', "VivaWallet callback " . date('d-m-Y'), $request->ip() .' '.config('gateways.viva.source_code'));
      try {
          $event_data = (object)$request->EventData;
        if ($event_data->SourceCode == config('gateways.viva.source_code') && $request->method() == 'POST') {

            $export_data = [url()->previous(), $request->getRequestUri(), $request->all()];
            $export_params = print_r($export_data, true);
            mail('george@skwebline.net', "VivaWallet callback " . date('d-m-Y'), $request->EventTypeId . " " . $export_params);

          /* Created Payment */
          if ($request->EventTypeId == '1796') {

            $viva_logs = Viva::where('OrderCode', $event_data->OrderCode)->orderBy('created_at', 'desc')->first();

            if ($viva_logs == null) {
              return response('', 200);
            }
            else if ($viva_logs->callback) {
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

            if (!$viva_logs->run_script) {
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
}
