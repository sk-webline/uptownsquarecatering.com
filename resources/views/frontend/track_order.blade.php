@extends('frontend.layouts.app')

@section('content')
    <section class="my-50px my-lg-90px my-xxl-125px">
        <div class="bg-login py-20px py-sm-40px text-center l-space-1-2 text-default-60 fs-11 sm-fs-18">
            <div class="container">
                <div class="mw-475px mx-auto">
                    <h1 class="text-secondary fw-700 font-play fs-20 sm-fs-40 mb-10px mb-sm-20px">{{ translate('Track Order') }}</h1>
                    <p>{{ translate('Check your order status and progress.')}}</p>
                </div>
            </div>
        </div>
        <div class="profile mt-20px mt-sm-30px">
            <div class="container">
                <div class="mw-365px mx-auto fs-13 sm-fs-16 fw-500">
                    <form class="" action="{{ route('orders.track') }}" method="GET" enctype="multipart/form-data">
                        <div class="form-group mb-10px mb-sm-15px">
                            <input type="text" class="form-control" placeholder="{{ translate('Order Code')}}" name="order_code" required>
                        </div>
                        <button type="submit" class="btn btn-outline-primary btn-block fw-500 fs-14 sm-fs-16">{{toUpper(translate('Track Order'))}}</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @isset($order)
        @php
            $status = $order->orderDetails->first()->delivery_status;
        @endphp
        <section class="mb-50px mb-lg-90px mb-xxl-125px">
            <div class="container">
                <div class="mx-auto mw-800px">
                    <div class="bg-account shadow-sm">
                        <div class="px-15px px-md-50px py-30px border-bottom border-black-200">
                            <h5 class="text-default-50 fw-600 fs-15 md-fs-18 m-0">{{ translate('Order No.')}}: {{ $order->code }}</h5>
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
                        <div class="card fs-12 md-fs-14 text-default-50">
                            <div class="card-header px-15px px-md-50px py-5px border-bottom border-black-200">
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
                                            <div class="col-6 col-lg-7">{{ json_decode($order->shipping_address)->name }}</div>
                                        </div>
                                        <div class="row gutters-5 mb-10px">
                                            <div class="col-6 col-lg-5 fw-600 text-default">{{ translate('Email')}}:</div>
                                            @if ($order->user_id != null)
                                                <div class="col-6 col-lg-7">{{ $order->user->email }}</div>
                                            @else
                                                <div class="col-6 col-lg-7">{{ json_decode($order->shipping_address)->email }}</div>
                                            @endif
                                        </div>
                                        <div class="row gutters-5 mb-10px">
                                            <div class="col-6 col-lg-5 fw-600 text-default">{{ translate('Shipping address')}}:</div>
                                            <div class="col-6 col-lg-7">{{ json_decode($order->shipping_address)->address }}, {{ \App\City::findOrFail(json_decode($order->shipping_address)->city)->name }}, {{ json_decode($order->shipping_address)->postal_code }}, {{ \App\Country::findOrFail(json_decode($order->shipping_address)->country)->name }}</div>
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
                                            <div class="col-6">{{ date('d-m-Y H:i A', $order->date) }}</div>
                                        </div>
                                        <div class="row gutters-5 mb-10px">
                                            <div class="col-6 fw-600 text-default">{{ translate('Order Status')}}:</div>
                                            <div class="col-6">{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}</div>
                                        </div>
                                        <div class="row gutters-5 mb-10px">
                                            <div class="col-6 fw-600 text-default">{{ translate('Total')}}:</div>
                                            <div class="col-6">{{ single_price($order->grand_total) }}</div>
                                        </div>
                                        @if($order->orderDetails->first()->shipping_type)
                                            <div class="row gutters-5 mb-10px">
                                                <div class="col-6 fw-600 text-default">{{ translate('Delivery Type')}}:</div>
                                                <div class="col-6">
                                                    {{shippingMethodName($order->orderDetails->first()->shipping_type)}}
                                                    @if (!empty($order->orderDetails->first()->pickup_point_id))
                                                        <br>
                                                        ({{ \App\PickupPoint::findOrFail($order->orderDetails->first()->pickup_point_id)->name }})
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        <div class="row gutters-5 mb-10px">
                                            <div class="col-6 fw-600 text-default">{{ translate('Payment method')}}:</div>
                                            <div class="col-6">{{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}</div>
                                        </div>
                                        <div class="row gutters-5 mb-10px">
                                            <div class="col-6 fw-600 text-default">{{ translate('Payment Status')}}:</div>
                                            <div class="col-6">{{ ucwords(str_replace('_', ' ', $order->payment_status)) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-white fs-12 md-fs-14 text-default-50">
                            <div class="card-header px-15px px-md-50px py-5px border-bottom border-black-200">
                                <h5 class="fs-16 md-fs-18 m-0 fw-400">{{ translate('Order Details') }}</h5>
                            </div>
                            <div class="card-body px-15px px-md-50px py-5px">
                                <div class="d-none d-md-block">
                                    <table class="table border-0 text-default-50">
                                        <thead class="text-default">
                                        <tr>
                                            <th class="fw-600">#</th>
                                            <th class="fw-600" width="75%">{{ translate('Product')}}</th>
                                            <th class="fw-600 text-right">{{ translate('Quantity')}}</th>
                                            <th class="fw-600 text-right">{{ translate('Price')}}</th>
                                            <th class="fw-600 text-right">{{ translate('Total')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($order->orderDetails as $key => $orderDetail)
                                            <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td>
                                                    @if ($orderDetail->product != null)
                                                        <a class="text-secondary" href="{{ route('product', $orderDetail->product->slug) }}" target="_blank">
                                                            {{ $orderDetail->product->getTranslation('name') }}@if($orderDetail->variation != null) ({{ getStrFromProductVariant($orderDetail->product, $orderDetail->variation) }}) @endif
                                                        </a>
                                                        @if($orderDetail->product->barcode)
                                                            <div class="fs-12 mt-1">{{translate('Reference No.')}}: {{$orderDetail->product->barcode}}</div>
                                                        @endif
                                                    @else
                                                        <strong>{{  translate('Product Unavailable') }}</strong>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $orderDetail->quantity }}
                                                </td>
                                                <td>
                                                    {{ single_price($orderDetail->price/$orderDetail->quantity) }}
                                                </td>
                                                <td>{{ single_price($orderDetail->price + $orderDetail->tax) }}</td>
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
                                                <div class="col-4 text-default fw-600">{{ translate('Price')}}</div>
                                                <div class="col-8 text-right">
                                                    {{ single_price($orderDetail->price + $orderDetail->tax) }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="card card-white fs-14 text-default-50">
                            <div class="card-header px-15px py-5px p-md-0 border-bottom border-black-200">
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
                                                    <span class="text-italic">{{ single_price($order->vat_amount) }}</span>
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
                    </div>
                </div>
            </div>
        </section>
    @endisset
@endsection
