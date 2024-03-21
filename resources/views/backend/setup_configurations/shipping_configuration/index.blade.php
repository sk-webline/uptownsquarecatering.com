@extends('backend.layouts.app')

@section('content')
    

@if(hasAccessOnContent())
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Select Shipping Method')}}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('shipping_configuration.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="shipping_type">
                        <div class="radio mar-btm">
                            <input id="product-shipping" class="magic-radio" type="radio" name="shipping_type" value="product_wise_shipping" <?php if(\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'product_wise_shipping') echo "checked";?>>
                            <label for="product-shipping">
                                <span>{{translate('Product Wise Shipping Cost')}}</span>
                                <span></span>
                            </label>
                        </div>
                        <div class="radio mar-btm">
                            <input id="flat-shipping" class="magic-radio" type="radio" name="shipping_type" value="flat_rate" <?php if(\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'flat_rate') echo "checked";?>>
                            <label for="flat-shipping">{{translate('Flat Rate Shipping Cost')}}</label>
                        </div>
                        <div class="radio mar-btm">
                            <input id="seller-shipping" class="magic-radio" type="radio" name="shipping_type" value="seller_wise_shipping" <?php if(\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'seller_wise_shipping') echo "checked";?>>
                            <label for="seller-shipping">{{translate('Seller Wise Flat Shipping Cost')}}</label>
                        </div>
                        <div class="radio mar-btm">
                            <input id="area-shipping" class="magic-radio" type="radio" name="shipping_type" value="area_wise_shipping" <?php if(\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'area_wise_shipping') echo "checked";?>>
                            <label for="area-shipping">{{translate('Area Wise Flat Shipping Cost')}}</label>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Note')}}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            1. {{ translate('Product Wise Shipping Cost calulation: Shipping cost is calculate by addition of each product shipping cost') }}.
                        </li>
                        <li class="list-group-item">
                            2. {{ translate('Flat Rate Shipping Cost calulation: How many products a customer purchase, doesn\'t matter. Shipping cost is fixed') }}.
                        </li>
                        <li class="list-group-item">
                            3. {{ translate('Seller Wise Flat Shipping Cost calulation: Fixed rate for each seller. If customers purchase 2 product from two seller shipping cost is calculated by addition of each seller flat shipping cost') }}.
                        </li>
                        <li class="list-group-item">
                            4. {{ translate('Area Wise Flat Shipping Cost calulation: Fixed rate for each area. If customers purchase multiple products from one seller shipping cost is calculated by the customer shipping area. To configure area wise shipping cost go to ') }} <a href="{{ route('cities.index') }}">{{ translate('Shipping Cities') }}</a>.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>



    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Flat Rate Cost')}}</h5>
                </div>
                <form action="{{ route('shipping_configuration.update') }}" method="POST" enctype="multipart/form-data">
                  <div class="card-body">
                      @csrf
                      <input type="hidden" name="type" value="flat_rate_shipping_cost">
                      <div class="form-group">
                          <div class="col-lg-12">
                              <input class="form-control" type="text" name="flat_rate_shipping_cost" value="{{ \App\BusinessSetting::where('type', 'flat_rate_shipping_cost')->first()->value }}">
                          </div>
                      </div>
                      <div class="form-group mb-0 text-right">
                          <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                      </div>
                  </div>
                </form>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Note')}}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            {{ translate('1. Flat rate shipping cost is applicable if Flat rate shipping is enabled.') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif
<div class="row">

    <div class="col-lg-6">
        @if(hasAccessOnContent())
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Shipping Cost for Admin Products')}}</h5>
                </div>
                <form action="{{ route('shipping_configuration.update') }}" method="POST" enctype="multipart/form-data">
                  <div class="card-body">
                      @csrf
                      <input type="hidden" name="type" value="shipping_cost_admin">
                      <div class="form-group">
                          <div class="col-lg-12">
                              <input class="form-control" type="text" name="shipping_cost_admin" value="{{ \App\BusinessSetting::where('type', 'shipping_cost_admin')->first()->value }}">
                          </div>
                      </div>
                      <div class="form-group mb-0 text-right">
                          <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                      </div>
                  </div>
                </form>
            </div>
        @endif
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Handling Fees for International Shipping')}}</h5>
            </div>
            <form action="{{ route('shipping_configuration.update') }}" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                    @csrf
                    <input type="hidden" name="type" value="handling_fees_for_intern_shipp">
                    <div class="form-group">
                        <div class="col-lg-12">
                            <input class="form-control" type="number" step="0.01" min="0" name="handling_fees_for_intern_shipp" value="{{ \App\BusinessSetting::where('type', 'handling_fees_for_intern_shipp')->first()->value }}">
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Home Delivery Cost')}}</h5>
            </div>
            <form action="{{ route('shipping_weight_range_cost.update') }}" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                    @csrf
                    <div class="form-group">
                        <div class="row gutters-5 align-items-end">
                            <div class="col-md">
                                <div class="row gutters-5">
                                    <div class="col-lg-4">
                                        <label>{{ translate('From Weight (gr)') }}</label>
                                    </div>
                                    <div class="col-lg-4">
                                        <label>{{ translate('To Weight (gr)') }}</label>
                                    </div>
                                    <div class="col-lg-4">
                                        <label>{{ translate('Cost') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-auto">
                                <div class="form-group pointer-none opacity-0">
                                    <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" disabled>
                                        <i class="las la-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="shipping-range-costs">
                            @foreach (\App\ShippingWeightRangeCost::all() as $key => $shipping_cost)
                                <div class="row gutters-5 align-items-end">
                                    <div class="col-md">
                                        <div class="row gutters-5">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <input type="number" lang="en" min="0" class="form-control" placeholder="{{translate('From Weight (gr)')}}" name="from[]" value="{{ $shipping_cost->from }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <input type="number" lang="en" min="0" class="form-control" placeholder="{{translate('To Weight (gr)')}}" name="to[]" value="{{ $shipping_cost->to }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <input type="number" lang="en" min="0" step="0.01" class="form-control" placeholder="{{translate('Cost')}}" name="price[]" value="{{ $shipping_cost->price }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-auto">
                                        <div class="form-group">
                                            <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
                                                <i class="las la-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button
                            type="button"
                            class="btn btn-soft-secondary btn-sm mt-2"
                            data-toggle="add-more"
                            data-content='
							<div class="row gutters-5 align-items-end">
								<div class="col-md">
									<div class="row gutters-5">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <input type="number" lang="en" min="0" class="form-control" placeholder="{{translate('From Weight (gr)')}}" name="from[]" value="0" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <input type="number" lang="en" min="0" class="form-control" placeholder="{{translate('To Weight (gr)')}}" name="to[]" value="0" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <input type="number" lang="en" min="0" step="0.01" class="form-control" placeholder="{{translate('Cost')}}" name="price[]" value="0" required>
                                            </div>
                                        </div>
                                    </div>
								</div>
								<div class="col-md-auto">
									<div class="form-group">
										<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
											<i class="las la-times"></i>
										</button>
									</div>
								</div>
							</div>'
                            data-target=".shipping-range-costs">
                            {{ translate('Add New') }}
                        </button>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Cost for ACS')}}</h5>
            </div>
            <form action="{{ route('shipping_weight_range_cost_acs.update') }}" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                    @csrf
                    <div class="form-group">
                        <div class="row gutters-5 align-items-end">
                            <div class="col-md">
                                <div class="row gutters-5">
                                    <div class="col-lg-4">
                                        <label>{{ translate('From Weight (gr)') }}</label>
                                    </div>
                                    <div class="col-lg-4">
                                        <label>{{ translate('To Weight (gr)') }}</label>
                                    </div>
                                    <div class="col-lg-4">
                                        <label>{{ translate('Cost') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-auto">
                                <div class="form-group pointer-none opacity-0">
                                    <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" disabled>
                                        <i class="las la-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="anosis-range-costs">
                            @foreach (\App\ShippingWeightRangeAcsCost::all() as $key => $shipping_cost)
                                <div class="row gutters-5 align-items-end">
                                    <div class="col-md">
                                        <div class="row gutters-5">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <input type="number" lang="en" min="0" class="form-control" placeholder="{{translate('From Weight (gr)')}}" name="from[]" value="{{ $shipping_cost->from }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <input type="number" lang="en" min="0" class="form-control" placeholder="{{translate('To Weight (gr)')}}" name="to[]" value="{{ $shipping_cost->to }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <input type="number" lang="en" min="0" step="0.01" class="form-control" placeholder="{{translate('Cost')}}" name="price[]" value="{{ $shipping_cost->price }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-auto">
                                        <div class="form-group">
                                            <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
                                                <i class="las la-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button
                            type="button"
                            class="btn btn-soft-secondary btn-sm mt-2"
                            data-toggle="add-more"
                            data-content='
							<div class="row gutters-5 align-items-end">
								<div class="col-md">
									<div class="row gutters-5">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <input type="number" lang="en" min="0" class="form-control" placeholder="{{translate('From Weight (gr)')}}" name="from[]" value="0" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <input type="number" lang="en" min="0" class="form-control" placeholder="{{translate('To Weight (gr)')}}" name="to[]" value="0" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <input type="number" lang="en" min="0" step="0.01" class="form-control" placeholder="{{translate('Cost')}}" name="price[]" value="0" required>
                                            </div>
                                        </div>
                                    </div>
								</div>
								<div class="col-md-auto">
									<div class="form-group">
										<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
											<i class="las la-times"></i>
										</button>
									</div>
								</div>
							</div>'
                            data-target=".anosis-range-costs">
                            {{ translate('Add New') }}
                        </button>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @if(hasAccessOnContent())
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Note')}}</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        {{ translate('1. Shipping cost for admin is applicable if Seller wise shipping cost is enabled.') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection
