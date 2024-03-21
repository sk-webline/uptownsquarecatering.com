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
                        <span class=" strong">
							{{ shippingMethodName($order->shipping_method) }}
                            @if (!empty($order->pickup_point))
                                ({{ \App\PickupPoint::findOrFail($order->pickup_point)->name }})
                            @endif
						</span>
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
                <td class="strong">{{ $shipping_address->name }}</td>
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
            {{--	                <tr class="gry-color" style="background: #eceff4;">--}}
            {{--	                    <th width="35%" class="text-left">{{ translate('Product Name') }}</th>--}}
            {{--	                    <th width="10%" style="text-align: center">{{ translate('Qty') }}</th>--}}
            {{--	                    <th width="10%" style="text-align: center">{{ translate('Unit Price') }}</th>--}}
            {{--	                    <th width="15%" style="text-align: center">{{ translate('Unit VAT') }} {{$order->vat_percentage}}%</th>--}}
            {{--	                    <th width="15%" class="text-right">{{ translate('Total') }}</th>--}}
            {{--	                </tr>--}}

            <tr class="gry-color" style="background: #eceff4;">
                <th data-breakpoints="lg" class="min-col">#</th>
                {{--                            <th width="10%">{{translate('Photo')}}</th>--}}
                <th class="text-uppercase">{{translate('Plan name')}}</th>
                <th class="text-uppercase">{{translate('Card Info')}}</th>
                <th data-breakpoints="lg"
                    class="min-col text-center text-uppercase" width="190">{{translate('Period')}}</th>
                <th data-breakpoints="lg"
                    class="min-col text-center text-uppercase">{{translate('Unit Price')}}</th>
                <th data-breakpoints="lg"
                    class="min-col text-center text-uppercase" width="100">{{translate('Unit VAT')}} {{ $order->vat_percentage }}
                    %
                </th>
                <th data-breakpoints="lg"
                    class="min-col text-right text-uppercase">{{translate('Total')}}</th>
            </tr>
            </thead>
            <tbody class="strong">
            @foreach ($order->orderDetails as $key => $orderDetail)
                @if ($orderDetail->type == 'catering_plan')
                        <?php

                        $card = null;
                        $organisation_settings = null;
                        $organisation = null;

                        $plan_purchase = CateringPlanPurchase::find($orderDetail->type_id);

                        $plan_name = null;
                        $plan = null;
                        if ($plan_purchase != null) {
                            $plan = \App\Models\CateringPlan::find($plan_purchase->catering_plan_id);

                            if ($plan != null) {
                                $plan_name = $plan->name;
                            } else {
                                $plan_name = '-';
                            }
                        }
                        $card = Card::find($plan_purchase->card_id);
                        $organisation_settings = OrganisationSetting::find($plan_purchase->organisation_settings_id);
                        $organisation = Organisation::find($organisation_settings->organisation_id);

                        ?>

                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>
                            {{translate($plan_name)}}

                            @if($plan_purchase!=null && $plan_purchase->snack_quantity>0)
                                <br>

                                {{ translate('Snacks per day') }}: {{ $plan_purchase->snack_quantity }}




                            @endif
                            @if($plan_purchase!=null && $plan_purchase->meal_quantity>0)
                                <br>
                                <span class="d-block">

                                        {{ translate('Lunches per day') }}: {{ $plan_purchase->meal_quantity }}

                                </span>
                            @endif
                        </td>
                        <td>

                            <span class="d-block">
                                @if($card!=null)
                                    {{ $card->name }}
                                @else
                                    {{ translate('N/A') }}
                                @endif
                            </span>
                            <br>
                            <span class="d-block">
                                @if($card!=null)
                                    {{ $card->rfid_no }}
                                @else
                                    {{ translate('N/A') }}
                                @endif
                                        </span>
                            <br>
                            <span class="d-block">
                                        @if($organisation!=null)
                                    {{ $organisation->name }}
                                @else
                                    {{ translate('N/A') }}
                                @endif
                            </span>

                        </td>


                        <td class="text-center " style="text-align: center">
                            @if($plan_purchase!=null)
                                {{Carbon::create($plan_purchase->from_date)->format('d/m/Y')}}
                                -  {{Carbon::create($plan_purchase->to_date)->format('d/m/Y')}}
                            @else
                                {{ translate('N/A') }}
                            @endif
                        </td>
                        <td class="text-center" style="text-align: center">{{ single_price($orderDetail->price) }}</td>
                        <td class="text-center"
                            style="text-align: center">{{ single_price($orderDetail->vat_amount) }}</td>
                        <td class="text-right">{{ single_price($orderDetail->total) }}</td>
                    </tr>
                @endif
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
                        <?php /*
				                    <tr class="border-bottom">
							            <th class="gry-color text-left">{{ translate('Coupon Discount') }}</th>
							            <td class="currency">{{ single_price($order->coupon_discount) }}</td>
							        </tr>*/ ?>
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
