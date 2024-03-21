@extends('frontend.layouts.app')

@section('content')
    <section class="my-70px my-lg-150px my-xxl-125px" data-aos="fade-up">
        <div class="container">
            <div class="mw-1350px mx-auto">
                <div class="border-bottom-primary border-default-200 pb-15px mb-15px mb-md-30px">
                    <div class="row">
                        <div class="col-lg-6 mb-15px mb-lg-0">
                            <h1 class="fs-18 md-fs-30 fw-700 m-0 lh-1">{{ toUpper(translate('Payment is Pending!'))}}</h1>
                        </div>
                        <div class="col-lg-6 text-lg-right">
                            <h2 class="fs-14 lg-fs-18 fw-700 text-primary-50 m-0 lh-1">{{ toUpper(translate('Order Number:'))}} {{ $viva_log->OrderCode }}</h2>
                        </div>
                    </div>
                </div>
                <div class="text-primary-50 fs-16 md-fs-22">
                    <p>{{  translate('The payment for your order is pending. When the payment is completed you will receive a confirmation email') }}</p>
                </div>
            </div>
        </div>
    </section>
    <?php /*
    <div class="mb-4">
        <h5 class="fw-600 mb-3 fs-17 pb-2">{{ translate('Order Summary')}}</h5>
        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Order Code')}}:</td>
                        <td>{{ $order->code }}</td>
                    </tr>
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Name')}}:</td>
                        <td>{{ json_decode($order->shipping_address)->name }}</td>
                    </tr>
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Email')}}:</td>
                        <td>{{ json_decode($order->shipping_address)->email }}</td>
                    </tr>
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Shipping address')}}:</td>
                        <td>{{ json_decode($order->shipping_address)->address }}, {{ json_decode($order->shipping_address)->city }}, {{ json_decode($order->shipping_address)->country }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Order date')}}:</td>
                        <td>{{ date('d-m-Y H:i A', $order->date) }}</td>
                    </tr>
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Order status')}}:</td>
                        <td>{{ translate(ucfirst(str_replace('_', ' ', $status))) }}</td>
                    </tr>
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Total order amount')}}:</td>
                        <td>{{ single_price($order->orderDetails->sum('price') + $order->orderDetails->sum('tax')) }}</td>
                    </tr>
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Shipping')}}:</td>
                        <td>{{ translate('Flat shipping rate')}}</td>
                    </tr>
                    <tr>
                        <td class="w-50 fw-600">{{ translate('Payment method')}}:</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div>
        <h5 class="fw-600 mb-3 fs-17 pb-2">{{ translate('Order Details')}}</h5>
        <div>
            <table class="table table-responsive-md">
                <thead>
                <tr>
                    <th>#</th>
                    <th width="30%">{{ translate('Product')}}</th>
                    <th>{{ translate('Variation')}}</th>
                    <th>{{ translate('Quantity')}}</th>
                    <th>{{ translate('Delivery Type')}}</th>
                    <th class="text-right">{{ translate('Price')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($order->orderDetails as $key => $orderDetail)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>
                            @if ($orderDetail->product != null)
                                <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank" class="text-reset">
                                    {{ $orderDetail->product->getTranslation('name') }}
                                </a>
                            @else
                                <strong>{{  translate('Product Unavailable') }}</strong>
                            @endif
                        </td>
                        <td>
                            {{ $orderDetail->variation }}
                        </td>
                        <td>
                            {{ $orderDetail->quantity }}
                        </td>
                        <td>
                            @if ($orderDetail->shipping_type != null && $orderDetail->shipping_type == 'home_delivery')
                                {{  translate('Home Delivery') }}
                            @elseif ($orderDetail->shipping_type == 'pickup_point')
                                @if ($orderDetail->pickup_point != null)
                                    {{ $orderDetail->pickup_point->getTranslation('name') }} ({{ translate('Pickip Point') }})
                                @endif
                            @endif
                        </td>
                        <td class="text-right">{{ single_price($orderDetail->price) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-xl-5 col-md-6 ml-auto mr-0">
                <table class="table ">
                    <tbody>
                    <tr>
                        <th>{{ translate('Subtotal')}}</th>
                        <td class="text-right">
                            <span class="fw-600">{{ single_price($order->orderDetails->sum('price')) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ translate('Shipping')}}</th>
                        <td class="text-right">
                            <span class="font-italic">{{ single_price($order->orderDetails->sum('shipping_cost')) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ translate('Tax')}}</th>
                        <td class="text-right">
                            <span class="font-italic">{{ single_price($order->orderDetails->sum('tax')) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ translate('Coupon Discount')}}</th>
                        <td class="text-right">
                            <span class="font-italic">{{ single_price($order->coupon_discount) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th><span class="fw-600">{{ translate('Total')}}</span></th>
                        <td class="text-right">
                            <strong><span>{{ single_price($order->grand_total) }}</span></strong>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>*/ ?>
@endsection
