@php

use Illuminate\Support\Facades\DB;
use App\Models\AppRefundDetail;

    $carbon_date = \Carbon\Carbon::create($order->created_at);

    $carbon_now = \Carbon\Carbon::now();
    $canteen_purchases = $canteen_purchases->toArray();

    // Custom comparison function for sorting by date and then by break
    function sortByDateAndBreak($a, $b) {

      $dateComparison = strtotime($a['date']) - strtotime($b['date']);
           // If dates are equal, compare by 'break'
       if ($dateComparison == 0) {
           return $a['break_num'] - $b['break_num'];
       }

       return $dateComparison;
    }

    // Use usort to sort the array using the custom function
    usort($canteen_purchases, 'sortByDateAndBreak');

//    $break_hour = [];
//    $titles = [];
//    $items = [];
//    $dates = [];
//    $breaks = [];
//    $name = [];
//    $breaks_start_hour = [];

    foreach($canteen_purchases as $key => $meal){
        $carbon = \Carbon\Carbon::create($meal['date'] . ' ' . $meal['break_hour_from']);
        $title = toUpper($carbon->format('l')) . ' ' . $carbon->format('d/m/y') ;
        $titles[$meal['date']] = $title;
    }

    foreach($refunds as $key => $refund){
        $carbon = \Carbon\Carbon::create($refund->date . ' ' . $refund->break_hour_from);
        $title = toUpper($carbon->format('l')) . ' ' . $carbon->format('d/m/y') ;
        $titles[$refund->date] = $title;
    }

    $titles = array_unique($titles);


@endphp

<div class="fix-area border-bottom border-primary-100 mb-25px fs-15">
    <div class="row mb-10px">
        <div class="col fw-700">{{toUpper(translate('Order Code'))}}: <span class="text-primary-50 fw-500"> {{$order->code}} </span></div>
    </div>
    <div class="row mb-5px">
        <div class="col fw-700">{{toUpper(translate('Order Date & Time'))}}: <span class="text-primary-50 fw-500"> {{$carbon_date->format('d/m/Y')}} - {{$carbon_date->format('H:i')}} </span></div>
        <div class="col-auto fw-700">{{toUpper(translate('Total Cost'))}}:  <span class="text-primary-50 fw-500"> {{single_price($order->grand_total)}} </span></div>
    </div>
</div>
<div class="history-break-items-scroll scroll-area c-scrollbar fs-15 ">
    <div class="overflow-hidden">
        <div class="mb-10px border-bottom border-primary-100 pb-5px text-primary-50 fw-700">
            <div class="row">
                <div class="col">{{toUpper(translate('Items'))}}</div>
                <div class="col-2 text-center">{{toUpper(translate('Break'))}}</div>
                <div class="col-3 text-center">{{toUpper(translate('Meal Code'))}}</div>
                <div class="col-3 text-right">{{toUpper(translate('Received Time'))}}</div>
            </div>
        </div>

        @foreach($titles as $date => $title)

            @php

               $products = [];
               $quantities = [];
               $prices = [];


               $purchases = \App\Models\CanteenPurchase::select('*', DB::raw('SUM(canteen_purchases.quantity) as total_quantity'))
                               ->whereIn('canteen_order_detail_id', $order_details_ids)
                               ->where('canteen_app_user_id', $canteen_user->id)
                               ->where('date', $date)
                               ->groupBy('canteen_product_id', 'break_num')
                               ->orderBy('break_num', 'asc')->get();


               $refunds = AppRefundDetail::select('app_refund_details.app_order_code', 'app_refund_details.items_refunded_quantity as quantity ', 'canteen_purchases.date', 'canteen_purchases.break_num', 'canteen_purchases.price',
                    'canteen_purchases.canteen_product_id as product_id', 'canteen_purchases.break_hour_from', 'canteen_purchases.meal_code', DB::raw('SUM(canteen_purchases.quantity) as total_quantity'))
                    ->join('app_order_details', 'app_order_details.id', '=', 'app_refund_details.app_order_detail_id')
                    ->join('canteen_purchases', 'app_order_details.id', '=', 'canteen_purchases.canteen_order_detail_id')
                    ->where('app_refund_details.app_order_id', $order->id)
                    ->where('canteen_purchases.date', $date)
                    ->where('canteen_app_user_id', $canteen_user->id)
                    ->where(function ($refund) {
                        $refund
                            ->where('canteen_purchases.deleted_at', null)
                            ->orWhere('canteen_purchases.deleted_at', '!=', null);
                    })
                    ->groupBy('canteen_purchases.canteen_product_id', 'canteen_purchases.break_num')
                    ->orderBy('canteen_purchases.break_num', 'asc')
                    ->get();

//               dd($refunds);



            @endphp

            <div class="history-break-item ">

                <div class="history-break-title py-10px fs-12 fw-600">
                    <div class="row border-bottom border-primary-100 border-width-1 pb-5px">
                        <div class="col opacity-60">{{$title}}</div>
                    </div>
                </div>
                <div class="history-break-products opacity-60">

                    @foreach($purchases as $key => $purchase)

                        @php

                           $pr = \App\Models\CanteenProduct::find($purchase->canteen_product_id);

                           $received_time = null;

                           $price = $purchase->price;

                            $upcoming = false;

                            if(\Carbon\Carbon::create($purchase->date . ' ' . $purchase->break_hour_from)->gt($carbon_now)){
                                $upcoming = true;
                            }

                            if(!$upcoming){
                                $delivery = \App\Models\CanteenPurchaseDelivery::select('canteen_purchase_deliveries.created_at')->join('canteen_purchases', 'canteen_purchases.id', '=', 'canteen_purchase_deliveries.canteen_purchase_id')
                                    ->whereIn('canteen_purchases.canteen_order_detail_id', $order_details_ids)
                                    ->where('canteen_purchases.canteen_app_user_id', $canteen_user->id)
                                    ->where('canteen_purchases.date', $purchase->date)->where('break_num', $purchase->break_num)
                                    ->where('canteen_purchases.canteen_product_id', $purchase->canteen_product_id)
                                    ->orderBy('canteen_purchase_deliveries.created_at', 'desc')
                                    ->first();

                                if($delivery==null){
                                    $received_time = translate('Not Received');
                                }else{
                                    $received_time = \Carbon\Carbon::create($delivery->created_at)->format('H:i');
                                }
                            }else{
                                 $received_time = translate('Pending');
                            }

                        @endphp

                        <div class="history-break-product py-5px">
                             <div class="row">
                                 <div class="col">
                                     @if($purchase->total_quantity > 1)
                                         ({{$purchase->total_quantity}})
                                     @endif
                                         @if($pr!=null)
                                            {{$pr->getTranslation('name')}} ({{single_price($price)}})
                                         @else
                                            {{translate('Not available')}} ({{single_price($price)}})
                                         @endif


                                 </div>
                                 <div class="col-2 text-center">{{ordinal($purchase->break_num)}}</div>
                                 <div class="col-3 text-center">{{ordinal($purchase->meal_code)}}</div>
                                 <div class="col-3 text-right">{{$received_time}}</div>
                             </div>
                        </div>

                    @endforeach

                        @foreach($refunds as $refund)

                            @php
                                $pr = \App\Models\CanteenProduct::find($refund->product_id);
                                $price = $refund->price;
                            @endphp

                            <div class="history-break-product py-5px">
                                <div class="row vertical-line">
                                    <div class="col">
                                        @if($refund->total_quantity > 1)
                                            ({{$refund->total_quantity}})
                                        @endif
                                            @if($pr!=null)
                                                {{$pr->getTranslation('name')}} ({{single_price($price)}})
                                            @else
                                                {{translate('Not available')}} ({{single_price($price)}})
                                            @endif
                                    </div>
                                    <div class="col-2 text-center">{{ordinal($refund->break_num)}}</div>
                                    <div class="col-3 text-center">{{$refund->meal_code}}</div>
                                    <div class="col-3 text-right"></div>
                                </div>
                            </div>
                        @endforeach


                </div>
            </div>
        @endforeach


    </div>
</div>

