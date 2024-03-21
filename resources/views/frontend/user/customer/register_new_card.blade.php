@extends('frontend.layouts.user_panel')
@section('meta_title'){{ translate('Register New Card') }}@stop
@section('panel_content')

    <h1 class="fs-16 mb-15px text-primary-50 fw-700 lh-1">{{ toUpper(translate('Register New Card')) }}</h1>

    <div class="background-brand-grey p-10px p-md-30px p-xl-40px fs-14 md-fs-16">
        <div class="row xxl-gutters-25">
            <div class="col-xl-6 mb-15px mb-md-20px">
                <div class="input-group" id="rfid_no_div">
                    <div class="form-control-with-label small-focus animate flex-grow-1 @if(old('rfid_no')) focused @endif">
                        <label>{{ translate('RFID no.')}}</label>
                        <input type="text" class="form-control remove-all-spaces remove-last-space" value="{{ old('rfid_no') }}" name="rfid_no" id="rfid_no" autocomplete="off">
                    </div>
                    <div class="input-group-append line w-60px w-xxl-85px">
                        <div id="submit_button_div" class="flex-grow-1">
                            <button id="rfid_no_submit"
                                    class="btn btn-primary btn-block fs-12 xxl-fs-14 px-2px fw-400"
                                    type="button">{{ toUpper(translate('Submit'))}}</button>
                        </div>

                        <div class="loader flex-grow-1" id="loader-div" style="display: none">
                        </div>

                        <div id="correct_rfid" class="flex-grow-1" style="display: none">

                            <div class="w-100 h-30px position-absolute custom-div d-flex flex-column justify-content-center">
                                <div class="text-center">

                                    <svg class="h-30px" fill="green"
                                         xmlns="http://www.w3.org/2000/svg" height="25" width="25"
                                         viewBox="0 0 30 30">
                                        <use
                                            xlink:href="{{static_asset('assets/img/icons/tick.svg')}}#tick"></use>
                                    </svg>
                                </div>
                            </div>

                        </div>

                        <div id="already_registered_rfid" class="flex-grow-1" style="display: none">

                            <div class="w-100 h-30px position-absolute custom-div d-flex flex-column justify-content-center">
                                <div class="text-center">

                                    <svg class=" h-lg-30px"
                                         xmlns="http://www.w3.org/2000/svg" height="30" width="30"
                                         viewBox="0 0 21.18 21.27">
                                        <use
                                            xlink:href="{{static_asset('assets/img/icons/warning_icon.svg')}}#warning_svg"></use>
                                    </svg>
                                </div>
                            </div>

                        </div>

                        <div id="incorrect_rfid" class="flex-grow-1" style="display: none">

                            <div class="w-100 h-30px position-absolute custom-div d-flex">
                                <div class="text-center m-auto" style="color: red;">
                                    <svg class="z-1 h-17px" fill="red"
                                         xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 25.39 25.39">
                                        <use xlink:href="{{static_asset('assets/img/icons/x_icon_error.svg')}}#x-icon-error"></use>
                                    </svg>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <span id="rfid-error-msg" class="fw-500 text-red fs-14" ></span>
{{--                <span id="rfid-registered-error-msg" class="fw-500 text-red fs-14" style="display: none">{{ translate("RFID is already registered")}}</span>--}}

            </div>
        </div>

        <div class="border-bottom-grey mb-5px"></div>

        <h3 class="fs-14 fw-600 mb-10px mb-md-5px">{{toUpper(translate('Card Info'))}}</h3>

        <form id="card-info-form" class="form-default" role="form" action="{{ route('card.register_card') }}" method="POST">
            @csrf

            <div class="row xxl-gutters-25">

                <input type="hidden" name="card_to_register" id="card_to_register_reg_form">

                <div class="col-md-6">
                    <div class="form-group mb-15px mb-md-25px">
                        <div class="form-control-with-label small-focus">
                            <label>{{  translate('Name your Card') }}*</label>
                            <input type="text" class="form-control" name="card_name" required>
                        </div>
                        <div id="card_name_message" class="invalid-feedback fs-13 d-block"></div>
                    </div>
                </div>

{{--                @if($organisation->required_field_name != null)--}}
                    <div id="required_field_div"  class="col-md-6 d-none">
                        <div class="form-group mb-15px mb-md-25px">
                            <div class="form-control-with-label small-focus">
                                <label id="required_field_name_label"></label>
{{--                                    {{  translate($organisation->required_field_name) }}*--}}
                                <input type="text" class="form-control" id="required_field_input" name="required_field" >
                            </div>
                            <div id="required_field_message" class="invalid-feedback fs-13 d-block"></div>
                        </div>
                    </div>
{{--                @endif--}}

                <div class="col-md-6 ml-auto">
                    <button id="submit_from_btn" type="submit" class="btn btn-primary btn-block fs-16 md-fs-18 py-10px py-md-13px" disabled>{{ toUpper(translate('Register New Card')) }}</button>
                </div>
            </div>


        </form>
    </div>

@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    @if(App::getLocale() == "gr")
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/localization/messages_el.min.js"></script>
    @endif
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

    <script type="text/javascript">

        let error_msg=0;

        $(document).ready(function () {

            $(document).on('keypress keyup', '#rfid_no', function (e) {
                let $this = $(this);
                if (e.keyCode === 13) {
                    $('#rfid_no_submit').click();
                }
            });
        });

        $(document).on('keydown', '#rfid_no', function (e) {

            if (error_msg == 1) {
                original_display();
            }
            original_display();
            // if(($('##rfid_no').val()).length==0){
            //     original_display();
            // }
        });

        function submit_rfid_no(rfid_no) {

            $('#rfid-error-msg').text('');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('rfid-card-exists')}}",
                type: 'get',
                data: {
                    rfid_no: rfid_no
                },
                success: function (response) {

                    console.log(response);
                    $('#loader-div').hide();
                    if(response['status']==1){

                        $('#rfid_no_div').css('border-color', ' rgba(128, 128, 128, 0.13)');
                        if (response['user_id'] == null) {
                            $('#card_to_register').val($('#rfid_no').val());
                            $('#card_to_register_reg_form').val($('#rfid_no').val());
                            $('#already_registered_rfid').hide();
                            $('#incorrect_rfid').hide();
                            $('#correct_rfid').show();

                            if(response['required_field_name']!=null){
                                $('#required_field_name_label').text(response['required_field_name'] + '*' );

                                $('#required_field_input').prop("required", true);
                                $('#required_field_div').removeClass('d-none');
                            }

                            $("#submit_from_btn").prop("disabled", false);
                        } else {
                            error_msg=1;
                            $('#incorrect_rfid').hide();
                            $('#correct_rfid').hide();
                            $('#already_registered_rfid').show();
                            $('#rfid-error-msg').text('{{translate("RFID no is already registered")}}');
                            $('#rfid-error-msg').removeClass('text-red');
                            $('#rfid-error-msg').css('color', '#f4b400');

                            $('#required_field_name_label').text('');
                            $('#required_field_input').prop("required", false);
                            $('#required_field_div').addClass('d-none');

                            $("#submit_from_btn").prop("disabled", true);
                        }
                    } else {
                        error_display();
                    }
                }
            });
        }



        $('#rfid_no_submit').on("click", function () {

            $('#rfid-error-msg').text('');

            $('#correct_rfid').hide();
            $('#incorrect_rfid').hide();
            $('#already_registered_rfid').hide();



            if(($('#rfid_no').val()).length>=1) {
                $('#loader-div').show();
                $('#submit_button_div').hide();
                submit_rfid_no($('#rfid_no').val());
            }


        });

        function error_display(){
            error_msg=1;
            $('#already_registered_rfid').hide();
            $('#correct_rfid').hide();
            $('#incorrect_rfid').show();

            $('#rfid_no_div').css('border-color', 'red');
            $('#rfid-error-msg').addClass('text-red');
            $('#rfid-error-msg').text('{{ translate("RFID does not exist")}}');
            $("#submit_from_btn").prop("disabled", true);
        }

        function original_display(){

            error_msg=0;

            $('#card_to_register').val('');
            $('#card_to_register_reg_form').val('');
            $('#rfid_no_div').css('border-color', ' rgba(128, 128, 128, 0.13)');
            $('#already_registered_rfid').hide();
            $('#incorrect_rfid').hide();
            // $('#rfid-error-msg').hide();
            $('#rfid-error-msg').text('');

            $('#correct_rfid').hide();
            $('#submit_button_div').show();
            $("#submit_from_btn").prop("disabled", true);

            $('#required_field_name_label').text('');
            $('#required_field_input').prop('required',false);
            $('#required_field_div').addClass('d-none');
        }




        /*Validations*/
        $('#card-info-form').validate({
            errorClass: 'is-invalid',
            rules: {
                card_name: {
                    required: true,
                },
                required_field: {
                    required: true,
                },
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") === "card_name" ) {
                    $("#card_name_message").html(error);
                }
                else if (element.attr("name") === "required_field" ) {
                    $("#required_field_message").html(error);
                }
            }
        });
    </script>
@endsection
