@extends('frontend.layouts.app_cashier')

@section('content')

    <div id="content_body" class="mh-100-svh">

        @include('frontend.inc.cashier_nav', ['route' => route('cashier.buffet_scanning'), 'route_text' => translate('Buffet Scanning')])

        <div class="cashier-body">
            <div class="cashier-grid flex-grow-1 d-flex flex-column justify-content-center pb-10px pb-md-50px">
                <div class="text-center">
                    <h1 class="fw-300 fs-20 md-fs-35 xxl-fs-50 m-0 l-space-05">{{translate('RFID Scan')}}</h1>
                    <h2 class="fw-700 fs-15 md-fs-20 xxl-fs-25 l-space-05 m-0">{{translate('For')}} <span class="text-underline">{{translate($type)}}</span></h2>
                    <div class="mt-30px mt-md-40px">

                        <label class="pr-5px">{{translate('RFID')}}</label>
                        <label class="sk-switch sk-custom-switch sk-switch-grey pt-5px">
                            <input type="checkbox" name="rfid_check" checked>
                            <span></span>
                        </label>
                        <label class="pl-5px">{{translate('RFID Dec')}}</label>
                    </div>
                </div>

                <div class="mt-20px mt-md-20px mx-auto mw-565px w-100">

                    <div class="w-100 d-flex justify-content-center mx-auto mb-10px">
                        <input id="rfid_input" type="text" class="p-10px align-bottom fs-14 sm-fs-18 xl-fs-20 xxl-fs-30 w-100 lh-1 form-control remove-all-spaces remove-last-space" autocomplete="off">
                        <button id="btn_arrow" class="btn btn-primary p-10px w-sm-100px border-radius-30px-right border-none lh-1"
                                onclick="submit_rfid_no()">
                            <svg id="arrow_icon" class="h-15px h-sm-17px h-md-20px h-xl-25px" fill="white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26.22 19.89">
                                <use xlink:href="{{static_asset('assets/img/icons/arrow_icon.svg')}}#arrow-icon"></use>
                            </svg>
                        </button>

                        <button id="btn-error" class="p-10px w-sm-100px border-radius-30px-right background-bright-red border-none lh-1" style="display: none" onclick="reset_display()">
                            <svg class="h-15px h-sm-17px h-md-20px h-xl-25px" fill="white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.39 25.39">
                                <use xlink:href="{{static_asset('assets/img/icons/x_icon_error.svg')}}#x-icon-error"></use>
                            </svg>
                        </button>
                    </div>
                    <div id="rfid_error" class="text-red position-relative mb-35px" style="display: none">
                        <span id="error_rfid_text" class="position-absolute">{{translate("RFID doesn't exist. Please scan another one.")}} </span>
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
                            <span class="fw-300">{{toUpper(translate('Successful scans: '))}} </span>
                            <span id="success_scans" class="fw-600">0</span>
                        </div>
                    </div>
                    <div class="col-6 col-sm-auto bg-dark-red">
                        <div class="px-md-20px p-5px">
                            <span class="fw-300">{{toUpper(translate('Failed scans: '))}} </span>
                            <span id="failed_scans" class="fw-600">0</span>
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

        let last_rfid=null, last_scan=null;

        $(document).ready(function () {
            $('#rfid_input').focus();
            last_scan = moment();

        });

        let failed_scans = 0, success_scans = 0;

        /*$("#rfid_input").on("keyup", debounce(function () {

            if ($(this).val() != '') {
                submit_rfid_no();
            }else{
                originalPageDisplay();
            }

        }, 1500));*/

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

                if(last_scan!=null && last_rfid==$("#rfid_input").val()){

                    var duration = moment.duration(moment().diff(last_scan));

                    if(duration.asSeconds()<5){

                        $('#rfid_error span').text('{{translate("Wait 5 seconds to scan again the same RFID No.")}}');
                        errorPageDisplay();
                    }else{
                        sendAjaxCall();
                    }

                }else {

                    sendAjaxCall();

                }
            }
        }

        function sendAjaxCall(){

            $('#arrow_icon').css('opacity', 0);

            $('#btn_arrow').addClass('loader dots');
            $('#rfid_input').attr('readonly', true);

            if($('input[name="rfid_check"]').prop('checked')){
                var rfid_check = 'rfid_no_dec';
            }else{
                var rfid_check = 'rfid_no';
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('catering_plan_purchase.serve_meal_type') }}',
                data: {
                    rfid_no: $('#rfid_input').val(),
                    type: '{{$type}}',
                    rfid_check: rfid_check

                },
                dataType: "JSON",
                success: function (data) {

                    last_rfid = $('#rfid_input').val();
                    last_scan = moment();



                    if (data['status'] === 0) {
                        let audio = new Audio('/public/assets/sounds/error_sound.mp3');
                        audio.play();
                        failed_scans = failed_scans + 1;
                        $('#failed_scans').html(failed_scans);
                        if (data.message) {
                            $('#rfid_error span').text(data.message);
                        }

                        if (data.last_card_usage_msg!=null && data.last_card_usage_time!='' ) {

                            $('#previous_meal').text(data.last_card_usage_msg);
                            $('#previous_meal_time').text(data.last_card_usage_time);

                        }
                        errorPageDisplay();
                    } else if (data['status'] === 1) {
                        let audio = new Audio('/public/assets/sounds/success.mp3');
                        audio.play();
                        success_scans = success_scans + 1;
                        $('#success_scans').html(success_scans);
                        originalPageDisplay();
                        // setTimeout(function () {
                            $('#rfid_input').val('');
                        // }, 3000);
                    }
                },
                error: function () {
                }
            });
        }

        function originalPageDisplay(){

            $('#rfid_error').hide();
            $('#rfid_error span').text('');
            $('#previous_meal').text('');
            $('#previous_meal_time').text('');
            $('#content_body').css('background-color', 'var(--white)');
            $('#rfid_input').css('border', '');
            $('#btn-error').hide();
            $('#btn_arrow').removeClass('loader dots');
            $('#btn_arrow').show();
            $('#arrow_icon').css('opacity', 1);
            $('#rfid_input').attr('readonly', false);
            $('#rfid_input').focus();

        }

        function errorPageDisplay(){

            $('#content_body').css('background-color', 'rgb(232, 31, 38, 0.1)');
            $('#rfid_input').css('border-color', '#DD0735');
            $('#btn_arrow').hide();
            $('#btn-error').show();
            $('#rfid_error').show();
            $('#rfid_input').attr('readonly', false);

        }

        function reset_display(){

            originalPageDisplay();
            $('#rfid_input').val('').focus();

        }




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
        };



    </script>



@endsection
