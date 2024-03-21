@extends('frontend.layouts.app')

@section('content')
    <section class="py-65px">
        <div class="profile">
            <div class="container">
                <div class="mw-510px mx-auto">
                    <div class="card custom-card-background p-20px p-sm-40px fs-14 sm-fs-16">
                        <h1 class="fs-18 sm-fs-22 fw-700 mb-10px lh-1 text-center">{{toUpper(translate('Reset your Password'))}}</h1>
                        <div class="w-55px border-top border-secondary border-width-2 mx-auto"></div>
                        <div class="text-center my-10px text-primary-50 mw-365px mx-auto fs-14 sm-fs-16">
                            <p>{{ translate('Enter your email address and new password and confirm password.')}}</p>
                        </div>
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <div class="form-group mb-15px">
                                <div class="form-control-with-label small-focus small-field @if(old('email')) focused @endif ">
                                    <label>{{ translate('Email') }}</label>
                                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" required autofocus>
                                </div>
                                @if ($errors->has('email'))
                                    <div class="invalid-feedback fs-12 d-block" role="alert">{{ $errors->first('email') }}</div>
                                @endif
                            </div>
                            <div class="form-group mb-15px">
                                <div class="form-control-with-label small-focus small-field @if(old('code')) focused @endif ">
                                    <label>{{ translate('Verification Code') }}</label>
                                    <input id="code" type="text" class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}" name="code" value="{{ $email ?? old('code') }}" required autofocus>
                                </div>
                                @if ($errors->has('code'))
                                    <div class="invalid-feedback fs-12 d-block" role="alert">{{ $errors->first('code') }}</div>
                                @endif
                            </div>
                            <div class="form-group mb-15px">
                                <div class="form-control-with-label small-focus small-field">
                                    <label>{{ translate('New Password') }}</label>
                                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                                </div>
                                @if ($errors->has('password'))
                                    <div class="invalid-feedback fs-12 d-block" role="alert">{{ $errors->first('password') }}</div>
                                @endif
                            </div>
                            <div class="form-group mb-15px">
                                <div class="form-control-with-label small-focus small-field">
                                    <label>{{ translate('Confirm Password') }}</label>
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-outline-primary btn-block fs-16 sm-fs-18 fw-500 py-5px py-sm-13px">{{ toUpper(translate('Reset Password')) }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
