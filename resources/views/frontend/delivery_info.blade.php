@extends('frontend.layouts.app')

@section('content')
    <section class="checkout-steps mt-65px mt-md-100px mb-35px mb-sm-40px text-center text-md-left font-play overflow-hidden">
        <div class="container">
            <div class="mx-auto mw-1350px">
                <div class="row gutters-5 lg-gutters-15">
                    <div class="col-4">
                        <a href="{{ route('cart') }}" class="d-block px-2px px-md-10px px-xl-20px py-10px py-sm-15px checkout-step-box">
                            <span class="d-block fs-13 md-fs-16 xl-fs-18 fw-700 m-0">
                                <span class="fs-13 md-fs-18 xl-fs-20 mr-1 mr-md-2">01.</span>
                                {{ translate('My cart')}}
                            </span>
                        </a>
                    </div>
                    <div class="col-4">
                        <div class="px-2px px-md-10px px-xl-20px py-10px py-sm-15px checkout-step-box active">
                            <h3 class="fs-13 md-fs-16 xl-fs-18 fw-700 m-0">
                                <span class="fs-13 md-fs-18 xl-fs-20 mr-1 mr-md-2">02.</span>
                                <span class="d-none d-md-inline">{{ translate('Shipping & Delivery')}}</span>
                                <span class="d-md-none">{{ translate('Shipping')}}</span>
                            </h3>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="px-2px px-md-10px px-xl-20px py-10px py-sm-15px checkout-step-box">
                            <h3 class="fs-13 md-fs-16 xl-fs-18 fw-700 m-0">
                                <span class="fs-13 md-fs-18 xl-fs-20 mr-1 mr-md-2">03.</span>
                                {{ translate('Payment')}}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="mb-75px mb-lg-100px mb-xxl-150px">
        <div class="container">
            <div class="mx-auto mw-1350px delivery-info-content loader">
                @php
                    $admin_products = array();
                    $seller_products = array();
                    foreach (Session::get('cart') as $key => $cartItem){
                        if(\App\Product::find($cartItem['id'])->added_by == 'admin'){
                            array_push($admin_products, $cartItem['id']);
                        }
                        else{
                            $product_ids = array();
                            if(array_key_exists(\App\Product::find($cartItem['id'])->user_id, $seller_products)){
                                $product_ids = $seller_products[\App\Product::find($cartItem['id'])->user_id];
                            }
                            array_push($product_ids, $cartItem['id']);
                            $seller_products[\App\Product::find($cartItem['id'])->user_id] = $product_ids;
                        }
                    }
                @endphp

                @if (!empty($admin_products))
                    <form id="delivery_form" class="form-default" action="{{ route('checkout.store_delivery_info') }}" role="form" method="POST">
                        @csrf
                        <div class="border-bottom border-default-300 border-width-3 text-black-30 pb-10px">
                            <h5 class="fs-11 md-fs-16 fw-600 mb-0">{{ translate('Shipping Address') }}</h5>
                        </div>
                        @if(Auth::check())
                            <input type="hidden" name="checkout_type" value="logged">
                            <div class="row gutters-5 lg-gutters-15">
                                @foreach (Auth::user()->addresses as $key => $address)
                                    @php
                                    $checked = false;
                                    if($key == 0 || $address->set_default) {
                                      $checked = true;
                                    }
                                    @endphp
                                    <div class="col-sm-6 col-lg-4 mb-10px mb-md-15px mb-xl-25px">
                                        <div class="position-relative h-100">
                                            <label class="sk-megabox megabox-addresses d-block mb-0 h-100">
                                                <input type="radio" name="address_id" value="{{ $address->id }}" data-country="{{$address->country}}" data-city="{{$address->city}}" @if($checked) checked @endif>
                                                <span class="d-flex p-5px p-sm-10px sk-megabox-elem h-100 rounded-0">
                                                <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pr-4 pl-10px text-left text-default-50">
                                                    <span class="d-block">
                                                        <span class="text-default">{{ translate('Address') }}:</span> {{ $address->address }}
                                                    </span>
                                                    <span class="d-block">
                                                        <span class="text-default">{{ translate('Post Code') }}:</span> {{ $address->postal_code }}
                                                    </span>
                                                    <span class="d-block">
                                                        <span class="text-default">{{ translate('City') }}:</span> {{ getCityName($address->city) }}
                                                    </span>
                                                    <span class="d-block">
                                                        <span class="text-default">{{ translate('Country') }}:</span> {{ \App\Country::find($address->country)->name }}
                                                    </span>
                                                    <span class="d-block">
                                                        <span class="text-default">{{ translate('Phone') }}:</span> {{ $address->phone }}
                                                    </span>
                                                </span>
                                            </span>
                                            </label>
                                            <div class="position-absolute right-0 top-0">
                                                <a class="btn btn-icon hov-text-primary" onclick="edit_address('{{$address->id}}', '{{$address->city}}')">
                                                    <i class="las la-pen"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-sm-6 col-lg-4 mb-10px mb-md-15px mb-xl-25px">
                                    <div class="pt-lg-30px h-100">
                                        <div class="border border-inherit text-default-60 p-10px c-pointer text-center h-100 min-h-70px xxl-min-h-90px d-flex align-items-center justify-content-center side-popup-toggle" data-rel="new-address-modal">
                                            + {{ toUpper(translate('Add New Address')) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="checkout_type" value="guest">
                            @php
                                if($errors->has('name') || old('name')){
                                    $val_name = old('name');
                                } else {
                                    $full_name = $shipping_info['name'];
                                    $val_name_arr = getAccountName($full_name);
                                    $val_name = $val_name_arr['name'];
                                }
                                if($errors->has('surname') || old('surname')){
                                    $val_surname = old('surname');
                                } else {
                                    $full_surname = $shipping_info['name'];
                                    $val_surname_arr = getAccountName($full_surname);
                                    $val_surname = $val_surname_arr['surname'];
                                }
                                if ($errors->has('email') || old('email')){
                                    $val_email = old('email');
                                } else {
                                    $val_email = $shipping_info['email'];
                                }
                                if ($errors->has('address') || old('address')){
                                    $val_address = old('address');
                                } else {
                                    $val_address = $shipping_info['address'];
                                }
                                if ($errors->has('country') || old('country')){
                                    $val_country = old('country');
                                } else {
                                    $val_country = $shipping_info['country'];
                                }

                                if  ($val_country == '54') {
                                    if ($errors->has('city') || old('city')){
                                        $val_city = old('city');
                                    } else {
                                        $val_city = $shipping_info['city'];
                                    }
                                }
                                else {
                                    if ($errors->has('city_name') || old('city_name')){
                                        $val_city = old('city_name');
                                    } else {
                                        $val_city = $shipping_info['city'];
                                    }
                                }

                                if ($errors->has('postal_code') || old('postal_code')){
                                    $val_postal_code = old('postal_code');
                                } else {
                                    $val_postal_code = $shipping_info['postal_code'];
                                }
                                if ($errors->has('phone') || old('phone')){
                                    $val_phone = old('phone');
                                } else {
                                    $val_phone = $shipping_info['phone'];
                                }
                            @endphp
                            @php
                                $full_name = getAccountName($shipping_info['name']);
                            @endphp
                            <div class="row mt-15px mt-md-30px">
                                <div class="col-md-6">
                                    <div class="form-group mb-15px mb-md-25px">
                                        <div class="form-control-with-label small-focus-md @if($val_name) focused @endif ">
                                            <label>{{ translate('Name')}}</label>
                                            <input type="text" class="form-control fs-13 md-fs-16 form-no-space{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{$val_name}}">
                                        </div>
                                        @if ($errors->has('name'))
                                            <div class="invalid-feedback fs-10 md-fs-12 d-block" role="alert">
                                                {{ $errors->first('name') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-15px mb-md-25px">
                                        <div class="form-control-with-label small-focus-md @if($val_surname) focused @endif ">
                                            <label>{{ translate('Surname')}}</label>
                                            <input type="text" class="form-control fs-13 md-fs-16{{ $errors->has('surname') ? ' is-invalid' : '' }}" name="surname" value="{{$val_surname}}">
                                        </div>
                                        @if ($errors->has('surname'))
                                            <div class="invalid-feedback fs-10 md-fs-12 d-block" role="alert">
                                                {{ $errors->first('surname') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-15px mb-md-25px">
                                        <div class="form-control-with-label small-focus-md @if($val_email) focused @endif ">
                                            <label>{{ translate('Email')}}</label>
                                            <input type="text" class="form-control fs-13 md-fs-16{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{$val_email}}" @if(Auth::check()) readonly @endif>
                                        </div>
                                        @if ($errors->has('email'))
                                            <div class="invalid-feedback fs-10 md-fs-12 d-block" role="alert">
                                                {{ $errors->first('email') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-15px mb-md-25px">
                                        <div class="form-control-with-label small-focus-md @if($val_address) focused @endif ">
                                            <label>{{ translate('Address')}}</label>
                                            <input type="text" class="form-control fs-13 md-fs-16{{ $errors->has('address') ? ' is-invalid' : '' }}" name="address" value="{{$val_address}}">
                                        </div>
                                        @if ($errors->has('address'))
                                            <div class="invalid-feedback fs-10 md-fs-12 d-block" role="alert">
                                                {{ $errors->first('address') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-15px mb-md-25px">
                                        <div class="form-control-with-label small-focus-md always-focused">
                                            <label>{{ translate('Country')}}</label>
                                            <select id="country-user" class="form-control fs-13 md-fs-16 sk-selectpicker country-select {{ $errors->has('country') ? ' is-invalid' : '' }} val_country-{{ $val_country }}" data-live-search="true" name="country">
                                                @foreach (\App\Country::getActiveCountriesForShipping() as $key => $country)
                                                    <option value="{{ $country->id }}" @if($val_country == $country->id || (empty($val_country) && $country->id == 54)) selected @endif>{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if ($errors->has('country'))
                                            <div class="invalid-feedback fs-10 md-fs-12 d-block" role="alert">
                                                {{ $errors->first('country') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-15px mb-md-25px" data-rel="country-user">
                                        <div class="form-control-with-label small-focus-md always-focused">
                                            <label>{{ translate('City')}}</label>
                                            <select class="form-control fs-13 md-fs-16 sk-selectpicker{{ $errors->has('city') ? ' is-invalid' : '' }}" data-live-search="true" name="city">
                                                <option value="">{{translate('City')}}</option>
                                            </select>
                                            <input type="text" name="city_name" class="form-control fs-13 md-fs-16 {{ $errors->has('city_name') ? ' is-invalid' : '' }} d-none" disabled>
                                        </div>
                                        @if ($errors->has('city_name'))
                                            <div class="invalid-feedback fs-10 md-fs-12 d-block" role="alert">
                                                {{ $errors->first('city_name') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-15px mb-md-25px">
                                        <div class="form-control-with-label small-focus-md @if($val_postal_code) focused @endif ">
                                            <label>{{ translate('Postal code')}}</label>
                                            <input type="text" class="form-control fs-13 md-fs-16{{ $errors->has('postal_code') ? ' is-invalid' : '' }}" value="{{$val_postal_code}}" name="postal_code">
                                        </div>
                                        @if ($errors->has('postal_code'))
                                            <div class="invalid-feedback fs-10 md-fs-12 d-block" role="alert">
                                                {{ $errors->first('postal_code') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-15px mb-md-25px">
                                        <div class="form-control-with-label small-focus-md always-focused">
                                            <label>{{ translate('Phone')}}</label>
                                            <input type="number" lang="en" min="0" class="form-control form-control-phone fs-13 md-fs-16{{ $errors->has('phone') ? ' is-invalid' : '' }}" value="{{$val_phone}}" name="phone">
                                            <div class="form-control-phone-code fs-13 md-fs-16" data-rel="country-user"></div>
                                            <input type="hidden" name="phone_code" value="" data-rel="country-user">
                                        </div>
                                        @if ($errors->has('phone'))
                                            <div class="invalid-feedback fs-10 md-fs-12 d-block" role="alert">
                                                {{ $errors->first('phone') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if(\App\BusinessSetting::where('type', 'pickup_point')->first()->value == 1)
                            <div class="border-bottom border-default-300 border-width-3 text-black-30 pb-10px mb-15px mb-md-25px mt-15px">
                                <h5 class="fs-11 md-fs-16 fw-600 mb-0">{{ translate('Delivery Method') }}</h5>
                            </div>
                            <div id="shipping_methods" class="row fs-13 md-fs-16 fw-600">
                                @include('frontend.shipping_methods')
                            </div>
                        @endif
                        <div class="row justify-content-end mt-25px mt-md-30px">
                            <div class="col-lg-4">
                                <button type="submit" name="owner_id" value="{{ App\User::where('user_type', 'admin')->first()->id }}" class="btn btn-secondary btn-block fs-13 md-fs-18 py-10px py-md-13px px-5px" disabled>
                                    {{ toUpper(translate('Continue to Payment'))}}
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
                @if (!empty($seller_products))
                    <form class="form-default"  action="{{ route('checkout.store_delivery_info') }}" role="form" method="POST">
                        @csrf
                        @if(Auth::check())
                            <input type="hidden" name="checkout_type" value="logged">
                        @else
                            <input type="hidden" name="checkout_type" value="guest">
                        @endif
                        @foreach ($seller_products as $key => $seller_product)
                            <div class="card mb-3 shadow-sm border-0 rounded">
                                <div class="card-header p-3">
                                    <h5 class="fs-16 fw-600 mb-0">{{ \App\Shop::where('user_id', $key)->first()->name }} {{ translate('Products') }}</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        @foreach ($seller_product as $cartItem)
                                            @php
                                                $product = \App\Product::find($cartItem);
//                                            @endphp
                                            <li class="list-group-item">
                                                <div class="d-flex">
                                                    <span class="mr-2">
                                                        <img
                                                                src="{{ uploaded_asset($product->thumbnail_img) }}"
                                                                class="img-fit size-60px rounded"
                                                                alt="{{  $product->getTranslation('name')  }}"
                                                        >
                                                    </span>
                                                    <span class="fs-14 opacity-60">{{ $product->getTranslation('name') }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if (\App\BusinessSetting::where('type', 'pickup_point')->first()->value == 1)
                                        <div class="row border-top pt-3">
                                            <div class="col-md-6">
                                                <h6 class="fs-15 fw-600">{{ translate('Choose Delivery Type') }}</h6>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row gutters-5">
                                                    <div class="col-6">
                                                        <label class="sk-megabox d-block bg-white mb-0">
                                                            <input
                                                                    type="radio"
                                                                    name="shipping_type_{{ $key }}"
                                                                    value="home_delivery"
                                                                    onchange="show_pickup_point(this)"
                                                                    data-target=".pickup_point_id_{{ $key }}"
                                                                    checked
                                                            >
                                                            <span class="d-flex p-3 sk-megabox-elem">
                                                                    <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                                    <span class="flex-grow-1 pl-3 fw-600">{{  translate('Home Delivery') }}</span>
                                                                </span>
                                                        </label>
                                                    </div>
                                                    @if (is_array(json_decode(\App\Shop::where('user_id', $key)->first()->pick_up_point_id)))
                                                        <div class="col-6">
                                                            <label class="sk-megabox d-block bg-white mb-0">
                                                                <input
                                                                        type="radio"
                                                                        name="shipping_type_{{ $key }}"
                                                                        value="pickup_point"
                                                                        onchange="show_pickup_point(this)"
                                                                        data-target=".pickup_point_id_{{ $key }}"
                                                                >
                                                                <span class="d-flex p-3 sk-megabox-elem">
                                                                    <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                                    <span class="flex-grow-1 pl-3 fw-600">{{  translate('Local Pickup') }}</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                                @if (\App\BusinessSetting::where('type', 'pickup_point')->first()->value == 1)
                                                    @if (is_array(json_decode(\App\Shop::where('user_id', $key)->first()->pick_up_point_id)))
                                                        <div class="mt-4 pickup_point_id_{{ $key }} d-none">
                                                            <select
                                                                    class="form-control sk-selectpicker"
                                                                    name="pickup_point_id_{{ $key }}"
                                                                    data-live-search="true"
                                                            >
                                                                <option>{{ translate('Select your nearest pickup point')}}</option>
                                                                @foreach (json_decode(\App\Shop::where('user_id', $key)->first()->pick_up_point_id) as $pick_up_point)
                                                                    @if (\App\PickupPoint::find($pick_up_point) != null)
                                                                        <option
                                                                                value="{{ \App\PickupPoint::find($pick_up_point)->id }}"
                                                                                data-content="<span class='d-block'>
                                                                                        <span class='d-block fs-16 fw-600 mb-2'>{{ \App\PickupPoint::find($pick_up_point)->getTranslation('name') }}</span>
                                                                                        <span class='d-block opacity-50 fs-12'><i class='las la-map-marker'></i> {{ \App\PickupPoint::find($pick_up_point)->getTranslation('address') }}</span>
                                                                                        <span class='d-block opacity-50 fs-12'><i class='las la-phone'></i> {{ \App\PickupPoint::find($pick_up_point)->phone }}</span>
                                                                                    </span>"
                                                                        >
                                                                        </option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer justify-content-end">
                                    <button type="button" name="owner_id" value="{{ $key }}" class="btn fw-600 btn-primary">{{ translate('Continue to Payment')}}</button>
                                </div>
                            </div>
                        @endforeach

                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
@if(Auth::check())
    @section('modal')

    @include('frontend.user.address.addresses_modal_layout')
@endsection
@endif
@section('script')
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    @if(App::getLocale() == "gr")
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/localization/messages_el.min.js"></script>
    @endif
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script type="text/javascript">
        /*Validations*/
        $('#add-address-form').validate({
            errorClass: 'is-invalid',
            rules: {
                address: {
                    required: true,
                },
                country: {
                    required: true,
                },
                city: {
                    required: true,
                },
                city_name: {
                    required: true,
                },
                postal_code: {
                    required: true,
                },
                phone: {
                    required: true,
                    digits: true,
                    minlength: 8,
                },
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") === "address" ) {
                    $("#add_address_message").html(error);
                }
                else if (element.attr("name") === "country" ) {
                    $("#add_country_message").html(error);
                }
                else if (element.attr("name") === "city" ) {
                    $("#add_city_message").html(error);
                }
                else if (element.attr("name") === "city_name" ) {
                    $("#add_city_name_message").html(error);
                }
                else if (element.attr("name") === "postal_code" ) {
                    $("#add_postal_message").html(error);
                }
                else if (element.attr("name") === "phone" ) {
                    $("#add_phone_message").html(error);
                }
            }
        });

        function editValidate() {
            $('#edit-address-form').validate({
                errorClass: 'is-invalid',
                rules: {
                    address: {
                        required: true,
                    },
                    country: {
                        required: true,
                    },
                    city: {
                        required: true,
                    },
                    city_name: {
                        required: true,
                    },
                    postal_code: {
                        required: true,
                    },
                    phone: {
                        required: true,
                        digits: true,
                        minlength: 8,
                    },
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") === "address" ) {
                        $("#edit_address_message").html(error);
                    }
                    else if (element.attr("name") === "country" ) {
                        $("#edit_country_message").html(error);
                    }
                    else if (element.attr("name") === "city" ) {
                        $("#edit_city_message").html(error);
                    }
                    else if (element.attr("name") === "city_name" ) {
                        $("#edit_city_name_message").html(error);
                    }
                    else if (element.attr("name") === "postal_code" ) {
                        $("#edit_postal_message").html(error);
                    }
                    else if (element.attr("name") === "phone" ) {
                        $("#edit_phone_message").html(error);
                    }
                }
            });
        }

      function display_option(key){

      }
      show_pickup_point($("input[name='shipping_type']:checked"));


      function show_pickup_point(el) {
        var value = $(el).val();
        var target = $(el).data('target');

        // console.log(value);

        if(value == 'pickup_point'){
          $(target).removeClass('d-none');
        }else{
          if(!$(target).hasClass('d-none')){
            $(target).addClass('d-none');
          }
        }
      }

      function edit_address(address) {
        var url = '{{ route("addresses.edit", ":id") }}';
        url = url.replace(':id', address);

        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: url,
          type: 'GET',
          success: function (response) {
            $('#edit-address-modal-content').html(response);
            $('body').addClass('side-popup-opened');
            $('#edit-address-modal').addClass('active');
            SK.plugins.bootstrapSelect('refresh');
            var country = $("#country-edit").val();
            var country_rel = 'country-edit';
            if (country==='54') {
              $('[data-rel="'+ country_rel +'"] select[name="city"]').prop('disabled', false).prop('required', true).removeClass('d-none').parent('div').removeClass('d-none');
              $('[data-rel="'+ country_rel +'"] input[name="city_name"]').prop('disabled', true).prop('required', false).addClass('d-none');
              get_selected_city(country, $('[name="selected_city"]').val(), country_rel);
              SK.plugins.bootstrapSelect('refresh');
            } else {
              $('[data-rel="'+ country_rel +'"] select[name="city"]').prop('disabled', true).prop('required', false).addClass('d-none');
              $('[data-rel="'+ country_rel +'"] input[name="city_name"]').prop('disabled', false).prop('required', true).removeClass('d-none').attr('value', $('[name="selected_city"]').val());
              SK.plugins.bootstrapSelect('refresh');
            }
            get_phone_code(country, country_rel);
              editValidate();
          }
        });
      }

      @if(Auth::check())
      /*DO TO - Allazoun diaforetika ta shipping methods*/
      $(document).on('change', '[name=country]', function() {
        var country = $(this).val();
        var country_rel = $(this).attr('id');
        if (country==='54') {
          $('[data-rel="'+ country_rel +'"] select[name="city"]').prop('disabled', false).prop('required', true).removeClass('d-none').parent('div').removeClass('d-none');
          $('[data-rel="'+ country_rel +'"] input[name="city_name"]').prop('disabled', true).prop('required', false).addClass('d-none');
          get_city(country, country_rel);
          SK.plugins.bootstrapSelect('refresh');
        } else {
          $('[data-rel="'+ country_rel +'"] select[name="city"]').prop('disabled', true).prop('required', false).addClass('d-none');
          $('[data-rel="'+ country_rel +'"] input[name="city_name"]').prop('disabled', false).prop('required', true).removeClass('d-none');
          SK.plugins.bootstrapSelect('refresh');
        }
        get_phone_code(country, country_rel);
      });
      $(document).on('change', 'input[name=address_id]', function() {
        addLoader();
        get_shipping_methods($('[name="address_id"]:checked').data('country'), true);
      });
      @else
      $(document).on('change', '[name=country]', function() {
        addLoader();
        var country = $(this).val();
        var country_rel = $(this).attr('id');
        if (country==='54') {
          $('select[name="city"]').prop('disabled', false).removeClass('d-none').parent('div').removeClass('d-none');
          $('input[name="city_name"]').prop('disabled', true).addClass('d-none');
          get_city(country, country_rel);
          SK.plugins.bootstrapSelect('refresh');
        }
        else {
          $('select[name="city"]').prop('disabled', true).addClass('d-none');
          $('input[name="city_name"]').prop('disabled', false).removeClass('d-none');
          SK.plugins.bootstrapSelect('refresh');
        }
        get_phone_code(country, country_rel);
        get_shipping_methods(country, true);
      });
      @endif

      $(document).on('change', 'input[type="radio"][name="shipping_type"]', function() {
        let selected_shipping_method = $('input[type="radio"][name="shipping_type"]:checked').val();
        if (selected_shipping_method.length === 0 || selected_shipping_method === 'pickup_point') {
          disablePaymentButton();
          if (selected_shipping_method === 'pickup_point') {
            $('select[name="pickup_point_id"]').change();
          }
          return false;
        }
        set_shipping_method(selected_shipping_method);
        disablePaymentButton(false);
      });

      $(document).on('change', 'select[name="pickup_point_id"]', function() {
        let selected_pickup_point = $(this).val();
        if (selected_pickup_point.length === 0) {
          disablePaymentButton();
          return false;
        }
        set_shipping_method('pickup_point', selected_pickup_point);
        disablePaymentButton(false);
      });

      @if(Auth::check())
      /*TO DO*/
        $(document).ready(function() {
        $('[name="country"]').each(function () {
          var country_id = $(this).val();
          var country_rel = $(this).attr('id');
          get_phone_code(country_id, country_rel);
          get_selected_city(country_id, '{{Auth::user()->city ?? ''}}', country_rel);
        });
        $('.delivery-info-content').addClass('loader');
        setTimeout(function() {
            if ($("[name='address_id']:checked").length > 0) {
                get_shipping_methods($('[name="address_id"]:checked').data('country'), true);
            }
            else {
                $('.delivery-info-content').removeClass('loader');
            }
        }, 1000);
        /*else {
          get_shipping_methods({{Auth::user()->country ?? ''}});
        }*/
      });
      @else
      $(document).ready(function() {
        $('select[name=country]').change();
        $('[name="country"]').each(function () {
          var country_id = $(this).val();
          var country_rel = $(this).attr('id');
          get_phone_code(country_id, country_rel);
          get_selected_city(country_id, '{{ $val_city }}', country_rel);
          if(country_rel == "country-user") {
            get_shipping_methods(country_id);
          }
        });
      });
      @endif

      function get_selected_city(country, city, related) {
          if (!isNumber(city) || city.length == 0) {
              if (country==='54') {
                  $('select[name="city"]').prop('disabled', false).removeClass('d-none').parent('div').removeClass('d-none');
                  $('input[name="city_name"]').prop('disabled', true).addClass('d-none');
                  SK.plugins.bootstrapSelect('refresh');
              }
              else {
                  $('select[name="city"]').prop('disabled', true).addClass('d-none');
                  $('input[name="city_name"]').prop('disabled', false).removeClass('d-none').val(city);
                  SK.plugins.bootstrapSelect('refresh');
                  $('.delivery-info-content').removeClass('loader');
                  return false;
              }
          }
          $('[data-rel="'+ related +'"] [name="city"]').html("");
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{route('get-selected-city')}}",
          type: 'POST',
          data: {
            country_id: country,
            city_id: city
          },
          success: function (response) {
            var obj = JSON.parse(response);
            if(obj != '') {
              $('[data-rel="'+ related +'"] [name="city"]').html(obj);
              SK.plugins.bootstrapSelect('refresh');
            } else {
              SK.plugins.bootstrapSelect('refresh');
            }
            // $('.delivery-info-content').removeClass('loader');
          }
        });
      }

      function get_phone_code(country, related) {
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{route('get-phone-code')}}",
          type: 'POST',
          data: {
            country_id: country,
          },
          success: function (response) {
            var obj = JSON.parse(response);
            if(obj != '') {
              $('[name="phone_code"][data-rel="'+ related +'"]').attr('value', obj);
              $('.form-control-phone-code[data-rel="'+ related +'"]').text('+' + obj);
            }
          }
        });
      }

      function get_city(country, related) {
        $('[data-rel="' + related + '"] [name="city"]').html("");
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{route('get-city')}}",
          type: 'POST',
          data: {
            country_id: country
          },
          success: function (response) {
            var obj = JSON.parse(response);
            if(obj != '') {
              $('[data-rel="' + related + '"] [name="city"]').html(obj);
            }
            SK.plugins.bootstrapSelect('refresh');
          }
        });
      }

      function get_shipping_methods(country, remove_loader = false) {
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{route('checkout.get_shipping_methods')}}",
          type: 'POST',
          data: {
            country: country
          },
          dataType: "JSON",
          success: function (response) {
            $('#shipping_methods').html(response.shipping_methods);
            disablePaymentButton();
            if ($('input[name="shipping_type"]:checked').length > 0) {
              disablePaymentButton(false);
            }
            if (remove_loader) {
                setTimeout(function() {
                    SK.plugins.bootstrapSelect('refresh');
                    removeLoader();
                    $('.delivery-info-content').removeClass('loader');
                }, 500);
            }
            SK.plugins.bootstrapSelect('refresh');
          }
        });

      }

      function disablePaymentButton(status = true) {
        $('form#delivery_form button[name="owner_id"]').prop('disabled', status);
      }

      function set_shipping_method(shipping_method, pickup_point = null) {
        addLoader();
        $.post('{{ route('checkout.select_shipping_method') }}',{_token: '{{ csrf_token() }}', shipping_method:shipping_method, pickup_point:pickup_point}, function(data) {
          removeLoader();
        });
      }

      $(document).on('click', 'form#delivery_form button[name="owner_id"]', function(e) {
          e.preventDefault();
          submitShippingForm();
      });

      function submitShippingForm() {
          var selected_address = $('[name="address_id"]:checked');
          if ($('[name="address_id"]').length > 0) {
              if (selected_address.length < 1) {
                  SK.plugins.notify('danger', '{{ translate('Please select shipping address') }}');
                  return false;
              }
          }
          $('form#delivery_form').submit();
      }

    </script>
@endsection
