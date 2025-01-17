@extends('backend.layouts.app')

@section('content')

    <?php

    use App\Models\Organisation;
    use Carbon\Carbon;
    use App\Models\PlatformSetting;

    $vat = PlatformSetting::where('type', 'vat_percentage')->first()->value;

    $organisation = Organisation::findorfail($organisation_setting->organisation_id);

    $working_week_days = json_decode($organisation_setting->working_week_days);

    $holidays = json_decode($organisation_setting->holidays);

    $extra_days = json_decode($organisation_setting->extra_days()->select('date')->get());

    $start_date_organisation_setting = Carbon::create($organisation_setting->date_from)->format('Y-m-d');
    $end_date_organisation_setting = Carbon::create($organisation_setting->date_to)->format('Y-m-d');

    $preorder_days = $organisation_setting->preorder_days_num;



    ?>


    <div class="row">
        <div class="col-lg-8 mx-auto">
            <form class="form-horizontal" role="form"
                  action="{{ route('catering_plans.store', ['organisation_setting_id'=>$organisation_setting->id] ) }}"
                  method="POST" enctype="multipart/form-data">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">

                            <a href="{{route('organisations.index')}}" class="text-black" >{{translate('Organisations')}} </a> > {{$organisation->name}} >
                            <a href="{{route('organisation_settings.index', $organisation->id)}}" class="text-black" >{{translate('Periods')}} </a> >
                            {{date("d/m/Y", strtotime($organisation_setting->date_from))}}
                                - {{date("d/m/Y", strtotime($organisation_setting->date_to))}}
                            > {{translate('Add New Catering Plan')}}</h5>
                    </div>

                    <?php

                    use App\Models\OrganisationPrice;
                    use App\Models\OrganisationPriceRange;


                    $prices = \App\Models\OrganisationPriceRange::where('organisation_setting_id', $organisation_setting->id)->get();
                    $prices = json_decode($prices);
                    ?>

                    <div class="card-body">

                        @csrf

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Name')}}</label>
                            <div class="col-md-9">
                                <input type="text" value="{{ old('name') }}" id="name"
                                       name="name" class="form-control" required >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Description')}}</label>
                            <div class="col-md-9">
                                {{--                                <textarea rows=3 id="description" name="description" class="form-control"--}}
                                {{--                                          placeholder="{{translate('Catering Plan Description')}}" required></textarea>--}}
                                <textarea
                                    class="sk-text-editor form-control"
                                    data-buttons='[["font", ["bold", "underline", "italic", "clear"]],["para", ["ul", "ol", "paragraph"]],["style", ["style"]],["color", ["color"]],["table", ["table"]],["insert", ["link", "picture", "video"]],["view", ["fullscreen", "codeview", "undo", "redo"]]]'
                                    data-min-height="300"
                                    name="description"
                                    id="description" class="form-control"
                                    placeholder="{{translate('Catering Plan Description')}}"
                                ></textarea>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Start Date')}}</label>
                            <div class="col-md-9">
                                <input type="date" id="from_date" name="from_date"
                                       class="form-control dd_mm_formatted" data-date-format="DD/MM/YYYY"
                                      @if(old('from_date')!=null) data-date="{{Carbon::create(old('from_date'))->format('d/m/Y')}}" @endif  value="{{old('from_date')}}"
                                       min="{{$start_date_organisation_setting}}"
                                       max="{{$end_date_organisation_setting}}"
                                       onchange="calculate_days()" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('End Date')}}</label>
                            <div class="col-md-9">
                                <input type="date" id="to_date" name="to_date"
                                       class="form-control dd_mm_formatted" data-date-format="DD/MM/YYYY"
                                       @if(old('to_date')!=null) data-date="{{Carbon::create(old('to_date'))->format('d/m/Y')}}" @endif
                                       onchange="calculate_days()" value="{{old('to_date')}}"
                                       min="{{$start_date_organisation_setting}}"
                                       max="{{$end_date_organisation_setting}}"
                                       required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Publish Date')}}</label>
                            <div class="col-md-9">
                                <input type="date" id="publish_date" name="publish_date"
                                       class="form-control dd_mm_formatted" data-date-format="DD/MM/YYYY"
                                       @if(old('publish_date')!=null) data-date="{{Carbon::create(old('publish_date'))->format('d/m/Y')}}" @endif
                                       value="{{old('publish_date')}}"
                                       min="{{ Carbon::now()->format('Y-m-d') }}"
                                       max="{{$end_date_organisation_setting}}"
                                       required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Number of Snacks')}}</label>
                            <div class="col-md-9">
                                <input type="number" id="snack_input" name="snack_num" class="form-control" min="0" value="{{old('snack_num')}}"
                                       placeholder="{{translate('max.')}} {{$organisation_setting->max_snack_quantity}}"
                                       required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Number of Lunches')}}</label>
                            <div class="col-md-9">
                                <input type="number" id="meal_input" name="meal_num" class="form-control" min="0" value="{{old('meal_num')}}"
                                       placeholder="{{translate('max.')}} {{$organisation_setting->max_meal_quantity}}"
                                       required>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Price')}} {{translate('Includes')}} {{$vat}}% {{(translate('vat'))}}</label>
                            <div class="col-md-3">


{{--                                <input type="number" id="price" name="price" class="form-control" min="1" step="0.01"--}}
{{--                                       required value="{{old('price')}}">--}}


                                        <div class="input-group" >
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary font-weight-medium px-2">€</div>
                                            </div>
                                            <input type="number" id="price" name="price" class="form-control" min="0.01" step="0.01"
                                                   required value="{{old('price')}}">
                                        </div>







                                <div id="recommended_price" style="display: none">
                                    <span class="lh-2">{{translate('Recommended Price')}}: </span>
                                    <span id="total_cost" class="lh-2"></span>
                                    <div id="total_days_div">
                                        <span class="lh-2">{{translate('Total Days')}}: </span>
                                        <span id="total_days_calculated" class="lh-2">122</span>
                                    </div>

                                    <div id="date-error" class="d-none">
                                        <span class="lh-2 text-red">{{translate('End Date should be greater than Start Date')}} </span>

                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Active')}}</label>
                            <div class="col-md-9">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="active" checked>
                                    <span></span>
                                </label>
                            </div>
                        </div>


                    </div>
                    <div class="form-group mb-2  mr-2 text-right">
                        <a href="{{route('catering_plans.index', $organisation_setting->id)}}">
                            <button type="button" class="btn btn-soft-danger">{{translate('Cancel')}}</button>
                        </a>
                        <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                    </div>

                </div>

            </form>
        </div>
    </div>

@endsection

@section('script')

    <script type="text/javascript">

        // document.addEventListener('DOMContentLoaded', function () {
        //     document.getElementById('organisation-link').classList.add('active');
        // });

        let all_prices, max_snacks, max_meals, max_days = 0, total_days = 0, price = 0, price_check = 0;
        let preorder_days = '{{$preorder_days}}'

        let holidays = [];
        let weekdays = [];
        let extra_days = [];

        $(document).ready(function () {

            all_prices = {!! json_encode($all_prices) !!};

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

            for (var x = 0; x < all_prices.length; x++) {
                if (all_prices[x].end_range > max_days) {
                    max_days = all_prices[x].end_range;
                }
            }



            console.log(all_prices);
            console.log(max_days);


            $(document).on('keypress keyup', '#snack_input', function (e) {
                if ($('#snack_input').val() > max_snacks) {
                    $('#snack_input').val(max_snacks);
                }

                $('#total_cost').html('');
                $('#recommended_price').hide();

                calculate_price();
            });

            $(document).on('keypress keyup', '#meal_input', function (e) {

                if ($('#meal_input').val() > max_meals) {
                    $('#meal_input').val(max_meals);
                }

                $('#total_cost').html('');
                $('#recommended_price').hide();

                calculate_price();

            });

            $(document).on('keypress keyup', '#from_date', function (e) {

                if ($('#from_date').val() != '' && $('#to_date').val() != '') {
                    calculate_days();
                }

            });

            $(document).on('keypress keyup', '#to_date', function (e) {

                if ($('#from_date').val() != '' && $('#to_date').val() != '') {
                    calculate_days();
                }

            });


        });

        $("#from_date").on('change', function () {
            // alert('change');
            // console.log('preorder_days: ', preorder_days);

            var max_date = moment($(this).val()).subtract(preorder_days, 'days');

            // console.log('max_date: ', max_date.format('YYYY-MM-DD') );

            $("#publish_date").attr("max",max_date.format('YYYY-MM-DD'));


        });


        function calculate_price() {

            // console.log('in calculate price:');
            // console.log('snack:', $('#snack_input').val());
            // console.log('meal:', $('#meal_input').val());
            // console.log('ckeck:', price_check);



            var snack_price =0, meal_price=0;

            if (total_days != 0) {

                $('#total_days_div').removeClass('d-none');
                $('#date-error').addClass('d-none');

                price = 0;

                if ($('#snack_input').val() != '' && $('#snack_input').val() != 0) {
                    // console.log('in snack input:');

                    if (total_days > max_days) {

                        for (var i = 0; i < all_prices.length; i++) {
                            if (all_prices[i].end_range == max_days && all_prices[i].type == 'snack') {
                                // console.log('1st');
                                if ($('#snack_input').val() >= max_snacks) {
                                    snack_price =  all_prices[i].price * total_days;

                                } else {
                                    if (all_prices[i].quantity == $('#snack_input').val()) {
                                        snack_price =  all_prices[i].price * total_days;
                                    }
                                }
                            }
                        }

                    } else {
                        for (var i = 0; i < all_prices.length; i++) {
                            if (all_prices[i].start_range <= total_days && all_prices[i].end_range >= total_days && all_prices[i].type == 'snack') {
                                // console.log('1st');
                                if ($('#snack_input').val() >= max_snacks) {
                                    snack_price =  all_prices[i].price * total_days;

                                } else {
                                    if (all_prices[i].quantity == $('#snack_input').val()) {
                                        snack_price =  all_prices[i].price * total_days;
                                    }
                                }
                            }
                        }
                    }
                }

                if ($('#meal_input').val() != '' && $('#meal_input').val() != 0) {
                    // console.log('in meal input:');
                    if (total_days > max_days) {

                        for (var i = 0; i < all_prices.length; i++) {
                            if (all_prices[i].end_range == max_days && all_prices[i].type == 'meal') {

                                if ($('#meal_input').val() >= max_meals) {
                                    meal_price =  all_prices[i].price * total_days;

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
                                    meal_price =all_prices[i].price * total_days;
                                } else {
                                    if (all_prices[i].quantity == $('#meal_input').val()) {
                                        meal_price =  all_prices[i].price * total_days;
                                    }
                                }
                            }
                        }
                    }
                }
            }else{
                $('#total_days_div').addClass('d-none');
                $('#date-error').removeClass('d-none');
            }

            price = snack_price + meal_price;

            var price_output = '{{currency_symbol()}}' + price.toFixed(2);

            $('#total_cost').html(price_output);
            $('#total_days_calculated').html(total_days);
            $('#recommended_price').show();



        }

        function calculate_days() {

            total_days = 0;

            if ($('#from_date').val() != '' && $('#to_date').val() != '') {


                var c = 0;
                var this_date = moment($('#from_date').val());
                var end_date = moment($('#to_date').val());


                while (this_date < end_date) {

                    if (c == 0) {
                        c = 1;
                    } else {
                        this_date = this_date.add(1, 'day');
                    }

                    if (this_date >= moment('{{$organisation_setting->date_from}}') && this_date <= moment(moment('{{$organisation_setting->date_to}}'))) {

                        // console.log('date to check: ', this_date);

                        var day = this_date.weekday();

                        if (day == 7) {
                            day = 0;
                        }

                        if (weekdays.includes(day)) {

                            if (!holidays.includes(this_date.format('YYYY-MM-DD'))) {
                                total_days = total_days + 1;
                            }

                        } else {

                            for (var i = 0; i < extra_days.length; i++) {
                                if (extra_days[i].date == this_date.format('YYYY-MM-DD')) {
                                    total_days = total_days + 1;
                                }
                            }
                        }

                    }


                }

                $('#total_days_calculated').html(total_days);
                if ($('#snack_input').val() != '' && $('#snack_input').val() != 0) {
                    calculate_price();
                }

            }



        }


    </script>
@endsection

