@extends('frontend.layouts.app')

@section('meta_title'){{ $page->meta_title }}@stop

@section('meta_description'){{ $page->meta_description }}@stop

@section('meta_keywords'){{ $page->tags }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $page->meta_title }}">
    <meta itemprop="description" content="{{ $page->meta_description }}">
    <meta itemprop="image" content="{{ uploaded_asset($page->meta_img) }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ $page->meta_title }}">
    <meta name="twitter:description" content="{{ $page->meta_description }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ uploaded_asset($page->meta_img) }}">
    <meta name="twitter:data1" content="{{ single_price($page->unit_price) }}">
    <meta name="twitter:label1" content="Price">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $page->meta_title }}" />
    <meta property="og:type" content="product" />
    <meta property="og:url" content="{{ route('product', $page->slug) }}" />
    <meta property="og:image" content="{{ uploaded_asset($page->meta_img) }}" />
    <meta property="og:description" content="{{ $page->meta_description }}" />
    <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
    <meta property="og:price:amount" content="{{ single_price($page->unit_price) }}" />
@endsection

@section('content')
    <div id="partnership-header" class="line-slider-item overflow-hidden">
        <div class="line-slider-image">
            <img
                    class="absolute-full h-100 img-fit"
                    src="{{ uploaded_asset($page->banner) }}"
                    alt=""
                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
            >
            <div class="line-slider-over">
                <div class="row no-gutters">
                    <div class="col-lg-4">
                        <div class="line-slider-over-left">
                            <div class="line-slider-over-box"></div>
                            <div class="line-slider-over-box slogan d-lg-flex justify-content-center">
                                <div class="line-slider-over-box-inner">
                                    <h1 class="font-play fs-25 md-fs-30 xl-fs-45 xxl-fs-60 fw-700 m-0">{{ $page->getTranslation('title') }}</h1>
                                </div>
                            </div>
                            <div class="line-slider-over-box d-none d-lg-block"></div>
                        </div>
                    </div>
                    <div class="col-lg-8 d-none d-lg-block">
                        <div class="line-slider-over-right">
                            <div class="line-slider-over-box"></div>
                            <div class="line-slider-over-box slogan"></div>
                            @if($page->getTranslation('banner_desc'))
                                <div class="line-slider-over-box bg-white text-default-50 fs-16 xl-fs-18 xxl-fs-23 xxxl-fs-28 font-play l-space-1 px-xxl-50px d-flex flex-column justify-content-end lh-1-4 position-relative">
                                    <div class="pr-xxl-40px">
                                        <p>{{$page->getTranslation('banner_desc')}}</p>
                                    </div>
                                    <div class="side-right-bar d-none d-xxl-block">
                                        <a href="javascript:void(0);" class="side-right-bar-link side-popup-toggle fs-25" data-rel="b2b-side-popup">
                                            <span class="d-block side-right-bar-arrow"></span>
                                            <span class="d-block side-right-bar-text-wrap">
                                                <span class="side-right-bar-text">{{toUpper(translate('Equiry'))}}</span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="line-slider-over-box position-relative d-none d-xxl-block">
                                    <div class="side-right-bar">
                                        <a href="javascript:void(0);" class="side-right-bar-link side-popup-toggle fs-25" data-rel="b2b-side-popup">
                                            <span class="d-block side-right-bar-arrow"></span>
                                            <span class="d-block side-right-bar-text-wrap">
                                                <span class="side-right-bar-text">{{toUpper(translate('Equiry'))}}</span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($page->getTranslation('banner_desc'))
        <div class="my-30px my-md-50px d-lg-none">
            <div class="container">
                <div class="text-default-50 fs-16 font-play l-space-1-2 lh-1-7 mw-850px">
                    <p>{{$page->getTranslation('banner_desc')}}</p>
                </div>
            </div>
        </div>
    @endif
    @if (get_setting('reasons_slider_images') != null)
        @php
            $reasons_images = json_decode(get_setting('reasons_slider_images'), true);
            $count = 1;
        @endphp
        <div id="partnership-reasons" class="mt-30px mt-lg-90px mt-xxl-150px overflow-hidden">
            <div class="container fs-16 lg-fs-20 xxl-fs-23 l-space-1-2 fw-500 mb-30px mb-lg-40px mb-xxl-50px text-default-50">
                <h2 class="fs-30 lg-fs-50 xxl-fs-70 font-play fw-700 mb-5px mb-md-10px">
                    {{count($reasons_images)}} <span class="text-secondary">{{translate('reasons')}}</span> {{translate('why')}}
                </h2>
                <p>{{translate('Partner with us')}}</p>
            </div>
            <div class="container-left">
                <div class="about-history-container">
                    <div class="swiper about-history-swiper">
                        <div class="swiper-wrapper" data-items="4" data-xl-items="3" data-md-items="3" data-sm-items="2" data-arrows="false" data-dots="false" data-infinite="true">
                            @foreach ($reasons_images as $key => $value)
                                @php
                                    $number = ($count < 10) ? '0'.$count : $count;
                                @endphp
                                <div class="swiper-slide">
                                    <div class="about-history-res-item">
                                        <div class="about-history-res-wrap l-space-1-2 text-default-50 fw-500 fs-11 sm-fs-14 xl-fs-16 xxxl-fs-18">
                                            <div class="about-history-res-image">
                                                <img
                                                        class="absolute-full h-100 img-fit"
                                                        src="{{ uploaded_asset($reasons_images[$key]) }}"
                                                        alt=""
                                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
                                                >
                                                <div class="about-history-res-number fs-30 lg-fs-40 xxl-fs-50 fw-700 font-play text-white-50">{{$number}}</div>
                                            </div>
                                            <h3 class="about-history-res-year my-5px my-lg-15px lh-1 fw-700 font-play fs-18 lg-fs-23 xxl-fs-35 text-default">
                                                {{json_decode(get_setting('reasons_slider_titles'), true)[$key]}}
                                            </h3>
                                            <p class="about-history-res-desc">{{json_decode(get_setting('reasons_slider_short_desc'), true)[$key]}}</p>
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $count++;
                                @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @php
        $b2b_categories = \App\Category::where('show_b2b', 1)->limit(6)->get();
    @endphp
    @if(count($b2b_categories) > 0)
        <div id="partnership-products" class="mt-50px mt-lg-100px mt-xxl-150px overflow-hidden">
            <div class="about-brands-top">
                @php
                    $count = 0;
                @endphp
                @foreach($b2b_categories as $b2b_category)
                    @php
                        $image = ($b2b_category->b2b_banner) ? $b2b_category->b2b_banner : $b2b_category->header;
                        $b2b_description = ($b2b_category->getTranslation('b2b_description')) ? $b2b_category->getTranslation('b2b_description') : $b2b_category->getTranslation('short_description');
                    @endphp
                    <div class="about-brand-res-item @if($count == 0) active @endif " data-id="{{$b2b_category->id}}">
                        <div class="d-lg-none">
                            <div class="font-play fs-18 l-space-1-2 text-default-50 container py-15px pb-20px lh-1-5">
                                <p>{{$b2b_description}}</p>
                            </div>
                            <a href="javascript:void(0);" class="btn btn-primary btn-block fw-500 l-space-1 fs-14 md-fs-16 py-5px side-popup-toggle" data-rel="b2b-side-popup">
                                {{toUpper(translate('Enquiry Form'))}}
                                <span class="btn-arrow right ml-3"></span>
                            </a>
                        </div>
                        <div class="position-relative">
                            <div class="about-brand-res-image">
                                <img class="img-fit h-100 absolute-full lazyload"
                                     src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                     data-src="{{uploaded_asset($image)}}"
                                     onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                     alt="">
                            </div>
                            <div class="absolute-full about-brand-res-over">
                                <div class="row align-items-center no-gutters">
                                    <div class="col-lg-8 d-none d-lg-block">
                                        <div class="position-relative bg-white min-h-190px xxl-min-h-250px">
                                            <div class="font-play fs-20 xxl-fs-25 l-space-1-2 text-default-50 container py-15px lh-2">
                                                <p>{{$b2b_description}}</p>
                                            </div>
                                            <div class="side-right-bar">
                                                <a href="javascript:void(0);" class="side-right-bar-link side-popup-toggle b2b-btn-toggle" data-rel="b2b-side-popup">
                                                    <span class="d-block side-right-bar-arrow"></span>
                                                    <span class="d-block side-right-bar-text-wrap">
                                                    <span class="side-right-bar-text">{{toUpper(translate('Enquiry Form'))}}</span>
                                                </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php /*
                                @if($b2b_category->icon)
                                    <div class="about-brand-res-over-icon">
                                        <img class="img-contain h-100 absolute-full lazyload"
                                             src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                             data-src="{{uploaded_asset($b2b_category->icon)}}"
                                             onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                             alt="">
                                    </div>
                                @endif*/ ?>
                            </div>
                        </div>
                    </div>
                    @php
                        $count++;
                    @endphp
                @endforeach
            </div>
            <div class="about-brands-bottom bg-default text-white-50 fw-500 l-space-1-2 fs-12 sm-fs-14 lg-fs-16 xxl-fs-18 text-nowrap">
                <div class="hor-swipe" data-simplebar>
                    <div class="row no-gutters flex-nowrap">
                        @php
                            $count = 0;
                        @endphp
                        @foreach($b2b_categories as $b2b_category)
                            <div class="col about-brands-btn-item">
                                <div class="about-brands-btn py-5px py-sm-15px py-lg-25px px-15px @if($count == 0) active @endif " data-id="{{$b2b_category->id}}">
                                    <div class="about-brands-btn-back"></div>
                                    {{toUpper($b2b_category->getTranslation('name'))}}
                                </div>
                            </div>
                            @php
                                $count++;
                            @endphp
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    @include('frontend.partials.feature_brands')

    <div id="b2b-side-popup" class="side-popup">
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
                        <div class="l-space-1-2 mb-25px mb-sm-50px text-default-50 fs-11 sm-fs-16">
                            <h2 class="fs-32 sm-fs-45 font-play mb-10px mb-sm-20px fw-700 lh-1">
                                <span class="text-secondary">{{translate('Partnership')}}</span> {{translate('enquiry')}}
                            </h2>
                            <p>We look forward to doing business with you in the near future.</p>
                        </div>
                        <div class="text-black">
                            <div id="partnership-form-container">
                                @php
                                    if($errors->has('name') || old('name')){
                                        $val_name = old('name');
                                    } elseif(Auth::check()) {
                                        $full_name = Auth::user()->name;
                                        $val_name_arr = getAccountName($full_name);
                                        $val_name = $val_name_arr['name'];
                                    } else {
                                        $val_name = '';
                                    }
                                    if($errors->has('surname') || old('surname')){
                                        $val_surname = old('surname');
                                    } elseif(Auth::check()) {
                                        $full_name = Auth::user()->name;
                                        $val_surname_arr = getAccountName($full_name);
                                        $val_surname = $val_surname_arr['surname'];
                                    } else {
                                        $val_surname = '';
                                    }
                                    if ($errors->has('email') || old('email')){
                                        $val_email = old('email');
                                    } elseif(Auth::check()) {
                                        $val_email = Auth::user()->email;
                                    } else {
                                        $val_email = '';
                                    }
                                    if ($errors->has('country') || old('country')){
                                        $val_country = old('country');
                                    } elseif(Auth::check()) {
                                        $val_country = Auth::user()->country;
                                    } else {
                                        $val_country = '';
                                    }
                                    if ($errors->has('city') || old('city')){
                                        $val_city = old('city');
                                    } elseif(Auth::check()) {
                                        $val_city = Auth::user()->city;
                                    } else {
                                        $val_city = '';
                                    }
                                    if ($errors->has('phone') || old('phone')){
                                        $val_phone = old('phone');
                                    } elseif(Auth::check()) {
                                        $val_phone = Auth::user()->phone;
                                    } else {
                                        $val_phone = '';
                                    }
                                @endphp
                                <form id="partnership_form" method="POST" action="{{ route('b2b.send') }}">
                                    @csrf
                                    <div class="form-group mb-10px mb-sm-15px">
                                        <div class="form-control-with-label small-focus small-field @if($val_name) focused @endif ">
                                            <label>{{ translate('Name') }}</label>
                                            <input type="text" class="form-control form-no-space{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ $val_name }}">
                                        </div>
                                        @if ($errors->has('name'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('name') }}
                                            </div>
                                        @endif
                                        <div id="nameError" class="invalid-feedback fs-10 d-block" role="alert"></div>
                                    </div>
                                    <div class="form-group mb-10px mb-sm-15px">
                                        <div class="form-control-with-label small-focus small-field @if($val_surname) focused @endif ">
                                            <label>{{ translate('Surname') }}</label>
                                            <input type="text" class="form-control{{ $errors->has('surname') ? ' is-invalid' : '' }}" name="surname" value="{{ $val_surname }}">
                                        </div>
                                        @if ($errors->has('surname'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('surname') }}
                                            </div>
                                        @endif
                                        <div id="surnameError" class="invalid-feedback fs-10 d-block" role="alert"></div>
                                    </div>
                                    <div class="form-group mb-10px mb-sm-15px">
                                        <div class="form-control-with-label small-focus small-field @if(old('company')) focused @endif ">
                                            <label>{{ translate('Company Name') }}</label>
                                            <input type="text" class="form-control{{ $errors->has('company') ? ' is-invalid' : '' }}" name="company" value="{{ old('company') }}">
                                        </div>
                                        @if ($errors->has('company'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('company') }}
                                            </div>
                                        @endif
                                        <div id="companyError" class="invalid-feedback fs-10 d-block" role="alert"></div>
                                    </div>
                                    <div class="form-group mb-10px mb-sm-15px">
                                        <div class="form-control-with-label small-focus small-field @if($val_email) focused @endif ">
                                            <label>{{ translate('Email') }}</label>
                                            <input type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $val_email }}" @if(Auth::check()) readonly @endif>
                                        </div>
                                        @if ($errors->has('email'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('email') }}
                                            </div>
                                        @endif
                                        <div id="emailError" class="invalid-feedback fs-10 d-block" role="alert"></div>
                                    </div>

                                    <div class="form-group mb-10px mb-sm-15px">
                                        <div class="form-control-with-label small-focus small-field always-focused">
                                            <label>{{ translate('Country') }}</label>
                                            <select class="form-control sk-selectpicker{{ $errors->has('country') ? ' is-invalid' : '' }}" data-live-search="true" name="country">
                                                @foreach (\App\Country::getActiveCountriesForShipping() as $key => $country)
                                                    <option value="{{ $country->id }}" @if($val_country == $country->id || (!$val_country && $country->id == 54)) selected @endif>{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if ($errors->has('country'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('country') }}
                                            </div>
                                        @endif
                                        <div id="countryError" class="invalid-feedback fs-10 d-block" role="alert"></div>
                                    </div>

                                    <div class="form-group mb-10px mb-sm-15px">
                                        <div class="form-control-with-label small-focus small-field always-focused">
                                            <label>{{ translate('City') }}</label>
                                            <select class="form-control sk-selectpicker{{ $errors->has('city') ? ' is-invalid' : '' }}" data-live-search="true" name="city">
                                                @foreach (\App\City::get() as $key => $city)
                                                    <option value="{{ $city->id }}">{{ $city->getTranslation('name') }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="city_name" class="form-control fs-13 md-fs-16 d-none" disabled>
                                        </div>
                                        @if ($errors->has('city'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('city') }}
                                            </div>
                                        @endif
                                        @if ($errors->has('city_name'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('city_name') }}
                                            </div>
                                        @endif
                                        <div id="cityError" class="invalid-feedback fs-10 d-block" role="alert"></div>
                                    </div>

                                    <div class="form-group mb-10px mb-sm-15px">
                                        <div class="position-relative">
                                            <input type="number" lang="en" min="0" class="form-control form-control-phone{{ $errors->has('phone') ? ' is-invalid' : '' }}" value="{{$val_phone}}" name="phone">
                                            <div class="form-control-phone-code"></div>
                                            <input type="hidden" name="phone_code" value="">
                                        </div>
                                        @if ($errors->has('phone'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('phone') }}
                                            </div>
                                        @endif
                                        <div id="phoneError" class="invalid-feedback fs-10 d-block" role="alert"></div>
                                    </div>
                                    <div class="my-15px text-black-50">
                                        <h3 class="fs-14 sm-fs-16 fw-500 text-black">{{translate('Interested in')}}</h3>
                                        <div class="row gutters-10">
                                            <div class="col-12">
                                                <label class="sk-checkbox mb-5px checkbox-secondary">
                                                    <input type="checkbox" name="interests[]" value="{{ translate('Parts')}}" {{(old('interests') && in_array('Parts', old('interests'))) ? ' checked' : '' }}>
                                                    <span class="sk-square-check"></span>
                                                    {{ translate('Parts')}}
                                                </label>
                                            </div>
                                            <div class="col-12">
                                                <label class="sk-checkbox mb-5px checkbox-secondary">
                                                    <input type="checkbox" name="interests[]" value="{{ translate('Accessories & Apparel')}}" {{(old('interests') && in_array('Accessories & Apparel', old('interests'))) ? ' checked' : '' }}>
                                                    <span class="sk-square-check"></span>
                                                    {{ translate('Accessories & Apparel')}}
                                                </label>
                                            </div>
                                            <div class="col-12">
                                                <label class="sk-checkbox mb-5px checkbox-secondary">
                                                    <input type="checkbox" name="interests[]" value="{{ translate('Water Sport Equipment')}}" {{(old('interests') && in_array('Water Sport Equipment', old('interests'))) ? ' checked' : '' }}>
                                                    <span class="sk-square-check"></span>
                                                    {{ translate('Water Sport Equipment')}}
                                                </label>
                                            </div>
                                            <div class="col-12">
                                                <label class="sk-checkbox mb-5px checkbox-secondary">
                                                    <input type="checkbox" name="interests[]" value="{{ translate('Other')}}" {{(old('interests') && in_array('Other', old('interests'))) ? ' checked' : '' }}>
                                                    <span class="sk-square-check"></span>
                                                    {{ translate('Other')}}
                                                </label>
                                            </div>
                                        </div>
                                        @if ($errors->has('interests'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('interests') }}
                                            </div>
                                        @endif
                                        <div id="interestsError" class="invalid-feedback fs-10 d-block" role="alert"></div>
                                    </div>
                                    <div class="form-group mb-5px">
                                        <div class="form-control-with-label small-focus small-field textarea-label @if(old('message')) focused @endif ">
                                            <label>{{ translate('Message') }}</label>
                                            <textarea name="message" rows="5" class="form-control resize-off {{ $errors->has('message') ? ' is-invalid' : '' }}">{{ old('message') }}</textarea>
                                        </div>
                                        @if ($errors->has('message'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('message') }}
                                            </div>
                                        @endif
                                        <div id="messageError" class="invalid-feedback fs-10 d-block" role="alert"></div>
                                    </div>
                                    <div class="mb-5px text-right text-black-50">
                                        <label class="sk-checkbox fs-11 sm-fs-14">
                                            <input type="checkbox" name="agree_policies">
                                            <span class="sk-square-check"></span>
                                            {{ translate('I agree with the')}}
                                            <a class="text-reset hov-text-primary" href="{{ route('custom-pages.show_custom_page', 'terms-policies' ) }}" target="_blank">{{ translate('Terms&Policies')}}</a>
                                        </label>
                                        <div id="partnership-form-error-agree" class="invalid-feedback fs-10 d-block mt-0 mb-10px" role="alert"></div>
                                    </div>
                                    <button id="partnership-form-btn" class="btn btn-outline-primary btn-block fs-16 fw-500 py-10px">{{ toUpper(translate('Enquire')) }}</button>
                                    @if(\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
                                        <div id="recaptcha" class="g-recaptcha" data-sitekey="{{ getRecaptchaKeys()->public }}" data-callback="onSubmit" data-size="invisible"></div>
                                        <div id="partnership-form-error-recaptcha" class="invalid-feedback fs-10 text-right" role="alert"></div>
                                    @endif
                                </form>
                            </div>
                            <div id="partnership-form-success" class="text-default-50 l-space-1-2 fw-500" style="display: none;">
                                <div class="border-top border-width-2 border-primary w-100px mb-30px mb-sm-50px"></div>
                                <h3 class="fs-14 sm-fs-17 fw-700 font-play mb-20px text-secondary">{{translate('Thank you for your enquiry!')}}</h3>
                                <p>{{translate('We have received your request and we will look into it.')}}</p>
                                <p>{{translate('Stay tuned as we will get back to you soon to discuss the details!')}}</p>
                                <div class="border-top border-width-2 border-secondary mt-30px mt-sm-50px"></div>
                            </div>
                        </div>
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
    <script>
      // making the CAPTCHA  a required field for form submission
      $(document).ready(function(){
          @if(\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
          function onSubmit(token) {
            if (token.length > 0) {
                $.ajax({
                    type: "POST",
                    url: '{{ route('b2b.send_ajax') }}',
                    data: $('#partnership_form').serializeArray(),
                    beforeSend: function() {
                        $('#partnership-form-btn').addClass('loader');
                        $('#nameError, #surnameError, #companyError, #emailError, #countryError, #cityError, #phoneError, #interestsError, #messageError').text('');
                    },
                    success: function (data) {
                        if(data.status == 1) {
                            $('#partnership-form-container').hide();
                            $('#partnership-form-success').show();
                        } else {
                            if(data.status == 4) {
                                SK.plugins.notify('warning', '{{ translate('A user or a partner request already exist with this email') }}');
                            } else if(data.status == 3) {
                                if(data.validator) {
                                    if(data.validator.name) {
                                        $('#nameError').text(data.validator.name);
                                    }
                                    if(data.validator.surname) {
                                        $('#surnameError').text(data.validator.surname);
                                    }
                                    if(data.validator.company) {
                                        $('#companyError').text(data.validator.company);
                                    }
                                    if(data.validator.email) {
                                        $('#emailError').text(data.validator.email);
                                    }
                                    if(data.validator.country) {
                                        $('#countryError').text(data.validator.country);
                                    }
                                    if(data.validator.city) {
                                        $('#cityError').text(data.validator.city);
                                    }
                                    if(data.validator.city_name) {
                                        $('#cityError').text(data.validator.city_name);
                                    }
                                    if(data.validator.phone) {
                                        $('#phoneError').text(data.validator.phone);
                                    }
                                    if(data.validator.interests) {
                                        $('#interestsError').text(data.validator.interests);
                                    }
                                    if(data.validator.message) {
                                        $('#messageError').text(data.validator.message);
                                    }
                                }
                            } else {
                                SK.plugins.notify('warning', '{{ translate('Something went wrong.') }}');
                            }
                        }
                        $('#partnership-form-btn').removeClass('loader');
                        grecaptcha.reset();
                    }
                });
            }
          }
        window.onSubmit = onSubmit;
        $('#partnership-form-btn').on("click", function(evt){
          evt.preventDefault();
          if($('input[name="agree_policies"]').prop('checked')==false) {
            $('#partnership-form-error-agree').addClass('d-block').text('{{translate('You need to agree with our policies')}}');
            return false;
          } else {
            grecaptcha.execute();
            return true;
          }
        });
          @else
          $("#partnership_form").on("submit", function(){
            if($('input[name="agree_policies"]').prop('checked')==false){
              $('#partnership-form-error-agree').addClass('d-block').text('{{translate("You need to agree with our policies")}}');
              return false;
            } else {
              $('#partnership-form-btn').addClass('loader');
            }
          });
          @endif
      });

      $(document).on('click', '.about-brands-btn', function () {
        var offset = $(this).data('offset');
        var width = $(this).outerWidth();
        $('.about-brands-btn').not(this).removeClass('active');
        $(this).addClass('active');
        $('.about-brand-res-item').removeClass('active');
        $('.about-brand-res-item[data-id="' + $(this).data('id') + '"]').addClass('active');
        setHeightofSideText();
        if($('body').outerWidth() < offset + width) {
          console.log(offset);
          $('.about-brands-bottom .hor-swipe .simplebar-content-wrapper').animate({
            scrollLeft: offset - $('body').outerWidth() + width
          }, 350);
        } else if($(this).offset().left < 0) {
          $('.about-brands-bottom .hor-swipe .simplebar-content-wrapper').animate({
            scrollLeft: offset
          }, 350);
        }
      });

      @if(old('opened_partnership') == true || (isset($_GET['open_form']) && $_GET['open_form'] == 1))
        $('.b2b-btn-toggle').trigger('click');
      @endif

      $(document).on('change', '[name=country]', function() {
        var country = $(this).val();
          if (country==='54') {
              $('select[name="city"]').prop('disabled', false).prop('required', true).removeClass('d-none').parent('div').removeClass('d-none');
              $('input[name="city_name"]').prop('disabled', true).prop('required', false).addClass('d-none');
              get_city(country);
              SK.plugins.bootstrapSelect('refresh');
          } else {
              $('select[name="city"]').prop('disabled', true).prop('required', false).addClass('d-none');
              $('input[name="city_name"]').prop('disabled', false).prop('required', true).removeClass('d-none');
              SK.plugins.bootstrapSelect('refresh');
          }
        get_phone_code(country);
      });

      $(document).ready(function() {
          var country = $('[name="country"]').val();
          if (country==='54') {
              $('select[name="city"]').prop('disabled', false).prop('required', true).removeClass('d-none').parent('div').removeClass('d-none');
              $('input[name="city_name"]').prop('disabled', true).prop('required', false).addClass('d-none');
              get_city(country);
              SK.plugins.bootstrapSelect('refresh');
          } else {
              $('select[name="city"]').prop('disabled', true).prop('required', false).addClass('d-none');
              $('input[name="city_name"]').prop('disabled', false).prop('required', true).removeClass('d-none');
              SK.plugins.bootstrapSelect('refresh');
          }
        get_phone_code(country);
        get_selected_city(country, {{ $val_city }});
      });

      function get_city(country) {
        $('[name="city"]').html("");
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
              $('[name="city"]').html(obj);
              SK.plugins.bootstrapSelect('refresh');
            }
          }
        });
      }

      function get_selected_city(country, city) {
          @if(!Auth::check())
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
                  return false;
              }
          }
          @endif
        $('[name="city"]').html("");
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
              $('[name="city"]').html(obj);
              SK.plugins.bootstrapSelect('refresh');
              $('.delivery-info-content').removeClass('loader');
            }
          }
        });
      }

      function get_phone_code(country) {
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
              $('[name="phone_code"]').attr('value', obj);
              $('.form-control-phone-code').text('+' + obj);
            }
          }
        });
      }

      var about_swiper = new Swiper('.about-history-swiper', {
        loop: true,
        slidesPerView: 2,
        spaceBetween: 20,
        breakpoints: {
          768: {
            slidesPerView: 3,
            spaceBetween: 20,
          },
          992: {
            slidesPerView: 3,
            spaceBetween: 40,
          },
          1500: {
            slidesPerView: 4,
            spaceBetween: 40,
          },
          1750: {
            slidesPerView: 4,
            spaceBetween: 60,
          },
        },
      });
    </script>
@endsection
