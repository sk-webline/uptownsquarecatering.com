@extends('frontend.layouts.app')

@section('content')
    <section class="py-65px">
        <div class="profile">
            <div class="container">
                <div class="mw-510px mx-auto">
                    <div class="card custom-card-background p-20px p-sm-40px fs-14 sm-fs-16">
                        <h1 class="fs-18 sm-fs-22 fw-700 mb-10px lh-1 text-center">{{toUpper(translate('Forgot Password?'))}}</h1>

                        <div class="w-55px border-top border-secondary border-width-2 mx-auto"></div>

                        <div class="text-center my-10px text-primary-50 mw-365px mx-auto fs-14 sm-fs-16">
                            <p>{{ translate('Enter your email address to recover your password.')}}</p>
                        </div>

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="form-group mb-15px">
                                <div class="form-control-with-label small-focus small-field @if(old('email')) focused @endif ">
                                    @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                        <label>{{ translate('Email or Phone') }}</label>
                                        <input id="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>
                                    @else
                                        <label>{{ translate('Email') }}</label>
                                        <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" name="email">
                                    @endif
                                </div>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback fs-12 d-block" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                            </div>
                            <button class="btn btn-outline-primary btn-block fs-16 sm-fs-18 fw-500 py-5px py-sm-13px" type="submit">{{ toUpper(translate('Send Reset Link')) }}</button>
                        </form>
                        <div class="text-center mt-35px mt-sm-50px border-top border-default-200 text-default-50 pt-20px pt-sm-30px">
                            <p class="mb-5px fw-600 text-primary-50">{{ translate('Have you found your password?')}}</p>
                            <a href="{{route('user.login')}}" class="border-bottom border-inherit hov-text-secondary fs-14 sm-fs-16">
                                {{toUpper(translate('Back to Login'))}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
