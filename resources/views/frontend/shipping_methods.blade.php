@if(isset($country_id))
    @if($country_id == '54')
        @php
            $total_weight = Session::get('total_weight_cart');

            $has_delivery_methods = false;

            $courier_cost = \App\ShippingWeightRangeCost::where('from', '<=', $total_weight)->where('to', '>=', $total_weight)->first();
            $acs_cost = \App\ShippingWeightRangeAcsCost::where('from', '<=', $total_weight)->where('to', '>=', $total_weight)->first();
            if($courier_cost!=null || $acs_cost!=null) {
              $has_delivery_methods = true;
            }
        @endphp
        @if(\App\BusinessSetting::where('type', 'pickup_point')->first()->value == 1)
            <div class="col-lg-4">
                <label class="sk-megabox megabox-shipping-method d-block bg-white mb-0">
                    <input
                            type="radio"
                            name="shipping_type"
                            value="pickup_point"
                            onchange="show_pickup_point(this)"
                            data-target=".pickup_point_id_admin"
                            {{ $selected_shipping_method == 'pickup_point' ? 'checked' : '' }}
                    >
                    <span class="d-block px-10px py-5px py-md-10px sk-megabox-elem with-border rounded-0">
                        <div class="d-flex align-items-center">
                            <div class="sk-rounded-check flex-shrink-0 mt-1"></div>
                            <div class="flex-grow-1 pl-3">{{  translate('Pick up from our Stores') }}</div>
                            <span id="pickup_point_amount" class="megabox-delivery-price">FREE</span>
                        </div>
                        <div class="mt-10px border-top border-secondary pt-10px fw-400 pickup_point_id_admin {{ $selected_shipping_method == 'pickup_point' ? '' : 'd-none' }}">
                            <select
                                    class="form-control fs-13 md-fs-16 sk-selectpicker"
                                    name="pickup_point_id"
                                    data-live-search="false"
                            >
                                <option value="">{{ translate('Select your nearest store')}}</option>
                                @foreach (\App\PickupPoint::where('pick_up_status',1)->get() as $key => $pick_up_point)
                                    <option value="{{ $pick_up_point->id }}" {{ $pick_up_point->id == $selected_pickup_point ? 'selected' : '' }}>{{ $pick_up_point->getTranslation('name') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </span>
                </label>
            </div>
        @endif
        @if($courier_cost!=null)
            <div class="col-lg-4 mt-10px mt-lg-0">
                <label class="sk-megabox megabox-shipping-method d-block bg-white mb-0">
                    <input
                            type="radio"
                            name="shipping_type"
                            value="home_delivery"
                            onchange="show_pickup_point(this)"
                            data-target=".pickup_point_id_admin"
                            {{ $selected_shipping_method == 'home_delivery' ? 'checked' : '' }}
                    >
                    <span class="d-flex align-items-center px-10px py-5px py-md-10px sk-megabox-elem with-border rounded-0">
                        <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                        <span class="flex-grow-1 pl-3">{{  translate('Home Delivery') }}</span>
                        <span id="home_delivery_amount" class="megabox-delivery-price">{{single_price(calcVatPrice($courier_cost->price))}}</span>
                    </span>
                </label>
            </div>
        @endif
        @if($acs_cost!=null)
            <div class="col-lg-4 mt-10px mt-lg-0">
                <label class="sk-megabox megabox-shipping-method d-block bg-white mb-0">
                    <input
                        type="radio"
                        name="shipping_type"
                        value="acs_delivery"
                        onchange="show_pickup_point(this)"
                        data-target=".pickup_point_id_admin"
                        {{ $selected_shipping_method == 'acs_delivery' ? 'checked' : '' }}
                    >
                    <span class="d-flex align-items-center px-10px py-5px py-md-10px sk-megabox-elem with-border rounded-0">
                        <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                        <span class="flex-grow-1 pl-3">{{  translate('Collect from ACS Courier') }}</span>
                        <span id="acs_delivery_amount" class="megabox-delivery-price">{{single_price(calcVatPrice($acs_cost->price))}}</span>
                    </span>
                </label>
            </div>
        @endif
    @else
        @if($parcels_epg_status)
        <div class="col-lg-4 mt-10px mt-lg-0">
            <label class="sk-megabox megabox-shipping-method d-block bg-white mb-0">
                <input
                    type="radio"
                    name="shipping_type"
                    value="epg_parcels"
                    {{ $parcels_epg_status && $selected_shipping_method == 'epg_parcels' ? 'checked' : '' }}
                    {{ !$parcels_epg_status ? 'disabled' : ''}}
                >
                <span class="d-flex align-items-center px-10px py-5px py-md-10px sk-megabox-elem with-border rounded-0">
                    <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                    <span class="flex-grow-1 pl-3">Parcels</span>
                    <span id="epg_parcels_amount" class="megabox-delivery-price">{{ format_price($parcels_epg_cost) }}</span>
                </span>
            </label>
        </div>
        @endif
        @if($ems_datapost_status)
        <div class="col-lg-4 mt-10px mt-lg-0">
            <label class="sk-megabox megabox-shipping-method d-block bg-white mb-0">
                <input
                    type="radio"
                    name="shipping_type"
                    value="ems_datapost"
                    {{ $ems_datapost_status && $selected_shipping_method == 'ems_datapost' ? 'checked' : '' }}
                    {{ !$ems_datapost_status ? 'disabled' : ''}}
                >
                <span class="d-flex align-items-center px-10px py-5px py-md-10px sk-megabox-elem with-border rounded-0">
                    <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                    <span class="flex-grow-1 pl-3">EMS Datapost</span>
                    <span id="ems_datapost_amount" class="megabox-delivery-price">{{ format_price($ems_datapost_cost) }}</span>
                </span>
            </label>
        </div>
        @endif
        @if(!$parcels_epg_status && !$ems_datapost_status)
            <div class="col-lg-12 mb-20px">
                {{translate('Your order can not be sent due to the package weight. Please remove some items so you can proceed.')}}
            </div>
        @endif
    @endif
@else
    <div class="col-lg-4">
        <h3 class="text-black-30 fs-11 md-fs-16 fw-600 mb-0">Please Select Shipping Address</h3>
    </div>
@endif
