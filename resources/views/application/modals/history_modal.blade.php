@php

        $user = auth()->guard('application')->user();
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

        $break_hour = [];
        $titles = [];
        $items = [];
        $dates = [];
        $breaks = [];
        $breaks_start_hour = [];

        foreach($canteen_purchases as $key => $meal){
            $carbon = \Carbon\Carbon::create($meal['date'] . ' ' . $meal['break_hour_from']);
            $title = $carbon->format('d/m') . ' - ' . ordinal($meal['break_num']) . ' ' . translate('Break');
            $titles[] = $title;
            $dates[$title] = $meal['date'];
            $breaks[$title] = $meal['break_num'];
            $break_hour[$title] = $carbon->format('H:i');

        }

        foreach($refunds as $key => $refund){
            $carbon = \Carbon\Carbon::create($refund->date . ' ' . $refund->break_hour_from);
            $title = $carbon->format('d/m') . ' - ' . ordinal($refund->break_num) . ' ' . translate('Break');
            $titles[] = $title;
            $dates[$title] = $refund->date;
            $breaks[$title] = $refund->break_num;
            $break_hour[$title] = $carbon->format('H:i');
        }


//        foreach($canteen_purchases as $key => $meal){
//            $carbon = \Carbon\Carbon::create($meal['date'] . ' ' . $meal['break_hour_from']);
//            $title = $carbon->format('d/m') . ' - ' . ordinal($meal['break_num']) . ' ' . translate('Break');
//            $titles[] = $title;
//            $dates[$title] = $meal['date'];
//            $breaks[$title] = $meal['break_num'];
//            $break_hour[$title] = $carbon->format('H:i');
//        }

        $titles = array_unique($titles);

@endphp

<div class="fix-area">
    <div class="row mb-10px">
        <div class="col fw-700">{{toUpper(translate('Order Code'))}}:</div>
        <div class="col-auto text-black-50">{{$order->code}}</div>
    </div>
    <div class="row mb-10px">
        <div class="col fw-700">{{toUpper(translate('Order Date & Time'))}}:</div>
        <div class="col-auto text-black-50">{{$carbon_date->format('d/m/Y')}} - {{$carbon_date->format('H:i')}}</div>
    </div>
    <div class="row mb-25px">
        <div class="col fw-700">{{toUpper(translate('Total Cost'))}}:</div>
        <div class="col-auto text-black-50">{{single_price($order->grand_total)}}</div>
    </div>
</div>
<div class="history-break-items-scroll scroll-area c-scrollbar">
    <div class="overflow-hidden">
        <div class="mb-10px border-bottom border-primary-100 pb-5px text-black-50 fw-700">
            <div class="row">
                <div class="col">{{toUpper(translate('Items'))}}</div>
                <div class="col-auto">{{toUpper(translate('Received Time'))}}</div>
            </div>
        </div>

        @foreach($titles as $title)

            @php

                $products = [];
                $quantities = [];
                $prices = [];
                $purchases = \App\Models\CanteenPurchase::whereIn('canteen_order_detail_id', $order_details_ids)->where('canteen_app_user_id', $user->id)->where('date', $dates[$title])->where('break_num', $breaks[$title])->get();

                $upcoming = false;
                if(\Carbon\Carbon::create($dates[$title] . ' ' . $break_hour[$title])->gt($carbon_now)){
                    $upcoming = true;
                }

                $delivery_times = [];
                $meal_codes = [];

                if(!$upcoming){
                    $deliveries = \App\Models\CanteenPurchaseDelivery::join('canteen_purchases', 'canteen_purchases.id', '=', 'canteen_purchase_deliveries.canteen_purchase_id')
                        ->whereIn('canteen_purchases.canteen_order_detail_id', $order_details_ids)->where('canteen_app_user_id', $user->id)->where('date', $dates[$title])->where('break_num', $breaks[$title])->get();

                    foreach ($deliveries as $delivery){
                        $delivery_times[$delivery->canteen_product_id] = \Carbon\Carbon::create($deliveries)->format('h:i a');
                    }
                }

                foreach ($purchases as $purchase){
                    if(!isset($products[$purchase->canteen_product_id])){
                        $pr = \App\Models\CanteenProduct::find($purchase->canteen_product_id);
                        if($pr!=null){
                            $products[$purchase->canteen_product_id] = $pr->getTranslation('name');
                        }else{
                             $products[$purchase->canteen_product_id] = 'Not available';
                        }
                        $prices[$purchase->canteen_product_id] = $purchase->price;
                    }

                    if(!isset($quantities[$purchase->canteen_product_id])){
                        $quantities[$purchase->canteen_product_id] = $purchase->quantity;
                    }else{
                        $quantities[$purchase->canteen_product_id] += $purchase->quantity;
                    }

                    $meal_codes[$title] = $purchase->meal_code;

                }

                $refunded_products = [];
                $refunded_quantities = [];
                $refunded_prices = [];

                foreach ($refunds as $refund){
                     if(!isset($refunded_products[$refund->product_id])){
                        $pr = \App\Models\CanteenProduct::find($refund->product_id);
                        if($pr!=null){
                            $refunded_products[$refund->product_id] = $pr->getTranslation('name');
                        }else{
                             $refunded_products[$refund->product_id] = 'Not available';
                        }
                        $refunded_prices[$refund->product_id] = $refund->price;
                    }

                    if(!isset($refunded_quantities[$refund->product_id])){
                        $refunded_quantities[$refund->product_id] = $refund->quantity;
                    }else{
                        $refunded_quantities[$refund->product_id] += $refund->quantity;
                    }
                }

            @endphp

            <div class="history-break-item">


                <div class="history-break-title">
                    <div class="row">
                        <div class="col">{{$title}}</div>
                        @if(isset($meal_codes[$title]))
                        <div class="col-auto">Meal Code: <span class="fw-700">{{$meal_codes[$title]}}</span></div>
                        @endif
                    </div>
                </div>
                <div class="history-break-products">


                    @foreach($products as $product_id => $product)
                        <div class="history-break-product">
                            <div class="row">
                                <div class="col">
                                    @if($quantities[$product_id]>1)({{$quantities[$product_id]}}) @endif {{$product}} ({{single_price($prices[$product_id])}})</div>

                             @if($upcoming)
                                <div class="col-auto">{{translate('Upcoming')}}</div>
                             @else
                                    @if(isset($delivery_times[$product_id]))
                                        <div class="col-auto">{{$delivery_times[$product_id]}}</div>
                                    @else
                                        <div class="col-auto">{{translate('Not Received')}}</div>
                                    @endif

                             @endif
                            </div>
                        </div>
                    @endforeach

                        @foreach($refunded_products as $product_id => $product)
                            <div class="history-break-product">
                                <div class="row">
                                    <div class="col">
                                        <span class="vertical-line">
                                            @if($refunded_quantities[$product_id]>1)
                                                ({{$refunded_quantities[$product_id]}})
                                            @endif {{$product}} ({{single_price($refunded_prices[$product_id])}})
                                        </span>
                                    </div>

                                </div>
                            </div>
                        @endforeach

                </div>
            </div>
        @endforeach


    </div>
</div>
