@extends('frontend.layouts.app')

@section('meta_title'){{ $detailedProduct->meta_title }}@stop

@section('meta_description'){{ $detailedProduct->meta_description }}@stop

@section('meta_keywords'){{ $detailedProduct->tags }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $detailedProduct->meta_title }}">
    <meta itemprop="description" content="{{ $detailedProduct->meta_description }}">
    <meta itemprop="image" content="{{ uploaded_asset($detailedProduct->meta_img) }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ $detailedProduct->meta_title }}">
    <meta name="twitter:description" content="{{ $detailedProduct->meta_description }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ uploaded_asset($detailedProduct->meta_img) }}">
    <meta name="twitter:data1" content="{{ single_price($detailedProduct->unit_price) }}">
    <meta name="twitter:label1" content="Price">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $detailedProduct->meta_title }}" />
    <meta property="og:type" content="og:product" />
    <meta property="og:url" content="{{ route('product', $detailedProduct->slug) }}" />
    <meta property="og:image" content="{{ uploaded_asset($detailedProduct->meta_img) }}" />
    <meta property="og:description" content="{{ $detailedProduct->meta_description }}" />
    <meta property="og:site_name" content="{{ get_setting('meta_title') }}" />
    <meta property="og:price:amount" content="{{ single_price($detailedProduct->unit_price) }}" />
    <meta property="product:price:currency" content="{{ \App\Currency::findOrFail(\App\BusinessSetting::where('type', 'system_default_currency')->first()->value)->code }}" />
    <meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">
@endsection

@section('content')
    @php
        $spare_parts = ($detailedProduct->brand != null && env('YAMAHA_BRAND') == $detailedProduct->brand->id && env('SPARE_PART_CATEGORY') == $detailedProduct->category_id) ? true : false;
        $qty = 0;
        if($detailedProduct->variant_product){
            foreach ($detailedProduct->stocks as $key => $stock) {
                $qty += $stock->qty;
            }
        }
        else{
            $qty = $detailedProduct->current_stock;
        }
        $srp_price = srp_price($detailedProduct->id);
        $price_with_discount = home_discounted_price($detailedProduct->id);
        $price_without_discount = home_price($detailedProduct->id);
    @endphp
    <div class="mt-20px mt-md-40px overflow-hidden">
        <div class="container">
            <div class="mw-1350px mx-auto">
                <div class="mb-30px mb-md-55px">
                    <ul class="breadcrumb fs-10 md-fs-12">
                        <li class="breadcrumb-item">
                            <a class="hov-text-primary" href="{{ route('used_page') }}">
                                {{ translate('Used') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a class="hov-text-primary" href="{{ route('product', $detailedProduct->slug) }}">
                                {{ $detailedProduct->getTranslation('name') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="row lg-gutters-30 xxl-gutters-60">
                    <div class="col-12 col-lg-45per order-lg-1">
                        <div class="row gutters-10 align-items-start mb-5px mb-md-15px">
                            @if($detailedProduct->brand != null)
                                <div class="col">
                                    <img class="h-10px h-md-20px product-res-brand" src="{{uploaded_asset($detailedProduct->brand->logo)}}" alt="">
                                </div>
                            @endif
                            @if(home_base_price($detailedProduct->id) != home_discounted_base_price($detailedProduct->id))
                                @php
                                    $discount_icon = ($detailedProduct->discount_type == 'percent') ? '%': currency_symbol();
                                @endphp
                                <div class="col d-flex justify-content-end overflow-hidden">
                                    <div class="d-flex align-items-center font-play fs-10 lg-fs-12 xxl-fs-14 fw-700 l-space-1-2 product-res-offer">
                                        <span>
                                            {{ toUpper(translate('Special Price')) }}
{{--                                            <span class="d-none d-md-inline"> - {{$detailedProduct->discount}}{{$discount_icon}}</span>--}}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @php
                            $photos = explode(',', $detailedProduct->photos);
                        @endphp
                        <div class="position-relative">
                            <div class="sk-carousel product-gallery" data-nav-for='.product-gallery-thumb' data-fade='true' data-auto-height='true'>
                                <div class="carousel-box img-zoom">
                                    <div class="quick-view-thumb-wrap">
                                        <img
                                                class="img-contain lazyload h-100 absolute-full"
                                                src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                data-src="{{ uploaded_asset($detailedProduct->thumbnail_img) }}"
                                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                        >
                                    </div>
                                </div>
                                @foreach ($detailedProduct->stocks as $key => $stock)
                                    @if ($stock->image != null)
                                        <div class="carousel-box img-zoom">
                                            <div class="quick-view-thumb-wrap">
                                                <img
                                                        class="img-contain lazyload h-100 absolute-full"
                                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                        data-src="{{ uploaded_asset($stock->image) }}"
                                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                                >
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                @foreach ($photos as $key => $photo)
                                    @if($photo)
                                        <div class="carousel-box img-zoom">
                                            <div class="quick-view-thumb-wrap">
                                                <img
                                                        class="img-contain lazyload h-100 absolute-full"
                                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                        data-src="{{ uploaded_asset($photo) }}"
                                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                                >
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @if(isPartner() && $srp_price != $price_with_discount)
                                <div class="product-res-srp text-right lh-1 p-2px fs-14 @if($detailedProduct->category->for_sale==0) visibility-hidden @endif">
                                    <div class="text-primary fs-10 fw-700">{{translate('SRP')}}</div>
                                    <div class="">{{ $srp_price }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12 order-lg-3">
                        <div class="my-10px my-lg-20px border-bottom border-default-200">
                            <div class="d-lg-none">
                                <h2 class="l-space-1-2 fs-10 sm-fs-12 fw-500 text-default-50 mb-0 pb-2px">{{translate('Part Number')}}: {{$detailedProduct->part_number}}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-45per order-lg-4">
                        <div class="sk-carousel gutters-5 lg-gutters-10 xxl-gutters-15 product-gallery-thumb" data-items='5' data-xl-items="4" data-md-items="5" data-sm-items="4" data-nav-for='.product-gallery' data-focus-select='true' data-arrows='false'>
                            <div class="carousel-box c-pointer" data-variation="{{ $stock->variant }}">
                                <div class="quick-view-thumb-wrap">
                                    <img
                                            class="img-contain lazyload h-100 absolute-full"
                                            src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                            data-src="{{ uploaded_asset($detailedProduct->thumbnail_img) }}"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                    >
                                </div>
                            </div>
                            @foreach ($detailedProduct->stocks as $key => $stock)
                                @if ($stock->image != null)
                                    <div class="carousel-box c-pointer" data-variation="{{ $stock->variant }}">
                                        <div class="quick-view-thumb-wrap">
                                            <img
                                                    class="img-contain lazyload h-100 absolute-full"
                                                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                    data-src="{{ uploaded_asset($stock->image) }}"
                                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                            >
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            @foreach ($photos as $key => $photo)
                                @if($photo)
                                    <div class="carousel-box c-pointer">
                                        <div class="quick-view-thumb-wrap">
                                            <img
                                                    class="img-contain lazyload h-100 absolute-full"
                                                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                    data-src="{{ uploaded_asset($photo) }}"
                                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                            >
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="col-12 col-lg-55per order-lg-2 d-flex flex-column justify-content-between mt-25px mt-md-50px mt-lg-0">
                        <div class="publish-right-used">
                            <div class="publish-right-top">
                                <h2 class="l-space-1-2 fs-11 sm-fs-12 fw-500 text-default-80 mb-15px mb-md-35px d-none d-lg-block">{{translate('Part Number')}}: {{$detailedProduct->part_number}}</h2>
                                <h3 class="font-play text-secondary fs-11 sm-fs-13 l-space-1-2 fw-400 mb-0 mb-sm-5px">{{toUpper(\App\Category::find($detailedProduct->category_id)->getTranslation('name'))}}</h3>
                                <h1 class="text-default-50 fs-18 lg-fs-24 xxl-fs-30 font-play l-space-1-2 fw-700 lh-1">{{ $detailedProduct->getTranslation('name') }}</h1>
                                <div class="publish-price font-play mb-20px mb-lg-10px fs-10 lg-fs-14 xxl-fs-18">
                                    <span class="fs-18 lg-fs-26 xxl-fs-35 fw-700">{{ $price_with_discount }}</span>
                                    @if($price_without_discount != $price_with_discount)
                                        <span class="product-res-price-del">{{ $price_without_discount }}</span>
                                    @endif
                                </div>
                                <div class="publish-not-for-sale-desc used mt-40px text-default-50 l-space-1-2 fs-11 lg-fs-15 xxl-fs-18">
                                    <div class="c-scrollbar">
                                        <div class="pb-lg-90px fw-500 l-space-1-2 text-default-50">
                                            @if($detailedProduct->getTranslation('description'))
                                                <?php echo $detailedProduct->getTranslation('description'); ?>
                                            @else
                                                <p>{{translate('There is no description of this product.')}}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-outline-primary btn-b-width-2 btn-block publish-not-for-sale-contact fs-18 py-5px py-lg-15px interested-btn side-popup-toggle" data-rel="interested-request-side-popup">
                                        {{toUpper(translate("I'm Interested"))}}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
        $related_products = filter_products(\App\Product::where('category_id', $detailedProduct->category_id)->where('id', '!=', $detailedProduct->id))->limit(10)->get();
    @endphp
    @if(count($related_products) > 0)
        <div class="mt-45px mt-lg-85px mt-xxl-175px overflow-hidden">
            <div class="container">
                <div class="mw-1350px mx-auto publish-container-target">
                    <h3 class="fs-13 md-fs-18 xxl-fs-23 fw-50 l-space-1-2 text-default-50 lh-1 m-0">{{translate('You may also like these')}}</h3>
                    <h2 class="fs-40 md-fs-55 xxl-fs-70 fw-700 mb-25px mb-md-50px mb-xxl-75px font-play lh-1">{{toUpper(translate('Similar Products'))}}</h2>
                </div>
            </div>
            <div class="container publish-container-adjustable">
                <div class="mw-1350px mx-auto">
                    <div class="container-left-1350px">
                        <div class="product-carousel-container">
                            <div class="swiper featured-products-swiper product-carousel-arrows">
                                <div class="swiper-wrapper" data-center="false" data-items="5" data-xl-items="4" data-lg-items="3"  data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows='true' data-infinite='true'>
                                    @foreach ($related_products as $key => $related_product)
                                        <div class="swiper-slide">
                                            @include('frontend.partials.product_listing.forsale_listing',['product' => $related_product, 'type_id' => null , 'brand_id' => null])
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-arrow swiper-prev">
                                    <span></span>
                                </div>
                                <div class="swiper-arrow swiper-next">
                                    <span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @include('frontend.partials.feature_brands')
    <div id="interested-request-side-popup" class="side-popup">
        <div class="side-popup-box">
            <div class="side-popup-close">
                <div class="side-popup-close-icon"></div>
                <div class="side-popup-close-text-wrap">
                    <div class="side-popup-close-text">{{toUpper(translate('Close'))}}</div>
                </div>
            </div>
            <div class="side-popup-container">
                <div class="side-popup-scroll c-scrollbar">
                    <div class="px-20px px-sm-25px py-20px py-sm-40px fs-13 sm-fs-16 fw-500">
                        <form id="used_request_form" method="POST" action="{{ route('product.used_request') }}">
                            @csrf
                            <input type="hidden" name="product" value="{{$detailedProduct->id}}">
                            <div class="l-space-1-2 mb-20px text-default-50 fs-11 sm-fs-16">
                                <h2 class="fs-32 sm-fs-45 font-play mb-10px mb-sm-20px fw-700 lh-1 text-secondary">
                                    {{translate('Request this Item')}}
                                </h2>
                                <p>{{translate('Ask more info on the product you are interested in. Our team will get back to you as soon as possible.')}}</p>
                            </div>
                            <div class="form-group mb-10px mb-sm-15px">
                                <div class="form-control-with-label small-focus small-field @if(old('name')) focused @endif ">
                                    <label>{{ translate('Full Name') }}</label>
                                    <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}">
                                </div>
                                @if ($errors->has('name'))
                                    <div class="invalid-feedback fs-10 d-block" role="alert">
                                        {{ $errors->first('name') }}
                                    </div>
                                @endif
                            </div>
                            <div class="form-group mb-10px mb-sm-15px">
                                <div class="form-control-with-label small-focus small-field @if(old('email')) focused @endif ">
                                    <label>{{ translate('Email') }}</label>
                                    <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}">
                                </div>
                                @if ($errors->has('email'))
                                    <div class="invalid-feedback fs-10 d-block" role="alert">
                                        {{ $errors->first('email') }}
                                    </div>
                                @endif
                            </div>
                            <div class="form-group mb-10px mb-sm-15px">
                                <div class="form-control-with-label small-focus small-field always-focused">
                                    <label>{{ translate('Country') }}</label>
                                    <select class="form-control sk-selectpicker {{ $errors->has('country') ? ' is-invalid' : '' }}" data-live-search="true" data-placeholder="{{translate('Country')}}" name="country">
                                        @foreach (\App\Country::getActiveCountriesForShipping() as $key => $country)
                                            <option value="{{ $country->id }}" @if(old('country') == $country->id || (!old('country') && $country->id == 54)) selected @endif >{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($errors->has('country'))
                                    <div class="invalid-feedback fs-10 d-block" role="alert">
                                        {{ $errors->first('country') }}
                                    </div>
                                @endif
                            </div>
                            <div class="form-group mb-5px">
                                <div class="form-control-with-label small-focus small-field textarea-label @if(old('comments')) focused @endif ">
                                    <label>{{ translate('Comments') }}</label>
                                    <textarea name="comments" rows="3" class="form-control resize-off {{ $errors->has('comments') ? ' is-invalid' : '' }}">{{ old('comments') }}</textarea>
                                </div>
                                @if ($errors->has('comments'))
                                    <div class="invalid-feedback fs-10 d-block" role="alert">
                                        {{ $errors->first('comments') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-20px mb-sm-35px text-right text-black-50">
                                <label class="sk-checkbox fs-11 sm-fs-14">
                                    <input type="checkbox" name="agree_policies">
                                    <span class="sk-square-check"></span>
                                    {{ translate('I agree with the')}}
                                    <a class="text-reset hov-text-primary" href="{{ route('custom-pages.show_custom_page', 'terms-policies' ) }}" target="_blank">{{ translate('Terms&Policies')}}</a>
                                </label>
                                <div id="used-request-form-error-agree" class="invalid-feedback fs-10 d-block mt-0" role="alert"></div>
                            </div>
                            @if(\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
                                <div id="recaptcha" class="g-recaptcha" data-sitekey="{{ getRecaptchaKeys()->public }}" data-callback="onSubmit" data-size="invisible"></div>
                                <div id="used-request-form-error-recaptcha" class="invalid-feedback fs-10 text-right" role="alert"></div>
                            @endif
                            <button id="used-request-form-btn" class="btn btn-outline-primary btn-block fs-16 fw-500 py-10px">
                                {{ toUpper(translate('Request this Item')) }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="out_of_stock_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-lg" role="document">
            <div class="modal-content position-relative">
                <div class="modal-body pt-40px px-40px pb-70px fs-16 fw-500 l-space-1-2 text-default-50">
                    <h2 class="text-secondary fs-45 fw-700 font-play mb-55px">{{translate('Order item explanation')}}</h2>
                    <p>{{translate('We do not have the desired product in stock, but we can order it for you from our supplier in the quantity you require.')}}</p>
                    <p>{{translate('As soon as we will receive the delivery from our supplier, we will send the order to you on the same day.')}}</p>
                </div>
                <div class="side-modal-close" data-dismiss="modal" aria-label="Close">
                    <div class="side-modal-close-icon"></div>
                    <div class="side-modal-close-text-wrap">
                        <div class="side-modal-close-text">{{toUpper(translate('Close'))}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="chat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="modal-header">
                    <h5 class="modal-title fw-600 h5">{{ translate('Any query about this product')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="" action="{{ route('conversations.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $detailedProduct->id }}">
                    <div class="modal-body gry-bg px-3 pt-3">
                        <div class="form-group">
                            <input type="text" class="form-control mb-3" name="title" value="{{ $detailedProduct->name }}" placeholder="{{ translate('Product Name') }}" required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="8" name="message" required placeholder="{{ translate('Your Question') }}">{{ route('product', $detailedProduct->slug) }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary fw-600" data-dismiss="modal">{{ translate('Cancel')}}</button>
                        <button type="submit" class="btn btn-primary fw-600">{{ translate('Send')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="login_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">{{ translate('Login')}}</h6>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-3">
                        <form class="form-default" role="form" action="{{ route('cart.login.submit') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                    <input type="text" class="form-control h-auto form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{ translate('Email Or Phone')}}" name="email" id="email">
                                @else
                                    <input type="email" class="form-control h-auto form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{  translate('Email') }}" name="email">
                                @endif
                                @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                    <span class="opacity-60">{{  translate('Use country code before number') }}</span>
                                @endif
                            </div>

                            <div class="form-group">
                                <input type="password" name="password" class="form-control h-auto form-control-lg" placeholder="{{ translate('Password')}}">
                            </div>

                            <div class="row mb-2">
                                <div class="col-6">
                                    <label class="sk-checkbox">
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <span class=opacity-60>{{  translate('Remember Me') }}</span>
                                        <span class="sk-square-check"></span>
                                    </label>
                                </div>
                                <div class="col-6 text-right">
                                    <a href="{{ route('password.request') }}" class="text-reset opacity-60 fs-14">{{ translate('Forgot password?')}}</a>
                                </div>
                            </div>

                            <div class="mb-5">
                                <button type="submit" class="btn btn-primary btn-block fw-600">{{  translate('Login') }}</button>
                            </div>
                        </form>

                        <div class="text-center mb-3">
                            <p class="text-muted mb-0">{{ translate('Dont have an account?')}}</p>
                            <a href="{{ route('user.registration') }}">{{ translate('Register Now')}}</a>
                        </div>
                        @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                            <div class="separator mb-3">
                                <span class="bg-white px-3 opacity-60">{{ translate('Or Login With')}}</span>
                            </div>
                            <ul class="list-inline social colored text-center mb-5">
                                @if (\App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1)
                                    <li class="list-inline-item">
                                        <a href="{{ route('social.login', ['provider' => 'facebook']) }}" class="facebook">
                                            <i class="lab la-facebook-f"></i>
                                        </a>
                                    </li>
                                @endif
                                @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1)
                                    <li class="list-inline-item">
                                        <a href="{{ route('social.login', ['provider' => 'google']) }}" class="google">
                                            <i class="lab la-google"></i>
                                        </a>
                                    </li>
                                @endif
                                @if (\App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                                    <li class="list-inline-item">
                                        <a href="{{ route('social.login', ['provider' => 'twitter']) }}" class="twitter">
                                            <i class="lab la-twitter"></i>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @if(\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
    <script type="text/javascript">
      // making the CAPTCHA  a required field for form submission
      $(document).ready(function(){
          @if(\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
          function onSubmit(token) {
            if (token.length > 0) {
              $('#used_request_form').submit();
            }
          }
        window.onSubmit = onSubmit;
        $('#used-request-form-btn').on("click", function(evt){
          evt.preventDefault();
          if($('input[name="agree_policies"]').prop('checked')==false) {
            $('#used-request-form-error-agree').addClass('d-block').text('{{translate('You need to agree with our policies')}}');
            return false;
          } else {
            $('#used-request-form-btn').addClass('loader');
            grecaptcha.execute();
            return true;
          }
        });
          @else
          $("#used_request_form").on("submit", function(){
            if($('input[name="agree_policies"]').prop('checked')==false){
              $('#used-request-form-error-agree').addClass('d-block').text('{{translate("You need to agree with our policies")}}');
              return false;
            } else {
              $('#used-request-form-btn').addClass('loader');
            }
          });
          @endif
      });

        function CopyToClipboard(e) {
          var url = $(e).data('url');
          var $temp = $("<input>");
          $("body").append($temp);
          $temp.val(url).select();
          try {
            document.execCommand("copy");
            SK.plugins.notify('success', '{{ translate('Link copied to clipboard') }}');
          } catch (err) {
            SK.plugins.notify('danger', '{{ translate('Oops, unable to copy') }}');
          }
          $temp.remove();
          // if (document.selection) {
          //     var range = document.body.createTextRange();
          //     range.moveToElementText(document.getElementById(containerid));
          //     range.select().createTextRange();
          //     document.execCommand("Copy");

          // } else if (window.getSelection) {
          //     var range = document.createRange();
          //     document.getElementById(containerid).style.display = "block";
          //     range.selectNode(document.getElementById(containerid));
          //     window.getSelection().addRange(range);
          //     document.execCommand("Copy");
          //     document.getElementById(containerid).style.display = "none";

          // }
          // SK.plugins.notify('success', 'Copied');
        }
        function show_chat_modal(){
            @if (Auth::check())
            $('#chat_modal').modal('show');
            @else
            $('#login_modal').modal('show');
            @endif
        }

        @if(old('opened_interested') == true)
            $('.interested-btn').trigger('click');
        @endif

      var numberOfSlides = document.querySelectorAll('.featured-products-swiper .swiper-slide').length;
      var featured_swiper = undefined;
      function initSwiper() {
        if(featured_swiper != undefined) {
          featured_swiper.destroy();
        }
        var screen = $('body').outerWidth();
        var navigation = false;
        if(screen >= 1500 && numberOfSlides > 4) {
          navigation = true;
        } else if(screen >= 1200 && numberOfSlides > 3) {
          navigation = true;
        } else if(screen >= 768 && numberOfSlides > 2) {
          navigation = true;
        } else if(screen <= 767 && numberOfSlides > 1) {
          navigation = true;
        }
        if(navigation == true) {
          $('.featured-products-swiper .swiper-prev, .featured-products-swiper .swiper-next').removeClass('d-none');
        } else {
          $('.featured-products-swiper .swiper-prev, .featured-products-swiper .swiper-next').addClass('d-none');
        }
        featured_swiper = new Swiper('.featured-products-swiper', {
          loop: numberOfSlides > 1 ? true : false,
          slidesPerView: 2,
          spaceBetween: 40,
          navigation: {
            nextEl: '.swiper-next',
            prevEl: '.swiper-prev',
            enabled: navigation,
          },
          breakpoints: {
            768: {
              slidesPerView: 3,
              spaceBetween: 40,
              loop: numberOfSlides > 2 ? true : false,
            },
            992: {
              slidesPerView: 3,
              spaceBetween: 60,
              loop: numberOfSlides > 2 ? true : false,
            },
            1200: {
              slidesPerView: 4,
              spaceBetween: 60,
              loop: numberOfSlides > 3 ? true : false,
            },
            1500: {
              slidesPerView: 5,
              spaceBetween: 60,
              loop: numberOfSlides > 4 ? true : false,
            },
            1750: {
              slidesPerView: 5,
              spaceBetween: 80,
              loop: numberOfSlides > 4 ? true : false,
            },
          },
        });
      }
      initSwiper();
      //Swiper plugin initialization on window resize
      $(window).on('resize', function(){
        initSwiper();
      });
    </script>
@endsection
