@extends('frontend.layouts.user_panel')

@section('meta_title')
    {{ translate('Dashboard') }}
@stop

@section('panel_content')
    <h1 class="fs-16 mb-15px text-primary-50 fw-700 lh-1">{{toUpper(translate('Dashboard'))}}</h1>
    <?php

    use App\Models\Organisation;
    use App\Models\CateringPlanPurchase;
    use Carbon\Carbon;
    use App\Models\Card;
    use App\Http\Controllers\CardController;
    use App\Http\Controllers\CateringPlanPurchaseController;
    use \App\Models\CanteenAppUser;

    $cards = json_decode($cards);
    ?>

    @foreach($cards as $card)

         <?php

            $cardController = new CardController;
            $purchaseController = new CateringPlanPurchaseController;

            $response = $purchaseController->find_valid_subscription($card->id, 2);
            $valid_subscriptions = $response['response'] ?? [];
            $subscription_status = $response['status'] ?? 'No Subscription';
            $status_code = $response['status_code'];
            $canteen_user = CanteenAppUser::where('card_id', $card->id)->first();
            $credit_cards = Auth::user()->credit_cards;

         ?>

        <div class="dashboard-res-item mb-35px fs-14">

            <div class="text-white-50 bg-primary-50 min-h-45px fs-16 dashboard-res-header">
                <div class="row gutters-10">

                    <div class="col-12 col-xxl-25per dashboard-res-header-col">
                        <div class="dashboard-res-header-cell ">
                            <div class="row gutters-0 align-items-center ">

                                    <div class="col">
                                            <span class="fs-12 d-block">{{ translate('RFID Card Name') }}:</span>
                                            @if($card->name!=null)
                                                <span class="text-white">{{ $card->name }}</span>
                                            @else
                                                <span class="text-white">-</span>
                                            @endif
                                    </div>

                                    <div class="col-auto px-15px px-xxl-0">
                                        <a href="javascript:void(0);" class="edit-card-name"
                                           data-cardID="{{$card->id}}" data-cardName="{{ $card->name }}">
                                            <img class="h-20px"
                                                 src="{{static_asset('assets/img/icons/pencil-icon.svg')}}" alt="">
                                        </a>
                                    </div>

                            </div>
                        </div>

                    </div>

                    <div class="col-12 col-xxl-25per dashboard-res-header-col">
                        <div class="dashboard-res-header-cell">
                            <span class="fs-12 d-block">{{ translate('Organisation') }}: </span>
                                <?php $organisation = Organisation::findorfail($card->organisation_id); ?>
                            <span class="text-white">{{ $organisation->name }}</span>
                        </div>
                    </div>
                    <div class="col-xxl-6 dashboard-res-header-col">
                        <div class="row no-gutters xxl-gutters-10 justify-content-end">
                            <div class="col-auto">
                                <div class="dashboard-res-header-cell border-none">
                                    <div class="row gutters-5 align-items-center">
                                        <div class="col">
                                            <span class="fs-12 d-block">{{ translate('RFID No.') }}</span>
                                            <span class="fs-16 fw-700 ">{{format_rfid_no($card->rfid_no) }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <a href="javascript:void(0);" class="edit-rfid-btn"
                                               data-cardID="{{$card->id}}">
                                                <img class="h-20px"
                                                     src="{{static_asset('assets/img/icons/pencil-icon.svg')}}" alt="">
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row gutters-10 mt-10px align-items-stretch">

                <div class="col-12 col-lg-6 align-self-stretch mb-2 mb-lg-0">

                    <div class="dashboard-res-body fs-14 lg-fs-12 xl-fs-14 fw-500 h-100 position-relative ">

                        <div class="row no-gutters align-items-center p-10px px-15px border-bottom border-primary border-width-2">
                            <div class="col lh-1 fw-700 ">
                                <span class="fw-800">{{toUpper(translate('Meal Plan'))}}</span>
                            </div>

                            <div class="col-auto text-primary py-5px px-10px">

                                @if($status_code == 1 || $status_code == 2)
{{--                                    active --}}      {{-- Upcoming Subscription --}}
                                    <span class="success-dot">
                                    </span>
                                    <span class="text-success"> {{toUpper(translate($subscription_status))}}</span>
                                @else
                                {{--  No Subscription --}}  {{--  Expired Subscription --}}

                                <div class="opacity-50">
                                      <span class="success-dot bg-black-40">
                                    </span>
                                    <span class="text-black-50"> {{toUpper(translate($subscription_status))}}</span>
                                </div>
                                @endif
                            </div>

                        </div>

                        <div class="pb-50px fs-13 lg-fs-10 xl-fs-13">

                            {{-- IF THERE IS A VALID SUBSCRIPTION --}}
                            @foreach($valid_subscriptions as $valid_subscription)
                                <div class="text-primary-70 fw-500">

                                    <div class="row no-gutters border-bottom border-primary border-width-2 align-items-stretch">
                                        <div class="col-5">
                                            <div class="dashboard-res-body-cell">
                                                <span class="text-primary fw-700">{{ toUpper(translate($valid_subscription['name']))  }}</span>
                                            </div>
                                        </div>


                                        <div class="col align-self-stretch opacity-80 border-left border-primary-400 border-width-1">
                                            <div class="row mx-0 h-100 align-items-stretch ">
                                                <div class="col-xs-12 col-sm col-lg-12 col-xl d-flex flex-column justify-content-center border-bottom border-primary-400 border-width-1">
                                                    <div class="">
                                                        <span class="fw-800">{{ translate('Snack') }}</span> :
                                                        @if($valid_subscription['snack_quantity']>=1)
                                                            {{ $valid_subscription['snack_quantity']}} {{ translate('per day') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </div>

                                                </div>
                                                <div class="col-xs-12 col-sm col-lg-12 col-xl d-flex flex-column justify-content-center border-bottom border-primary-400 border-width-1">
                                                    <div class="">
                                                        <span class="fw-800">{{ translate('Lunch') }}</span>:
                                                        @if($valid_subscription['meal_quantity']>=1)
                                                            {{ $valid_subscription['meal_quantity']}} {{ translate('per day') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </div>

                                                </div>
                                                <div class="col-12 px-auto d-flex flex-column justify-content-center">
                                                    <div class="">
                                                        <span class="fw-800">{{ translate('Subscription') }}</span>
                                                        : {{ Carbon::create($valid_subscription['from_date'])->format('d/m/Y') }}
                                                        - {{Carbon::create($valid_subscription['to_date'])->format('d/m/Y')}}
                                                    </div>
                                                </div>
                                            </div>


                                        </div>

                                    </div>

                                </div>

                            @endforeach

                            @php

                                $cardController = new CardController;

                                $working_details = $cardController->organisation_working_details($card->id);

                            @endphp

                            @if($working_details!=null)

                                @php

                                    if ($working_details != null) {
                                        $working_week_days = json_decode($working_details->working_week_days);

                                        $holidays = json_decode($working_details->holidays);

                                        $extra_days = json_decode($working_details->extra_days()->select('date')->get());


                                        $catering_plan_controller = new \App\Http\Controllers\CateringPlanController();

                                        $available_plans_exists = $catering_plan_controller->available_subscriptions_exist($card->id)['status'];

                                    }



                                @endphp

                                <div class="buttons-cl fs-13 lg-fs-10 xl-fs-13">
                                    <div class="row gutters-10 py-15px px-10px">

                                        <div class="col-xs-12 col-sm col-lg-12 col-xl mb-2 mb-xl-0 btn-dashboard-item">
                                            <button
                                                class="btn btn-dashboard show_calendar_modal " id="{{$card->id}}"
                                                @if($status_code==0) disabled @endif
                                            >
                                                <svg class=" h-lg-30px" fill="var(--primary)"
                                                     xmlns="http://www.w3.org/2000/svg" height="15" width="15"
                                                     viewBox="0 0 14.04 13.71">
                                                    <use
                                                        xlink:href="{{static_asset('assets/img/icons/upcoming_meals_icon.svg')}}#upcoming_meals"></use>
                                                </svg>
                                                <span class="pl-2">{{ toUpper(translate('Upcoming Meals')) }}</span></button>
                                        </div>
                                        <div class="col-xs-12 col-sm mb-2 mb-xl-0 btn-dashboard-item ">
                                            <a class="btn btn-dashboard @if($status_code==0) disabled @endif"
                                               @if($status_code!=0) href="{{route('dashboard.subscription_history' , ['card_id'=> encrypt($card->id)])}}" @endif>
                                                {{ toUpper(translate('Subscription History')) }}
                                            </a>
                                        </div>
                                        <div class="col-xs-12 col-sm col-lg-auto btn-dashboard-item">
                                            <a class="btn btn-dashboard px-5px @if($status_code==0) disabled @endif"
                                               @if($status_code!=0) href="{{route('dashboard.meals_history' , ['card_id'=> encrypt($card->id)])}}" @endif >
                                                {{ toUpper(translate('Meal History')) }}
                                            </a>
                                        </div>
                                    </div>



                                </div>

                                <div class="position-absolute bottom-0 w-100 fs-13 lg-fs-10 xl-fs-13">
                                    <a class="btn btn-primary btn-dashboard-primary btn-block @if(!$available_plans_exists) disabled @endif"
                                       @if($available_plans_exists ) href="{{route('new_subscriptions.get_subscriptions' , ['card_id'=> encrypt($card->id)])}}"> @endif
                                        <span class="circle lh-1">+</span>
                                        {{ toUpper(translate('Add New Subscription')) }}
                                    </a>
                                    <a href="{{route('tutorial')}}?question=4" class="dashboard-question-link" target="_blank" data-toggle="tooltip" data-title="{{ toUpper(translate('I need help')) }}">
                                        <svg class="h-15px h-sm-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 17.39 17.38">
                                            <use xlink:href="{{static_asset('assets/img/tutorials/question.svg')}}#content"></use>
                                        </svg>
                                    </a>
                                </div>
                            @else

                                <div class="">

                                    <div class="buttons-cl fs-13 lg-fs-10 xl-fs-13">
                                        <div class="row gutters-10 py-15px px-10px">

                                            <div class="col-xs-12 col-sm col-lg-12 col-xl mb-2 mb-xl-0 btn-dashboard-item">
                                                <button class="btn btn-dashboard show_calendar_modal " id="{{$card->id}}" disabled>
                                                    <svg class=" h-lg-30px" fill="var(--primary)"
                                                         xmlns="http://www.w3.org/2000/svg" height="15" width="15"
                                                         viewBox="0 0 14.04 13.71">
                                                        <use
                                                            xlink:href="{{static_asset('assets/img/icons/upcoming_meals_icon.svg')}}#upcoming_meals"></use>
                                                    </svg>
                                                    <span class="pl-2">{{ toUpper(translate('Upcoming Meals')) }}</span></button>
                                            </div>
                                            <div class="col-xs-12 col-sm mb-2 mb-xl-0 btn-dashboard-item ">
                                                <a class="btn btn-dashboard disabled ">
                                                    {{ toUpper(translate('Subscription History')) }}
                                                </a>
                                            </div>
                                            <div class="col-xs-12 col-sm col-lg-auto btn-dashboard-item">
                                                <a class="btn btn-dashboard px-5px disabled ">
                                                    {{ toUpper(translate('Meal History')) }}
                                                </a>
                                            </div>
                                        </div>


                                    </div>

                                    <div class="position-absolute bottom-0 w-100 fs-13 lg-fs-10 xl-fs-13">
                                        <a class="btn btn-primary btn-dashboard-primary btn-block  disabled ">
                                            <span class="circle ">+</span>
                                            {{ toUpper(translate('Add New Subscription')) }}
                                        </a>
                                        <a href="{{route('tutorial')}}?question=4" class="dashboard-question-link" target="_blank" data-toggle="tooltip" data-title="{{ toUpper(translate('I need help')) }}">
                                            <svg class="h-15px h-sm-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 17.39 17.38">
                                                <use xlink:href="{{static_asset('assets/img/tutorials/question.svg')}}#content"></use>
                                            </svg>
                                        </a>
                                    </div>

                                </div>

                            @endif

                        </div>




                    </div>

                </div>


                <div class="col-12 col-lg-6 pl-lg-0">

                    <div id="canteen_card" class="border-width-2 border-primary border text-primary lg-fs-12 xl-fs-14 h-100 align-self-stretch">

{{--                        if there is no user registered--}}
                        @if($canteen_user == null)

                            <div class="row no-gutters align-items-center p-10px">
                                <div class="col lh-1 fw-700 ">
                                    {{toUpper(translate('Canteen Wallet'))}}
                                </div>

                                <div class="col-auto lg-fs-10 xl-fs-14 bg-primary text-white border-radius-30px py-5px px-10px hov-opacity-80 c-pointer what-is-this">
                                    {{--                            if no user --}}
                                    {{translate('What is this?')}}
                                </div>

                            </div>

                            <div class="col-12 border-width-2 border-primary border-top px-0"></div>

                            <div class="p-10px fs-14 lg-fs-10 xl-fs-14">
                                <p class="fs-18 lg-fs-14 xl-fs-18 fw-600 pb-10px">{{translate("Empower your child's canteen choices!")}} </p>

                                <span class="d-block opacity-50 fw-400">{{translate("Our app lets you set spending limits and track their canteen orders.")}} </span>
                                <span class="d-block opacity-50 fw-400">{{translate("Link your card for easy, monitored meal selection at every break.")}} </span>

                                <span class="d-block fw-600 p-0 pt-15px opacity-50">{{translate("Start now by entering your Card Information and your child's login.")}} </span>
                                <span class="d-block  fw-600 pb-10px opacity-50">{{translate("We'll handle the rest.")}} </span>

                            </div>

                            <div class="p-10px">

                                <div class="btn btn-primary opacity-50 set-up-canteen-account" data-cardName="{{ $card->name }}" data-cardID="{{ $card->id }}">{{toUpper(translate("Set up your child's account"))}}</div>

                            </div>

{{--                  END - if there is no user registered--}}

                        @else

                            @include('frontend.partials.canteen_card_dashboard', ['canteen_user' => $canteen_user, 'credit_cards' => $credit_cards])


                        @endif

                    </div>


                </div>

            </div>



        </div>
    @endforeach

@endsection

@section('modal')


    @include('modals.add_credit_card')
    @include('modals.delete_credit_card')
    @include('modals.assign_credit_card')
    @include('modals.canteen_intro')
    @include('modals.setUpCanteenAccount')
    @include('modals.unassign_credit_card')
    @include('modals.changeCanteenUserPassword')
    @include('modals.changeCreditCardNickname')
    @include('modals.changeUsernameCanteenUser')
    @include('modals.changeDailyLimitCanteenUser')


    <div class="modal fade" id="calendar-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered  my-modal-lg-2" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="c-preloader">
                    <i class="fa fa-spin fa-spinner"></i>
                </div>
                <div id="calendar-modal-body">

                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="edit-rfid-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content position-relative">
                <div class="c-preloader">
                    <i class="fa fa-spin fa-spinner"></i>
                </div>
                <div id="edit-rfid-modal-body">
                    @include('modals.edit_rfid_modal')
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit-card-name-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content position-relative">
                <div class="c-preloader">
                    <i class="fa fa-spin fa-spinner"></i>
                </div>
                <div id="edit-rfid-modal-body">
                    @include('modals.edit_card_name_modal')
                </div>
            </div>
        </div>
    </div>
@endsection
@php
    if  (!isset($working_week_days)) {
        $working_week_days = [];
    }
@endphp
@section('script')
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    @if(App::getLocale() == "gr")
        <script
            src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/localization/messages_el.min.js"></script>
    @endif
    <script type="text/javascript">
        let calendar, range_start_date, range_end_date;
        let card_id, old_event_count = 0, start_info = null;
        let credit_cards = [];

        $(document).ready(function () {

            // $("#change-password-canteen-account").modal("show");

            @if(isset($errors) && $errors->has('type') && $errors->type == 'update_username')


            @endif
        });


        $(".show_calendar_modal").click(function (e) {
            e.preventDefault();

            card_id = this.id;

            $('.c-preloader').show();

            // for this card get the organisations working days

            var holidays = [];
            var weekdays = [];
            var extra_days = [];

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


            $('#calendar-modal-body').html('');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "get",
                url: '{{ route('card.card_upcoming_meals') }}',
                data: {
                    card_id: card_id
                },
                dataType: "JSON",
                success: function (data) {

                    $('.c-preloader').hide();

                    $('#calendar-modal-body').html(data.view);
                    $("#calendar-modal").modal("show");

                },
                error: function () {

                    $('#calendar-modal-body').html(null);
                    $('.c-preloader').hide();
                    $('#addToCart').modal('hide');

                }
            });


        });


        /* Edit RFID */
        $(document).on('click', '.edit-rfid-btn', function () {

            var cardID = $(this).attr('data-cardID');

            $("#old_card_id").val(cardID);

            $('#rfid_div').removeClass('border-bright-red');
            $('#already_registered_rfid').hide();
            $('#incorrect_rfid').hide();
            $('#correct_rfid').hide();
            $('#rfid-error-msg').text('');
            $('#rfid_no').val('');

            $("#edit-rfid-modal").modal("show");
        });

        /* Edit RFID */
        $(document).on('click', '.edit-card-name', function () {

            var cardID = $(this).attr('data-cardID');
            var cardName = $(this).attr('data-cardName');



            $("#edit_card_name_id").val(cardID);

            $('#edit_card_name_div').removeClass('border-bright-red');
            // $('#already_registered_rfid').hide();
            // $('#incorrect_rfid').hide();
            // $('#correct_rfid').hide();
            // $('#rfid-error-msg').text('');

            // console.log(cardName);
            $('#card_name_edit').val(cardName);
            $('#card_name_edit').parents('div').addClass('focused');



            $("#edit-card-name-modal").modal("show");
        });


        $('#rfid_no').on('keyup keypress change', function () {
            edit_rfid_input();
        });


        function edit_rfid_input() {

            $('#rfid_div').removeClass('border-bright-red');
            $('#already_registered_rfid').hide();
            $('#incorrect_rfid').hide();
            $('#correct_rfid').hide();
            $('#rfid-error-msg').text('');
            $('#submit_button_div').removeClass('d-none');


            var edit_rfid_input_value = $('#rfid_no').val();
            if (edit_rfid_input_value.length > 5) {
                $('#submit_button_div').removeClass('d-none');
            } else {
                $('#submit_button_div').addClass('d-none');
            }
        }

        $(document).on('click', '#rfid_no_submit', function () {

            $('#rfid_no').prop('readonly', true);
            $('#loader-div').show();

            $('#submit_button_div').addClass('d-none');
            // $('#submit_button_div').hide();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('rfid-can-be-edited')}}",
                type: 'post',
                data: {
                    rfid_no: $('#rfid_no').val(),
                    old_card_id: $("#old_card_id").val(),
                },
                success: function (response) {


                    $('#loader-div').hide();

                    var data = JSON.parse(response);

                    if (data.status == 1) {
                        // alert(data);
                        $('#edit-rfid-submit').attr('disabled', false);
                        $('#correct_rfid').show();
                        $('#edit-rfid-submit').prop('disabled', false);
                        // edit-rfid-submit
                    } else if (data.status == '2') {

                        // existing_rfid
                        $('#rfid_div').removeClass('border-bright-red');
                        $('#rfid-error-msg').text(data.message);

                        $('#already_registered_rfid').show();

                    } else {

                        // errorDisplay();
                        $('#rfid_div').addClass('border-bright-red');
                        $('#rfid-error-msg').text(data.message);
                        $('#incorrect_rfid').show();
                    }

                }
            });

            $('#rfid_no').prop('readonly', false);


        });


        $('#edit-card-name-submit').on('click', function (e) {
            // e.preventDefault();
            let card_name = $('#card_name_edit').val();

            if(card_name.length < 1) {
                $('#card-name-error-msg').text("{{ translate('Please add card name') }}");
            }
            else {
                $('#edit-card-name-form').submit();
            }

        });
        $('#card_name_edit').on('change keyup', function() {
            let card_name = $('#card_name_edit').val();
            if(card_name.length < 1) {
                $('#card-name-error-msg').text("{{ translate('Please add card name') }}");
            }
            else {
                $('#card-name-error-msg').text("");
            }
        });

        $(document).on('keyup keydown', '#assign-credit-card-modal input[name=nickname]', function () {

            if($(this).hasClass('is-invalid')){
                $(this).removeClass('is-invalid')
            }

            $('#assign-credit-card-modal span.nickname-error').html('');

        });


        $(document).on('click', '#assign-credit-card-modal .enter_card_info', function () {

            var nickname = $('#assign-credit-card-modal input[name=nickname]').val();
            var canteen_user_id = $('#assign-credit-card-modal input[name=canteen_user_id]').val();

            if(nickname==null || nickname=='' || canteen_user_id==null){

                $('#assign-credit-card-modal input[name=nickname]').addClass('is-invalid');
                $('#assign-credit-card-modal span.nickname-error').html('{{translate('This field is required')}}')
                return;
            }

            if ($('#assign-credit-card-modal input[name=agree_policies_add_card]').prop('checked') == false) {
                $('#error-agree-add-card-2').addClass('d-block').text('{{translate("You need to agree with our policies")}}');
            } else {
                $('#error-agree-add-card-2').text('');

                //send ajax to create card token

                $(this).parents('div.modal-dialog').addClass('loader');

                $.ajax({
                    method: "POST",
                    url: "{{ route('viva.save_card') }}",
                    dataType: "JSON",
                    data: {
                        _token: '{{ csrf_token() }}',
                        lng: '1',
                        nickname: nickname,
                        type: 'add',
                        canteen_user_id: canteen_user_id
                    },
                    success: function (data) {
                        console.log('data: ', data);

                        if(data.status == 1){
                            window.location.href = data.RedirectUrl;
                        }else if(data.status == 0){
                            // $("#add-credit-card-modal").modal("show");
                            SK.plugins.notify('warning', "{{ translate('Something went wrong!') }}");

                        }

                        $(this).parents('div.modal-dialog').removeClass('loader');
                        // window.location.href = data.RedirectUrl;
                        // removeLoader();
                    }
                });



            }
        });


        $(document).on('click', '#assign-credit-card-modal .save_selection', function () {

            if ($('#assign-credit-card-modal input[name=agree_policies_save_selection]').prop('checked') == false) {
                $('#error-agree-save-selection').addClass('d-block').text('{{translate("You need to agree with our policies")}}');

            } else {
                $('#error-agree-save-selection').text('');

                //send ajax to create card token

            }
        });

        $(document).on('click', '.what-is-this', function () {
            $("#canteen-intro-modal").modal("show");
        });

        $(document).on('click', '.set-up-canteen-account', function () {

            $("#set-up-canteen-account .set-account-name").html($(this).attr('data-cardName'));
            $("form[name=set_canteen_account_form] input[name=card_id]").val($(this).attr('data-cardID'));

            // console.log('data-cardID: ',$(this).attr('data-cardID') );


            $("#set-up-canteen-account").modal("show");
        });

        $(document).on('click', '#set-up-canteen-account .set-account-save', function () {

            if ($('#set-up-canteen-account input[name=agree_policies_set_account]').prop('checked') == false) {
                $('#error-agree-set-account').addClass('d-block').text('{{translate("You need to agree with our policies")}}');

                //send ajax to create canteen account

            } else {
                $('#error-agree-set-account').text('');
            }
        });

        $(document).on('show.bs.modal', '#set-up-canteen-account', function () {
                $(this).find('input').each(function (){
                    if($(this).attr('name') != '_token' && $(this).attr('name') != 'card_id'){
                        $(this).val('');
                    }
                });
        });



        /*Validations*/
        $('form[name=set_canteen_account_form]').validate({
            errorClass: 'is-invalid',
            rules: {
                username: {
                    required: true,
                    minlength: 6
                },
                password: {
                    required: true,
                    minlength: 6
                },
                password_confirmation: {
                    required: true,
                    equalTo: "form[name=set_canteen_account_form] input[name=password]",
                },
                daily_limit: {
                    required: true,
                    number: true,
                    range: [0, 10000000]
                },
                agree_policies_set_account: {
                    required: true,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") === "username") {
                    $("form[name=set_canteen_account_form] .username_error").html(error);
                } else if (element.attr("name") === "password") {
                    $("form[name=set_canteen_account_form] .password_error").html(error);
                } else if (element.attr("name") === "password_confirmation") {
                    $("form[name=set_canteen_account_form] .confirm_password_error").html(error);
                } else if (element.attr("name") === "daily_limit") {
                    $("form[name=set_canteen_account_form] .daily_limit_error").html(error);
                } else if (element.attr("name") === "agree_policies_set_account") {
                    $("#error-agree-set-account").html('{{translate("You need to agree with our policies")}}');
                }

                // console.log('error: ', element, error);
            }
        });

        $(document).on('change keyup keydown', 'input', function () {
            $(this).parent('div').removeClass('is-invalid');
            ($(this).parent('div').nextAll('.invalid-feedback:first')).html('');
        });

        $(document).on('change keyup keydown', 'select', function () {
            $(this).removeClass('is-invalid');
            ($(this).parent('div').nextAll('.invalid-feedback:first')).html('');
        });

        $(document).on('submit', 'form[name=set_canteen_account_form]', function (e) {

            e.preventDefault();

            // modal-content loader

            $(this).parents('.modal-content').first().addClass('loader');

            //send ajax to create canteen user

            // $(this).find('button').addClass('loader');
            $.ajax({
                type:"POST",
                url: '{{ route('canteen_app_user.store_ajax') }}',
                data: $('form[name=set_canteen_account_form]').serializeArray(),
                success: function(data){


                    // console.log('data ajax: ', data);

                    if(data.status == 'validator_error'){
                        var error_keys = Object.keys(data.errors);

                        error_keys.forEach((element, index) => {
                            $('form[name=set_canteen_account_form] .' + element + '_error').prev().find('input').addClass('is-invalid');
                            $('form[name=set_canteen_account_form] .' + element + '_error').html(data.errors[element]);
                        });

                    }else if(data.status == '1'){

                        SK.plugins.notify('success', data.msg);

                        // if(credit_cards.length > 0 ){
                            //assign card
                            $("#assign-credit-card-modal").modal('show');
                        // }

                        $('#canteen_card').html(data.view);

                        $("#set-up-canteen-account").modal('hide');

                    }else if(data.status == '0'){

                        SK.plugins.notify('danger', data.msg);
                        $("#set-up-canteen-account").modal('hide');
                        location.reload();

                    }

                    $(this).parents('.modal-content').first().removeClass('loader');

                    // $(this).find('button').removeClass('loader');



                }
            });


        });


        $('form[name=change_password_canteen_account]').validate({
            errorClass: 'is-invalid',
            rules: {
                password: {
                    required: true,
                    minlength: 6
                },
                password_confirmation: {
                    required: true,
                    equalTo: "form[name=change_password_canteen_account] input[name=password]",
                }
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") === "password") {
                    $("form[name=change_password_canteen_account] .password_error").html(error);
                } else if (element.attr("name") === "password_confirmation") {
                    $("form[name=change_password_canteen_account] .confirm_password_error").html(error);
                }

            }
        });

        $(document).on('click', '.canteen-user-pass-change', function () {

            // console.log('olaa');

            $("#change-password-canteen-account").modal("show");

            var canteen_user_id = $(this).attr('data-canteenUser');

            $("#change-password-canteen-account input[name=canteen_user_id]").val(canteen_user_id);

        });

        $(document).on('submit', 'form[name=change_password_canteen_account]', function (e) {

            e.preventDefault();

            $.ajax({
                type:"POST",
                url: '{{ route('canteen_app_user.change_password') }}',
                data: $('form[name=change_password_canteen_account]').serializeArray(),
                success: function(data){
                    console.log('data: ', data);

                    if (data.status == 1) {
                        SK.plugins.notify('success', data.msg);
                    } else {
                        SK.plugins.notify('danger', data.msg);
                    }

                    $("#change-password-canteen-account").modal("hide");
                }


            });

        });


        $(document).on('click', '.assign-credit-card, .add-credit-card', function () {

            $("#assign-credit-card-modal").modal("show");

            // console.log('eeee');

            var canteen_user_id = $(this).attr('data-canteenUser');

            $('#assign-credit-card-modal input[name=canteen_user_id]').val(canteen_user_id);

        });

        $(document).on('click', '.add-credit-card-modal', function () {

            // console.log('olaa');

            $("#change-password-canteen-account").modal("show");

            var canteen_user_id = $(this).attr('data-canteenUser');

            $("#change-password-canteen-account input[name=canteen_user_id]").val(canteen_user_id);

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

        $(document).on('click', '.update_username', function () {

            // console.log('ateee')
            $("#change-canteen-username").modal("show");

            var canteenUserID = $(this).attr('data-canteenUser');
            var username = $(this).attr('data-username');

            $("#change-canteen-username input[name=username]").val(username);
            $("#change-canteen-username input[name=canteen_user_id]").val(canteenUserID);

        });

        $('#change-canteen-username form').validate({
            errorClass: 'is-invalid',
            rules: {
                username: {
                    required: true,
                    minlength: 6
                }
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") === "username") {
                    $("#change-canteen-username .username_error").html(error);
                }
            }
        });

        $(document).on('submit', '#change-canteen-username form', function (e) {

            e.preventDefault();

            // console.log('ela mou')

            $.ajax({
                type:"POST",
                url: '{{ route('canteen_app_user.update_username') }}',
                data: $('#change-canteen-username form').serializeArray(),
                success: function(data){
                    console.log('data: ', data);

                    if(data.status == 'validator_error'){
                        var error_keys = Object.keys(data.errors);

                        error_keys.forEach((element, index) => {
                            $('#change-canteen-username form .' + element + '_error').prev().find('input').addClass('is-invalid');
                            $('#change-canteen-username form .' + element + '_error').html(data.errors[element]);
                        });

                    } else if (data.status == 1) {
                        SK.plugins.notify('success', data.msg);
                        location.reload();
                    } else {
                        SK.plugins.notify('danger', data.msg);
                    }

                    $("#change-password-canteen-account").modal("hide");
                }


            });

        });

        $(document).on('click', '.update_daily_limit', function () {

            // console.log('ateee')
            $("#change-canteen-daily-limit").modal("show");

            var canteenUserID = $(this).attr('data-canteenUser');
            var daily_limit = $(this).attr('data-dailyLimit');

            $("#change-canteen-daily-limit input[name=daily_limit]").val(daily_limit);
            $("#change-canteen-daily-limit input[name=canteen_user_id]").val(canteenUserID);

        });

        $('#change-canteen-daily-limit form').validate({
            errorClass: 'is-invalid',
            rules: {
                daily_limit: {
                    required: true,
                    number: true,
                    range: [0, 10000000]
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") === "daily_limit") {
                    $("#change-canteen-daily-limit .daily_limit_error").html(error);
                }
            }
        });

        let continue_on_submit = false;

        $('form[name=assign_existing_card]').validate({
            errorClass: 'is-invalid',
            rules: {
                agree_policies_save_selection: {
                    required: true,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") === "agree_policies_save_selection") {
                    $("#error-agree-save-selection").html(error);
                }
            }
        });

        // $(document).on('change', 'select[name=selected_credit_card]', function (e) {
        //
        //     $('form[name=assign_existing_card] input[name=canteen_user_id]').
        //
        // });


        $(document).on('submit', 'form[name=assign_existing_card]', function (e) {

            var credit_card_id = $('select[name=selected_credit_card]').val();

            if(credit_card_id==null || credit_card_id==''){
                e.preventDefault();
                $('select[name=selected_credit_card]').addClass('is-invalid');
                $('form[name=assign_existing_card] .selected_credit_card_error').html('{{translate('Please select a credit card')}}');

            }

        });











    </script>
@endsection
