@extends('frontend.layouts.app')
@section('content')
    @php
        $full_name = getAccountName(Auth::user()->name);
    @endphp
<section class="my-35px my-md-80px my-xl-125px">
    <div class="container">
        <div class="mw-1350px mx-auto">
            <h2 class="fw-700 fs-22 md-fs-25 mb-30px mb-md-20px" data-aos="fade-right">{{toUpper(translate('Hello'))}} {{ toUpper($full_name['name']) }}!</h2>
            <div class="d-lg-flex align-items-start">
                @include('frontend.inc.user_side_nav')
                <div class="sk-user-panel mt-45px mt-lg-0" data-aos="fade-left">
                    @yield('panel_content')
                </div>
            </div>
            <div class="d-lg-none mt-45px" data-aos="fade-up">
                <h3 class="mb-15px fs-14 xl-fs-16 fw-700">{{translate('Help & Info')}}</h3>
                <div class="fs-12 xl-fs-14 text-primary-50">
                    <p class="mb-2">
                        <a class="hov-text-primary account-side-link" href="tel:{{ get_setting('contact_phone') }}">
                            <svg class="w-25px align-middle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 25">
                                <use xlink:href="{{static_asset('assets/img/icons/account-tel.svg')}}#content"></use>
                            </svg>
                            {{ get_setting('contact_phone') }}
                        </a>
                    </p>
                    <p class="mb-2 account-side-link">
                        <svg class="w-25px align-middle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 25">
                            <use xlink:href="{{static_asset('assets/img/icons/account-mail.svg')}}#content"></use>
                        </svg>
                        <img class="text-email" src="{{static_asset('assets/img/icons/email-text-dashboard.svg')}}" alt="">
                    </p>
                    <p class="mt-20px">
                        <a class="border-bottom border-inherit hov-text-primary" href="{{ route('logout') }}">{{translate('Logout')}}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
