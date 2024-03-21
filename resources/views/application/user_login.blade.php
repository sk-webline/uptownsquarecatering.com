@extends('application.layouts.login')

@section('meta_title'){{ translate('Login') }}@endsection

@section('content')

    <button class="btn btn-primary d-none" id="install-btn"> Install App</button>

    <div id="login" class="my-50px">
        <div class="container">
            <div class="p-20px login-box">
                <div class="text-center mb-30px">
                    <h1 class="fs-18 fw-700 m-0">{{toUpper(translate('Login'))}}</h1>
                    <div class="border-top border-width-2 border-secondary w-55px mx-auto my-10px"></div>
                    <p class="text-black-50">{{translate('Login with your credentials created in our website')}}</p>
                </div>
                <form class="fs-16" action="{{ route('application.auth') }}" method="POST">
                    @csrf
                    <div class="form-group mb-15px">
                        <div
                            class="form-control-with-label small-focus @if (old('username')) focused @endif">
                            <label>{{ translate('Username')}}</label>
                            <input type="text"
                                   class="form-control pt-20px pb-5px {{ $errors->has('username') ? ' is-invalid' : '' }}"
                                   value="{{ old('username') }}" name="username" id="username" >
                        </div>
                        @if ($errors->has('username'))
                            <div class="invalid-feedback fs-14 d-block" role="alert">{{ $errors->first('username') }}</div>
                        @endif
                    </div>
                    <div class="form-group mb-10px">
                        <div class="form-control-with-label small-focus">
                            <label>{{ translate('Password')}}</label>
                            <input type="password"
                                   class="form-control pt-20px pb-5px {{ $errors->has('password') ? ' is-invalid' : '' }}"
                                   name="password"
                            >
                        </div>
                        @if ($errors->has('password'))
                            <div class="invalid-feedback fs-14 d-block" role="alert">{{ $errors->first('password') }}</div>
                        @endif
                    </div>
                    <div class="row text-gray fs-12 mb-10px">
                        <div class="col">
                            <div class="text-left position-relative">
                                <label class="sk-checkbox m-0">
                                    <input type="checkbox" id="remember" name="remember">
                                    <span class="sk-square-check custom-square-box"></span>
                                    {{ translate('Remember Me')}}
                                </label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="javascript:void(0);" class="border-bottom border-inherit hov-text-secondary" data-notification="forgot-notification">{{translate('Forgot Password')}}</a>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-block btn-outline-black fs-16 py-5px fw-500">{{toUpper(translate('Login'))}}</button>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('notification-popup')
    <div id="forgot-notification" class="notification-pop">
        <div class="notification-pop-scroll c-scrollbar">
            <div class="notification-pop-wrap">
                <div class="notification-pop-box rounded-30px">
                    <div class="row gutters-5 fs-12 align-items-center text-center">
                        <div class="col-auto">
                            <img class="h-15px" src="{{static_asset('assets/img/icons/notification-info.svg')}}" alt="">
                        </div>
                        <div class="col">{{translate('Password cannot be changed from the App but from the parent’s panel.')}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

