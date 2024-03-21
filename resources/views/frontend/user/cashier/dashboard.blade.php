@extends('frontend.layouts.app_cashier')

@section('content')
    {{--    <div class="sk-titlebar mt-20px mb-15px mb-lg-40px">--}}
    {{--        <h1 class="fs-16 sm-fs-20 fw-500 m-0">{{ translate(' Cashier Dashboard') }}</h1>--}}
    {{--    </div>--}}

    <?php

    $error = Session::has('error_input');

    if ($error) {

        $old_rfid = Session::get('error_input');
        Session::forget('error_input');
    }


    ?>

    <div id="content_body" class="mh-100-svh">

        @include('frontend.inc.cashier_nav', ['route' => null, 'route_text' => null])

        <div class="cashier-body">
            <div class="cashier-grid flex-grow-1 d-flex flex-column">
                <h1 class="fw-300 fs-20 md-fs-35 xxl-fs-50 m-0 l-space-05 text-center">{{translate('Home Screen')}}</h1>

                <div class="flex-grow-1 mt-10px mt-md-30px mt-lg-50px cashier-body-dashboard-boxes">
                    <div class="row xl-gutters-35">

                        <div class="col-lg-6 mb-20px mb-lg-0">
                            <a class="c-pointer text-capitalize" href="{{ route('cashier.buffet_scanning') }}">
                            <span class="d-block text-center background-soft-grey cashier-dashboard-box">
                                <span class="d-flex flex-column cashier-dashboard-box-wrap">
                                    <span class="d-flex flex-grow-1 flex-column justify-content-center">
                                        <svg class="h-50px h-sm-100px h-lg-240px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 278.87 244.96" fill="var(--primary)">
                                            <use xlink:href="{{static_asset('assets/img/icons/catering_icon.svg')}}#catering-icon"></use>
                                        </svg>
                                    </span>

                                    <span class="d-block fs-15 md-fs-20 text-primary-60 fw-700 l-space-05 mt-10px mt-md-20px">{{toUpper(translate('Buffet Scanning'))}}</span>
                                </span>
                            </span>
                            </a>
                        </div>

                        <div class="col-lg-6">
                            <div class="text-center background-soft-grey cashier-dashboard-box">
                                <div class="d-flex flex-column cashier-dashboard-box-wrap">
                                    <div class="flex-grow-1 d-flex flex-column justify-content-center">
                                        <div class="d-block">
                                            <svg class="h-30px h-md-50px h-lg-90px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 91.16 91.16" fill="var(--primary)">
                                                <use xlink:href="{{static_asset('assets/img/icons/search_icon.svg')}}#search-icon"></use>
                                            </svg>
                                        </div>

                                        <?php

                                        $organisation = \App\Models\Organisation::findorfail(Session::get('organisation_id'));
                                        $required_field_name = $organisation->required_field_name;

                                        ?>

                                        <form id="rfid_plan_form" class="form-default" role="form" action="{{ route('catering_plan_purchase.get_card_today_plan') }}" method="GET">
{{--                                            @csrf--}}
                                            <div class="mt-10px mt-md-20px mt-xxl-50px mx-auto d-flex justify-content-center px-xl-40px">

                                                <input id="rfid_input" name="rfid_input" type="text"
                                                       class="form-no-space p-10px py-md-3 px-md-15px px-xxl-30px align-bottom fs-14 sm-fs-18 xl-fs-23 xxl-fs-25 w-100 border-white-c text-primary border-radius-30px-left border-none lh-1-5 @if($error) border-bright-red @endif remove-all-spaces remove-last-space"
                                                       @if($required_field_name==null) placeholder="{{translate('Search by RFID')}}"
                                                       @else  placeholder="{{translate('Search by RFID')}}/{{$required_field_name}}..."
                                                       @endif @if($error) value="{{$old_rfid}}" readonly @endif required autocomplete="off">


                                                <button class="btn btn-primary p-10px py-md-3 px-md-20px border-radius-30px-right border-none"
                                                        id="btn_arrow" @if($error) style="display: none" @endif>
                                                    <svg id="arrow_icon" class="h-15px h-sm-17px h-md-20px" fill="white" xmlns="http://www.w3.org/2000/svg"
                                                         stroke-width="10px" viewBox="0 0 26.22 19.89">
                                                        <use
                                                            xlink:href="{{static_asset('assets/img/icons/arrow_icon.svg')}}#arrow-icon"></use>
                                                    </svg>
                                                </button>

                                                <button id="btn-error" class="btn p-10px py-md-3 px-md-20px border-radius-30px-right background-bright-red border-none" @if(!$error) style="display: none" @endif onclick="reset_form()">
                                                    <svg class="h-15px h-sm-17px h-md-20px" fill="white"
                                                         xmlns="http://www.w3.org/2000/svg"
                                                         viewBox="0 0 25.39 25.39">
                                                        <use xlink:href="{{static_asset('assets/img/icons/x_icon_error.svg')}}#x-icon-error"></use>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="fs-12 sm-fs-1em h-15px px-xl-40px mt-10px text-red text-left mb-10px">
                                                <span id="rfid_error" @if(!$error) style="display: none" @endif >{{translate("RFID doesn't exist. Please scan another one.")}} </span>
                                            </div>
                                            <div class="">

                                                <label class="pr-5px">{{translate('RFID')}}</label>
                                                <label class="sk-switch sk-custom-switch pt-5px">
                                                    <input type="checkbox" name="rfid_check" onchange="rfid_check_change()" checked>
                                                    <span></span>
                                                </label>
                                                <label class="pl-5px">{{translate('RFID Dec')}}</label>
                                             </div>
                                        </form>
                                    </div>
                                    <div class="d-block fs-15 md-fs-20 text-primary-60 fw-700 l-space-05 mt-10px mt-md-20px">{{toUpper(translate('Search'))}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('frontend.inc.cashier_footer')
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        let failed_scans = 0, success_scans = 0;
        let @if($error) reset_error = 0
        @else reset_error = 1 @endif;

        // $("#btn_arrow").on("keyup", debounce(function () {

            $("#btn_arrow").click(function (e) {

            if ($(this).val() != '' && reset_error == 1) {
                // submit_rfid_no();
                $('#btn_arrow').addClass('loader dots');
                $('#arrow_icon').css('opacity', 0);
                $('#btn_arrow').addClass('loader dots');

                $('#rfid_plan_form').submit();
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

        function rfid_check_change(){
            console.log($('input[name="rfid_check"]').prop('checked'));

            if(!$('input[name="rfid_check"]').prop('checked')) {


                // $('input[name="rfid_check"]').prop('checked', true);

            } else {
                // $('input[name="rfid_check"]').prop('checked', false);
                // $('input[name="catering"]').val(1);

                // $('#custom_packets_div').css("display", "");
            }

            // console.log($('input[name="catering"]').val());
        }
    </script>
@endsection
