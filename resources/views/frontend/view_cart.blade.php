@extends('frontend.layouts.app')

@section('content')

    <div class="my-55px my-md-90px my-xxl-125px">
        <div class="container">
            <div class="mx-auto mw-1350px">
                <h1 class="fs-18 md-fs-22 xxl-fs-25 fw-700 mb-20px mb-md-30px" data-aos="fade-right">{{toUpper(translate('My Cart'))}}</h1>
                <div class="row gutters-3 md-gutters-10 xxl-gutters-15 mb-20px mb-md-30px fs-16">
                    <div class="col-6" data-aos="fade-right">
                        <div class="cart-step-box active">
                            {{toUpper(translate('Additions'))}}
                        </div>
                    </div>
                    <div class="col-6" data-aos="fade-left">
                        <div class="cart-step-box">
                            {{toUpper(translate('Payment'))}}
                        </div>
                    </div>
                </div>

                <div id="cart-summary" data-aos="fade-up">
                    @include('frontend.partials.cart_table')
                </div>
            </div>
        </div>
    </div>
    {{--    <div class="contain-without-footer">--}}
    {{--        <section class="checkout-steps mt-65px mt-md-100px mb-35px mb-sm-40px text-center text-md-left font-play overflow-hidden">--}}
    {{--            <div class="container">--}}
    {{--                <div class="mx-auto mw-1350px">--}}
    {{--                    <div class="row gutters-5 lg-gutters-15">--}}
    {{--                        <div class="col-4">--}}
    {{--                            <div class="px-2px px-md-10px px-xl-20px py-10px py-sm-15px checkout-step-box active">--}}
    {{--                                <h3 class="fs-13 md-fs-16 xl-fs-18 fw-700 m-0">--}}
    {{--                                    <span class="fs-13 md-fs-18 xl-fs-20 mr-1 mr-md-2">01.</span>--}}
    {{--                                    {{ translate('My cart')}}--}}
    {{--                                </h3>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                        <div class="col-4">--}}
    {{--                            <div class="px-2px px-md-10px px-xl-20px py-10px py-sm-15px checkout-step-box">--}}
    {{--                                <h3 class="fs-13 md-fs-16 xl-fs-18 fw-700 m-0">--}}
    {{--                                    <span class="fs-13 md-fs-18 xl-fs-20 mr-1 mr-md-2">02.</span>--}}
    {{--                                    <span class="d-none d-md-inline">{{ translate('Shipping & Delivery')}}</span>--}}
    {{--                                    <span class="d-md-none">{{ translate('Shipping')}}</span>--}}
    {{--                                </h3>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                        <div class="col-4">--}}
    {{--                            <div class="px-2px px-md-10px px-xl-20px py-10px py-sm-15px checkout-step-box">--}}
    {{--                                <h3 class="fs-13 md-fs-16 xl-fs-18 fw-700 m-0">--}}
    {{--                                    <span class="fs-13 md-fs-18 xl-fs-20 mr-1 mr-md-2">03.</span>--}}
    {{--                                    {{ translate('Payment')}}--}}
    {{--                                </h3>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
    {{--                    </div>--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--        </section>--}}
    {{--        @if(Session::get('cyprus_only_warning'))--}}
    {{--            <section class="mb-20px">--}}
    {{--                <div class="container">--}}
    {{--                    <div class="mx-auto mw-1350px">--}}
    {{--                        <div class="cyprus-warning-alert fs-11 fw-500">--}}
    {{--                            <h3 class="fs-12 fw-700">{{translate('Shipping Restriction: Cyprus Only')}}</h3>--}}
    {{--                            <p>{{translate("We're sorry, but some items in your cart cannot be shipped outside Cyprus. To continue with your order, please remove the restricted item(s) from your cart. If you have any questions, feel free to contact our customer support.")}}</p>--}}
    {{--                        </div>--}}
    {{--                    </div>--}}
    {{--                </div>--}}
    {{--            </section>--}}
    {{--        @endif--}}
    {{--        <section class="mb-75px mb-lg-100px mb-xxl-150px overflow-hidden" id="cart-summary">--}}
    {{--            @include('frontend.partials.cart_table')--}}
    {{--        </section>--}}
    {{--    </div>--}}
@endsection

@section('modal')
    @if(!Auth::check())
        <div id="guest-checkout-side-popup" class="side-popup">
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
                            <div class="l-space-1-2 mb-20px text-default-50 fs-11 sm-fs-16">
                                <h2 class="fs-32 sm-fs-45 font-play mb-10px mb-sm-20px fw-700 lh-1 text-secondary">
                                    {{translate('Please login to continue')}}
                                </h2>
                                <p>{{translate('If you have an account, login with your email address.')}}</p>
                            </div>
                            <form class="form-default" role="form" action="{{ route('cart.login.submit') }}"
                                  method="POST">
                                @csrf
                                <div class="form-group mb-10px mb-sm-15px">
                                    <div
                                        class="form-control-with-label small-focus small-field @if(old('email')) focused @endif ">
                                        @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                            <label>{{translate('Email Or Phone')}}</label>
                                            <input type="text"
                                                   class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                   value="{{ old('email') }}" name="email" id="email">
                                        @else
                                            <label>{{translate('Email')}}</label>
                                            <input type="email"
                                                   class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                   value="{{ old('email') }}" name="email">
                                        @endif
                                    </div>
                                    @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                        <span
                                            class="opacity-60">{{  translate('Use country code before number') }}</span>
                                    @endif
                                </div>

                                <div class="form-group mb-10px">
                                    <div class="form-control-with-label small-focus small-field">
                                        <label>{{translate('Password')}}</label>
                                        <input type="password" name="password" class="form-control">
                                    </div>
                                </div>

                                <div class="row gutters-5 mb-10px text-black-50 fs-10 sm-fs-14 fw-500">
                                    <div class="col-6">
                                        <label class="sk-checkbox m-0">
                                            <input type="checkbox"
                                                   name="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <span class="sk-square-check"></span>
                                            {{  translate('Remember Me') }}
                                        </label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <a href="{{ route('password.request') }}"
                                           class="hov-text-primary">{{ translate('Forgot password?')}}</a>
                                    </div>
                                </div>
                                <div class="mb-30px mb-sm-40px">
                                    <button type="submit"
                                            class="btn btn-outline-primary btn-block fs-16 fw-500 py-10px">{{toUpper(translate('Login'))}}</button>
                                </div>
                            </form>
                            <div
                                class="text-center py-20px py-sm-30px text-default-50 border-top border-bottom border-default-200">
                                <p class="mb-1">{{ translate('Dont have an account?')}}</p>
                                <a href="{{ route('user.registration') }}"
                                   class="border-bottom border-inherit text-secondary hov-text-primary">
                                    {{toUpper(translate('Create Account Now'))}}
                                </a>
                            </div>
                            @if (\App\BusinessSetting::where('type', 'guest_checkout_active')->first()->value == 1)
                                <div
                                    class="text-center py-20px py-sm-30px text-default-50 border-bottom border-default-200">
                                    <p class="mb-1">{{translate('You can also')}}</p>
                                    <a href="{{ route('checkout.shipping_info') }}"
                                       class="border-bottom border-inherit text-secondary hov-text-primary">
                                        {{toUpper(translate('Checkout as Guest'))}}
                                    </a>
                                </div>
                            @endif
                            @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                                <div class="separator mb-3">
                                    <span class="bg-white px-3 opacity-60">{{ translate('Or Login With')}}</span>
                                </div>
                                <ul class="list-inline social colored text-center mb-3">
                                    @if (\App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1)
                                        <li class="list-inline-item">
                                            <a href="{{ route('social.login', ['provider' => 'facebook']) }}"
                                               class="facebook">
                                                <i class="lab la-facebook-f"></i>
                                            </a>
                                        </li>
                                    @endif
                                    @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1)
                                        <li class="list-inline-item">
                                            <a href="{{ route('social.login', ['provider' => 'google']) }}"
                                               class="google">
                                                <i class="lab la-google"></i>
                                            </a>
                                        </li>
                                    @endif
                                    @if (\App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                                        <li class="list-inline-item">
                                            <a href="{{ route('social.login', ['provider' => 'twitter']) }}"
                                               class="twitter">
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
    @endif
@endsection

@section('script')
    <script type="text/javascript">
        function removeFromCartView(e, key) {
            e.preventDefault();
            removeFromCart(key);
        }

        {{--function updateQuantity(key, element) {--}}
        {{--    $('#cart-summary').addClass('loader');--}}
        {{--    $.post('{{ route('cart.updateQuantity') }}', {--}}
        {{--        _token: '{{ csrf_token() }}',--}}
        {{--        key: key,--}}
        {{--        quantity: element.value--}}
        {{--    }, function (data) {--}}
        {{--        updateNavCart();--}}
        {{--        $('#cart-summary').html(data).removeClass('loader');--}}
        {{--    });--}}
        {{--}--}}

        {{--function showCheckoutModal() {--}}
        {{--    $('#GuestCheckout').modal();--}}
        {{--}--}}

        function submitOrder(el) {
            $(el).prop('disabled', true);
            if ($('#agree_checkbox').is(":checked")) {
                addLoader();
                $.ajax({
                    method: "POST",
                    url: "{{ route('viva.pay_order') }}",
                    dataType: "JSON",
                    data: {
                        _token: '{{ csrf_token() }}',
                        lng: '1'
                    },
                    success: function (data) {
                        window.location.href = data.RedirectUrl;
                        // removeLoader();
                    }
                });

            } else {
                $('#checkout-error-agree').text('{{translate('You need to agree with our policies')}}');
                $(el).prop('disabled', false);
            }
        }


        function hide_checkout_error(){
            if($('#agree_checkbox').is(":checked")){
                $('#checkout-error-agree').text('');
            } else {
                $('#checkout-error-agree').text('You need to agree with our policies');
            }
        }

    </script>
@endsection
