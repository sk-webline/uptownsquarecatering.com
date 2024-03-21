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

            ?>

        <div class="dashboard-res-item mb-35px fs-14">

            <div class="text-white-50 bg-primary-50 min-h-45px fs-16 dashboard-res-header">
                <div class="row gutters-10">
                    <div class="col-12 col-xxl-25per dashboard-res-header-col">
                        <div class="dashboard-res-header-cell">
                            <span class="text-white fs-17">{{ toUpper(translate($subscription_status)) }}</span>
                        </div>
                    </div>

                    <div class="col-12 col-xxl-25per dashboard-res-header-col">

                        <div class="row gutters-5 align-items-center">
                            <div class="col">
                                <div class="dashboard-res-header-cell">
                                    <span class="fs-12 d-block">{{ translate('Card Name') }}:</span>
                                    @if($card->name!=null)
                                        <span class="text-white">{{ $card->name }}</span>
                                    @else
                                        <span class="text-white">-</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                                    <a href="javascript:void(0);" class="edit-card-name"
                                       data-cardID="{{$card->id}}" data-cardName="{{ $card->name }}">
                                        <img class="h-20px"
                                             src="{{static_asset('assets/img/icons/pencil-icon.svg')}}" alt="">
                                    </a>
                            </div>
                        </div>
{{--                        if ($request->ip() == '82.102.76.201') {--}}

                    </div>
                    <div class="col-xxl-6 dashboard-res-header-col">
                        <div class="row no-gutters xxl-gutters-10 justify-content-between">
                            <div class="col-md-6 col-xxl-auto">
                                <div class="dashboard-res-header-cell">
                                    <span class="fs-12 d-block">{{ translate('Organisation') }}: </span>
                                        <?php $organisation = Organisation::findorfail($card->organisation_id); ?>
                                    <span class="text-white">{{ $organisation->name }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-xxl-auto">
                                <div class="dashboard-res-header-cell">
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


            {{-- IF THERE IS A VALID SUBSCRIPTION --}}
            @foreach($valid_subscriptions as $valid_subscription)
                <div class="dashboard-res-body text-primary-70 fs-14 fw-500">
                    <div class="row gutters-10">
                        <div class="col-12 col-xxl-35per dashboard-res-body-col">
                            <div class="dashboard-res-body-cell">
                                <span class="text-primary fs-15 fw-700">{{ toUpper(translate($valid_subscription['name']))  }}</span>
                            </div>
                        </div>
                        @if($valid_subscription['snack_quantity']>=1)
                            <div class="col-6 col-xxl-15per dashboard-res-body-col">
                                <div class="dashboard-res-body-cell">
                                    {{ translate('Snack') }}
                                    : {{ $valid_subscription['snack_quantity']}} {{ translate('per day') }}
                                </div>
                            </div>
                        @endif
                        @if($valid_subscription['meal_quantity']>=1)
                            <div class="col-6 col-xxl-15per dashboard-res-body-col">
                                <div class="dashboard-res-body-cell">
                                    {{ translate('Lunch') }}
                                    : {{ $valid_subscription['meal_quantity']}} {{ translate('per day') }}
                                </div>
                            </div>
                        @endif
                        <div class="col-12 col-xxl-35per ml-auto dashboard-res-body-col">
                            <div class="dashboard-res-body-cell">
                                {{ translate('Subscription') }}
                                : {{ Carbon::create($valid_subscription['from_date'])->format('d/m/Y') }}
                                - {{Carbon::create($valid_subscription['to_date'])->format('d/m/Y')}}
                            </div>
                        </div>
                    </div>
                </div>

            @endforeach
                <?php

                $cardController = new CardController;

                $working_details = $cardController->organisation_working_details($card->id);

                ?>

            @if($working_details!=null)

                    <?php

                    if ($working_details != null) {
                        $working_week_days = json_decode($working_details->working_week_days);

                        $holidays = json_decode($working_details->holidays);

                        $extra_days = json_decode($working_details->extra_days()->select('date')->get());


                        $catering_plan_controller = new \App\Http\Controllers\CateringPlanController();

                        $available_plans_exists = $catering_plan_controller->available_subscriptions_exist($card->id)['status'];

//                        echo $available_plans;

//                        $custom_plan_availability = $organisation->custom_packets;

                    }

                    ?>
                <div class="row no-gutters">

                    <div class="col-12 col-md-4 col-xxl-2 btn-dashboard-item">
                        <button
                            class="btn btn-dashboard show_calendar_modal" data-id="{{$card->id}}"
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
                    <div class="col-12 col-md-4 col-xxl-2 btn-dashboard-item">
                        <a class="btn btn-dashboard @if($status_code==0) disabled @endif"
                           @if($status_code!=0) href="{{route('dashboard.subscription_history' , ['card_id'=> encrypt($card->id)])}}" @endif>
                            {{ toUpper(translate('Subscription History')) }}
                        </a>
                    </div>
                    <div class="col-12 col-md-4 col-xxl-2 btn-dashboard-item">
                        <a class="btn btn-dashboard @if($status_code==0) disabled @endif"
                           @if($status_code!=0) href="{{route('dashboard.meals_history' , ['card_id'=> encrypt($card->id)])}}" @endif >
                            {{ toUpper(translate('Meal History')) }}
                        </a>
                    </div>
                    <div class="col-12 col-xxl-6">
                        <a class="btn btn-primary btn-dashboard-primary btn-block fs-14 py-5px lh-1-7 @if(!$available_plans_exists) disabled @endif"
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
                </div>

            @else

                <div class="row no-gutters">

                    <div class="col-12 col-md-4 col-xxl-2 btn-dashboard-item">
                        <button
                            class="btn btn-dashboard show_calendar_modal" disabled
                        >
                            <svg class=" h-lg-30px" fill="var(--primary)"
                                 xmlns="http://www.w3.org/2000/svg" height="15" width="15"
                                 viewBox="0 0 14.04 13.71">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/upcoming_meals_icon.svg')}}#upcoming_meals"></use>
                            </svg>
                            <span class="pl-2">{{ toUpper(translate('Upcoming Meals')) }}</span></button>

                    </div>
                    <div class="col-12 col-md-4 col-xxl-2 btn-dashboard-item">
                        <a class="btn btn-dashboard disabled">
                            {{ toUpper(translate('Subscription History')) }}
                        </a>
                    </div>
                    <div class="col-12 col-md-4 col-xxl-2 btn-dashboard-item">
                        <a class="btn btn-dashboard disabled ">
                            {{ toUpper(translate('Meal History')) }}
                        </a>
                    </div>
                    <div class="col-12 col-xxl-6">
                        <a class="btn btn-primary btn-dashboard-primary btn-block fs-14 py-5px lh-1-7  disabled ">
                            <span class="circle">
                            +
                        </span>
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
            <div class="dashboard-res-border border-top border-width-2 border-primary-200 mt-35px"></div>
        </div>
    @endforeach

@endsection

@section('modal')

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
    <script type="text/javascript">
        let calendar, range_start_date, range_end_date;
        let card_id, old_event_count = 0, start_info = null;


        $(".show_calendar_modal").click(function (e) {
            e.preventDefault();

            card_id = $(this).attr('data-id');

            console.log('card id: ', card_id);

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

                    console.log('data: ', data);
                    $('.c-preloader').hide();

                    $('#calendar-modal-body').html(data.view);

                    calendar.render();
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


    </script>
@endsection
