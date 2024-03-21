@extends('frontend.layouts.app_cashier')

@section('content')

    <?php

//    $today_plan = Session::get('today_plan');
//
//    $card = $today_plan['card'];
//    $available_snack_num = $today_plan['available_snack_num'];
//    $available_meal_num = $today_plan['available_meal_num'];
//    $meal_plan_snack = $today_plan['meal_plan_snack'];
//    $meal_plan_lunch = $today_plan['meal_plan_lunch'];
//    $today_purchased_plans = $today_plan['today_purchased_plans'];

    use App\Models\CardUsageHistory;
    use Carbon\Carbon;

    $canteen_setting = $organisation->current_canteen_settings();
    $cancel_minutes = $canteen_setting->max_undo_delivery_minutes;

//    $cancel_minutes = 50;

    $carbon_now = Carbon::now();
    $now = $carbon_now->format('Y-m-d H:i:s');

    if(!isset($executed)){
        $executed = false;
    }

    $removal_timeout = 0;

    ?>

    <div id="content_body" class="mh-100-svh fs-12 md-fs-15 lg-fs-17 ">
        <div class="cashier-header ">
            <div class="cashier-grid bg-soft-secondary border-bottom border-width-2 border-primary">
                <div class="row gutters-30 align-items-center align-content-around opacity-70 py-10px ">
                    <div class="col-auto px-sm-10px px-md-40px">
                        <span>{{toUpper('Name')}}:</span> <span class="fw-700">{{toUpper($card->name)}}</span>
                    </div>

                    <div class="col px-sm-10px  px-md-40px">
                        <span>{{toUpper('RFID NO.')}}:</span> <span class="fw-700">{{toUpper($card->rfid_no)}}</span>
                    </div>

                    <div class="col-auto">
                        <a href="{{ route('canteen_cashier.dashboard') }}">
                            <svg class="h-20px h-md-35px home-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 71.59 71.59">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/catering_home.svg')}}#catering_home_icon"></use>
                            </svg>
                        </a>
                    </div>

                </div>
            </div>

            <div class="cashier-grid border-bottom border-width-2 border-primary position-relative">
                <div class="row gutters-30 align-items-center align-content-around py-10px ">
                    <div class="col">
                        <span>{{toUpper('Meal Code')}}:</span> <span class="fw-700 fs-30">{{toUpper($meal_code)}}</span>
                    </div>

                    <div class="col-auto">
                        <span>{{toUpper('Total Items')}}:</span> <span class="fw-700 fs-30">{{$total_items}}</span>
                    </div>

                    @if($executed)
                    <div class="position-absolute message-box">

                        <div class="border-radius-30px shadow-md fs-15 lh-1-7 px-15px ">
                                        <span class="text-primary">
                                             <svg class="h-20px mr-1 "
                                                  xmlns="http://www.w3.org/2000/svg"
                                                  viewBox="0 0 21.18 21.27">
                                                            <use
                                                                xlink:href="{{static_asset('assets/img/icons/warning_icon_cashier.svg')}}#warning_svg"></use>
                                                        </svg>
                                            {{translate("The order has already been executed")}}</span>

                            <svg class="h-10px mx-1 x-icon c-pointer"
                                 xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 15.34 15.34">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/x-icon.svg')}}#x_icon"></use>
                            </svg>
                        </div>

                    </div>

                    @endif

                </div>
            </div>

        </div>

        <div class="cashier-body scrollable-div d-block cashier-custom-scrollbar">

                @foreach($breaks as $break)
                    @php
                        $meal_code = null;
                        $cost = 0;
                        $items = [];
                        $prices = [];
                        $total_items = 0;
                    @endphp

                    @foreach($canteen_purchases as $purchase)
                        @if($purchase->break_num==$break->break_num)
                            @php

                                $active = false;

                                   if($accessible_break->break_num == $break->break_num ){
                                       $active = true;
                                   }

                                   $cost = $cost + ($purchase->quantity * $purchase->price);
                                   $meal_code = $purchase->meal_code;

                                   if(isset($items[$purchase->canteen_product_id])){
                                       $items[$purchase->canteen_product_id] += $purchase->quantity;
                                   }else{
                                       $items[$purchase->canteen_product_id] = $purchase->quantity;
                                   }

                                   $total_items += $purchase->quantity;
                                   $prices[$purchase->canteen_product_id] = $purchase->price;

                            @endphp
                        @endif
                    @endforeach


                    @if($items != [])
                        <div class="cashier-grid @if(!$active) opacity-50 @endif">

                            @php
                                $temp_carbon_start = Carbon::create($carbon_now->format('Y-m-d') . ' ' . $break->hour_from);
                                $temp_carbon_end = Carbon::create($carbon_now->format('Y-m-d') . ' ' . $break->hour_to);
                            @endphp

                            <div class="row no-gutters align-items-end align-content-around border-bottom border-width-1 border-primary pb-5px">
                                <div class="col">
                                 <span class="fw-700 d-block">
                                     @if($accessible_break->break_num == $break->break_num )
                                         {{toUpper(translate('Current Break'))}}:
                                     @elseif($carbon_now->gt($temp_carbon_end))
                                         {{toUpper(translate('Past Break'))}}:
                                     @elseif($carbon_now->gte($temp_carbon_start) && $carbon_now->lte($temp_carbon_end))
                                         {{toUpper(translate('Current Break'))}}:
                                     @else
                                         {{toUpper(translate('Upcoming Break'))}}:
                                     @endif
                                     </span>
                                    <span>{{toUpper(ordinal($break->break_num))}} {{toUpper(translate('Break'))}} - {{toUpper($carbon_now->format('l'))}} {{toUpper($carbon_now->format('d/m/Y'))}}
                                </div>

                                @if($executed && $active)

                                    <div class="col-auto fw-600 fs-14 pr-10px text-right">

                                        <div class="py-2px">
                                            <span> {{toUpper('Order Cost')}}: {{single_price($cost)}}</span>
                                        </div>

                                        <div class="pt-2px">
                                            <span> {{toUpper('Received Time')}}: {{$received_time}}</span>
                                        </div>
                                    </div>

                                    @php

                                        $minutes_diff = Carbon::create($carbon_now->format('Y-m-d') . ' ' . $received_time)->diffInMinutes($carbon_now);
                                    @endphp

                                    @if($minutes_diff <= $cancel_minutes)

                                        @php

                                            $removal_timeout = $cancel_minutes - $minutes_diff;

//                                            dd($removal_timeout, $cancel_minutes, $minutes_diff);

                                        @endphp

                                    <div class="undo-element col-auto fw-600 fs-14 border border-red border-width-2 p-10px text-red c-pointer">
                                        <button id="undo-button" type="button" class="row no-gutters alight-items-end p-0 text-red border-none bg-white fw-700">
                                                <svg class="h-15px mx-1 trash-icon"
                                                     xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 10 10">
                                                    <use
                                                        xlink:href="{{static_asset('assets/img/icons/trash-icon.svg')}}#content"></use>
                                                </svg>
                                               <span>{{toUpper(translate('Undo Delivery'))}}</span>
                                            </button>
                                    </div>

                                    @endif

                                @else

                                <div class="col-auto fw-600 fs-14">

{{--                                    <div class="border-bottom border-width-2 border-primary py-2px">--}}
{{--                                        <span> {{toUpper('Total Items')}}: {{$total_items}}</span>--}}
{{--                                    </div>--}}

                                    <div class="pt-2px">
                                        <span> {{toUpper('Order Cost')}}: {{single_price($cost)}}</span>
                                    </div>
                                </div>

                                @endif
                            </div>

                            <div class="row gutters-10 sm-gutters-30 my-20px text-black align-content-around">

                                @foreach($items as $canteen_product_id => $quantity)

                                    @php

                                        $product = \App\Models\CanteenProduct::find($canteen_product_id);

                                    @endphp

                                    <div class="col-auto product-card @if($active && $executed) selected @endif">

                                        <div class="item-card px-3 pt-3 position-relative">

                                            <div class="snack-res-item w-200px">
                                                <div class="snack-res-image h-150px">
                                                    <img src="{{ uploaded_asset($product->thumbnail_img) }}?width=400&qty=50"
                                                         alt="{{ $product->name }}" class="img-fit h-100 absolute-full">
                                                    <div class="snack-res-add"
                                                         data-productID="{{$product->id}}">

                                                        <div class="bg-white px-5px text-center">
                                                            {{$quantity}}
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="snack-res-text pt-10px">
                                                    <h3 class="snack-res-title fs-17 fw-400 mb-2px ">{{ $product->getTranslation('name') }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                @endforeach

                                    @if($active && !$executed)

                                        <div class="col">
                                            <form action="{{route('canteen_cashier.current_break_delivery')}}"
                                                  method="post"
                                                  class="form-horizontal ">
                                                @csrf
                                                <input type="hidden" name="canteen_app_user"
                                                       value="{{$canteen_user->id}}">
                                                <input type="hidden" name="date"
                                                       value="{{$carbon_now->format('Y-m-d')}}">
                                                <input type="hidden" name="break_id" value="{{$accessible_break->id}}">
                                                <div class="okay_btn">
                                                    <button id="okay_btn" type="submit"
                                                            class="c-pointer bg-transparent border-none">
                                                        <svg
                                                            class="h-50px h-md-90px h-xl-130px"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 149.51 149.51">
                                                            <use
                                                                xlink:href="{{static_asset('assets/img/icons/catering_ok_button.svg')}}#catering-ok-btn"></use>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>


                                    @endif

                            </div>
                        </div>
                    @endif

                @endforeach

        </div>

        @include('frontend.inc.canteen_cashier_footer')

    </div>

@endsection

@section('modal')
    @if($executed)
        @include('modals.undo_canteen_delivery')
    @endif

@endsection

@section('script')
    <script type="text/javascript">

        const remove_time = '{{$removal_timeout}}' * 60 * 1000 ;

        $(document).ready(function() {

            {{--console.log('remove_time: ', remove_time, '{{$removal_timeout}}')--}}
            @if($executed)

            $("#undo-canteen-delivery input[name=break_id]").val('{{$accessible_break->id}}');
            $("#undo-canteen-delivery input[name=canteen_user]").val('{{$canteen_user->id}}');
            $("#undo-canteen-delivery input[name=date]").val('{{$carbon_now->format('Y-m-d')}}');

            // Set a timeout to remove the element after 5 minutes (300,000 milliseconds)
            setTimeout(function() {
                $(".undo-element").remove();
                $("#undo-canteen-delivery").remove();
            }, remove_time);

            @endif
        });

        $(document).on('click', '.x-icon', function (){

            $(this).parents('div.message-box').first().addClass('d-none');

        });

        $(document).on('click', '#undo-button', function (){

            $("#undo-canteen-delivery").modal("show");

        });


    </script>

@endsection
