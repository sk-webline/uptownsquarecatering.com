<div class="modal-body p-0">
    <div class="p-10px">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
            <svg class="h-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14.74 14.74">
                <use xlink:href="{{static_asset('assets/img/icons/popup-close.svg')}}#content"></use>
            </svg>
        </button>
    </div>
    <div class="px-15px px-lg-35px pb-30px">

        <h3 class="text-center fs-18 lg-fs-25 fw-700 mb-30px mb-sm-35px">{{toUpper(translate('Edit Card Name'))}}</h3>

        <div class="w-55px border-top border-secondary border-width-2 mb-30px mb-sm-35px mx-auto"></div>
        <div class="text-center mb-20px">
            <p>{{translate('Change your Card Name')}}</p>
        </div>

        <form id="edit-card-name-form" method="POST" action="{{route('card.edit_card_name')}}">
            @csrf
            <div class="input-group" id="edit_card_name_div">
                <div class="form-control-with-label small-focus animate flex-grow-1 @if(old('rfid_no')) focused @endif">
                    <label>{{ translate('Card Name')}}</label>
                    <input type="text" class="form-control {{ $errors->has('card_name_edit') ? ' is-invalid' : '' }}" value="{{ old('card_name_edit') }}" name="card_name_edit" id="card_name_edit"
                           autocomplete="off">



                    <input type="hidden" class="form-control" name="edit_card_name_id" id="edit_card_name_id" autocomplete="off">

                </div>
{{--                <div class="input-group-append line w-60px w-xxl-85px">--}}
{{--                    <div id="submit_button_div" class="flex-grow-1 d-none">--}}
{{--                        <button id="rfid_no_submit"--}}
{{--                                class="btn btn-primary btn-block fs-12 xxl-fs-14 px-2px fw-400"--}}
{{--                                type="button">{{ toUpper(translate('Submit'))}}</button>--}}
{{--                    </div>--}}

{{--                    <div class="loader flex-grow-1" id="loader-div" style="display: none">--}}
{{--                    </div>--}}

{{--                    <div id="correct_rfid" class="flex-grow-1" style="display: none">--}}

{{--                        <div--}}
{{--                            class="w-100 h-30px position-absolute custom-div d-flex flex-column justify-content-center">--}}
{{--                            <div class="text-center">--}}

{{--                                <svg class="h-30px" fill="green"--}}
{{--                                     xmlns="http://www.w3.org/2000/svg" height="25" width="25"--}}
{{--                                     viewBox="0 0 30 30">--}}
{{--                                    <use--}}
{{--                                        xlink:href="{{static_asset('assets/img/icons/tick.svg')}}#tick"></use>--}}
{{--                                </svg>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                    </div>--}}

{{--                    <div id="already_registered_rfid" class="flex-grow-1" style="display: none">--}}

{{--                        <div--}}
{{--                            class="w-100 h-30px position-absolute custom-div d-flex flex-column justify-content-center">--}}
{{--                            <div class="text-center">--}}

{{--                                <svg class=" h-25px"--}}
{{--                                     xmlns="http://www.w3.org/2000/svg" height="30" width="30"--}}
{{--                                     viewBox="0 0 21.18 21.27">--}}
{{--                                    <use--}}
{{--                                        xlink:href="{{static_asset('assets/img/icons/warning_icon.svg')}}#warning_svg"></use>--}}
{{--                                </svg>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                    </div>--}}

{{--                    <div id="incorrect_rfid" class="flex-grow-1" style="display: none">--}}

{{--                        <div class="w-100 h-30px position-absolute custom-div d-flex">--}}
{{--                            <div class="text-center m-auto" style="color: red;">--}}
{{--                                <svg class="z-1 h-17px" fill="red"--}}
{{--                                     xmlns="http://www.w3.org/2000/svg"--}}
{{--                                     viewBox="0 0 25.39 25.39">--}}
{{--                                    <use--}}
{{--                                        xlink:href="{{static_asset('assets/img/icons/x_icon_error.svg')}}#x-icon-error"></use>--}}
{{--                                </svg>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                    </div>--}}
{{--                </div>--}}
            </div>


            <span id="card-name-error-msg" class="text-error fs-14"></span>

            <button id="edit-card-name-submit" type="button" class="btn btn-outline-primary btn-block fw-700 mt-20px">{{toUpper(translate('Save Changes'))}}</button>
        </form>
    </div>
</div>


{{--@section('script')--}}
{{--    <script type="text/javascript">--}}

{{--        // $(document).ready(function() {--}}
{{--        --}}{{--    $('#edit-card-name-submit').on('click', function (e) {--}}
{{--        --}}{{--        // e.preventDefault();--}}
{{--        --}}{{--        let card_name = $('#card_name_edit').val();--}}

{{--        --}}{{--        if(card_name.length < 1) {--}}
{{--        --}}{{--            console.log('{{ translate('Please add card name') }}');--}}
{{--        --}}{{--            $('#card-name-error-msg').text("{{ translate('Please add card name') }}");--}}
{{--        --}}{{--        }--}}
{{--        --}}{{--        else {--}}
{{--        --}}{{--            $('#edit-card-name-form').submit();--}}
{{--        --}}{{--        }--}}

{{--        --}}{{--    });--}}
{{--        // });--}}

{{--        /*Validations*/--}}
{{--        // $('#edit-card-name-form').validate({--}}
{{--        //     errorClass: 'is-invalid',--}}
{{--        //     rules: {--}}
{{--        //         card_name_edit: {--}}
{{--        //             required: true,--}}
{{--        //         }--}}
{{--        //     },--}}
{{--        //     errorPlacement: function(error, element) {--}}
{{--        //         if (element.attr("name") === "address" ) {--}}
{{--        //             $("#add_address_message").html(error);--}}
{{--        //         }--}}
{{--        //         else if (element.attr("name") === "country" ) {--}}
{{--        //             $("#add_country_message").html(error);--}}
{{--        //         }--}}
{{--        //         else if (element.attr("name") === "city" ) {--}}
{{--        //             $("#add_city_message").html(error);--}}
{{--        //         }--}}
{{--        //         else if (element.attr("name") === "postal_code" ) {--}}
{{--        //             $("#add_postal_message").html(error);--}}
{{--        //         }--}}
{{--        //         else if (element.attr("name") === "phone" ) {--}}
{{--        //             $("#add_phone_message").html(error);--}}
{{--        //         }--}}
{{--        //     }--}}
{{--        // });--}}

{{--        </script>--}}
{{--@endsection--}}

