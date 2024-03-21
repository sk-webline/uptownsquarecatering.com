@php
    $status = $order->orderDetails->first()->delivery_status;
    $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
@endphp

<div class="modal-body p-0">
    <div class="overflow-hidden">
        <div class="modal-header px-0 py-10px">
            <div class="row no-gutters flex-grow-1">
                <div class="col-12">
                    <div class="px-10px">
                        <button type="button" class="close fs-20 md-fs-30" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="col-12">
                    <div class="px-15px px-md-50px mt-15px">
                        <h5 class="text-default-70 fw-600 fs-15 md-fs-18 m-0">{{ translate('Order No.')}}: {{ $order->code }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-25px mb-md-40px px-15px px-md-50px py-15px fs-12 md-fs-14">
            <div class="row gutters-10">
                <div class="col-auto col-lg mb-5px order-step-item @if($status == 'pending') text-secondary @else text-secondary @endif">
                    <div class="title">{{ toUpper(translate('Order placed'))}}</div>
                </div>
                <div class="col-12 d-lg-none"></div>
                <div class="col-auto col-lg mb-5px order-step-item @if($status == 'confirmed') text-secondary @elseif($status == 'on_delivery' || $status == 'delivered') text-secondary @endif">
                    <div class="title">{{ toUpper(translate('Confirmation'))}}</div>
                </div>
                <div class="col-12 d-lg-none"></div>
                <div class="col-auto col-lg mb-5px order-step-item @if($status == 'on_delivery') text-secondary @elseif($status == 'delivered') text-secondary @endif">
                    <div class="title">{{ toUpper(translate('On delivery'))}}</div>
                </div>
                <div class="col-12 d-lg-none"></div>
                <div class="col-auto col-lg mb-5px order-step-item @if($status == 'delivered') text-secondary @endif">
                    <div class="title">{{ toUpper(translate('Delivered'))}}</div>
                </div>
            </div>
        </div>
        <div class="card card-white fs-12 md-fs-14 text-default-70">
            <div class="card-header px-15px px-md-50px py-5px">
                <h5 class="fs-16 md-fs-18 m-0 fw-400">{{ translate('Order Summary') }}</h5>
            </div>
            <div class="card-body px-15px px-md-50px py-15px">
                <div class="row gutters-25">
                    <div class="col-lg-6">
                        <div class="row gutters-5 mb-10px">
                            <div class="col-6 col-lg-5 fw-600 text-default">{{ translate('Order Code')}}:</div>
                            <div class="col-6 col-lg-7">{{ $order->code }}</div>
                        </div>
                        <div class="row gutters-5 mb-10px">
                            <div class="col-6 col-lg-5 fw-600 text-default">{{ translate('Customer')}}:</div>
                            <div class="col-6 col-lg-7">{{ $shipping_address->name }}</div>
                        </div>
                        <div class="row gutters-5 mb-10px">
                            <div class="col-6 col-lg-5 fw-600 text-default">{{ translate('Email')}}:</div>
                            @if ($order->user_id != null)
                                <div class="col-6 col-lg-7">{{ $order->user->email }}</div>
                            @endif
                        </div>
                        <div class="row gutters-5 mb-10px">
                            <div class="col-6 col-lg-5 fw-600 text-default">{{ translate('Shipping address')}}:</div>
                            <div class="col-6 col-lg-7">{{ $shipping_address->address }}, {{ getCityName($shipping_address->city) }}, {{ $shipping_address->postal_code }}, {{ \App\Country::findOrFail($shipping_address->country)->name }}</div>
                        </div>
                        @if($order->tracking_number)
                        <div class="row gutters-5 mb-10px">
                            <div class="col-6 col-lg-5 fw-600 text-default">{{ translate('Tracking Number')}}:</div>
                            <div class="col-6 col-lg-7">{{ $order->tracking_number }}</div>
                        </div>
                        @endif
                    </div>
                    <div class="col-lg-6">
                        <div class="row gutters-5 mb-10px">
                            <div class="col-6 fw-600 text-default">{{ translate('Order Date')}}:</div>
                            <div class="col-6">{{ date('d-m-Y H:i', $order->date) }}</div>
                        </div>
                        <div class="row gutters-5 mb-10px">
                            <div class="col-6 fw-600 text-default">{{ translate('Order Status')}}:</div>
                            <div class="col-6">{{ translate(ucfirst(str_replace('_', ' ', $status))) }}</div>
                        </div>
                        <div class="row gutters-5 mb-10px">
                            <div class="col-6 fw-600 text-default">{{ translate('Total')}}:</div>
                            <div class="col-6">{{ single_price($order->grand_total) }}</div>
                        </div>
                        @if($order->shipping_method)
                            <div class="row gutters-5 mb-10px">
                                <div class="col-6 fw-600 text-default">{{ translate('Shipping Method')}}:</div>
                                <div class="col-6">
                                    {{shippingMethodName($order->shipping_method)}}
                                    @if (!empty($order->pickup_point))
                                        <br>
                                        ({{ \App\PickupPoint::findOrFail($order->pickup_point)->name }})
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="row gutters-5 mb-10px">
                            <div class="col-6 fw-600 text-default">{{ translate('Payment Method')}}:</div>
                            <div class="col-6">{{ paymentMethodName($order->payment_type) }}</div>
                        </div>
                        <div class="row gutters-5 mb-10px">
                            <div class="col-6 fw-600 text-default">{{ translate('Payment Status')}}:</div>
                            <div class="col-6">{{ ucwords(str_replace('_', ' ', $order->payment_status)) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-white fs-12 md-fs-14 text-default-70">
            <div class="card-header px-15px px-md-50px py-5px">
                <h5 class="fs-16 md-fs-18 m-0 fw-400">{{ translate('Order Details') }}</h5>
            </div>
            <div class="card-body px-15px px-md-50px py-5px">
                <div class="d-none d-md-block">
                    <table class="table border-0 text-default-70">
                        <thead class="text-default">
                        <tr>
                            <th class="fw-600 text-left">#</th>
                            <th class="fw-600 text-left" width="45%">{{ translate('Product')}}</th>
                            <th class="fw-600 text-center" width="10%">{{ translate('Qty')}}</th>
                            <th class="fw-600 text-center" width="15%">{{ translate('Unit Price')}}</th>
                            <th class="fw-600 text-center" width="20%">{{ translate('Unit VAT ')}}  {{$order->vat_percentage}}%</th>
                            <th class="fw-600 text-right" width="10%">{{ translate('Total')}}</th>
                          <?php /*
                            @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                                <th>{{ translate('Refund')}}</th>
                            @endif */ ?>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($order->orderDetails as $key => $orderDetail)
                            <tr>
                                <td class="text-left">{{ $key+1 }}</td>
                                <td class="text-left">
                                    @if ($orderDetail->product != null)
                                        <a class="text-secondary" href="{{ route('product', $orderDetail->product->slug) }}" target="_blank">
                                            {{ $orderDetail->product->getTranslation('name') }}@if($orderDetail->variation != null) ({{getStrFromProductVariant($orderDetail->product, $orderDetail->variation)}}) @endif
                                        </a>
                                        @if($orderDetail->product->barcode)
                                            <div class="fs-12 mt-1">{{translate('Reference No.')}}: {{$orderDetail->product->barcode}}</div>
                                        @endif
                                    @else
                                        <strong>{{  translate('Product Unavailable') }}</strong>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $orderDetail->quantity }}
                                </td>
                                <td class="text-center">
                                    {{ single_price($orderDetail->price/$orderDetail->quantity) }}
                                </td>
                                <td class="text-center">
                                    {{ single_price($orderDetail->vat_amount) }}
                                </td>
                                <td class="text-right">{{ single_price($orderDetail->price + $orderDetail->tax) }}</td>
                              <?php /*
                            @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                                @php
                                    $no_of_max_day = \App\BusinessSetting::where('type', 'refund_request_time')->first()->value;
                                    $last_refund_date = $orderDetail->created_at->addDays($no_of_max_day);
                                    $today_date = Carbon\Carbon::now();
                                @endphp
                                <td>
                                    @if ($orderDetail->product != null &&
                                    $orderDetail->product->refundable != 0 &&
                                    $orderDetail->refund_request == null &&
                                    $today_date <= $last_refund_date &&
                                    $orderDetail->payment_status == 'paid' &&
                                    $orderDetail->delivery_status == 'delivered')
                                        <a href="{{route('refund_request_send_page', $orderDetail->id)}}" class="btn btn-primary btn-sm">{{  translate('Send') }}</a>
                                    @elseif ($orderDetail->refund_request != null && $orderDetail->refund_request->refund_status == 0)
                                        <b class="text-info">{{  translate('Pending') }}</b>
                                    @elseif ($orderDetail->refund_request != null && $orderDetail->refund_request->refund_status == 2)
                                        <b class="text-success">{{  translate('Rejected') }}</b>
                                    @elseif ($orderDetail->refund_request != null && $orderDetail->refund_request->refund_status == 1)
                                        <b class="text-success">{{  translate('Approved') }}</b>
                                    @elseif ($orderDetail->product->refundable != 0)
                                        <b>{{  translate('N/A') }}</b>
                                    @else
                                        <b>{{  translate('Non-refundable') }}</b>
                                    @endif
                                </td>
                            @endif */ ?>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-md-none">
                    @foreach ($order->orderDetails as $key => $orderDetail)
                        <div class="border-bottom border-quaternary-300 mb-10px">
                            <div class="row gutters-5 mb-10px">
                                <div class="col-4 text-default fw-600">{{ translate('Product')}}</div>
                                <div class="col-8 text-right text-primary">
                                    @if ($orderDetail->product != null)
                                        <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank">{{ $orderDetail->product->getTranslation('name') }}</a>
                                    @else
                                        <strong>{{  translate('Product Unavailable') }}</strong>
                                    @endif
                                </div>
                            </div>
                            <div class="row gutters-5 mb-10px">
                                <div class="col-4 text-default fw-600">{{ translate('Quantity')}}</div>
                                <div class="col-8 text-right">
                                    {{ $orderDetail->quantity }}
                                </div>
                            </div>
                            <div class="row gutters-5 mb-10px">
                                <div class="col-4 text-default fw-600">{{ translate('Unit Price')}}</div>
                                <div class="col-8 text-right">
                                    {{ single_price($orderDetail->price / $orderDetail->quantity) }}
                                </div>
                            </div>`
                            <div class="row gutters-5 mb-10px">
                                <div class="col-4 text-default fw-600">{{ translate('Unit VAT')}}  {{$order->vat_percentage}}%</div>
                                <div class="col-8 text-right">
                                    {{ single_price($orderDetail->vat_amount) }}
                                </div>
                            </div>
                            <div class="row gutters-5 mb-10px">
                                <div class="col-4 text-default fw-600">{{ translate('Total')}}</div>
                                <div class="col-8 text-right">
                                    {{ single_price($orderDetail->price + $orderDetail->tax) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card card-white fs-14 text-default-70">
            <div class="card-header px-15px py-5px p-md-0">
                <h5 class="fs-16 md-fs-18 m-0 fw-400 d-md-none">{{ translate('Order Amount') }}</h5>
            </div>
            <div class="card-body px-15px px-md-50px py-10px">
                <div class="row">
                    <div class="col-lg-8 d-none d-md-block">
                        <h5 class="fs-16 md-fs-18 m-0 fw-400">{{ translate('Order Amount') }}</h5>
                    </div>
                    <div class="col-lg-4">
                        <div class="row gutters-10 mb-10px">
                            <div class="col-6 fw-600">{{ translate('Subtotal')}}</div>
                            <div class="col-6 text-right">
                                <span class="strong-600">{{ single_price($order->subtotal) }}</span>
                            </div>
                        </div>
                        <div class="row gutters-10 mb-10px">
                            <div class="col-6 fw-600">{{ translate('Shipping')}}</div>
                            <div class="col-6 text-right">
                                {{ single_price($order->shipping_cost) }}
                            </div>
                        </div>
                        @php
                            $taxes = \App\Tax::where('tax_status', 1)->get();
                        @endphp
                        @if(count($taxes) > 2)
                            <div class="row gutters-10 mb-10px">
                                <div class="col-6 fw-600">{{ translate('VAT')}}</div>
                                <div class="col-6 text-right">
                                    <span class="text-italic">{{ single_price($order->orderDetails->sum('tax')) }}</span>
                                </div>
                            </div>
                        @else
                            <div class="row gutters-10 mb-10px">
                                <div class="col-6 fw-600">
                                    {{ translate('VAT')}} {{$order->vat_percentage}}%
                                </div>
                                <div class="col-6 text-right">
                                    <span class="text-italic">{{ single_price($order->vat_amount) }}</span>
                                </div>
                            </div>
                        @endif
                      <?php /*
                        <div class="row gutters-10 mb-10px">
                            <div class="col-6 fw-600">{{ translate('Coupon')}}</div>
                            <div class="col-6 text-right">
                                <span class="text-italic">{{ single_price($order->coupon_discount) }}</span>
                            </div>
                        </div>*/ ?>
                        <div class="row gutters-10 text-default">
                            <div class="col-6 fw-600">{{ translate('Total')}}</div>
                            <div class="col-6 text-right fw-700">
                                {{ single_price($order->grand_total) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
{{--        @if ($order->manual_payment && $order->manual_payment_data == null)--}}
{{--            <button onclick="show_make_payment_modal({{ $order->id }})" class="btn btn-block btn-primary">{{ translate('Make Payment')}}</button>--}}
{{--        @endif--}}
    </div>
</div>

<script type="text/javascript">
  function show_make_payment_modal(order_id){
    $.post('{{ route('checkout.make_payment') }}', {_token:'{{ csrf_token() }}', order_id : order_id}, function(data){
      $('#payment_modal_body').html(data);
      $('#payment_modal').modal('show');
      $('input[name=order_id]').val(order_id);
    });
  }
</script>
