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
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta charset="UTF-8">
    <style media="all">
        @font-face {
            font-family: 'Roboto';
            src: url("{{ static_asset('fonts/Roboto-Regular.ttf') }}") format("truetype");
            font-weight: normal;
            font-style: normal;
        }

        * {
            margin: 0;
            padding: 0;
            line-height: 1.3;
            font-family: 'Roboto';
            color: #333542;
        }

        body {
            font-size: .875rem;
        }

        .gry-color *,
        .gry-color {
            color: #878f9c;
        }

        table {
            width: 100%;
        }

        table th {
            font-weight: normal;
        }

        table.padding th {
            padding: .5rem .7rem;
        }

        table.padding td {
            padding: .7rem;
        }

        table.sm-padding td {
            padding: .2rem .7rem;
        }

        .border-bottom td,
        .border-bottom th {
            border-bottom: 1px solid #eceff4;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .small {
            font-size: .85rem;
        }

        .currency {

        }
    </style>
</head>
<body>
<div>
    @php
        $logo = get_setting('png_logo');
    @endphp
    <table border="0" bgcolor="#eceff4" cellpadding="5">
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td>
                &nbsp;&nbsp;
                <img src="{{ uploaded_asset(get_setting('header_logo')) }}" height="50" style="display:inline-block;"
                     alt="{{ env('APP_NAME') }}">
            </td>
            <td class="text-right strong">{{  toUpper(translate('ORDER')) }}&nbsp;&nbsp;</td>
        </tr>
        <tr>
            <td class="gry-color small">&nbsp;&nbsp;{{ get_setting('contact_address') }}</td>
            <td class="text-right"><span class="gry-color small">{{  translate('Order ID') }}:</span> <span
                    class="strong">{{ $order->code }}</span>&nbsp;&nbsp;
            </td>
        </tr>
        <tr>
            <td class="gry-color small">&nbsp;&nbsp;{{  translate('Email') }}: {{ get_setting('contact_email') }}</td>
            <td class="text-right small"><span class="gry-color small">{{  translate('Order Date') }}:</span> <span
                    class=" strong">{{ date('d/m/Y', $order->date) }}</span>&nbsp;&nbsp;
            </td>
        </tr>
        <tr>
            <td class="gry-color small">&nbsp;&nbsp;{{  translate('Phone') }}: {{ get_setting('contact_phone') }}</td>
            <td class="text-right small">
                @if($order->shipping_method)
                    <span class="gry-color small">{{  translate('Shipping Method') }}:</span>
                    <span class=" strong">
							{{shippingMethodName($order->shipping_method)}}
                        @if (!empty($order->pickup_point))
                            ({{ \App\PickupPoint::findOrFail($order->pickup_point)->name }})
                        @endif
						</span>
                @endif
                &nbsp;&nbsp;
            </td>
        </tr>
        <tr>
            <td class="gry-color small"></td>
            <td class="text-right small">
                <span class="gry-color small">{{  translate('Payment Method') }}:</span>
                <span class=" strong">{{ paymentMethodName($order->payment_type) }}</span>
            </td>
        </tr>
        <tr>
            <td class="gry-color small"></td>
            <td class="text-right small">
                <span class="gry-color small">{{  translate('Payment Status') }}:</span>
                <span class=" strong">{{ ucwords($order->payment_status) }}</span>
            </td>
        </tr>
    </table>
    <div style="padding: 1.5rem;padding-bottom: 0">
        <table border="0" cellpadding="5">
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
    <div style="padding: 1.5rem;">
        <table border="0" class="padding text-left small border-bottom" cellpadding="5">
            <thead>
            <tr class="gry-color" style="background: #eceff4;">
                <th data-breakpoints="lg" class="min-col">#</th>
                {{--                            <th width="10%">{{translate('Photo')}}</th>--}}
                <th class="text-uppercase">{{translate('Plan Name')}}</th>
                <th class="text-uppercase">{{translate('Card Info')}}</th>
                <th data-breakpoints="lg"
                    class="min-col text-center text-uppercase" width="190">{{translate('Period')}}</th>
                <th data-breakpoints="lg"
                    class="min-col text-center text-uppercase">{{translate('Price')}}</th>
                <th data-breakpoints="lg"
                    class="min-col text-center text-uppercase"
                    width="80">{{translate('VAT')}} {{ $order->vat_percentage }}%
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
                                <span
                                    style="font-size: 0.840rem ">{{ translate('Snacks per day') }}: {{ $plan_purchase->snack_quantity }}</span>
                            @endif
                            @if($plan_purchase!=null && $plan_purchase->meal_quantity>0)
                                <br>
                                <span
                                    style="font-size: 0.840rem ">{{ translate('Lunches per day') }}: {{ $plan_purchase->meal_quantity }}</span>
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
    <div class="d-flex justify-content-right" style="padding:0 1.5rem;">
        <table border="0" class="text-right small strong">
            <thead>
            <tr>
                <th width="80%"></th>
                <th width="20%"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                </td>
                <td>
                </td>
            </tr>

            <tr>
                <th class="gry-color text-right" style="text-align: right !important;"
                    align="right">{{ translate('Sub Total') }}</th>
                <td class="currency">{{ single_price($order->subtotal) }}</td>
            </tr>
            <tr>
                <th class="gry-color text-right" style="text-align: right !important;"
                    align="right">{{ translate('Total VAT') }} {{ $order->vat_percentage }}%
                </th>
                <td class="currency">{{ single_price($order->vat_amount) }}</td>
            </tr>

            <tr>
                <th class="text-right strong" style="text-align: right !important;"
                    align="right">{{ translate('Grand Total') }}</th>
                <td class="currency">{{ single_price($order->grand_total) }}</td>
            </tr>
            </tbody>

        </table>
    </div>
</div>
</body>
</html>
