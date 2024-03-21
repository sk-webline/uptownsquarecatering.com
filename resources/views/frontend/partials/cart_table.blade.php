<?php

use Carbon\Carbon;
use App\Models\PlatformSetting;

$vat = PlatformSetting::where('type', 'vat_percentage')->first()->value;

$total =0;
$total_vat=0;
$total_price =0;

?>

@if( Session::has('cart') && count(Session::get('cart')) > 0 )
    <div class="text-primary-60 pb-5px pb-md-10px fs-14 md-fs-16 border-bottom border-primary-100 border-width-2">
        <div class="row gutters-5">
            <div class="col col-lg-grow-360px">
                {{toUpper(translate('Subscriptions'))}}
            </div>
            <div class="col col-lg-120px d-none d-lg-block">
                {{toUpper(translate('Price'))}}
            </div>
            <div class="col col-lg-120px text-center d-none d-lg-block">
                {{toUpper(translate('VAT'))}} {{$vat}}%
            </div>
            <div class="col col-lg-120px text-right d-none d-lg-block">
                {{toUpper(translate('Amount'))}}
            </div>
        </div>
    </div>

    @foreach(Session::get('cart') as $key => $cart_item)
        <div class="border-bottom border-primary-100 border-width-2 py-5px py-md-15px cart-res-item">
            <div class="row gutters-5 align-items-center">
                <div class="col-12 col-lg-grow-360px">
                    <div class="d-md-none mb-10px lh-1 text-right">
                        <a href="javascript:void(0)" onclick="removeFromCartView(event, {{ $key }})" class="text-primary-40 hov-text-primary border-bottom border-inherit fs-10 fw-500 cart-trash-action">{{ toUpper(translate('Delete')) }}</a>
                    </div>
                    <div class="row gutters-10">
                        <div class="col-12 col-lg-250px col-xl-360px">
                            <div class="border border-primary border-width-2">
                                <div class="w-100 border-bottom border-primary border-width-2 p-2 fw-700 fs-16 lg-fs-14 xl-fs-18">
                                    {{toUpper($cart_item['name'])}}
                                </div>
                                <div class="p-2 fs-14 text-primary-50">
                                    @if($cart_item['snack_num']>0)
                                        <div>
                                            <span class="fs-16 pr-1 fw-700">{{ toUpper(translate('Snack')) }}:</span>
                                            {{ $cart_item['snack_num'] }}
                                            @if($cart_item['snack_num']==1)
                                                {{ translate('Snack per day') }}
                                            @else
                                                {{ translate('Snacks per day') }}
                                            @endif
                                        </div>
                                    @endif
                                    @if($cart_item['meal_num']>0)
                                        <div class="mt-10px">
                                            <span class="fs-16 pr-1 fw-700">{{ toUpper(translate('Lunch')) }}:</span>
                                            {{ $cart_item['meal_num'] }}
                                            @if($cart_item['meal_num']==1)
                                                {{ translate('Lunch per day') }}
                                            @else
                                                {{ translate('Lunches per day') }}
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-grow-250px col-xl-grow-360px d-flex justify-content-between flex-column lh-1 mt-15px mt-md-0">
                            <div class="mb-10px d-none d-md-block">
                                <a href="javascript:void(0)" onclick="removeFromCartView(event, {{ $key }})" class="text-primary-40 hov-text-primary border-bottom border-inherit fs-10 fw-500 cart-trash-action">{{ toUpper(translate('Delete')) }}</a>
                            </div>
                            <div>
                                <?php
                                $card = \App\Models\Card::findorfail($cart_item['card_id']);
                                $card_name = $card->name;
                                $start_date = Carbon::create($cart_item['from_date'])->format('d/m/Y');
                                $end_date = Carbon::create($cart_item['to_date'])->format('d/m/Y');
                                ?>
                                <div class="fs-16 mb-10px">{{ toUpper(translate('To')) }}: {{toUpper($card_name)}}</div>
                                <div class="fs-14 fw-500 text-primary-60">{{$start_date}} - {{$end_date}}</div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 d-lg-none">
                    <div class="border-top border-width-2 border-primary-200 mt-10px"></div>
                </div>
                    <?php
                    $vat_temp = 1 + ($vat/100);

                    $temp =round($cart_item['price'] / $vat_temp, 2);

                    $vat_value = $cart_item['price'] - $temp;

                    $price = $cart_item['price'] - $vat_value;
                    ?>
                <div class="col-12 col-lg-120px text-primary-50 mt-10px mt-lg-0">
                    <div class="row">
                        <div class="col d-lg-none">{{toUpper(translate('Price'))}}</div>
                        <div class="col-auto col-lg-12">
                            {{format_price($cart_item['price'])}}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-120px text-primary-50 text-lg-center mt-10px mt-lg-0">
                    <div class="row">
                        <div class="col d-lg-none">{{toUpper(translate('VAT'))}} {{$vat}}%</div>
                        <div class="col-auto col-lg-12">
                            {{format_price($cart_item['tax'])}}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-120px text-primary-50 text-lg-right mt-10px mt-lg-0">
                    <div class="row">
                        <div class="col d-lg-none">{{toUpper(translate('Amount'))}}</div>
                        <div class="col-auto col-lg-12 fw-700">
                            <span class="text-primary d-lg-none">{{format_price($cart_item['total'])}}</span>
                            <span class="d-none d-lg-inline">{{format_price($cart_item['total'])}}</span>
                        </div>
                    </div>
                </div>
                <?php
                $total_price = $total_price + $cart_item['price'];
                $total_vat = $total_vat + $cart_item['tax'] ;
                $total = $total + ($cart_item['total']);
                ?>
            </div>
        </div>
    @endforeach

    @php
        Session::put('total', $total);
        Session::put('subtotal', $total_price);
        Session::put('vat_amount', $total_vat);
    @endphp

    <div class="mt-10px mt-md-15px">
        <div class="row gutters-5">
            <div class="col-12 col-lg-360px order-lg-2">
                <div class="text-primary-50">
                    <div class="row align-items-center mb-10px mb-md-20px">
                        <div class="col fs-14 md-fs-16">
                            {{toUpper(translate('Subtotal'))}}
                        </div>
                        <div class="col-auto fw-700">
                            {{format_price($total_price)}}
                        </div>
                    </div>
                    <div class="row align-items-center mb-10px mb-md-15px">
                        <div class="col fs-14 md-fs-16">
                            {{toUpper(translate('VAT'))}} {{$vat}}%
                        </div>
                        <div class="col-auto fw-700">
                            {{format_price($total_vat)}}
                        </div>
                    </div>
                </div>

                <div class="border-bottom border-width-2 border-primary-100 mb-10px mb-md-15px"></div>

                <div class="row align-items-end fs-16 fw-700 mb-10px mb-md-15px">
                    <div class="col">
                        {{toUpper(translate('Total'))}}
                    </div>
                    <div class="col-auto fs-22 md-fs-30">
                        {{format_price($total)}}
                    </div>
                </div>
                <div class="border-bottom border-width-2 border-primary mb-15px mb-md-20px d-lg-none"></div>

                <div class="text-primary-60 fs-14 md-fs-16 position-relative mb-15px">
                    <label class="sk-checkbox m-0">
                        <input type="checkbox" required id="agree_checkbox" onchange="hide_checkout_error()">
                        <span class="sk-square-check big"></span>
                        {{ translate('I agree with the')}}
                        <a class="text-reset hov-text-primary" href="{{ route('termspolicies') }}" target="_blank">{{ translate('Terms&Policies')}}</a>
                    </label>
                    <div id="checkout-error-agree" class="invalid-feedback absolute fs-10 d-block mb-0" role="alert"></div>
                </div>

                <button class="btn btn-primary btn-block fw-500 py-10px py-md-13px fs-16 md-fs-18" onclick="submitOrder(this)">
                    {{toUpper(translate('Continue to payment'))}}
                </button>
            </div>
            <div class="col-12 col-lg-grow-360px order-lg-1 mt-10px">
                <img class="h-15px" src="{{static_asset('assets/img/icons/viva_wallet.svg')}}" alt="">
            </div>
        </div>
    </div>
@else
    <div class="text-center my-100px text-default-50">
        <i class="las la-frown la-3x mb-3"></i>
        <h3 class="fw-700 fs-16">{{translate('Your Cart is empty')}}</h3>
    </div>
@endif

{{--<div class="container">--}}
{{--    <div class="mx-auto mw-1350px">--}}
{{--        @if( Session::has('cart') && count(Session::get('cart')) > 0 )--}}
{{--            <div class="border-bottom border-default-300 border-width-3 text-black-30 fw-600 fs-11 md-fs-16 pb-10px">--}}
{{--                <div class="row gutters-5">--}}
{{--                    <div class="col-lg-grow-400px">{{ translate('Product')}}</div>--}}
{{--                    <div class="d-none d-lg-block col-lg-100px">{{ translate('Price')}}</div>--}}
{{--                    <div class="d-none d-lg-block col-lg-100px text-center">{{ translate('VAT')}} {{VatPercentage()}}%</div>--}}
{{--                    <div class="d-none d-lg-block col-lg-100px text-center">{{ translate('Qty')}}</div>--}}
{{--                    <div class="d-none d-lg-block col-lg-100px text-right">{{ translate('Total')}}</div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <ul class="list-group list-group-flush text-black">--}}
{{--                @php--}}
{{--                    $total = 0;--}}
{{--                    $checkProductStock = \App\Http\Controllers\CartController::checkProductStock();--}}
{{--                @endphp--}}
{{--                @foreach (Session::get('cart') as $key => $cartItem)--}}
{{--                    @php--}}
{{--                        $product = \App\Product::find($cartItem['id']);--}}
{{--                        if($product==null) {--}}
{{--                          removeFromCart($key);--}}
{{--                        }--}}
{{--                        $total = $total + (($cartItem['price']+calcPriceBeforeAddVat($cartItem['price'], "vat"))*$cartItem['quantity']);--}}
{{--                        $product_name_with_choice = $product->getTranslation('name');--}}
{{--                        if ($cartItem['variant'] != null) {--}}
{{--                            $product_name_with_choice .= ' - '.getStrFromProductVariant($product, $cartItem['variant']);--}}

{{--                            $product_stock = \App\ProductStock::where('product_id', $product->id)->where('variant', $cartItem['variant'])->first();--}}
{{--                            $part_number = $product_stock->part_number;--}}
{{--                            $stock = $product_stock->qty;--}}
{{--                        }--}}
{{--                        else {--}}
{{--                            $part_number = $product->part_number;--}}
{{--                            $stock = $product->current_stock;--}}
{{--                        }--}}
{{--//                                    $part_number = ($cartItem['variant'] != null) ? \App\ProductStock::where('product_id', $product->id)->where('variant', $cartItem['variant'])->first()->part_number : $product->part_number;--}}
{{--                    @endphp--}}
{{--                    <li class="list-group-item px-0 py-10px py-lg-15px @if(array_key_exists($key, $checkProductStock) && $checkProductStock[$key]['available_stock'] == 0) cart-res-out-of-stock @endif">--}}
{{--                        <div class="row gutters-5 align-items-center">--}}
{{--                            <div class="col-12 col-lg-grow-400px">--}}
{{--                                <div class="row gutters-5 align-items-center">--}}
{{--                                    <div class="col-auto">--}}
{{--                                        <a href="{{ route('product', $product->slug) }}">--}}
{{--                                            --}}{{-- George vale to class low-out-stock-image se if statement otan ine low or out of stock --}}
{{--                                            <span class="d-block cart-res-image @if(array_key_exists($key, $checkProductStock) && $checkProductStock[$key]['available_stock'] > 0) low-out-stock-image @endif">--}}
{{--                                                <img--}}
{{--                                                    class="img-contain size-65px h-md-90px w-md-110px lazyload"--}}
{{--                                                    src="{{ static_asset('assets/img/placeholder.jpg') }}"--}}
{{--                                                    data-src="{{ uploaded_asset($product->thumbnail_img) }}"--}}
{{--                                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"--}}
{{--                                                    alt=""--}}
{{--                                                >--}}
{{--                                                @if(array_key_exists($key, $checkProductStock))--}}
{{--                                                    @if($checkProductStock[$key]['available_stock'] == 0)--}}
{{--                                                        <span class="d-block cart-res-image-label low-out-stock-label">{{toUpper(translate('Out of Stock'))}}</span>--}}
{{--                                                    @else--}}
{{--                                                        <span class="d-block cart-res-image-label low-out-stock-label">{{toUpper(translate('Low Stock'))}}</span>--}}
{{--                                                    @endif--}}
{{--                                                @endif--}}
{{--                                            </span>--}}
{{--                                        </a>--}}
{{--                                    </div>--}}
{{--                                    <div class="col">--}}
{{--                                        <div class="row align-items-center">--}}
{{--                                            <div class="col-xl">--}}
{{--                                                <h5 class="fs-13 md-fs-16 fw-600 mb-0"><a class="text-truncate" href="{{ route('product', $product->slug) }}">{{ $product_name_with_choice }}</a></h5>--}}

{{--                                                @if(isset($cartItem['cyprus_shipping_only']) && $cartItem['cyprus_shipping_only'])--}}
{{--                                                    <h6 class="text-default-80 fs-10 md-fs-12 fw-500 l-space-1-2 mb-2px"><a href="{{ route('product', $product->slug) }}">{{translate('Part Number')}}: {{$part_number}}</a></h6>--}}
{{--                                                    <div>--}}
{{--                                                        <div class="cyprus-shipping-label fs-10 md-fs-12">--}}
{{--                                                            <img src="{{static_asset('assets/img/icons/red-location.svg')}}" alt=""> {{translate('Delivery in Cyprus ONLY')}}--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                @else--}}
{{--                                                    <h6 class="text-default-80 fs-10 md-fs-12 fw-500 l-space-1-2 mb-5px mb-md-15px"><a href="{{ route('product', $product->slug) }}">{{translate('Part Number')}}: {{$part_number}}</a></h6>--}}
{{--                                                @endif--}}
{{--                                                <a href="javascript:void(0)" onclick="removeFromCartView(event, {{ $key }})" class="hov-text-primary fs-10 md-fs-12 fw-600 text-black-30 cart-trash-action">--}}
{{--                                                    <svg class="size-10px mr-1 align-baseline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10">--}}
{{--                                                        <use xlink:href="{{static_asset('assets/img/icons/trash-icon.svg')}}#content"></use>--}}
{{--                                                    </svg>--}}
{{--                                                    {{translate('Delete')}}--}}
{{--                                                </a>--}}
{{--                                            </div>--}}
{{--                                            @if(array_key_exists($key, $checkProductStock))--}}
{{--                                                <div class="col-xl-6 text-center mt-10px mt-xl-0 d-none d-lg-block">--}}
{{--                                                    <div class="cart-res-box">--}}
{{--                                                        @if($checkProductStock[$key]['available_stock'] == 0)--}}
{{--                                                            {{translate('Unfortunately this item run our of stock.')}}--}}
{{--                                                            <span>{{translate('Please remove it from your cart.')}}</span>--}}
{{--                                                        @else--}}
{{--                                                            {{translate('The quantity of the item is lower than the quantity you requested.')}}--}}
{{--                                                            <span>{{translate('Please remove some items')}}</span>--}}
{{--                                                        @endif--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            @endif--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                @if(array_key_exists($key, $checkProductStock))--}}
{{--                                    <div class="mt-15px text-center d-lg-none">--}}
{{--                                        <div class="cart-res-box">--}}
{{--                                            @if($checkProductStock[$key]['available_stock'] == 0)--}}
{{--                                                {{translate('Unfortunately this item run our of stock.')}}--}}
{{--                                                <span>{{translate('Please remove it from your cart.')}}</span>--}}
{{--                                            @else--}}
{{--                                                {{translate('The quantity of the item is lower than the quantity you requested.')}}--}}
{{--                                                <span>{{translate('Please remove some items')}}</span>--}}
{{--                                            @endif--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                @endif--}}
{{--                                <div class="d-lg-none cart-res-column-border pb-10px mb-10px fs-11 md-fs-16 fw-600 text-black-30 mt-15px card-inactive-on-out-of-stock">--}}
{{--                                    <div class="row gutters-5 align-items-end">--}}
{{--                                        <div class="col-3">--}}
{{--                                            {{ translate('Price')}}--}}
{{--                                        </div>--}}
{{--                                        <div class="col-3 text-center">--}}
{{--                                            {{ translate('VAT')}} {{VatPercentage()}}%--}}
{{--                                        </div>--}}
{{--                                        <div class="col-3 text-center">--}}
{{--                                            {{ translate('Qty')}}--}}
{{--                                        </div>--}}
{{--                                        <div class="col-3 text-right">--}}
{{--                                            {{ translate('Total')}}--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-3 col-lg-100px card-inactive-on-out-of-stock">--}}
{{--                                <span class="fw-600">{{ single_price($cartItem['price']) }}</span>--}}
{{--                            </div>--}}
{{--                            <div class="col-3 col-lg-100px text-center card-inactive-on-out-of-stock">--}}
{{--                                <span class="fw-600">{{ single_price(calcPriceBeforeAddVat($cartItem['price'], "vat")) }}</span>--}}
{{--                            </div>--}}
{{--                            <div class="col-3 col-lg-100px text-default text-center card-inactive-on-out-of-stock">--}}
{{--                                @if($cartItem['digital'] != 1)--}}
{{--                                    <div class="row no-gutters align-items-center sk-plus-minus border border-default-300 mx-auto mw-60px md-mw-80px">--}}
{{--                                        <button class="btn col-auto fs-16 md-fs-20 fw-500 lh-1 p-1" type="button" data-type="minus" data-field="quantity[{{ $key }}]">--}}
{{--                                            ---}}
{{--                                        </button>--}}
{{--                                        <input type="text" name="quantity[{{ $key }}]" class="col border-0 text-center flex-grow-1 fs-13 md-fs-18 fw-500 input-number" placeholder="1" value="{{ $cartItem['quantity'] }}" min="1" max="{{ $stock }}" readonly onchange="updateQuantity({{ $key }}, this)">--}}
{{--                                        <button class="btn col-auto fs-16 md-fs-20 fw-500 lh-1 p-1" type="button" data-type="plus" data-field="quantity[{{ $key }}]">--}}
{{--                                            +--}}
{{--                                        </button>--}}
{{--                                    </div>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                            <div class="col-3 col-lg-100px text-right card-inactive-on-out-of-stock">--}}
{{--                                <span class="fw-600 text-secondary">{{ single_price(calcPriceBeforeAddVat($cartItem['price'])*$cartItem['quantity']) }}</span>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </li>--}}
{{--                @endforeach--}}
{{--            </ul>--}}
{{--            <div class="row justify-content-end mt-5px">--}}
{{--                <div class="col-12 col-lg-auto">--}}
{{--                    <div class="w-lg-390px">--}}
{{--                        <div class="row align-items-end justify-content-between mb-15px mb-md-65px">--}}
{{--                            <div class="col-auto text-black-30 fw-600 text-black-30 fs-14 md-fs-16">{{translate('Subtotal')}}</div>--}}
{{--                            <div class="col-auto fs-18 md-fs-25 fw-700 font-play text-secondary">{{ single_price($total) }}</div>--}}
{{--                        </div>--}}
{{--                        @if(Auth::check() || getSelectedCheckoutType() == 'guest')--}}
{{--                            <a href="{{ route('checkout.shipping_info') }}" class="btn btn-secondary btn-block fs-13 md-fs-18 py-10px py-md-13px" @if(!empty($checkProductStock)) disabled @endif>{{toUpper(translate('Continue to Shipping'))}}</a>--}}
{{--                        @else--}}
{{--                            <button class="btn btn-secondary btn-block fs-13 md-fs-18 py-10px py-md-13px side-popup-toggle" data-rel="guest-checkout-side-popup" @if(!empty($checkProductStock)) disabled @endif>{{toUpper(translate('Continue to Shipping'))}}</button>--}}
{{--                        @endif--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        div>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--</div>--}}
