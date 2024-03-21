@extends('application.layouts.app')

@section('meta_title'){{ translate('My Account') }}@endsection

@section('content')
    <div id="account" class="my-20px">
        <div class="container">
            <h1 class="fs-14 fw-300 text-black-50 border-bottom border-black-100 pb-5px mb-25px">{{toUpper(translate('My Account'))}}</h1>
            <h2 class="fs-19 fw-700 mb-25px">{{auth()->guard('application')->user()->username}}</h2>
            <div class="account-links">
                <div class="account-link-item next">
                    <a href="{{route('application.profile')}}">
                        <span class="icon size-15px">
                            <svg class="size-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11.95 13.65">
                                <use xlink:href="{{static_asset('assets/img/icons/app-account-profile.svg')}}#content"></use>
                            </svg>
                        </span>
                        <span class="text">{{translate('Manage Profile')}}</span>
                    </a>
                </div>
                <div class="account-link-item next">
                    <a href="{{route('application.available_balance')}}">
                        <span class="icon size-15px">
                            <svg class="size-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11.45 10.52">
                                <use xlink:href="{{static_asset('assets/img/icons/app-account-balance.svg')}}#content"></use>
                            </svg>
                        </span>
                        <span class="text">{{translate('My Available Balance')}}</span>
                    </a>
                </div>
                <div class="account-link-item next">
                    <a href="{{route('application.credit_card')}}">
                        <span class="icon size-15px">
                            <svg class="size-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12.94 9.74">
                                <use xlink:href="{{static_asset('assets/img/icons/app-account-credit.svg')}}#content"></use>
                            </svg>
                        </span>
                        <span class="text">{{translate('Credit Card')}}</span>
                    </a>
                </div>
                <div class="account-link-item next">
                    <a href="{{ route('application.upcoming_meals') }}">
                        <span class="icon size-15px">
                            <svg class="size-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12.44 15.45">
                                <use xlink:href="{{static_asset('assets/img/icons/app-account-upcoming.svg')}}#content"></use>
                            </svg>
                        </span>
                        <span class="text">{{translate('Upcoming Orders')}}</span>
                    </a>
                </div>
                <div class="account-link-item next">
                    <a href="{{ route('application.history') }}">
                        <span class="icon size-15px">
                            <svg class="size-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12.15 12.15">
                                <use xlink:href="{{static_asset('assets/img/icons/app-account-history.svg')}}#content"></use>
                            </svg>
                        </span>
                        <span class="text">{{translate('Order History')}}</span>
                    </a>
                </div>
                <div class="account-link-item next">
                    <a href="{{ route('application.cart') }}">
                        <span class="icon size-15px">
                            <svg class="size-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 13.2 11.05">
                                <use xlink:href="{{static_asset('assets/img/icons/app-account-cart.svg')}}#content"></use>
                            </svg>
                        </span>
                        <span class="text">{{translate('Cart')}}</span>
                    </a>
                </div>
                <div class="account-link-item">
                    <a href={{route('application.logout')}}>
                        <span class="icon size-15px">
                            <svg class="size-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16.19 11.86">
                                <use xlink:href="{{static_asset('assets/img/icons/app-account-logout.svg')}}#content"></use>
                            </svg>
                        </span>
                        <span class="text">{{translate('Log out')}}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
