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
                    <address>
                        {{ json_decode($order->shipping_address)->app_username }} <br>
                        <strong class="text-main">{{ json_decode($order->shipping_address)->parent_fullName }}</strong><br>
                        {{ json_decode($order->shipping_address)->email }}<br>
                    </address>

                </div>
                <div class="col-md-4 ml-auto">
                    <table class="table">
                        <tbody>
                        <tr>
                            <td class="text-main text-bold">{{translate('Order #')}}</td>
                            <td class="text-right text-info text-bold">    {{ $order->code }}</td>
                        </tr>
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

                        </tbody>
                    </table>
                </div>
            </div>
            <hr class="new-section-sm bord-no">
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table table-bordered sk-table invoice-summary text-center">
                        <thead>
                        <tr class="bg-trans-dark">
                            <th data-breakpoints="lg" class="min-col">#</th>
                            <th width="10%">{{translate('Photo')}}</th>
                            <th class="text-uppercase">{{translate('Product Name')}}</th>
                            <th class="text-uppercase">{{translate('Date')}}</th>
                            <th class="text-uppercase">{{translate('Break')}}</th>
                            <th data-breakpoints="lg"
                                class="min-col text-center text-uppercase">{{translate('Qty')}}</th>
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

                        @php

                        $orderDetails = \App\Models\AppOrderDetail::where('app_order_id', $order->id)
                                    ->join('canteen_purchases', 'canteen_purchases.canteen_order_detail_id', '=', 'app_order_details.id')
                                    ->select('app_order_details.*', 'canteen_purchases.date', 'canteen_purchases.meal_code', 'canteen_purchases.break_num', 'canteen_purchases.break_hour_from', 'canteen_purchases.break_hour_to', 'canteen_purchases.quantity')
                                    ->get();

//                        dd($orderDetails);
                        @endphp
                        @foreach ($orderDetails as $key => $orderDetail)

                                    @php

//                                        dd($orderDetail, $orderDetail->date);
                                       $product = \App\Models\CanteenProduct::find($orderDetail->product_id);

                                    @endphp
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>
                                        @if($product !=null)
                                            <img src="{{ uploaded_asset($product->thumbnail_img) }}?width=400&qty=50"
                                                 alt="Image" class="size-50px img-fit">
                                        @endif
                                    </td>

                                    <td>
                                        @if($product !=null)
                                            {{$product->getTranslation('name')}}
                                        @else
                                            {{translate('Product not found')}}
                                        @endif
                                    </td>

                                    <td>{{ Carbon::create($orderDetail->date)->format('d/m/Y') }}</td>

                                    <td>
                                        <span class="d-block"> {{ ordinal($orderDetail->break_num) }} {{translate('Break') }}</span>
                                        <span class="d-block"> {{  substr($orderDetail->break_hour_from, 0, 5) }} - {{substr($orderDetail->break_hour_to, 0, 5)}}</span>
                                    </td>

                                    <td>{{ $orderDetail->quantity }}</td>

                                    <td>
                                        {{single_price($orderDetail->price)}}
                                    </td>

                                    <td>
                                        {{single_price($orderDetail->vat_amount)}}
                                    </td>

                                    <td class="text-right">

                                        {{single_price($orderDetail->quantity * $orderDetail->price)}}

                                        @if($orderDetail->refunded == 1)
                                            <div>
                                                <span class="badge badge-inline badge-danger">{{translate('Refunded')}}</span>
                                            </div>

                                        @endif
                                    </td>

                                </tr>

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
                    <a href="{{ route('canteen_invoice.download', $order->id) }}" type="button"
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
