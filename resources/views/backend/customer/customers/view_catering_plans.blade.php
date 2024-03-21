@extends('backend.layouts.app')

@section('content')

    <div class="sk-titlebar text-left mt-2 mb-3">
        <div class="align-items-center">
            <h1 class="h3"><a href="{{route('customers.index')}}"
                              class="text-black hov-text-hov-primary">{{translate('All Customers')}} </a>
                > {{$user->name}}</h1>
        </div>
    </div>

    <div class="card">
        <div class="p-2">
            <span class="fs-16 p-2 fw-600">{{translate('All Plan Purchases')}} </span>
        </div>
    </div>


    @foreach($cards as $key => $card)
        <div class="card">
            <div class="card-header d-block d-lg-flex">
                <h5 class="mb-0 h6"> {{ $card->organisation_name }} - {{$card->name}} {{$card->rfid_no}}</h5>

                    <div class="text-right">
                        <button type="button" id="" data-cardRFID="{{$card->rfid_no}}"
                                data-cardID="{{$card->id}}" data-cardName="{{$card->name}}"
                                class="btn btn-primary change_card_details">{{ translate('Change Card Details') }}</button>
                        <button type="button" data-cardID="{{$card->id}}"
                                data-url="{{ route('card.remove_card_from_user', encrypt($card->id))}}"
                                class="btn  btn-soft-danger delete-card">{{ translate('Delete Card From Customer') }}</button>
                    </div>
            </div>

            <div class="card-body">
                <table class="table sk-table mb-0">
                    <thead>
                    <tr>
                        <th data-breakpoints="lg">#</th>
                        <th>{{ ucwords(translate('Plan Name'))}}</th>
                        <th data-breakpoints="lg">{{translate('Period')}}</th>
                        <th data-breakpoints="lg">{{translate('Lunches per day')}}</th>
                        <th data-breakpoints="lg">{{translate('Snacks per day')}}</th>
                        <th data-breakpoints="lg">{{translate('Price')}}</th>
                        <th data-breakpoints="lg">{{translate('Num of Days')}}</th>
                        <th data-breakpoints="lg">{{translate('Purchase Date')}}</th>
                        {{--                    <th data-breakpoints="lg">{{translate('Phone')}}</th>--}}

                        <th class="text-right">{{translate('Options')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($catering_plan_purchases as $key => $plan)

                        @if ($plan->card_id == $card->id)
                            {{--                        @php--}}
                            {{--                            $partner_user = \App\PartnershipUser::where('email', $customer->user->email)->first();--}}
                            {{--                        @endphp--}}
                            <tr>
                                <td>{{ ($key+1) }}</td>
                                <td>{{$plan->name}}</td>
                                <td>{{ \Carbon\Carbon::create($plan->from_date)->format('d/m/Y') }}
                                    - {{ \Carbon\Carbon::create($plan->to_date)->format('d/m/Y') }}</td>
                                <td>{{$plan->meal_quantity}}</td>
                                <td>{{$plan->snack_quantity}}</td>
                                <td>{{format_price($plan->price)}}</td>
                                <td>{{$plan->num_of_days}}</td>
                                <td>{{ \Carbon\Carbon::create($plan->created_at)->format('d/m/Y') }}</td>
                                <td class="text-right">

                                    @if(\Carbon\Carbon::create($plan->to_date)->gte(\Carbon\Carbon::today()))
                                        <a href="#"
                                           class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                           data-href="{{route('catering_plan_purchases.destroy', encrypt($plan->purchase_id))}}"
                                           title="{{ translate('Cancel Plan') }}">
                                            <i class="las la-trash"></i>
                                        </a>

                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
                <div class="sk-pagination">
                    {{--                {{ $customers->links() }}--}}
                </div>

            </div>
        </div>
    @endforeach


    <div class="modal fade" id="confirm-ban">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{translate('Do you really want to ban this Customer?')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <a type="button" id="confirmation" class="btn btn-primary">{{translate('Proceed!')}}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirm-unban">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{translate('Do you really want to unban this Customer?')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <a type="button" id="confirmationunban" class="btn btn-primary">{{translate('Proceed!')}}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')

    @include('modals.change_card_detail')

    <div class="modal fade" id="active_subscriptions_modal">
        <div class="modal-dialog modal-dialog-centered modal-lg " role="document">
            <div class="modal-content" id="modal-content">

                <div class="modal-header d-block align-items-center">

                    <button type="button" class="close p-0 pt-15px" data-dismiss="modal" aria-hidden="true"></button>
                    <h5 class="text-center mt-3 mb-30px mb-sm-35px">{{toUpper(translate('Card Cannot Be Deleted'))}}</h5>
                </div>
                <div class="modal-body pt-5px">
                    <div id="modal-body">

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">

        function sort_customers(el) {
            $('#sort_customers').submit();
        }

        function confirm_ban(url) {
            $('#confirm-ban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmation').setAttribute('href', url);
        }

        function confirm_unban(url) {
            $('#confirm-unban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmationunban').setAttribute('href', url);
        }

        function update_pay_on_credit(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('customers.pay_on_credit') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function (data) {
                if (data == 1) {
                    SK.plugins.notify('success', '{{ translate('Users Payment Methods updated successfully') }}');
                } else {
                    SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_pay_on_delivery(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('customers.pay_on_delivery') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function (data) {
                if (data == 1) {
                    SK.plugins.notify('success', '{{ translate('Users Payment Methods updated successfully') }}');
                } else {
                    SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_accept(el) {
            var customer_id = $(el).attr('data-customer');
            console.log('customer_id: ' + customer_id);
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('partnership-user.change-accept') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function (data) {
                if (data == 1) {
                    SK.plugins.notify('success', '{{ translate('Partnership Users updated successfully') }}');
                    setTimeout(function () {
                        $('input[name="excluded_vat_' + el.value + '"]').prop('checked', false).parents('label').remove();

                        if (status === 1) {
                            $('#customer_code_' + customer_id).removeClass('d-none');
                        } else {
                            $('#customer_code_' + customer_id).addClass('d-none');
                        }
                    }, 500);
                } else {
                    SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_excluded_vat(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('customers.excluded_vat') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function (data) {
                if (data == 1) {
                    SK.plugins.notify('success', '{{ translate('Updated successfully') }}');
                } else {
                    SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        $(".delete-card").click(function () {

            var cardID = $(this).attr('data-cardID');
            var url = $(this).attr('data-url');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('card.get_on_going_subscriptions')}}",
                type: 'POST',
                dataType: "JSON",
                data: {
                    card_id: cardID,
                },
                success: function (response) {

                    console.log(response)

                    if (response.status == 1) {

                        console.log('url: ', url)
                        $("#delete-modal").modal("show");
                        $("#delete-link").attr("href", url);

                    } else if (response.status == 0) {

                        $("#modal-body").html(response.view);

                        $("#active_subscriptions_modal").modal("show");
                        // ("#modal-body").modal("show");
                        // modal-body

                    }

                }
            });

        });


        $(".change_card_details").click(function () {

            var cardID = $(this).attr('data-cardID');
            var cardName = $(this).attr('data-cardName');

            $("#edit_card_name_id").val(cardID);
            $("#old_card_id").val(cardID);
            $("#old_card").val($(this).attr('data-cardRFID'));
            $("#card_name").val(cardName);

            $('#rfid_div').removeClass('border-bright-red');
            $('#already_registered_rfid').hide();
            $('#incorrect_rfid').hide();
            $('#correct_rfid').hide();
            $('#rfid-error-msg').text('');
            $('#rfid_no').val('');

            $("#change_card_details_modal").modal("show");
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

            // alert($('#rfid_no').val());
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

                    // console.log('rfid-can-be-edited: ',response);

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


    </script>
@endsection
