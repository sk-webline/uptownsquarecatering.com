<div class="px-15px px-xl-30px py-15px border-bottom">
    <h3 class="fs-14 sm-fs-16 fw-600 mb-0 lh-1-3">{{translate('Summary')}}</h3>
</div>

@if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
    @php
        $total_point = 0;
    @endphp
    @foreach (Session::get('cart') as $key => $cartItem)
        @php
            $product = \App\Product::find($cartItem['id']);
            $total_point += $product->earn_point*$cartItem['quantity'];
        @endphp
    @endforeach
    <div class="rounded px-2 mb-2 bg-soft-primary border-soft-primary border">
        {{ translate("Total Club point") }}:
        <span class="fw-700 float-right">{{ $total_point }}</span>
    </div>
@endif
@php
    sleep(2);
    if  (!Session::has('shipping_method')) {
        flash(translate('Please select shipping method'))->error();
        return redirect()->back();
    }

    $shipping = round(Session::get('shipping_method')['amount'], 2);
    $shipping_vat = round(Session::get('shipping_method')['vat'], 2);
    $product_shipping_cost = 0;
    $shipping_region = Session::get('shipping_info')['city'];
    $subtotal = 0;
    $tax = $shipping_vat;
@endphp
@foreach (Session::get('cart') as $key => $cartItem)
    @php
        $product = \App\Product::find($cartItem['id']);
        $subtotal += $cartItem['quantity'] * $cartItem['price'];
        $tax += calcPriceBeforeAddVat($cartItem['price'], "vat")*$cartItem['quantity'];

        if(isset($cartItem['shipping']) && is_array(json_decode($cartItem['shipping'], true))) {
            foreach(json_decode($cartItem['shipping'], true) as $shipping_info => $val) {
                if($shipping_region == $shipping_info) {
                    $product_shipping_cost = (double) $val;
                }
            }
        } else {
            $product_shipping_cost = (double) $cartItem['shipping'];
        }

        if($product->is_quantity_multiplied == 1 && get_setting('shipping_type') == 'product_wise_shipping') {
            $product_shipping_cost = $product_shipping_cost * $cartItem['quantity'];
        }

        $shipping += $product_shipping_cost;

        $product_name_with_choice = $product->getTranslation('name');
        if ($cartItem['variant'] != null) {
            $product_name_with_choice .= ' - '.getStrFromProductVariant($product, $cartItem['variant']);
        }

        $part_number = ($cartItem['variant'] != null) ? \App\ProductStock::where('product_id', $product->id)->where('variant', $cartItem['variant'])->first()->part_number : $product->part_number;
    @endphp
    <div class="cart_item px-15px px-xl-30px py-10px py-sm-15px border-bottom">
        <div class="row gutters-5 align-items-center">
            <div class="col-8">
                <h3 class="text-black fs-13 sm-fs-16 fw-600 lh-1 mb-5px text-truncate">{{ $product_name_with_choice }}</h3>
                <h4 class="fs-10 sm-fs-12 fw-500 sm-l-space-1-2 mb-10px text-default-80">{{translate('Part Number')}}: {{$part_number}}</h4>
                <div class="product-quantity text-black-40 fs-11 sm-fs-14">{{toUpper(translate('Qty'))}}: {{ $cartItem['quantity'] }}</div>
            </div>
            <div class="col-4 text-right fw-600 text-black">
                {{ single_price($cartItem['price']*$cartItem['quantity']) }}
            </div>
        </div>
    </div>
@endforeach
<div class="px-15px px-xl-30px py-5px py-sm-10px border-bottom fs-12 sm-fs-14 text-black">
    <div class="row align-items-center py-5px py-sm-10px">
        <div class="col-6">{{toUpper(translate('Subtotal'))}}</div>
        <div class="col-6 text-right">{{ single_price($subtotal) }}</div>
    </div>
    <div class="row align-items-center py-5px py-sm-10px">
        <div class="col-6">{{toUpper(translate('Shipping'))}}</div>
        <div class="col-6 text-right">{{ single_price($shipping) }}</div>
    </div>
    <div class="row align-items-center py-5px py-sm-10px">
        <div class="col-6">{{translate('V.A.T.')}} {{VatPercentage()}}%</div>
        <div class="col-6 text-right">{{ single_price($tax) }}</div>
    </div>
    @if (Session::has('club_point'))
        <div class="row align-items-center py-5px py-sm-10px">
            <div class="col-6">{{toUpper(translate('Redeem point'))}}</div>
            <div class="col-6 text-right">{{ single_price(Session::get('club_point')) }}</div>
        </div>
    @endif
    @if (Session::has('coupon_discount'))
        <div class="row align-items-center py-5px py-sm-10px">
            <div class="col-6">{{toUpper(translate('Coupon Discount'))}}</div>
            <div class="col-6 text-right">{{ single_price(Session::get('coupon_discount')) }}</div>
        </div>
    @endif
    @php
        $total = $subtotal+$tax+$shipping;

        if(Session::has('club_point')) {
            $total -= Session::get('club_point');
        }
        if(Session::has('coupon_discount')){
            $total -= Session::get('coupon_discount');
        }
        Session::put('total', $total);
        Session::put('subtotal', $subtotal);
        Session::put('vat_amount', $tax);
        Session::put('shipping', $shipping);
    @endphp
</div>
<div class="px-15px px-xl-30px py-10px border-bottom">
    <div class="row align-items-center py-sm-10px">
        <div class="col-6">{{translate('Total')}}</div>
        <div class="col-6 text-right fs-20 sm-fs-25 fw-700 font-play text-secondary">{{ single_price($total) }}</div>
    </div>
</div>

@if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
    @if (Session::has('club_point'))
        <div class="mt-3">
            <form class="" action="{{ route('checkout.remove_club_point') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="input-group">
                    <div class="form-control">{{ Session::get('club_point')}}</div>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">{{translate('Remove Redeem Point')}}</button>
                    </div>
                </div>
            </form>
        </div>
    @else
        {{--@if(Auth::user()->point_balance > 0)
            <div class="mt-3">
                <p>
                    {{translate('Your club point is')}}:
                    @if(isset(Auth::user()->point_balance))
                        {{ Auth::user()->point_balance }}
                    @endif
                </p>
                <form class="" action="{{ route('checkout.apply_club_point') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group">
                        <input type="text" class="form-control" name="point" placeholder="{{translate('Enter club point here')}}" required>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">{{translate('Redeem')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        @endif--}}
    @endif
@endif

@if (Auth::check() && \App\BusinessSetting::where('type', 'coupon_system')->first()->value == 1)
    @if (Session::has('coupon_discount'))
        <div class="mt-3">
            <form class="" action="{{ route('checkout.remove_coupon_code') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="input-group">
                    <div class="form-control">{{ \App\Coupon::find(Session::get('coupon_id'))->code }}</div>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">{{translate('Change Coupon')}}</button>
                    </div>
                </div>
            </form>
        </div>
    @else
        <div class="mt-3">
            <form class="" action="{{ route('checkout.apply_coupon_code') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="input-group">
                    <input type="text" class="form-control" name="code" placeholder="{{translate('Have coupon code? Enter here')}}" required>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">{{translate('Apply')}}</button>
                    </div>
                </div>
            </form>
        </div>
    @endif
@endif
