<header class="z-1020 bg-white border-bottom border-width-2 border-primary-100 py-10px">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-auto">
                <a href="{{ route('home') }}">
                    @if(get_setting('header_logo') != null)
                        <img class="h-55px h-md-75px" height="75"
                             src="{{ uploaded_asset(get_setting('header_logo')) }}"
                             alt="{{ get_setting('site_name') }}">
                    @else
                        <img class="h-55px h-md-75px" height="75"
                             src="{{static_asset('assets/img/icons/logo-25.svg')}}"
                             alt="{{ get_setting('site_name') }}">
                    @endif
                </a>
            </div>
            <div class="col">
                <div class="row justify-content-end gutters-5">
                    <div class="col-auto">
                        <div id="cart_icon">
                            @include('frontend.partials.cart')
                        </div>
                    </div>
                    <div class="col-auto">
                        <div id="my_account">
                            @include('frontend.partials.my_account')
                        </div>
                    </div>
                    @if(get_setting('show_language_switcher') == 'on')
                        <div class="col-auto">
                            <div class="dropdown lang-dropdown" id="lang-change">
                                @php
                                    if(Session::has('locale')){
                                        $locale = Session::get('locale', Config::get('app.locale'));
                                    }
                                    else{
                                        $locale = 'en';
                                    }
                                @endphp
                                <a href="javascript:void(0)" class="dropdown-toggle no-arrow" data-toggle="dropdown" data-display="static">
                                        <span class="header-icon">
                                            {{--                                        @include('frontend.partials.language_choice')--}}
                                            <svg class="h-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15.6 16.1">
                                                <use xlink:href="{{static_asset('assets/img/icons/languages-icon.svg')}}#language_choice"></use>
                                            </svg>
                                        </span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    @foreach (\App\Language::all() as $key => $language)
                                        <li>
                                            <a href="javascript:void(0)" data-flag="{{ $language->code }}" class="dropdown-item @if($locale == $language->code) active @endif">
                                                {{ toUpper($language->lang_code) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>

@if(get_setting('header_stikcy') == 'on')
    <div class="fixed-header z-1035 bg-white">
        <div class="bg-black-07 py-5px">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <a href="{{ route('home') }}">
                            @if(get_setting('header_logo') != null)
                                <img class="h-40px" height="40"
                                     src="{{ uploaded_asset(get_setting('header_logo')) }}"
                                     alt="{{ get_setting('site_name') }}">
                            @else
                                <img class="h-40px" height="40"
                                     src="{{static_asset('assets/img/icons/logo-25.svg')}}"
                                     alt="{{ get_setting('site_name') }}">
                            @endif
                        </a>
                    </div>
                    <div class="col">
                        <div class="row justify-content-end gutters-5">
                            <div class="col-auto">
                                <div id="cart_icon_fixed">
                                    @include('frontend.partials.cart')
                                </div>
                            </div>
                            <div class="col-auto">
                                <div id="my_account">
                                    @include('frontend.partials.my_account')
                                </div>
                            </div>
                            @if(get_setting('show_language_switcher') == 'on')
                                <div class="col-auto">
                                    <div class="dropdown lang-dropdown" id="lang-change-fixed">
                                        @php
                                            if(Session::has('locale')){
                                                $locale = Session::get('locale', Config::get('app.locale'));
                                            }
                                            else{
                                                $locale = 'en';
                                            }
                                        @endphp
                                        <a href="javascript:void(0)" class="dropdown-toggle no-arrow" data-toggle="dropdown" data-display="static">
                                        <span class="header-icon">
                                            {{--                                        @include('frontend.partials.language_choice')--}}
                                            <svg class="h-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15.6 16.1">
                                                <use xlink:href="{{static_asset('assets/img/icons/languages-icon.svg')}}#language_choice"></use>
                                            </svg>
                                        </span>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @foreach (\App\Language::all() as $key => $language)
                                                <li>
                                                    <a href="javascript:void(0)" data-flag="{{ $language->code }}" class="dropdown-item @if($locale == $language->code) active @endif">
                                                        {{ toUpper($language->lang_code) }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<?php /*<!-- Top Bar -->
<div class="top-navbar border-bottom border-soft-secondary z-1035">
    <div class="container container-2">
        <div class="row">

            <div class="col-lg-7 col">
                @if(hasAccessOnContent())
                    <ul class="list-inline d-flex justify-content-between justify-content-lg-start mb-0">
                        @if(get_setting('show_language_switcher') == 'on')
                            <li class="list-inline-item dropdown mr-3" id="lang-change">
                                @php
                                    if(Session::has('locale')){
                                        $locale = Session::get('locale', Config::get('app.locale'));
                                    }
                                    else{
                                        $locale = 'en';
                                    }
                                @endphp
                                <a href="javascript:void(0)" class="dropdown-toggle text-reset py-2"
                                   data-toggle="dropdown" data-display="static">
                                    <img src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                         data-src="{{ static_asset('assets/img/flags/'.$locale.'.png') }}"
                                         class="mr-2 lazyload"
                                         alt="{{ \App\Language::where('code', $locale)->first()->name }}" height="11">
                                    <span
                                        class="opacity-60">{{ \App\Language::where('code', $locale)->first()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-left">
                                    @foreach (\App\Language::all() as $key => $language)
                                        <li>
                                            <a href="javascript:void(0)" data-flag="{{ $language->code }}"
                                               class="dropdown-item @if($locale == $language) active @endif">
                                                <img src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                     data-src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}"
                                                     class="mr-1 lazyload" alt="{{ $language->name }}" height="11">
                                                <span class="language">{{ $language->name }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif

                        @if(get_setting('show_currency_switcher') == 'on')
                            <li class="list-inline-item dropdown" id="currency-change">
                                @php
                                    if(Session::has('currency_code')){
                                        $currency_code = Session::get('currency_code');
                                    }
                                    else{
                                        $currency_code = \App\Currency::findOrFail(\App\BusinessSetting::where('type', 'system_default_currency')->first()->value)->code;
                                    }
                                @endphp
                                <a href="javascript:void(0)" class="dropdown-toggle text-reset py-2 opacity-60"
                                   data-toggle="dropdown" data-display="static">
                                    {{ \App\Currency::where('code', $currency_code)->first()->name }} {{ (\App\Currency::where('code', $currency_code)->first()->symbol) }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right dropdown-menu-lg-left">
                                    @foreach (\App\Currency::where('status', 1)->get() as $key => $currency)
                                        <li>
                                            <a class="dropdown-item @if($currency_code == $currency->code) active @endif"
                                               href="javascript:void(0)"
                                               data-currency="{{ $currency->code }}">{{ $currency->name }}
                                                ({{ $currency->symbol }})</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    </ul>
                @endif
            </div>
            <div class="col-5 p-0 m-0 text-right d-none d-lg-block my-lh-0-7">
                <ul class="list-inline mb-0">

                    <li class="list-inline-item mr-3">
                        <a href="#cards-home-class" class="text-reset py-2 d-inline-block ff-Roboto fs-15 opacity-50">{{ translate('What')}}</a>
                    </li>
                    <li class="list-inline-item mr-3">
                        <a href="" class="text-reset py-2 d-inline-block ff-Roboto fs-15 opacity-50">{{ translate('|')}}</a>
                    </li>
                    <li class="list-inline-item mr-3">
                        <a href="#register-new-tag-class" class="text-reset py-2 d-inline-block ff-Roboto fs-15 opacity-50">{{ translate('Links')}}</a>
                    </li>
                    <li class="list-inline-item mr-3">
                        <a href="" class="text-reset py-2 d-inline-block ff-Roboto fs-15 opacity-50">{{ translate('|')}}</a>
                    </li>
                    <li class="list-inline-item mr-3">
                        <a href="" class="text-reset py-2 d-inline-block ff-Roboto fs-15 opacity-50">{{ translate('To')}}</a>
                    </li>
                    <li class="list-inline-item mr-3">
                        <a href="" class="text-reset py-2 d-inline-block ff-Roboto fs-15 opacity-50">{{ translate('|')}}</a>
                    </li>
                    <li class="list-inline-item mr-3">
                        <a href="" class="text-reset py-2 d-inline-block ff-Roboto fs-15 opacity-50">{{ translate('Put')}}</a>
                    </li>
                    <li class="list-inline-item mr-3">
                        <a href="" class="text-reset py-2 d-inline-block ff-Roboto fs-15 opacity-50">{{ translate('|')}}</a>
                    </li>
                    <li class="list-inline-item mr-3">
                        <a href="" class="text-reset py-2 d-inline-block ff-Roboto fs-15 opacity-50">{{ translate('Here')}}</a>
                    </li>


                </ul>
            </div>
        </div>
    </div>
</div>
<!-- END Top Bar -->

@if(hasAccessOnContent())
    <div class="d-lg-none ml-auto mr-0">
        <a class="p-2 d-block text-reset" href="javascript:void(0);" data-toggle="class-toggle"
           data-target=".front-header-search">
            <i class="las la-search la-flip-horizontal la-2x"></i>
        </a>
    </div>

    <div class="flex-grow-1 front-header-search d-flex align-items-center bg-white">
        <div class="position-relative flex-grow-1">
            <form action="{{ route('search') }}" method="GET" class="stop-propagation">
                <div class="d-flex position-relative align-items-center">
                    <div class="d-lg-none" data-toggle="class-toggle" data-target=".front-header-search">
                        <button class="btn px-2" type="button"><i class="la la-2x la-long-arrow-left"></i>
                        </button>
                    </div>
                    <div class="input-group">
                        <input type="text" class="border-0 border-lg form-control" id="search" name="q"
                               placeholder="{{translate('I am shopping for...')}}" autocomplete="off">
                        <div class="input-group-append d-none d-lg-block">
                            <button class="btn btn-primary" type="submit">
                                <i class="la la-search la-flip-horizontal fs-18"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <div
                class="typed-search-box stop-propagation document-click-d-none d-none bg-white rounded shadow-lg position-absolute left-0 top-100 w-100"
                style="min-height: 200px">
                <div class="search-preloader absolute-top-center">
                    <div class="dot-loader">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
                <div class="search-nothing d-none p-3 text-center fs-16">

                </div>
                <div id="search-content" class="text-left">

                </div>
            </div>
        </div>
    </div>
@endif


@if ( get_setting('header_menu_labels') !=  null )
    <div class="bg-white border-top border-gray-200 py-1">
        <div class="container">
            <ul class="list-inline mb-0 pl-0 mobile-hor-swipe text-center">
                @foreach (json_decode( get_setting('header_menu_labels'), true) as $key => $value)
                <li class="list-inline-item mr-0">
                    <a href="{{ json_decode( get_setting('header_menu_links'), true)[$key] }}" class="opacity-60 fs-14 px-3 py-2 d-inline-block fw-600 hov-opacity-100 text-reset">
                        {{ translate($value) }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

@if(Route::currentRouteName() != 'home')
    <div class="hover-category-menu position-absolute w-100 top-100 left-0 right-0 d-none z-3"
         id="hover-category-menu">
        <div class="container">
            <div class="row gutters-10 position-relative">
                <div class="col-lg-3 position-static">
                    @include('frontend.partials.category_menu')
                </div>
            </div>
        </div>
    </div>
@endif */ ?>
