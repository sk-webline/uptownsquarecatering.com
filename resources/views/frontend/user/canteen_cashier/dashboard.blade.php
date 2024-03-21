@extends('frontend.layouts.app_cashier')

@section('content')


    <?php

        use Carbon\Carbon;
        use Illuminate\Support\Facades\Session;
        use App\Models\CanteenLocation;
        use App\Models\Organisation;

//        $error = Session::has('error_input');
//
//        if ($error) {
//            $old_rfid = Session::get('error_input');
//            Session::forget('error_input');
//        }

        $rfid_check = 'rfid_no_dec';

        if(Session::has('rfid_check')){
            $rfid_check = Session::get('rfid_check');
        }

        if(old('rfid_check')){
            $rfid_check = old('rfid_check');
        }
        $error_msg= null;
        $card_error = false;
        $wrong_card = null;

        if($errors->has('wrong_card')){
            $card_error = true;
            $wrong_card = $errors->first('wrong_card');
            $error_msg = $errors->first('error_msg');
        }



        $view_order_break_id = null;
        if($errors->has('view_order_break_id')){
            $view_order_break_id = $errors->first('view_order_break_id');
        }

//        if (isset($errors) && count($errors) > 0){
//        }

//        $card_error = true;
//        $wrong_card = '3934182111';
//        $error_msg = $errors->first('error_msg');
//        $view_order_break_id = 57;

        $canteen_location = CanteenLocation::find(Session::get('location_id'));

        $organisation = Organisation::find($canteen_location->organisation_id);
        $canteen_setting = $organisation->current_canteen_settings();

        $breaks = $organisation->breaks;

        $accessible_break = null;

        $carbon_now = Carbon::now();

        foreach ($breaks as $break){

            $carbon_start = Carbon::create($carbon_now->format('Y-m-d') . ' ' . $break->hour_from)->subMinutes($canteen_setting->access_minutes);
            $carbon_end = Carbon::create($carbon_now->format('Y-m-d') . ' ' . $break->hour_to)->addMinutes($canteen_setting->access_minutes);

            if($carbon_now->gte($carbon_start) && $carbon_now->lte($carbon_end)){
                $accessible_break = $break;
                break;
            }

        }


    ?>

    <div id="content_body" class="mh-100-svh" @if($card_error) style="background-color: rgb(232, 31, 38, 0.1)" @endif>

        @if($accessible_break!=null)
        <div class="row justify-content-end px-15px">
            <div class="col-auto text-white bg-primary p-1">
                {{toUpper(ordinal($accessible_break->break_num))}} {{toUpper(translate('Break'))}}: {{substr($accessible_break->hour_from, 0, 5)}}
            </div>
        </div>
        @endif


        @include('frontend.inc.canteen_cashier_nav', ['route' => null, 'route_text' => null])

        <div class="cashier-body">
            <div class="cashier-grid flex-grow-1 d-flex flex-column justify-content-center pb-10px pb-md-50px">
                <div class="text-center">
                    <h1 class="fw-300 fs-20 md-fs-35 xxl-fs-50 m-0 l-space-05">{{translate('RFID Scan')}}</h1>
{{--                    <h2 class="fw-700 fs-15 md-fs-20 xxl-fs-25 l-space-05 m-0">{{translate('For')}} <span class="text-underline">{{translate('Break')}}</span></h2>--}}
                    <div class="mt-10px mt-md-20px">

                        <label class="pr-5px">{{translate('RFID')}}</label>
                        <label class="sk-switch sk-custom-switch sk-switch-grey pt-5px">
                            <input type="checkbox" name="rfid_check" @if($rfid_check == 'rfid_no_dec') checked @endif >
                            <span></span>
                        </label>
                        <label class="pl-5px">{{translate('RFID Dec')}}</label>
                    </div>

                    <div class="mt-30px mt-md-40px fw-700">

                        <label class="pr-5px text-underline">{{toUpper(translate('Break Scanning'))}}</label>
                        <label class="sk-switch sk-custom-switch sk-switch-grey pt-5px">

                             <input type="checkbox" name="unscheduled"  @if($accessible_break == null) checked disabled @else @if(Session::has('scanning_type') && Session::get('scanning_type') == 'unscheduled') checked @endif @endif>
                            <span></span>
                        </label>
                        <label class="pl-5px text-underline">{{toUpper(translate('Unscheduled Scanning'))}}</label>
                    </div>
                </div>

                <div class="mt-20px mt-md-20px mx-auto mw-565px w-100">

                    <form id="unscheduled_form" class="form-default" role="form" action="{{ route('canteen_cashier.unscheduled') }}" method="POST">
                        @csrf
                        <input id="form_input" name="rfid_no" type="hidden" value="">
                        <input name="rfid_check" type="hidden" value="{{$rfid_check}}">
                    </form>

                    <form id="break_scanning_form" class="form-default" role="form" action="{{ route('canteen_cashier.current_break_scanning') }}" method="POST">
                        @csrf
                        <input id="form_input" name="rfid_no" type="hidden" value="">
                        <input name="rfid_check" type="hidden" value="{{$rfid_check}}">
                    </form>

                    <div class="w-100 d-flex justify-content-center mx-auto mb-10px">


                        <input id="rfid_input" type="text" @if($card_error) value="{{$wrong_card}}" @endif value = "3934182111"
                               class="p-10px align-bottom fs-14 sm-fs-18 xl-fs-20 xxl-fs-30 w-100 lh-1 remove-all-spaces remove-last-space  "
                               autocomplete="off" @if($card_error) readonly style="border-color: #DD0735" @endif>
                        <button id="btn_arrow" type="button"
                                class="btn btn-primary p-10px w-sm-100px border-radius-30px-right border-none lh-1 @if($card_error) d-none @endif"
                                onclick="submit_rfid_no()">
                            <svg id="arrow_icon" class="h-15px h-sm-17px h-md-20px h-xl-25px" fill="white"
                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26.22 19.89">
                                <use xlink:href="{{static_asset('assets/img/icons/arrow_icon.svg')}}#arrow-icon"></use>
                            </svg>
                        </button>


                        <button id="btn-error"
                                class="p-10px w-sm-100px border-radius-30px-right background-bright-red border-none lh-1 @if(!$card_error) d-none @endif"
                                onclick="reset_display()">
                            <svg class="h-15px h-sm-17px h-md-20px h-xl-25px" fill="white"
                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.39 25.39">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/x_icon_error.svg')}}#x-icon-error"></use>
                            </svg>
                        </button>
                    </div>

                    <div id="rfid_error" class="text-red position-relative mb-35px @if(!$card_error) d-none @endif">
                        <span id="error_rfid_text">{{$error_msg}}</span>

                        @if($view_order_break_id!=null)

                            <form id="view_order_form" class="d-inline-block" role="form" action="{{ route('canteen_cashier.view_order') }}" method="POST">
                                @csrf
                                <input id="form_input" name="rfid_no" type="hidden" value="{{$wrong_card}}">
                                <input name="rfid_check" type="hidden" value="{{$rfid_check}}">
                                <input name="break_id" type="hidden" value="{{$view_order_break_id}}">
                                <button type="submit" class="btn d-inline-block rounded-30px bg-white py-2px px-15px text-underline mx-1">
                                    <span> {{trans('View Order')}}</span>
                                </button>

                            </form>


                        @endif
                    </div>

                    <div class="pt-7px text-center">
                        <div id="previous_meal" class="d-block text-capitalize text-primary-60 fw-400"></div>
                        <div class="fs-15">
                            <span id="previous_meal_time" class="fw-700"></span>
                        </div>
                    </div>



                </div>
            </div>

            <div class="position-absolute text-white text-center fs-12 sm-fs-14 md-fs-18 xxl-fs-27 lh-1 scans-report">
                <div class="row no-gutters">
                    <div class="col-6 col-sm-auto bg-primary">
                        <div class="px-md-20px p-5px">
                            <span class="fw-300">{{toUpper(translate('Served kids: '))}} </span>
                            <span id="success_scans" class="fw-600">
                                @if(!Session::has('canteen_served_kids')) 0 @else {{ Session::get('canteen_served_kids') }} @endif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('frontend.inc.canteen_cashier_footer')
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        // let failed_scans = 0, success_scans = 0;
        let @if($card_error) reset_error = 0 @else reset_error = 1 @endif;

        let last_rfid=null, last_scan=null;

        let @if($accessible_break == null) break_scanning = false; @else break_scanning = true; @endif

        $(document).ready(function () {
            $('#rfid_input').focus();
            last_scan = moment();

        });



        $("#btn_arrow").click(function (e) {

            if ($(this).val() != '' && reset_error == 1) {
                // submit_rfid_no();
                $('#btn_arrow').addClass('loader dots');
                $('#arrow_icon').css('opacity', 0);
                $('#btn_arrow').addClass('loader dots');

            }
        });


        // Returns a function, that, as long as it continues to be invoked, will not
        // be triggered. The function will be called after it stops being called for
        // N milliseconds. If `immediate` is passed, trigger the function on the
        // leading edge, instead of the trailing.
        function debounce(func, wait, immediate) {
            var timeout;
            return function () {
                var context = this, args = arguments;
                var later = function () {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        }

        function reset_form() {

            $('#rfid_error').css('display', 'none');
            $('#rfid_input').removeClass('border-bright-red');
            $('#btn-error').css('display', 'none');
            $('#btn_arrow').css('display', '');
            $('#rfid_input').val('');
            $('#rfid_input').attr('readonly', false);

            reset_error = 1;

        }

        $("#rfid_input").on("keyup", function (e){

            if ($(this).val() == '') {
                originalPageDisplay();
            }
            else if(e.keyCode === 13) {
                submit_rfid_no();
            }
        });

        function submit_rfid_no() {

            originalPageDisplay();

            if ($("#rfid_input").val() != '') {

                if(break_scanning == true && !$('input[name=unscheduled]').prop('checked')){
                    if(last_scan!=null && last_rfid==$("#rfid_input").val()){

                        var duration = moment.duration(moment().diff(last_scan));

                        if(duration.asSeconds()<5){

                            $('#rfid_error span').text('{{translate("Wait 5 seconds to scan again the same RFID No.")}}');
                            errorPageDisplay();
                        }else{

                            $('#break_scanning_form input[name=rfid_no]').val($("#rfid_input").val());

                            if($('input[name="rfid_check"]').prop('checked')){
                                $('#break_scanning_form input[name=rfid_check]').val('rfid_no_dec');
                            }else{
                                $('#break_scanning_form input[name=rfid_check]').val('rfid_no');
                            }

                            console.log('send_ajax')
                            $('#break_scanning_form').submit();
                        }

                    }else {

                        $('#break_scanning_form input[name=rfid_no]').val($("#rfid_input").val());

                        if($('input[name="rfid_check"]').prop('checked')){
                            $('#break_scanning_form input[name=rfid_check]').val('rfid_no_dec');
                        }else{
                            $('#break_scanning_form input[name=rfid_check]').val('rfid_no');
                        }

                        $('#break_scanning_form').submit();
                        // sendAjaxCall();



                    }
                }else{

                    $('#unscheduled_form input[name=rfid_no]').val($("#rfid_input").val());

                    if($('input[name="rfid_check"]').prop('checked')){
                        $('#unscheduled_form input[name=rfid_check]').val('rfid_no_dec');
                    }else{
                        $('#unscheduled_form input[name=rfid_check]').val('rfid_no');
                    }

                    // console.log('UNSCHEDULED SCANNING');
                    $('#unscheduled_form').submit();
                }


            }
        }



        function originalPageDisplay(){

            $('#rfid_error').addClass('d-none');
            $('#rfid_error span').text('');
            $('#previous_meal').text('');
            $('#previous_meal_time').text('');
            $('#content_body').css('background-color', 'var(--white)');
            $('#rfid_input').css('border', '');
            $('#btn-error').addClass('d-none');
            $('#btn_arrow').removeClass('loader dots');
            $('#btn_arrow').removeClass('d-none');
            $('#arrow_icon').css('opacity', 1);
            $('#rfid_input').attr('readonly', false);
            $('#rfid_input').focus();

        }

        function errorPageDisplay(){

            $('#content_body').css('background-color', 'rgb(232, 31, 38, 0.1)');
            $('#rfid_input').css('border-color', '#DD0735');
            $('#btn_arrow').addClass('d-none');
            $('#btn-error').removeClass('d-none');
            $('#rfid_error').removeClass('d-none');
            $('#rfid_input').attr('readonly', false);

        }

        function reset_display(){

            originalPageDisplay();
            $('#rfid_input').val('').focus();

        }



    </script>
@endsection
