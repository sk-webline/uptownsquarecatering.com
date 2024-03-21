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

    $cancel_minutes = Session::get('cancel_minutes');
//    $minutes_ago = Carbon::now()->subMinutes($cancel_minutes)->format('Y-m-d H:i:s');
    $carbon_now = Carbon::now();
    $now = $carbon_now->format('Y-m-d H:i:s');
//    $previous_meal = CardUsageHistory::where('card_id', $card->id)->orderBy('created_at', 'desc')->first();
//
//    $previous_meal_to_cancel = CardUsageHistory::where('card_id', $card->id)->where('created_at', '>=', $minutes_ago)
//        ->where('created_at', '<=', $now)->orderBy('created_at', 'desc')->first();
//
//    $max_snack = $organisation_setting->max_snack_quantity;
//    $max_meal = $organisation_setting->max_meal_quantity;

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
                            <svg class="h-20px h-md-35px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 71.59 71.59">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/catering_home.svg')}}#catering_home_icon"></use>
                            </svg>
                        </a>
                    </div>

                </div>
            </div>
        </div>


        <form action="{{route('canteen_cashier.unscheduled_delivery')}}" method="post" class="form-horizontal scrollable-div" >
        @csrf

            <input type="hidden" name="canteen_app_user" value="{{$canteen_user->id}}">
            <input type="hidden" name="date" value="{{$carbon_now->format('Y-m-d')}}">

        <div class="cashier-body">

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
                    <div class="cashier-grid ">

                        @php
                            $temp_carbon_start = Carbon::create($carbon_now->format('Y-m-d') . ' ' . $break->hour_from);
                            $temp_carbon_end = Carbon::create($carbon_now->format('Y-m-d') . ' ' . $break->hour_to);
                        @endphp

                        <div class="row no-gutters align-items-end align-content-around border-bottom border-width-1 border-primary pb-5px">
                            <div class="col">
                                 <span class="fw-700 d-block">
                                    @if($carbon_now->gt($temp_carbon_end))
                                                     {{toUpper(translate('Past Break'))}}:
                                                 @elseif($carbon_now->gte($temp_carbon_start) && $carbon_now->lte($temp_carbon_end))
                                                     {{toUpper(translate('Current Break'))}}:
                                                 @else
                                                     {{toUpper(translate('Upcoming Break'))}}:
                                                 @endif
                                     </span>
                                <span>{{toUpper(ordinal($break->break_num))}} {{toUpper(translate('Break'))}} - {{toUpper($carbon_now->format('l'))}} {{toUpper($carbon_now->format('d/m/Y'))}}
                            </div>
                            <div class="col-auto px-20px">
                                <span> {{toUpper('Meal Code')}}: <span class="fw-700 fs-25">{{$meal_code}}</span> </span>
                            </div>

                            <div class="col-auto fw-600 fs-14">

                                <div class="border-bottom border-width-2 border-primary py-2px">
                                    <span> {{toUpper('Total Items')}}: {{$total_items}}</span>
                                </div>

                                <div class="pt-2px">
                                    <span> {{toUpper('Order Cost')}}: {{single_price($cost)}}</span>
                                </div>


                            </div>

                        </div>

                        <div class="row gutters-10 sm-gutters-30 my-20px text-black">

                            <div class="col-auto">
                                <label class="sk-megabox d-block mb-0">
                                    <input type="checkbox" class="sk-radio" name="{{"breakNum_" . $break->break_num}}" >
                                    <span class="d-flex px-10px py-15px sk-megabox-elem-custom  rounded-0">
                                            <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                    </span>
                                </label>
                            </div>

                            @foreach($items as $canteen_product_id => $quantity)

                                    @php
                                        $product = \App\Models\CanteenProduct::find($canteen_product_id);
                                    @endphp

                                <div class="col-auto product-card">

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

                        </div>
                    </div>
                @endif

            @endforeach


        </div>


        <div class="okay_btn">
            <button id="okay_btn" type="submit" class="c-pointer bg-transparent border-none" disabled>
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

        @include('frontend.inc.canteen_cashier_footer')
    </div>

@endsection

@section('modal')
    <div class="modal fade" id="meal-submited-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xs modal-dialog-centered " role="document">
            <div class="modal-content position-relative rounded-40px">

                <div class="c-preloader">
                    <i class="fa fa-spin fa-spinner"></i>
                </div>

                <div class="modal-body text-center p-15px p-md-20px fs-17 md-fs-25 text-center">
                    <div class="row align-items-center gutters-10">
                        <div class="col-auto">
                            <svg
                                class=""
                                xmlns="http://www.w3.org/2000/svg"
                                height="30" width="30"
                                viewBox="0 0 31.07 31.07">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/circle-tick.svg')}}#circle-tick"></use>
                            </svg>
                        </div>
                        <div class="col">
                            <span id="modal_msg">{{toUpper(translate('Meal recorded!'))}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script type="text/javascript">

        $(document).on('click', 'input[type=checkbox]', function (){

            $('#okay_btn').prop('disabled', true);

            $('.product-card').each(function() {

                var input =  $(this).parents('.row').first().find('input[type=checkbox]').first();
                var checked = input.prop('checked');

                // Access the current input element
                console.log('input: ', input);
                console.log('checked: ', checked);

                if(checked){
                    $(this).addClass('selected');
                    $('#okay_btn').prop('disabled', false);
                }else{
                    $(this).removeClass('selected');
                }
            });
        });



        $(".submit-meal").click(function (e) {

            // alert($('#okay_btn').attr('data-href'));
            {{--$.ajax({--}}
            {{--    headers: {--}}
            {{--        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
            {{--    },--}}
            {{--    type: "POST",--}}
            {{--    url: '{{ route('catering_plan_purchase.submit_card_meal') }}',--}}
            {{--    data: {--}}
            {{--        card_id: '{{$card->id}}',--}}
            {{--        type: $('#okay_btn').attr('data-href'),--}}
            {{--        plans: {!! json_encode($today_purchased_plans) !!}--}}
            {{--    },--}}
            {{--    dataType: "JSON",--}}
            {{--    success: function (data) {--}}

            {{--        console.log(data);--}}

            {{--        if (data == 1) {--}}
            {{--            $('#modal_msg').html('{{toUpper(translate('Meal recorded!'))}}');--}}
            {{--            $('#meal-submited-modal').modal('show');--}}

            {{--            setTimeout(function () {--}}

            {{--                document.getElementById("go_to_dashboard").click();--}}
            {{--            }, 2000);--}}

            {{--        }--}}


            {{--    },--}}
            {{--    error: function () {--}}


            {{--    }--}}
            {{--});--}}

        });

{{--        @if($previous_meal_to_cancel != '')--}}

        function cancel_meal() {

            {{--$.ajax({--}}
            {{--    headers: {--}}
            {{--        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
            {{--    },--}}
            {{--    type: "POST",--}}
            {{--    url: '{{route('card_usage.cancel_meal' , ['card_id'=>encrypt($card->id), 'card_usage_id'=>encrypt($previous_meal_to_cancel->id) ])}}',--}}
            {{--    data: {--}}
            {{--        --}}{{--card_id: '{{$card->id}}',--}}
            {{--        --}}{{--type: $('#okay_btn').attr('data-href'),--}}
            {{--        --}}{{--plans: {!! json_encode($today_purchased_plans) !!}--}}
            {{--    },--}}
            {{--    dataType: "JSON",--}}
            {{--    success: function (data) {--}}

            {{--        console.log(data);--}}

            {{--        if (data == 1) {--}}


            {{--            $('#modal_msg').html('{{toUpper(translate('Meal Canceled!'))}}');--}}
            {{--            $('#meal-submited-modal').modal('show');--}}

            {{--            setTimeout(function () {--}}

            {{--                document.getElementById("go_to_dashboard").click();--}}
            {{--            }, 2000);--}}

            {{--        }--}}


            {{--    },--}}
            {{--    error: function () {--}}


            {{--    }--}}
            {{--});--}}
        }

{{--        @endif--}}


    </script>

@endsection
