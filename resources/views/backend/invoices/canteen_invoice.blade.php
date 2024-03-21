
<html>
<head>

    <?php use Carbon\Carbon;
    use App\Models\Card;
    use App\Models\CateringPlanPurchase;
    use App\Models\OrganisationSetting;
    use App\Models\Organisation;

    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} Order: #{{$order->code}}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <style media="all">
        @page {
            margin: 0;
            padding: 0;
        }

        body {
            font-size: 0.875rem;
            font-family: '<?php echo  $font_family ?>';
            font-weight: normal;
            direction: <?php echo  $direction ?>;
            text-align: <?php echo  $text_align ?>;
            padding: 0;
            margin: 0;
        }

        table {
            width: 100%;
        }

        table th {
            font-weight: normal;
        }

        table.padding th {
            padding: .25rem .7rem;
        }

        table.padding td {
            padding: .25rem .7rem;
        }

        table.sm-padding td {
            padding: .1rem .7rem;
        }

        .border-bottom td,
        .border-bottom th {
            border-bottom: 1px solid #eceff4;
        }

        .text-left {
            text-align: <?php echo  $text_align ?>;
        }

        .text-right {
            text-align: <?php echo  $not_text_align ?>;
        }

        .text-center{
            text-align: center!important;
        }

        .size-50px{
            width: 50px;!important;
        }

        .d-block{
            display: block;
        }

        .badge {
            display: -webkit-inline-box;
            display: -ms-inline-flexbox;
            display: inline-flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            height: 18px;
            width: 18px;
            font-size: 0.8rem;
            font-weight: 500;
            line-height: unset;
            background-color: #C10F06FF!important;
            border-radius: 3px;
            width: auto;
            color: white;
            padding: 5px;
        }

        .bg-danger { background: #C10F06FF!important; }


    </style>
</head>
<body>
<div>
    <div style="background: #eceff4;padding: 1rem;">
        <table>
            <tr>
                <td>
                    <img src="{{ uploaded_asset(get_setting('header_logo'), true) }}" height="40"
                         style="display:inline-block;" alt="{{ env('APP_NAME') }}">
                </td>
                <td style="font-size: 1.5rem;" class="text-right strong">{{  toUpper(translate('ORDER')) }}</td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="gry-color small">{{ get_setting('contact_address') }}</td>
                <td class="text-right"><span class="gry-color small">{{  translate('Order ID') }}:</span> <span
                        class="strong">{{ $order->code }}</span></td>
            </tr>
            <tr>
                <td class="gry-color small"></td>
                <td class="text-right small"><span class="gry-color small">{{  translate('Order Date') }}:</span> <span
                        class=" strong">{{ date('d/m/Y H:i', $order->date) }}</span></td>
            </tr>
            <tr>
                <td class="gry-color small"></td>
                <td class="text-right small">
                    @if($order->shipping_method)
                        <span class="gry-color small">{{  translate('Shipping Method') }}:</span>
{{--                        <span class=" strong">--}}
{{--							{{ shippingMethodName($order->shipping_method) }}--}}
{{--                            @if (!empty($order->pickup_point))--}}
{{--                                ({{ \App\PickupPoint::findOrFail($order->pickup_point)->name }})--}}
{{--                            @endif--}}
{{--						</span>--}}
                    @endif
                </td>
            </tr>
            @if($order->tracking_number)
                <tr>
                    <td class="gry-color small"></td>
                    <td class="text-right small">
                        <span class="gry-color small">{{  translate('Tracking Number') }}:</span>
                        <span class=" strong">{{ $order->tracking_number }}</span>
                    </td>
                </tr>
            @endif
            <tr>
                <td class="gry-color small">{{  translate('Email') }}: {{ get_setting('contact_email') }}</td>
                <td class="text-right small">
                    <span class="gry-color small">{{  translate('Payment Method') }}:</span>
                    <span class=" strong">{{ paymentMethodName($order->payment_type) }}</span>
                </td>
            </tr>
            <tr>
                <td class="gry-color small">{{  translate('Phone') }}: {{ get_setting('contact_phone') }}</td>
                <td class="text-right small">
                    <span class="gry-color small">{{  translate('Payment Status') }}:</span>
                    <span class=" strong">{{ ucwords(str_replace('_', ' ', $order->payment_status)) }}</span>
                </td>
            </tr>
        </table>

    </div>

    <div style="padding: 1rem;padding-bottom: 0">
        <table>
            @php
                $shipping_address = json_decode($order->shipping_address);
            @endphp
            <tr>
                <td class="strong small gry-color">{{ translate('Bill to') }}:</td>
            </tr>
            <tr>
                <td class="strong">{{ $shipping_address->parent_fullName }}</td>
            </tr>
            <?php /*
				<tr><td class="gry-color small">{{-- $shipping_address->address --}}, {{-- $shipping_address->postal_code --}}, {{-- getCityName($shipping_address->city) --}}, {{-- \App\Country::find($shipping_address->country)->name --}}</td></tr>*/ ?>
            <tr>
                <td class="gry-color small">{{ translate('Email') }}: {{ $shipping_address->email }}</td>
            </tr>
            <?php /*<tr><td class="gry-color small">{{ translate('Phone') }}: +{{ $shipping_address->phone_code }} {{ $shipping_address->phone }}</td></tr>*/ ?>
        </table>
    </div>
    <div style="padding: 1rem;">
        <table class="padding text-left small border-bottom">
            <thead>

            <tr class="gry-color" style="background: #eceff4;">
                <th data-breakpoints="lg" class="min-col">#</th>
                <th width="10%">{{translate('Photo')}}</th>
                <th class="text-uppercase text-center">{{translate('Product Name')}}</th>
                <th class="text-uppercase text-center">{{translate('Date')}}</th>
                <th  data-breakpoints="lg"
                     class="min-col text-center text-uppercase">{{translate('Break')}}</th>
                <th class="min-col text-center text-uppercase">{{translate('Qty')}}</th>
                <th class="min-col text-center text-uppercase">{{translate('Unit Price')}}</th>
                <th data-breakpoints="lg"
                    class="min-col text-center text-uppercase" >{{translate('Unit VAT')}} {{ $order->vat_percentage }}%
                </th>
                <th data-breakpoints="lg"
                    class="min-col text-right text-uppercase">{{translate('Total')}}</th>
            </tr>
            </thead>
            <tbody class="strong">

            @php

                $orderDetails = \App\Models\AppOrderDetail::where('app_order_id', $order->id)
                            ->join('canteen_purchases', 'canteen_purchases.canteen_order_detail_id', '=', 'app_order_details.id')
                            ->select('app_order_details.*', 'canteen_purchases.date', 'canteen_purchases.meal_code', 'canteen_purchases.break_num', 'canteen_purchases.break_hour_from', 'canteen_purchases.break_hour_to', 'canteen_purchases.quantity')
                            ->get();

            @endphp

            @foreach ($orderDetails as $key => $orderDetail)

                        <?php
                            $product = \App\Models\CanteenProduct::find($orderDetail->product_id);
                        ?>

                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>
                                @if($product !=null)
                                    <img src="{{ uploaded_asset($product->thumbnail_img) }}?width=400&qty=50"
                                         alt="Image" class="size-50px img-fit">
                                @endif
                            </td>

                            <td class="text-center">
                                @if($product !=null)
                                    {{$product->getTranslation('name')}}
                                @else
                                    {{translate('Product not found')}}
                                @endif

                                    @if($orderDetail->refunded == 1)
                                        <div style="padding: 4px!important; border-radius: 5px!important;">
                                            <span class="badge bg-danger">{{translate('Refunded')}}</span>
                                        </div>

                                    @endif
                            </td>

                            <td class="text-center">{{ Carbon::create($orderDetail->date)->format('d/m/Y') }}</td>

                            <td class="text-center">
                                <span class="d-block"> {{ ordinal($orderDetail->break_num) }} {{translate('Break') }}</span> <br>
                                <span class="d-block"> {{  substr($orderDetail->break_hour_from, 0, 5) }} - {{substr($orderDetail->break_hour_to, 0, 5)}}</span>
                            </td>

                            <td class="text-center">{{ $orderDetail->quantity }}</td>

                            <td class="text-center">
                                {{single_price($orderDetail->price)}}
                            </td>

                            <td class="text-center">
                                {{single_price($orderDetail->vat_amount)}}
                            </td>

                            <td class="text-right">

                                {{single_price($orderDetail->quantity * $orderDetail->price)}}


                            </td>
                        </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div style="">
        <table class="text-right sm-padding small strong">
            <thead>
            <tr>
                <th width="75%"></th>
                <th width="25%"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                </td>
                <td>
                    <table class="text-right sm-padding small strong">
                        <tbody>
                        <tr>
                            <th class="gry-color text-left">{{ translate('Sub Total') }}</th>
                            <td class="currency">{{ single_price($order->subtotal) }}</td>
                        </tr>
                        <tr>
                            <th class="gry-color text-left">{{ translate('Total VAT') }} {{$order->vat_percentage}}%
                            </th>
                            <td class="currency">{{ single_price($order->vat_amount) }}</td>
                        </tr>
                        <tr>
                            <th class="text-left strong">{{ translate('Grand Total') }}</th>
                            <td class="currency">{{ single_price($order->grand_total) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>

