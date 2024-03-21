@extends('frontend.layouts.user_panel')

@section('meta_title')
    {{ translate('Credit Cards') }}
@stop

@section('panel_content')

    @php

        if(!old('added_credit_card')){
            $added_credit_card = false;
        }else{
             $added_credit_card = true;
        }

    @endphp

    <p class="text-primary fs-16">
        {{translate("By linking your card to your child's RFID tag, you grant them the ability to make purchases at the school canteen.
                    You'll have the option to set a daily spending limit, giving you control")}}.
    </p>

    <p class="text-primary fs-16">
        {{translate("Rest assured, your card information will remain secure. You can easily manage and modify your card details, including deletion or editing, at any time")}}.
    </p>

    @if(!$added_credit_card)
    <div class="pt-15px border-primary border-width-1 border-bottom"></div>
    @endif

    @if($added_credit_card)
    <div class="row px-15px pt-15px align-items-center ">
        <div class="col pt-15px border-primary border-width-1 border-bottom">

        </div>

        <div class="col-auto border-radius-30px shadow-md p-15px fs-13">
            <div class="row no-gutters align-items-end">
                <svg class="h-15px mx-2"
                     xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 20 20">
                    <use
                        xlink:href="{{static_asset('assets/img/icons/check-mark.svg')}}#content"></use>
                </svg>
            <span class="text-primary px-10px">

                {{translate("You're all set! Your card details have been successfully added.")}}</span>
            </div>
        </div>

        <div class="col pt-15px border-primary border-width-1 border-bottom d-sm-block d-none">

        </div>
    </div>

    @endif

    <div class="row px-15px pt-15px align-items-center d-none">
        <div class="col pt-15px border-primary border-width-1 border-bottom"></div>

        <div class="col-auto border-radius-30px shadow-md p-15px fs-13">
            <span class="text-primary">
                 <svg class="h-20px px-5px "
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 20 20">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/x-mark.svg')}}#content"></use>
                            </svg>
                {{translate("Oops! Something went wrong. Please try again.")}}</span>
        </div>

        <div class="col pt-15px border-primary border-width-1 border-bottom"></div>
    </div>

    @php

        $credit_cards = Auth::user()->credit_cards;

    @endphp

    <div class="mt-20px row lg-gutters-20 xl-gutters-50 align-items-stretch">
        {{--        show existing credit cards --}}
        @foreach($credit_cards as $key => $credit_card)
            <div class="col-12 col-sm-6 py-15px py-sm-30px">
                <div
                    class="h-100 bg-primary px-40px opacity-30 border-radius-30px text-white pt-30px fs-15 letter-spacing-1px">
                    <div class="py-10px">
                        <span class="d-block fw-700 pb-5px"> {{translate('Credit Card Info')}}:</span>
                        <span class="">**** **** **** {{ substr($credit_card->credit_card_number, -4)}}</span>
                    </div>
                    <div class="py-10px">
                        <span class="d-block fw-700 pb-5px"> {{translate('Card Nickname')}}:</span>
                        <span class="">{{$credit_card->nickname}}</span>
                        <a class="c-pointer update-nickname" data-creditCardID="{{$credit_card->id}}"
                           data-nickname="{{$credit_card->nickname}}">
                            <svg class="h-20px px-5px"
                                 xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 19.95 19.95">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/pencil-icon-2.svg')}}#content"></use>
                            </svg>
                        </a>
                    </div>


                    <div class="py-10px">
                        @if($credit_card->expiration_date != null)
                        <span class="d-block fw-700 pb-5px"> {{translate('Expiration Date')}}:</span>
                        <span class="">{{\Carbon\Carbon::create($credit_card->expiration_date)->format('m/y')}}</span>
                        <a class="c-pointer px-5px edit-credit-card" data-creditCardID="{{$credit_card->id}}">
                            <svg class="h-20px"
                                 xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 20 20">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/pencil-icon-2.svg')}}#content"></use>
                            </svg>
                        </a>
                        @endif
                    </div>


                    <div class="text-right fs-12 py-15px fw-200 text-underline">
                        <a class="c-pointer delete-credit-card" data-cardID="{{$credit_card->id}}">
                        <span>
                            <svg class="h-10px mb-3px" fill="#fff"
                                 xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 10 10">
                            <use
                                xlink:href="{{static_asset('assets/img/icons/trash-icon.svg')}}#content"></use>
                        </svg>
                            {{translate('Delete Credit Card')}}</span>
                        </a>
                    </div>
                </div>
            </div>

        @endforeach

        {{--        add new credit card --}}
        <div class="col-12 col-sm-6 py-15px py-sm-30px h-100 ">
            <div class="bg-card-grey border-radius-30px py-60px text-center text-primary">
                <a class="btn btn-circle add-credit-card fs-60 fw-200 hov-bg-white">
                    +
                </a>

                <h1 class="fs-16 mt-25px text-primary-60 fw-500 lh-1">{{toUpper(translate('Credit Card'))}}</h1>
            </div>
        </div>
    </div>

@endsection


@section('modal')
    @include('modals.add_credit_card')
    {{--    @include('modals.assign_credit_card')--}}
    @include('modals.delete_credit_card')
    @include('modals.changeCreditCardNickname')
    {{--    @include('modals.unassign_credit_card')--}}
@endsection


@section('script')

    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

    <script type="text/javascript">

        $(document).ready(function () {

            // $("#unassign-credit-card-modal").modal("show");

            {{--$.ajax({--}}
            {{--    method:"POST",--}}
            {{--    url: "{{ route('viva.test') }}",--}}
            {{--    // dataType: "JSON",--}}
            {{--    data: {--}}
            {{--        _token: '{{ csrf_token() }}',--}}
            {{--        lng: '1'--}}
            {{--    },--}}
            {{--    success: function(data) {--}}

            {{--        console.log('viva data: ', data);--}}
            {{--        // window.location.href = data.RedirectUrl;--}}
            {{--        // removeLoader();--}}
            {{--    }--}}
            {{--});--}}

        });

        $(document).on('click', '.add-credit-card', function () {
            $("#add-credit-card-modal").modal("show");
        });

        $(document).on('keyup keydown', '#add-credit-card-modal input[name=nickname]', function () {

            if($(this).hasClass('is-invalid')){
                $(this).removeClass('is-invalid')
            }

            $('#add-credit-card-modal span.nickname-error').html('');

        });

        $(document).on('click', '#add-credit-card-modal .enter_card_info', function () {

            var nickname = $('#add-credit-card-modal input[name=nickname]').val();

            if(nickname==null || nickname==''){
                $('#add-credit-card-modal input[name=nickname]').addClass('is-invalid');
                $('#add-credit-card-modal span.nickname-error').html('{{translate('This field is required')}}')
                return;
            }

            if ($('#add-credit-card-modal input[name=agree_policies_add_card]').prop('checked') == false) {
                $('#error-agree-add-card-1').addClass('d-block').text('{{translate("You need to agree with our policies")}}');

            } else {
                $('#error-agree-add-card-1').text('');

                //send ajax to create card token

                $(this).parents('div.modal-dialog').addClass('loader');

                // console.log('go in');

                $.ajax({
                    method: "POST",
                    url: "{{ route('viva.save_card') }}",
                    dataType: "JSON",
                    data: {
                        _token: '{{ csrf_token() }}',
                        lng: '1',
                        nickname: nickname,
                        type: 'add',
                        card_id: '',
                    },
                    success: function (data) {
                        // console.log('data: ', data);

                        if(data.status == 1){
                            window.location.href = data.RedirectUrl;
                        }else if(data.status == 0){
                            $("#add-credit-card-modal").modal("show");
                            SK.plugins.notify('warning', "{{ translate('Something went wrong!') }}");

                        }

                        $(this).parents('div.modal-dialog').removeClass('loader');
                        // window.location.href = data.RedirectUrl;
                        // removeLoader();
                    }
                });


            }
        });


        $(document).on('click', '.update-nickname', function () {

            $("#change-credit-card-nickname").modal("show");

            var creditCardID = $(this).attr('data-creditCardID');
            // var creditCardID = 1;
            var nickname = $(this).attr('data-nickname');

            $("#change-credit-card-nickname input[name=nickname]").val(nickname);
            $("#change-credit-card-nickname input[name=credit_card_id]").val(creditCardID);

        });

        $('#change-credit-card-nickname form').validate({
            errorClass: 'is-invalid',
            rules: {
                nickname: {
                    required: true
                }
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") === "nickname") {
                    $("#change-credit-card-nickname .nickname_error").html(error);
                }
            }
        });


    </script>
@endsection
