@extends('frontend.layouts.user_panel_2')

@section('meta_title')
    {{translate('New Subscriptions')}}
@stop

@section('panel_content')

    <?php

    use App\Models\Organisation;
    use App\Http\Controllers\CartController;
    use Illuminate\Support\Facades\Session;

    $organisation = Organisation::findorfail($organisation_setting->organisation_id);

    $min_days = $organisation_setting->preorder_days_num;

    $working_week_days = json_decode($organisation_setting->working_week_days);

    $holidays = json_decode($organisation_setting->holidays);

    $extra_days = json_decode($organisation_setting->extra_days()->select('date')->get());

    $max_snack = $organisation_setting->max_snack_quantity;
    $max_meal = $organisation_setting->max_meal_quantity;




    ?>

    <h1 class="fs-18 md-fs-25 fw-700 mb-5px lh-1"
        data-aos="fade-left">{{ toUpper(translate('Choose your meal package')) }}</h1>
    <h2 class="fs-14 md-fs-16 fw-400 text-primary-50 mb-20px mb-md-70px mb-xxl-125px"
        data-aos="fade-left">{{toUpper($card->name)}} {{ toUpper(translate('Card')) }}
        | <span class="d-inline-block">{{toUpper($organisation->name)}}</span></h2>

    <div class="row xxl-gutters-25">
        @foreach($catering_plans as $catering_plan)
            <div data-aos="zoom-in" class="col-md-6 col-xxl-4 mb-25px mb-md-40px mb-xxl-60px">
                <span class="d-block subscription-style">
                    <span class="d-block px-13px px-md-20px py-5px subscription-style-header head-1">
                            <span class="d-block fs-14 d-block">{{toUpper(translate($catering_plan->name))}}</span>
                        </span>
                    <span class="d-block d-lg-none px-13px px-md-20px py-5px subscription-style-header head-2">
                        <span class="d-block fs-26 fw-700 lh-1">{{ format_price($catering_plan->price) }}</span>
                    </span>
                    <span class="d-block px-13px px-md-20px py-5px subscription-style-header head-3">
                        <span class="row gutters-5 align-items-center">
                            <span class="d-none d-lg-block col-6">
                                <span
                                    class="d-block fs-26 fw-700 lh-1">{{ format_price($catering_plan->price) }}</span>
                            </span>
                            <span class="d-block col-lg-6">
                                <span
                                    class="d-block fs-12 d-block text-primary-70 fw-500 mb-5px">{{translate('Subscription')}}</span>
                                <span
                                    class="d-block fs-14 fw-500">{{$catering_plan->from_date }} - {{$catering_plan->to_date }}</span>
                            </span>
                        </span>
                    </span>
                    <span class="d-flex flex-column justify-content-between px-13px px-md-20px py-15px py-md-25px subscription-style-body">
                        <span class="d-block">
                            @if($catering_plan->snack_num>0)
                                <span class="d-block text-primary-70 fw-500 fs-14 mb-10px">
                                    <span class="pr-1 fw-700 text-primary">{{ toUpper(translate('Snack')) }}:</span>
                                    {{ $catering_plan->snack_num }}
                                    @if($catering_plan->snack_num==1)
                                        {{ translate('Snack per day') }}
                                    @else
                                        {{ translate('Snacks per day') }}
                                    @endif
                                </span>
                            @endif
                            @if($catering_plan->meal_num>0)
                                <span class="d-block text-primary-70 fw-500 fs-14 mb-15px">
                                    <span class="pr-1 fw-700 text-primary">{{ toUpper(translate('Lunch')) }}:</span>
                                    {{ $catering_plan->meal_num }}
                                        @if($catering_plan->meal_num==1)
                                            {{ translate('Lunch per day') }}
                                        @else
                                            {{ translate('Lunches per day') }}
                                        @endif
                                </span>
                            @endif
                            @php
                                $cater_description = clear_html_enities($catering_plan->description);
                            @endphp
                            @if($cater_description)
                                <span class="d-block fs-14 fw-500 mb-15px">
                                    <span class="text-primary-50 text-truncate-2">{{$cater_description}}</span>
                                    <span class="border-bottom border-inherit hov-text-secondary meal-plan-desc-link" data-id="{{$catering_plan->id}}">{{toUpper(__('View More'))}}</span>
                                </span>
                            @endif
                        </span>
                        <a href="javascript:void(0);" data-id="{{$catering_plan->id}}" class="addCateringPlanToCart">
                            <span class="btn btn-block btn-outline-primary btn-subscription-style fw-500 fs-16 md-fs-18 py-10px lh-1">{{ toUpper(translate('Add To Cart')) }}</span>
                        </a>
                    </span>
                </span>
            </div>
        @endforeach
    </div>

    @if($organisation->custom_packets==1)
        <div class="row xxl-gutters-25">
            <div class="col-md-6 col-xxl-4" data-aos="zoom-in">
                <div class="bg-primary-10 fs-14 lg-fs-16 custom-packet-style">
                    <div class="border-bottom border-width-2 border-primary-100 px-13px px-md-20px py-15px">
                        <h3 class="m-0 fs-22 md-fs-25 fw-700 lh-1">{{ translate('Make your own plan') }}</h3>
                    </div>
                    <div class="px-13px px-md-20px py-20px">
                        <div class="row gutters-10">
                            <div class="col col-grow-80px align-items-end">
                                <div>{{ translate('For how long do you want them?') }}</div>
                                <div class="d-block text-primary-70 fw-500 fs-12 md-fs-14" id="date_range_value"></div>
                            </div>

                            <div class="col col-80px">
                                <button class="range-btn-style show_date_range_modal">
                                    <svg class="w-15px align-middle" xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 16.46 18.18">
                                        <use
                                            xlink:href="{{static_asset('assets/img/icons/calendar.svg')}}#calendar_svg"></use>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div id="preloader-prices" class="loader mt-10px" style="display: none">

                        </div>

                        <div id="prices_inputs" style="display: none">

                            @if($max_snack>0)
                                <div class="row gutters-10 mt-10px align-items-center">
                                    <div
                                        class="col col-grow-80px">{{ translate('How many daily snacks do you want?') }}</div>
                                    <div class="col col-80px">
                                        <input id="snack_input" type="number" class="form-control px-5px py-2px fs-14"
                                               min="0" max="{{$organisation_setting->max_snack_quantity}}"
                                               placeholder="{{translate('max.')}} {{$organisation_setting->max_snack_quantity}}">
                                    </div>
                                </div>
                            @endif
                            @if($max_meal>0)
                                <div class="row gutters-10 mt-10px align-items-center">
                                    <div
                                        class="col col-grow-80px">{{ translate('How many daily lunches do you want?') }}</div>
                                    <div class="col col-80px">
                                        <input id="meal_input" type="number" class="form-control px-5px py-2px fs-14"
                                               min="0" max="{{$organisation_setting->max_meal_quantity}}"
                                               placeholder="{{translate('max.')}} {{$organisation_setting->max_meal_quantity}}">
                                    </div>
                                </div>
                            @endif

                        </div>


                        <div class="row gutters-10 mt-10px align-items-end">
                            <div class="col">{{ translate('That would cost you...') }}</div>
                            <div class="col-auto fw-700 fs-30" id="total_cost"></div>
                        </div>

                        <div class=" text-right text-error fs-13 mt-20px">
                            <span id="custom_package_error"></span>
                        </div>

                        <button id="custom"
                                class="btn btn-outline-primary btn-block fs-16 mt-5px  addCateringPlanToCart">{{toUpper(translate('Add To Cart')) }}</button>


                    </div>
                </div>
            </div>
        </div>

    @endif

@endsection

@section('modal')

    @include('modals.pick-date-range-modal')

    <div class="modal fade" id="cart-one-option-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="c-preloader">
                    <i class="fa fa-spin fa-spinner"></i>
                </div>
                <div id="cart-one-option-modal-body">


                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="not-available-plan-modal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered " role="document">
            <div class="modal-content position-relative">
                {{--                <div class="c-preloader">--}}
                {{--                    <i class="fa fa-spin fa-spinner"></i>--}}
                {{--                </div>--}}
                <div id="not-available-plan-modal-body">


                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="catering-plan-added-to-cart-modal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered " role="document">
            <div class="modal-content position-relative">

                <div id="catering-plan-added-to-cart-modal-body">


                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="catering-plan-description-modal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered my-modal-xl-2" role="document">
            <div class="modal-content position-relative">
                <div id="catering-plan-description-modal-body">


                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        let lan = '{{App::getLocale()}}';

        $(document).on('click', '.meal-plan-desc-link', function (){
            $('#catering-plan-description-modal-body').html('');
            $('#catering-plan-description-modal').modal('show');
            var id = $(this).data('id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('catering_plan.meal_description') }}',
                data: {
                    id: id,
                },
                dataType: "JSON",
                success: function (data) {
                    $('#catering-plan-description-modal').modal('show');
                    $('#catering-plan-description-modal-body').html(data.view);
                },
            });
        });

        let calendar, range_start_date, range_end_date, total_days = 0, min_days = '{{$min_days}}';
        let price_check = 0;
        let card_id = null, old_event_count = 0, start_info = null;

        let all_prices, max_days = 0, max_days_key;
        let price = 0, organisation_setting_id;
        let selected_dates = [], max_snacks, max_meals;

        let c_start, c_end;

        let final_price = 0;


        $(document).ready(function () {

            if(lan=='gr'){
                lan = 'el';
            }


            $("#cart_icon").css('display', '');
            $('#cart_icon_fixed').css('display', '');
            $('#cart-one-option-modal-body').html('');
            $('#catering-plan-added-to-cart-modal-body').html('');
            $('#not-available-plan-modal-body').html('');

            // $('#catering-plan-added-to-cart-modal').modal('show');

            all_prices = {!! json_encode($all_prices) !!};
            card_id = '{{$card->id}}';
            organisation_setting_id = {!! json_encode($organisation_setting->id) !!};



            for (var x = 0; x < all_prices.length; x++) {
                if (all_prices[x].end_range > max_days) {
                    max_days = all_prices[x].end_range;
                    // max_days_key = x;
                }
            }


            $(document).on('keypress keyup', '#snack_input', function (e) {
                if ($('#snack_input').val() > max_snacks) {
                    $('#snack_input').val(max_snacks);

                }

                if ($('#snack_input').val() < 0) {
                    $('#snack_input').val(0);

                }

                $('#meal_input').css('border', 'none');
                $('#snack_input').css('border', 'none');
                $('#custom_package_error').text('');

                calculate_price();
            });

            $(document).on('keypress keyup', '#meal_input', function (e) {

                if ($('#meal_input').val() > max_meals) {
                    $('#meal_input').val(max_meals);
                }

                if ($('#meal_input').val() < 0) {
                    $('#meal_input').val(0);

                }

                $('#meal_input').css('border', 'none');
                $('#snack_input').css('border', 'none');
                $('#custom_package_error').text('');

                calculate_price();

            });
        });

        function calculate_price() {


            var snack_price = 0, meal_price = 0;


            if (total_days != 0) {

                price = 0;

                @if($max_snack>0)

                if ($('#snack_input').val() != '' && $('#snack_input').val() != 0) {


                    if (total_days > max_days) {

                        for (var i = 0; i < all_prices.length; i++) {
                            if (all_prices[i].end_range == max_days && all_prices[i].type == 'snack') {

                                if ($('#snack_input').val() >= max_snacks) {
                                    snack_price = all_prices[i].price * total_days;

                                } else {
                                    if (all_prices[i].quantity == $('#snack_input').val()) {
                                        snack_price = all_prices[i].price * total_days;
                                    }
                                }
                            }
                        }

                    } else {
                        for (var i = 0; i < all_prices.length; i++) {
                            if (all_prices[i].start_range <= total_days && all_prices[i].end_range >= total_days && all_prices[i].type == 'snack') {

                                if ($('#snack_input').val() >= max_snacks) {
                                    snack_price = all_prices[i].price * total_days;

                                } else {
                                    if (all_prices[i].quantity == $('#snack_input').val()) {
                                        snack_price = all_prices[i].price * total_days;
                                    }
                                }
                            }
                        }
                    }
                }

                @endif

                    @if($max_meal>0)

                if ($('#meal_input').val() != '' && $('#meal_input').val() != 0) {

                    if (total_days > max_days) {

                        for (var i = 0; i < all_prices.length; i++) {
                            if (all_prices[i].end_range == max_days && all_prices[i].type == 'meal') {

                                if ($('#meal_input').val() >= max_meals) {
                                    meal_price = all_prices[i].price * total_days;

                                } else {
                                    if (all_prices[i].quantity == $('#meal_input').val()) {
                                        meal_price = all_prices[i].price * total_days;
                                    }
                                }
                            }
                        }

                    } else {

                        for (var i = 0; i < all_prices.length; i++) {
                            if (all_prices[i].start_range <= total_days && all_prices[i].end_range >= total_days && all_prices[i].type == 'meal') {
                                if ($('#meal_input').val() >= max_meals) {
                                    meal_price = all_prices[i].price * total_days;
                                } else {
                                    if (all_prices[i].quantity == $('#meal_input').val()) {
                                        meal_price = all_prices[i].price * total_days;
                                    }

                                }
                            }
                        }
                    }
                }

                @endif
            }

            price = snack_price + meal_price;

            var price_output = '{{currency_symbol()}}' + price.toFixed(2);

            $('#total_cost').html(price_output);

            final_price = price;

        }

        $(".show_date_range_modal").click(function (e) {

            price = 0;
            $('#total_cost').html('');

            e.preventDefault();


            card_id = '{{$card->id}}';


            // for this card get the organisations working days
            var holidays = [];
            var weekdays = [];
            var extra_days = [];
            var full_dates = {!! json_encode(Session::get('full_dates')) !!};

            max_snacks = '{{$organisation_setting->max_snack_quantity}}';
            max_meals = '{{$organisation_setting->max_meal_quantity}}';


            @if(in_array('Mon', $working_week_days))
            weekdays.push(1);
            @endif
            @if(in_array('Tue', $working_week_days))
            weekdays.push(2);
            @endif
            @if(in_array('Wed', $working_week_days))
            weekdays.push(3);
            @endif
            @if(in_array('Thu', $working_week_days))
            weekdays.push(4);
            @endif
            @if(in_array('Fri', $working_week_days))
            weekdays.push(5);
            @endif
            @if(in_array('Sat', $working_week_days))
            weekdays.push(5);
            @endif
            @if(in_array('Sun', $working_week_days))
            weekdays.push(0);
            @endif

                holidays = {!! json_encode($holidays) !!};
            extra_days = {!! json_encode($extra_days) !!};

            var date_start = {!! json_encode($organisation_setting->date_from) !!};
            var date_end = {!! json_encode($organisation_setting->date_to) !!};

            var calendarEl = document.getElementById('calendar-date-range');


            calendar = new FullCalendar.Calendar(calendarEl, {
                locale: lan,
                headerToolbar: {
                    left: 'prev',
                    center: '',
                    right: 'next'
                },
                firstDay: 1,
                initialView: 'multiMonthFourMonth',
                businessHours: {
                    dow: weekdays,
                },
                views: {
                    multiMonthFourMonth: {
                        type: 'multiMonthYear',
                        // multiMonthMaxColumns: 2,
                        validRange: {
                            start: {!! json_encode($organisation_setting->date_from) !!}, // moment().format('YYYY-MM-DD'),
                            end: {!! json_encode($organisation_setting->date_to) !!}
                        },
                        duration: {months: 2},
                        dateIncrement: {months: 1},
                    }
                },
                dateClick: function (info) {

                    var day_picked = moment(info.dateStr);
                    var minimum_day = moment().add(2, 'd');

                    // addLoaderToModal();
                    if (full_dates.includes(info.dateStr) == false) {

                        var target_date = new Date();

                        if (range_start_date == null || range_start_date == '') {

                            price_check = 0;

                            range_start_date = info.dateStr;
                            start_info = info;

                            for (var i = 0; i < old_event_count; i++) {


                                if (calendar.getEventById(i) != null) {
                                    calendar.getEventById(i).remove();
                                }
                            }

                            old_event_count = 0;
                            // removeLoaderFromModal();
                            if (moment(info.dateStr) >= moment({!! json_encode($organisation_setting->date_from) !!}) && moment(info.dateStr) <= moment({!! json_encode($organisation_setting->date_to) !!})) {

                                calendar.addEvent({
                                    id: old_event_count,
                                    start: info.dateStr,
                                    display: 'background',
                                    color: '#66789c'
                                });


                            }

                            total_days = 0;

                            calendar.render();

                        } else if (range_end_date == null || range_end_date == '') {


                            range_end_date = info.dateStr;


                            var start = new Date(range_start_date);
                            var start_string = range_start_date;
                            var end = new Date(info.dateStr);
                            var end_string = info.dateStr;

                            for (var i = 0; i < old_event_count; i++) {
                                if (calendar.getEventById(i) != null) {
                                    calendar.getEventById(i).remove();
                                }
                            }


                            if (start > end) {
                                end = new Date(range_start_date);
                                end_string = range_start_date
                                start = new Date(info.dateStr);
                                start_string = info.dateStr;
                            }


                            var c = 0;
                            old_event_count = 1;

                            var this_date = moment(start_string);

                            while (this_date < moment(end_string)) {

                                if (c == 0) {
                                    c = 1;
                                } else {
                                    this_date = this_date.add(1, 'day');
                                }

                                if (this_date >= moment({!! json_encode($organisation_setting->date_from) !!}) && this_date <= moment({!! json_encode($organisation_setting->date_to) !!})
                                    && full_dates.includes(info.dateStr) == false) {


                                    var day = this_date.weekday();

                                    if (day == 7) {
                                        day = 0;
                                    }

                                    if (weekdays.includes(day)) {

                                        if (!holidays.includes(this_date.format('YYYY-MM-DD')) && full_dates.includes(this_date.format('YYYY-MM-DD')) == false) {

                                            calendar.addEvent({
                                                id: old_event_count,
                                                start: this_date.format('YYYY-MM-DD'),
                                                display: 'background',
                                                color: '#66789c'
                                            });

                                            old_event_count = old_event_count + 1;
                                            total_days = total_days + 1;

                                            selected_dates.push(this_date.format('YYYY-MM-DD'));

                                        }


                                    } else {

                                        for (var i = 0; i < extra_days.length; i++) {
                                            if (extra_days[i].date == this_date.format('YYYY-MM-DD') &&
                                                full_dates.includes(this_date.format('YYYY-MM-DD')) == false) {
                                                calendar.addEvent({
                                                    id: old_event_count,
                                                    start: this_date.format('YYYY-MM-DD'),
                                                    display: 'background',
                                                    color: '#66789c'
                                                });

                                                old_event_count = old_event_count + 1;
                                                total_days = total_days + 1;

                                                selected_dates.push(this_date.format('YYYY-MM-DD'));


                                            }
                                        }
                                    }

                                    // removeLoaderFromModal();


                                }
                            }

                            $("#select-dates").one('click', function () {

                                $("#prices_inputs").hide();
                                $('#preloader-prices').show();

                                $('.range-btn-style').css('border', 'none');
                                $('#custom_package_error').text('');

                                var value = moment(start_string).format('DD/MM/YYYY') + ' - ' + moment(end_string).format('DD/MM/YYYY');

                                c_start = start_string;
                                c_end = end_string;

                                $('#date_range_value').html(value);


                                price_check = 1;

                                //send ajax

                                $.ajax({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    type: "POST",
                                    url: '{{ route('catering_plan.plan_max_quantities') }}',
                                    data: {
                                        selected_dates: selected_dates,
                                        card_id: card_id,

                                    },
                                    dataType: "JSON",
                                    success: function (data) {

                                        if (data['meal'] < 0) {
                                            data['meal'] = 0;
                                        }

                                        if (data['snack'] < 0) {
                                            data['snack'] = 0;
                                        }

                                        var placeholder_snack = 'max. ' + data['snack'];
                                        var placeholder_meal = 'max. ' + data['meal'];

                                        max_snacks = data['snack'];
                                        max_meals = data['meal'];

                                        $("#snack_input").attr("placeholder", placeholder_snack);
                                        $("#meal_input").attr("placeholder", placeholder_meal);

                                        $("#snack_input").val('');
                                        $("#meal_input").val('');

                                        $('#preloader-prices').hide();
                                        $("#prices_inputs").show();


                                    },
                                    error: function () {


                                    }
                                });


                                $("#date-range-modal").modal("hide");


                                selected_dates = [];


                            });

                            range_start_date = null;
                            range_end_date = null;

                            calendar.render();

                        }


                    }
                },
                dateIncrement: {months: 1},
                datesSet: function (dateInfo) {
                    var prevButton = calendarEl.querySelector(".fc-prev-button");
                    var nextButton = calendarEl.querySelector(".fc-next-button");
                    nextButton.disabled = false;
                    prevButton.disabled = false;
                    if (calendar.view.currentEnd >= new Date(date_end)) {

                        if (nextButton) {
                            nextButton.disabled = true;
                        }
                    } else if (calendar.view.currentStart <= new Date(date_start)) {

                        if (prevButton) {
                            prevButton.disabled = true;
                        }
                    }
                },
                events: [

                        @foreach(Session::get('full_dates') as $full_date)

                    {
                        {{--id: '{{$full_date}}',--}}
                        start: '{{$full_date}}', // '2023-07-31' ,
                        display: 'background',
                        color: 'var(--grey)',

                    },
                    @endforeach
                ]


            });

            $("#date-range-modal").modal("show");

            if(moment('{{$organisation_setting->date_from}}').format("M")==moment('{{$organisation_setting->date_to}}').format("M")){
                calendar.changeView('dayGridMonth');
            }

            calendar.render();

        });


        $(".addCateringPlanToCart").click(function (e) {

            var catering_plan_id = $(this).data('id');
            var from = c_start, to = c_end;
            @if($max_snack>0)
            var snack_num = $('#snack_input').val();
            @else
                snack_num = 0;
            @endif


            @if($max_meal>0)
            var meal_num = $('#meal_input').val();
            @else
                meal_num = 0;
            @endif


            if (snack_num == '') {
                snack_num = 0;
            }

            if (meal_num == '') {
                meal_num = 0;
            }

            card_id = '{{$card->id}}';

            if (catering_plan_id == 'custom') {

                if (c_start == null || c_end == null) {

                    $('.range-btn-style').css('border-width', '1px');
                    $('.range-btn-style').css('border-color', 'red');
                    $('.range-btn-style').css('border-style', 'solid');

                    $('#custom_package_error').text('{{translate("Please choose your subscription dates.")}}');

                    return;


                }

                @if($max_meal>0 && $max_snack>0)
                if (meal_num == 0 && snack_num == 0) {
                    // snack_input
                    $('#meal_input').css('border-width', '1px');
                    $('#meal_input').css('border-color', 'red');
                    $('#meal_input').css('border-style', 'solid');

                    $('#snack_input').css('border-width', '1px');
                    $('#snack_input').css('border-color', 'red');
                    $('#snack_input').css('border-style', 'solid');

                    $('#custom_package_error').text('{{translate("Please choose daily snack and daily lunch number.")}}');

                    return;
                }
                @endif

                    @if($max_meal>0 && $max_snack<=0)
                if (meal_num == 0) {
                    // snack_input
                    $('#meal_input').css('border-width', '1px');
                    $('#meal_input').css('border-color', 'red');
                    $('#meal_input').css('border-style', 'solid');

                    $('#custom_package_error').text('{{translate("Please choose daily snack and daily lunch number.")}}');

                    return;
                }
                @endif

                    @if($max_meal<=0 && $max_snack>0)
                if (snack_num == 0) {
                    // snack_input
                    $('#snack_input').css('border-width', '1px');
                    $('#snack_input').css('border-color', 'red');
                    $('#snack_input').css('border-style', 'solid');

                    $('#custom_package_error').text('{{translate("Please choose daily snack and daily lunch number.")}}');

                    return;
                }
                @endif




            }


            $('.c-preloader').show();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('cart.addCateringPlanToCart') }}',
                data: {
                    id: catering_plan_id,
                    card_id: card_id,
                    organisation_setting_id: organisation_setting_id,
                    remove_prev: '0',
                    from: from,
                    to: to,
                    snack_num: snack_num,
                    meal_num: meal_num,
                    price: final_price
                },
                dataType: "JSON",
                success: function (data) {


                    $('#cart-one-option-modal-body').html('');
                    $('#catering-plan-added-to-cart-modal-body').html('');
                    $('#not-available-plan-modal-body').html('');

                    $('.c-preloader').hide();

                    if (data.status == 0) {

                        $('#cart-one-option-modal-body').html(data.view);

                        if ($('#one_option').attr('data-button') == 'remove_prev') {

                        }

                        $("#cart-one-option-modal").modal("show");
                        $("#catering-plan-added-to-cart-modal").modal("hide");
                        $('#not-available-plan-modal').modal('hide');


                    }

                    if (data.status == 1) {
                        $('#catering-plan-added-to-cart-modal-body').html(data.view);
                        $("#catering-plan-added-to-cart-modal").modal("show");
                        $("#cart-one-option-modal").modal("hide");
                        $('#not-available-plan-modal').modal('hide');

                    }

                    if (data.status == 3) {

                        $('#not-available-plan-modal-body').html(data.view);

                        $("#catering-plan-added-to-cart-modal").modal("hide");
                        $("#cart-one-option-modal").modal("hide");
                        $('#not-available-plan-modal').modal("show");

                    }


                    updateNavCart();


                },
                error: function () {

                    $('#addToCart-modal-body').html(null);
                    $('.c-preloader').hide();
                    $('#addToCart').modal('hide');

                }
            });

        });

        function go_to_cart() {

            var catering_plan_id = document.getElementById('this_catering_plan').value;

            var from = null, to = null, snack_num = null, meal_num = null;

            var price_to_give;

            card_id = {!! $card->id !!};

            if (catering_plan_id == 'custom') {

                from = document.getElementById('new_plan_from').value;
                to = document.getElementById('new_plan_to').value;
                price_to_give = document.getElementById('new_plan_price').value;
                snack_num = document.getElementById('new_plan_snack').value;
                meal_num = document.getElementById('new_plan_meal').value;


            }

            $('.c-preloader').show();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('cart.addCateringPlanToCart') }}',
                data: {
                    id: catering_plan_id,
                    card_id: card_id,
                    organisation_setting_id: organisation_setting_id,
                    remove_prev: '1',
                    from: from,
                    to: to,
                    snack_num: snack_num,
                    meal_num: meal_num,
                    price: price_to_give
                },
                dataType: "JSON",
                success: function (data) {


                    $('#cart-one-option-modal-body').html('');
                    $('.c-preloader').hide();

                    if (data.status == 0) {


                        $('#cart-one-option-modal-body').html(data.view);
                        $("#cart-one-option-modal").modal("show");
                        $("#catering-plan-added-to-cart-modal").modal("hide");

                    }

                    if (data.status == 1) {
                        $('#cart-one-option-modal-body').html('');
                        $('#catering-plan-added-to-cart-modal-body').html(data.view);
                        $("#catering-plan-added-to-cart-modal").modal("show");
                        $("#cart-one-option-modal").modal("hide");

                    }


                },
                error: function () {

                    $('#addToCart-modal-body').html(null);
                    $('.c-preloader').hide();
                    $('#addToCart').modal('hide');

                }
            });


        }


    </script>
@endsection
