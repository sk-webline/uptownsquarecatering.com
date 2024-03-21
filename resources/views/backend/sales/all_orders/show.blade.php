@extends('backend.layouts.app')

@section('content')

    <?php use Carbon\Carbon;
    use App\Models\Card;
    use App\Models\CateringPlanPurchase;
    use App\Models\OrganisationSetting;
    use App\Models\Organisation;

    ?>

    <div class="card">
        <div class="card-header">
            <h1 class="h2 fs-16 mb-0">{{ translate('Order Details') }}</h1>
        </div>
        <div class="card-body">
            <div class="row gutters-5 mb-4">
                <div class="col text-center text-md-left">
                </div>
                @php
                    $delivery_status = $order->delivery_status;
                    $payment_status = $order->payment_status;
                @endphp
                {{--            <div class="col-md-3 ml-auto">--}}
                {{--                <label for=update_payment_status"">{{translate('Payment Status')}}</label>--}}
                {{--                <select class="form-control sk-selectpicker"  data-minimum-results-for-search="Infinity" id="update_payment_status">--}}
                {{--                    <option value="paid" @if ($payment_status == 'paid') selected @endif>{{translate('Paid')}}</option>--}}
                {{--                    <option value="unpaid" @if ($payment_status == 'unpaid') selected @endif>{{translate('Unpaid')}}</option>--}}
                {{--                </select>--}}
                {{--            </div>--}}
                {{--            <div class="col-md-3 ml-auto">--}}
                {{--                <label for=update_delivery_status"">{{translate('Delivery Status')}}</label>--}}
                {{--                <select class="form-control sk-selectpicker"  data-minimum-results-for-search="Infinity" id="update_delivery_status">--}}
                {{--                    <option value="pending" @if ($delivery_status == 'pending') selected @endif>{{translate('Pending')}}</option>--}}
                {{--                    <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>{{translate('Confirmed')}}</option>--}}
                {{--                    <option value="on_delivery" @if ($delivery_status == 'on_delivery') selected @endif>{{translate('On delivery')}}</option>--}}
                {{--                    <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>{{translate('Delivered')}}</option>--}}
                {{--                    <option value="cancelled" @if ($delivery_status == 'cancelled') selected @endif>{{translate('Cancel')}}</option>--}}
                {{--                </select>--}}
                {{--            </div>--}}
            </div>
            <div class="row gutters-5">
                <div class="col text-center text-md-left">
                    {{--                <address>--}}
                    {{--                    <strong class="text-main">{{ json_decode($order->shipping_address)->name }}</strong><br>--}}
                    {{--                    {{ json_decode($order->shipping_address)->email }}<br>--}}
                    {{--                    +{{ json_decode($order->shipping_address)->phone_code }} {{ json_decode($order->shipping_address)->phone }}<br>--}}
                    {{--                    {{ json_decode($order->shipping_address)->address }}, {{ getCityName(json_decode($order->shipping_address)->city) }}, {{ json_decode($order->shipping_address)->postal_code }}<br>--}}
                    {{--                    {{ \App\Country::find(json_decode($order->shipping_address)->country)->name }}--}}
                    {{--                </address>--}}
                    @if ($order->manual_payment && is_array(json_decode($order->manual_payment_data, true)))
                        <br>
                        <strong class="text-main">{{ translate('Payment Information') }}</strong><br>
                        {{ translate('Name') }}: {{ json_decode($order->manual_payment_data)->name }}
                        , {{ translate('Amount') }}
                        : {{ single_price(json_decode($order->manual_payment_data)->amount) }}
                        , {{ translate('TRX ID') }}: {{ json_decode($order->manual_payment_data)->trx_id }}
                        <br>
                        <a href="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}" target="_blank"><img
                                src="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}" alt=""
                                height="100"></a>
                    @endif
                </div>
                <div class="col-md-4 ml-auto">
                    <table class="table">
                        <tbody>
                        <tr>
                            <td class="text-main text-bold">{{translate('Order #')}}</td>
                            <td class="text-right text-info text-bold">    {{ $order->code }}</td>
                        </tr>

                        @if($order->tracking_number)
                            <tr>
                                <td class="text-main text-bold">{{translate('Tracking Number')}}    </td>
                                <td class="text-right">{{ $order->tracking_number }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="text-main text-bold">{{translate('Order Date')}}    </td>
                            <td class="text-right">{{ date('d/m/Y h:i A', $order->date) }}</td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">
                                {{translate('Total amount')}}
                            </td>
                            <td class="text-right">
                                {{ single_price($order->grand_total) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">{{translate('Payment method')}}</td>
                            <td class="text-right">{{ paymentMethodName($order->payment_type) }}</td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">{{translate('Payment Status')}}</td>
                            <td class="text-right">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</td>
                        </tr>
                        @if($order->orderDetails->first()->shipping_type)
                            <tr>
                                <td class="text-main text-bold">{{translate('Shipping method')}}</td>
                                <td class="text-right">
                                    {{shippingMethodName($order->orderDetails->first()->shipping_type)}}
                                    @if (!empty($order->orderDetails->first()->pickup_point_id))
                                        ({{ \App\PickupPoint::findOrFail($order->orderDetails->first()->pickup_point_id)->name }}
                                        )
                                    @endif
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <hr class="new-section-sm bord-no">
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table table-bordered sk-table invoice-summary">
                        <thead>
                        <tr class="bg-trans-dark">
                            <th data-breakpoints="lg" class="min-col">#</th>
                            {{--                            <th width="10%">{{translate('Photo')}}</th>--}}
                            <th class="text-uppercase">{{translate('Plan Name')}}</th>
                            <th class="text-uppercase">{{translate('Card Info')}}</th>
                            <th class="text-uppercase">{{translate('Meals')}}</th>
                            <th data-breakpoints="lg"
                                class="min-col text-center text-uppercase">{{translate('Period')}}</th>
                            <th data-breakpoints="lg"
                                class="min-col text-center text-uppercase">{{translate('Unit Price')}}</th>
                            <th data-breakpoints="lg"
                                class="min-col text-center text-uppercase">{{translate('Unit VAT')}} {{ $order->vat_percentage }}
                                %
                            </th>
                            <th data-breakpoints="lg"
                                class="min-col text-right text-uppercase">{{translate('Total')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($order->orderDetails as $key => $orderDetail)

                            @if ($orderDetail->type == 'catering_plan')
                                    <?php

                                    $card = null;
                                    $organisation_settings = null;
                                    $organisation = null;

                                    $plan_purchase = CateringPlanPurchase::find($orderDetail->type_id);

//                                    if ($_SERVER['REMOTE_ADDR'] == '82.102.76.201' && $key>0) {
//                                        dd($orderDetail->type_id);
//
////                                        $card = Card::find($plan_purchase->card_id);
//                                    }
                                    $plan_name=null;
                                    $plan=null;
                                    if($plan_purchase!=null){

                                        $plan = \App\Models\CateringPlan::find($plan_purchase->catering_plan_id);

                                        if($plan!=null){
                                            $plan_name=$plan->name;
                                        }else{
                                            $plan_name='N/A';
                                        }

                                        $card = Card::find($plan_purchase->card_id);
                                        $organisation_settings = OrganisationSetting::find($plan_purchase->organisation_settings_id);
                                        $organisation = Organisation::find($organisation_settings->organisation_id);

                                    }else{
                                        $plan_name='N/A';
                                        $card= null;
                                        $organisation_settings = null;
                                        $organisation = null;
                                    }

//                                    if ($_SERVER['REMOTE_ADDR'] == '82.102.76.201' && $key>0) {
//                                       dd($plan_purchase);
//
//                                        $card = Card::find($plan_purchase->card_id);
//                                    }




                                    ?>

                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>
                                        {{translate($plan_name)}}
                                    </td>
                                    <td>

                                        <span class="d-block"> @if($card!=null)
                                                {{ $card->name }}
                                            @else
                                                {{ translate('N/A') }}
                                            @endif
                                        </span>
                                        <span class="d-block">
                                             @if($card!=null)
                                                {{ $card->rfid_no }}
                                            @else
                                                {{ translate('N/A') }}
                                            @endif
                                        </span>
                                        <span class="d-block">
                                        @if($organisation!=null)
                                            {{ $organisation->name }}
                                        @else
                                            {{ translate('N/A') }}
                                        @endif
                                        </span>


                                    </td>

                                    <td>
                                        @if($plan_purchase!=null && $plan_purchase->snack_quantity>0)


                                            {{ translate('Snacks per day') }}: {{ $plan_purchase->snack_quantity }}

                                            <br>
                                        @endif
                                        @if($plan_purchase!=null && $plan_purchase->meal_quantity>0)

                                        {{ translate('Lunches per day') }}: {{ $plan_purchase->meal_quantity }}

                                        @endif
                                    </td>

                                    <td class="text-center">
                                        @if($plan_purchase!=null)
                                            {{Carbon::create($plan_purchase->from_date)->format('d/m/Y')}}
                                            -  {{Carbon::create($plan_purchase->to_date)->format('d/m/Y')}}
                                        @else
                                            {{ translate('N/A') }}
                                        @endif
                                    </td>
                                    <td class="text-center">{{ single_price($orderDetail->price) }}</td>
                                    <td class="text-center">{{ single_price($orderDetail->vat_amount) }}</td>
                                    <td class="text-right">{{ single_price($orderDetail->total) }}</td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="clearfix float-right">
                <table class="table">
                    <tbody>
                    <tr>
                        <td>
                            <strong class="text-muted">{{translate('Sub Total')}} :</strong>
                        </td>
                        <td>
                            {{ single_price($order->subtotal) }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <strong class="text-muted">{{ translate('VAT') }} {{ $order->vat_percentage }}% :</strong>
                        </td>
                        <td>
                            {{ single_price($order->vat_amount) }}
                        </td>
                    </tr>
                    <?php /*
                    <tr>
                        <td>
                            <strong class="text-muted">{{translate('Coupon')}} :</strong>
                        </td>
                        <td>
                            {{ single_price($order->coupon_discount) }}
                        </td>
                    </tr>*/ ?>
                    <tr>
                        <td>
                            <strong class="text-muted">{{translate('TOTAL')}} :</strong>
                        </td>
                        <td class="text-muted h5">
                            {{ single_price($order->grand_total) }}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="text-right no-print">
                    <a href="{{ route('invoice.download', $order->id) }}" type="button"
                       class="btn btn-icon btn-light"><i class="las la-print"></i></a>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $('#update_delivery_status').on('change', function () {
            var order_id = {{ $order->id }};
            var status = $('#update_delivery_status').val();
            $.post('{{ route('orders.update_delivery_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function (data) {
                SK.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
            });
        });

        $('#update_payment_status').on('change', function () {
            var order_id = {{ $order->id }};
            var status = $('#update_payment_status').val();
            $.post('{{ route('orders.update_payment_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function (data) {
                SK.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
            });
        });
    </script>
@endsection
