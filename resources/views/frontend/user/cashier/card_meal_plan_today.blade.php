@extends('frontend.layouts.app_cashier')

@section('content')

    <?php

    $today_plan = Session::get('today_plan');

    $card = $today_plan['card'];
    $available_snack_num = $today_plan['available_snack_num'];
    $available_meal_num = $today_plan['available_meal_num'];
    $meal_plan_snack = $today_plan['meal_plan_snack'];
    $meal_plan_lunch = $today_plan['meal_plan_lunch'];
    $today_purchased_plans = $today_plan['today_purchased_plans'];

    use App\Models\CardUsageHistory;
    use Carbon\Carbon;

    $cancel_minutes = Session::get('cancel_minutes');
    $minutes_ago = Carbon::now()->subMinutes($cancel_minutes)->format('Y-m-d H:i:s');
    $now = Carbon::now()->format('Y-m-d H:i:s');
    $previous_meal = CardUsageHistory::where('card_id', $card->id)->orderBy('created_at', 'desc')->first();

    $previous_meal_to_cancel = CardUsageHistory::where('card_id', $card->id)->where('created_at', '>=', $minutes_ago)
        ->where('created_at', '<=', $now)->orderBy('created_at', 'desc')->first();

    $max_snack = $organisation_setting->max_snack_quantity;
    $max_meal = $organisation_setting->max_meal_quantity;

    ?>

    <div id="content_body" class="mh-100-svh">
        @include('frontend.inc.cashier_nav', ['route' => route('cashier.buffet_scanning'), 'route_text' => translate('Buffet Scanning')])

        <div class="cashier-body pt-0">
            <div
                class="row no-gutters background-brand-grey border-bottom-primary border-top-primary l-space-05 fs-14 md-fs-17 xxl-fs-20">
                <div
                    class="col-lg-6 border-bottom border-lg-bottom-0 border-lg-right border-primary border-width-2 border-lg-width-2">
                    <div class="cashier-grid py-5px">
                        <div class="text-primary-60 fw-600 ">{{toUpper(translate('Card name'))}}:</div>
                        <div class="fw-300 fs-20 md-fs-30 xxl-fs-40">{{toUpper(translate($card->name))}}</div>
                        <div class="mt-10px text-primary-60">
                            <div class="d-block fw-300">{{toUpper(translate('Rfid no'))}}:</div>
                            <div class="d-block fw-700">{{format_rfid_no($card->rfid_no)}}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="cashier-grid py-5px">
                        <div class="fw-300 fs-20 md-fs-30 xxl-fs-40">{{toUpper(translate('Meal plan'))}}</div>
                        <div class="row gutters-5 sm-gutters-15 align-items-end mt-10px">

                            <div class="col fw-700">
                                <div class="">
                                    <span class="text-primary-60">{{toUpper(translate('Snack'))}}:</span>
                                    <span class="fs-20 md-fs-24 xxl-fs-27">{{$meal_plan_snack}}</span>
                                </div>
                                <div class="mt-5px">
                                    <span class="text-primary-60">{{toUpper(translate('Lunch'))}}:</span>
                                    <span class="fs-20 md-fs-24 xxl-fs-27">{{$meal_plan_lunch}}</span>
                                </div>
                            </div>

                            <div class="col-auto fs-12 sm-fs-1em">
                                @if($previous_meal != null)
                                    <div class="row gutters-5 sm-gutters-15 align-items-end">
                                        <div class="col">
                                            <div
                                                class="d-block text-capitalize text-primary-60 fw-400">{{translate('Previous Meal')}}
                                                : {{translate($previous_meal->purchase_type)}}</div>
                                            <div class="xxl-fs-23">
                                                <span
                                                    class="fw-700">{{Carbon::create($previous_meal->created_at)->format('H:i')}}</span>
                                                - {{Carbon::create($previous_meal->created_at)->format('d/m/Y')}}
                                            </div>
                                        </div>
                                        <div class="col-auto text-center pb-5px">
                                            @if($previous_meal_to_cancel!='')
                                                <button class="d-block btn p-0" onclick="cancel_meal()">
                                                    <svg class="h-30px" xmlns="http://www.w3.org/2000/svg"
                                                         viewBox="0 0 35.82 35.82">
                                                        <use
                                                            xlink:href="{{static_asset('assets/img/icons/circle-trash.svg')}}#circle-trash"></use>
                                                    </svg>
                                                    <span
                                                        class="d-block border-bottom border-inherit text-primary-50 fw-700 fs-8 sm-fs-10">{{toUpper(translate('Cancel Meal'))}}</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cashier-grid flex-grow-1 d-flex flex-column">
                <div class="my-20px text-center">
                    <h1 class="fw-300 fs-18 md-fs-35 xxl-fs-50 m-0 l-space-05 lh-1">{{translate('Please Choose the Meal Type')}}
                        :</h1>
                </div>
                <div class="flex-grow-1 cashier-body-meal-plans-boxes">
                    <div class="row xl-gutters-35 justify-content-center">

                        @if($max_snack>0 )
                            <div class="col-lg-6 mb-15px mb-lg-0">
                                <button id="snack" class="w-100 btn catering-choice clickedSelection"
                                        @if($available_snack_num<=0) disabled @endif>
                                    <div class="text-md-center background-soft-grey cashier-meal-plan-box">
                                        <div
                                            class="d-flex flex-md-column align-items-center align-content-md-stretch justify-content-center justify-content-md-start cashier-meal-plan-box-wrap">
                                            <div
                                                class="d-flex flex-grow-md-1 flex-column justify-content-center cashier-meal-plan-box-left">
                                                <svg
                                                    class="h-25px h-sm-40px h-md-70px h-xl-100px h-xxl-180px cashier-meal-plan-box-image"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 290.03 299.7" fill="var(--primary)">
                                                    <use
                                                        xlink:href="{{static_asset('assets/img/icons/snack_icon.svg')}}#snack_icon"></use>
                                                </svg>
                                            </div>
                                            <div class="cashier-meal-plan-box-right lh-1 lh-1-2">
                                                <div
                                                    class="fs-12 sm-fs-15 md-fs-20 text-primary-60 fw-700 l-space-05 mt-md-20px">{{toUpper(translate('Snack'))}}</div>
                                                <div class="fs-14 sm-fs-20 md-fs-28 fw-700 text-primary">
                                                    @if($available_snack_num>0)
                                                        {{toUpper($available_snack_num)}} {{toUpper(translate('Available'))}}
                                                    @else
                                                        {{toUpper(translate('Not Available'))}}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            </div>

                        @endif

                        @if($max_meal>0 )

                            <div class="col-lg-6">
                                <button id="lunch" class="w-100 btn catering-choice clickedSelection"
                                        @if($available_meal_num<=0) disabled @endif
                                >
                                    <div class="text-md-center background-soft-grey cashier-meal-plan-box">
                                        <div
                                            class="d-flex flex-md-column align-items-center align-content-md-stretch justify-content-center justify-content-md-start cashier-meal-plan-box-wrap">
                                            <div
                                                class="d-flex flex-grow-md-1 flex-column justify-content-center cashier-meal-plan-box-left">
                                                <svg
                                                    class="h-25px h-sm-40px h-md-70px h-xl-100px h-xxl-180px cashier-meal-plan-box-image"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 296.31 248.78" fill="var(--primary)">
                                                    <use
                                                        xlink:href="{{static_asset('assets/img/icons/lunch_icon.svg')}}#lunch_icon"></use>
                                                </svg>
                                            </div>
                                            <div class="cashier-meal-plan-box-right lh-1 lh-1-2">
                                                <div
                                                    class="fs-12 sm-fs-15 md-fs-20 text-primary-60 fw-700 l-space-05 mt-md-20px">{{toUpper(translate('Lunch'))}}</div>
                                                <div class="fs-14 sm-fs-20 md-fs-28 fw-700 text-primary">
                                                    @if($available_meal_num>0)
                                                        {{toUpper($available_meal_num)}} {{toUpper(translate('Available'))}}
                                                    @else
                                                        {{toUpper(translate('Not Available'))}}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            </div>

                        @endif

                    </div>
                </div>

                <div class="row my-10px">
                    <div class="col-6">
                        <a href="{{route('cashier.dashboard')}}">
                            <svg
                                class="h-50px h-md-90px fw-700 opacity-50 hov-opacity-100"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0  96.98 96.98" fill="var(--primary)">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/catering_cancel_btn.svg')}}#catering_cancel_icon"></use>
                            </svg>
                        </a>
                    </div>

                    <div class="col-6 text-right">
                        <a id="okay_btn" data-href="" class="c-pointer border-none bg-transparent submit-meal"
                           style="display: none">
                            <svg
                                class="h-50px h-md-90px h-xl-130px"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 149.51 149.51">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/catering_ok_button.svg')}}#catering-ok-btn"></use>
                            </svg>
                        </a>
                    </div>
                </div>

                <a id="go_to_dashboard" href="{{route('cashier.dashboard')}}"></a>
            </div>

            <div class="position-absolute text-white text-center fs-12 sm-fs-14 md-fs-18 xxl-fs-27 lh-1 scans-report">
                <div class="row no-gutters">
                    <div class="col-6 col-sm-auto bg-primary">
                        <div class="px-md-20px p-5px">
                            {{toUpper(translate(\Carbon\Carbon::today()->format('l')))}}
                            - {{ \Carbon\Carbon::today()->format('d/m/Y')}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('frontend.inc.cashier_footer')
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


        function selectBuffetType() {
            console.log(this.id);
        }

        $(".clickedSelection").click(function (e) {
            e.preventDefault();

            if (this.id == 'snack') {
                console.log(this.id);

                @if($available_snack_num>0)
                $('#snack').addClass('active');
                $('#snack').removeClass('opacity-40');
                $('#lunch').removeClass('active');
                $('#lunch').addClass('opacity-40');
                $('#okay_btn').attr("data-href", 'snack');
                $('#okay_btn').show();
                @endif

            } else {

                @if($available_meal_num>0)
                $('#lunch').addClass('active');
                $('#snack').removeClass('active');
                $('#lunch').removeClass('opacity-40');
                $('#snack').addClass('opacity-40');
                $('#okay_btn').attr("data-href", 'lunch');
                $('#okay_btn').show();
                @endif

            }


        });


        $(".submit-meal").click(function (e) {

            // alert($('#okay_btn').attr('data-href'));
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('catering_plan_purchase.submit_card_meal') }}',
                data: {
                    card_id: '{{$card->id}}',
                    type: $('#okay_btn').attr('data-href'),
                    plans: {!! json_encode($today_purchased_plans) !!}
                },
                dataType: "JSON",
                success: function (data) {

                    console.log(data);

                    if (data == 1) {
                        $('#modal_msg').html('{{toUpper(translate('Meal recorded!'))}}');
                        $('#meal-submited-modal').modal('show');

                        setTimeout(function () {

                            document.getElementById("go_to_dashboard").click();
                        }, 2000);

                    }


                },
                error: function () {


                }
            });

        });

        @if($previous_meal_to_cancel != '')

        function cancel_meal() {

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{route('card_usage.cancel_meal' , ['card_id'=>encrypt($card->id), 'card_usage_id'=>encrypt($previous_meal_to_cancel->id) ])}}',
                data: {
                    {{--card_id: '{{$card->id}}',--}}
                    {{--type: $('#okay_btn').attr('data-href'),--}}
                    {{--plans: {!! json_encode($today_purchased_plans) !!}--}}
                },
                dataType: "JSON",
                success: function (data) {

                    console.log(data);

                    if (data == 1) {


                        $('#modal_msg').html('{{toUpper(translate('Meal Canceled!'))}}');
                        $('#meal-submited-modal').modal('show');

                        setTimeout(function () {

                            document.getElementById("go_to_dashboard").click();
                        }, 2000);

                    }


                },
                error: function () {


                }
            });
        }

        @endif


    </script>

@endsection
