@extends('frontend.layouts.app')

@section('content')
    @php
        $pay_on_credit = (Auth::check() && Auth::user()->pay_on_credit == 1) ? true : false;
        $pay_on_delivery = (Auth::check() && Auth::user()->pay_on_delivery == 1) ? true : false;
    @endphp
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
                        <a href="{{ route('checkout.shipping_info') }}" class="d-block px-2px px-md-10px px-xl-20px py-10px py-sm-15px checkout-step-box">
                            <span class="d-block fs-13 md-fs-16 xl-fs-18 fw-700 m-0">
                                <span class="fs-13 md-fs-18 xl-fs-20 mr-1 mr-md-2">02.</span>
                                <span class="d-none d-md-inline">{{ translate('Shipping & Delivery')}}</span>
                                <span class="d-md-none">{{ translate('Shipping')}}</span>
                            </span>
                        </a>
                    </div>
                    <div class="col-4">
                        <div class="px-2px px-md-10px px-xl-20px py-10px py-sm-15px checkout-step-box active">
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
    <section class="mb-75px mb-lg-100px mb-xxl-150px overflow-hidden" id="cart-summary">
        <div class="container">
            <div class="mx-auto mw-1350px">
                <div class="border-bottom border-default-300 border-width-3 text-black-30 pb-10px mb-15px mb-md-25px">
                    <h1 class="fs-11 md-fs-16 fw-600 mb-0">{{ translate('Select Payment Options') }}</h1>
                </div>
                <div class="row gutters-10">
                    <div class="col-lg-7">
                        <form action="{{ route('payment.checkout') }}" class="form-default" role="form" method="POST" id="checkout-form">
                            @csrf
                            <div class="row gutters-10 fs-13 xl-fs-16">
                                <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                    <label class="sk-megabox d-block mb-0">
                                        <input value="viva_wallet" class="online_payment" type="radio" name="payment_option" checked>
                                        <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                            <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                            <span class="flex-grow-1 pl-2 pr-50px text-black">{{toUpper(translate('Online Payment'))}}</span>
                                            <span class="sk-megabox-payment-img">
                                                <img class="h-20px" src="{{static_asset('assets/img/visa-master-card.png')}}" alt="">
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                @if($pay_on_credit)
                                    <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                        <label class="sk-megabox d-block mb-0">
                                            <input value="pay_on_credit" class="online_payment" type="radio" name="payment_option" checked>
                                            <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-2 text-black">{{ toUpper(translate('On Credit'))}}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                @if(\App\BusinessSetting::where('type', 'paypal_payment')->first()->value == 1)
                                    <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                        <label class="sk-megabox d-block mb-3">
                                            <input value="paypal" class="online_payment" type="radio" name="payment_option" checked>
                                            <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-2 text-black">{{ toUpper(translate('Paypal'))}}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                @if(\App\BusinessSetting::where('type', 'stripe_payment')->first()->value == 1)
                                    <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                        <label class="sk-megabox d-block mb-3">
                                            <input value="stripe" class="online_payment" type="radio" name="payment_option" checked>
                                            <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-2 text-black">{{ toUpper(translate('Stripe'))}}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                @if(\App\BusinessSetting::where('type', 'sslcommerz_payment')->first()->value == 1)
                                    <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                        <label class="sk-megabox d-block mb-3">
                                            <input value="sslcommerz" class="online_payment" type="radio" name="payment_option" checked>
                                            <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-2 text-black">{{ toUpper(translate('sslcommerz'))}}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                @if(\App\BusinessSetting::where('type', 'instamojo_payment')->first()->value == 1)
                                    <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                        <label class="sk-megabox d-block mb-3">
                                            <input value="instamojo" class="online_payment" type="radio" name="payment_option" checked>
                                            <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-2 text-black">{{ toUpper(translate('Instamojo'))}}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                @if(\App\BusinessSetting::where('type', 'razorpay')->first()->value == 1)
                                    <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                        <label class="sk-megabox d-block mb-3">
                                            <input value="razorpay" class="online_payment" type="radio" name="payment_option" checked>
                                            <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-2 text-black">{{ toUpper(translate('Razorpay'))}}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                @if(\App\BusinessSetting::where('type', 'paystack')->first()->value == 1)
                                    <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                        <label class="sk-megabox d-block mb-3">
                                            <input value="paystack" class="online_payment" type="radio" name="payment_option" checked>
                                            <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-2 text-black">{{ toUpper(translate('Paystack'))}}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                @if(\App\BusinessSetting::where('type', 'voguepay')->first()->value == 1)
                                    <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                        <label class="sk-megabox d-block mb-3">
                                            <input value="voguepay" class="online_payment" type="radio" name="payment_option" checked>
                                            <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-2 text-black">{{ toUpper(translate('VoguePay'))}}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                @if(\App\BusinessSetting::where('type', 'payhere')->first()->value == 1)
                                    <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                        <label class="sk-megabox d-block mb-3">
                                            <input value="payhere" class="online_payment" type="radio" name="payment_option" checked>
                                            <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-2 text-black">{{ toUpper(translate('payhere'))}}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                @if(\App\BusinessSetting::where('type', 'ngenius')->first()->value == 1)
                                    <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                        <label class="sk-megabox d-block mb-3">
                                            <input value="ngenius" class="online_payment" type="radio" name="payment_option" checked>
                                            <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-2 text-black">{{ toUpper(translate('ngenius'))}}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                @if(\App\BusinessSetting::where('type', 'iyzico')->first()->value == 1)
                                    <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                        <label class="sk-megabox d-block mb-3">
                                            <input value="iyzico" class="online_payment" type="radio" name="payment_option" checked>
                                            <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-2 text-black">{{ toUpper(translate('Iyzico'))}}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                @if(\App\BusinessSetting::where('type', 'nagad')->first()->value == 1)
                                    <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                        <label class="sk-megabox d-block mb-3">
                                            <input value="nagad" class="online_payment" type="radio" name="payment_option" checked>
                                            <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-2 text-black">{{ toUpper(translate('Nagad'))}}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                @if(\App\BusinessSetting::where('type', 'bkash')->first()->value == 1)
                                    <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                        <label class="sk-megabox d-block mb-3">
                                            <input value="bkash" class="online_payment" type="radio" name="payment_option" checked>
                                            <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-2 text-black">{{ toUpper(translate('Bkash'))}}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                @if(\App\Addon::where('unique_identifier', 'african_pg')->first() != null && \App\Addon::where('unique_identifier', 'african_pg')->first()->activated)
                                    @if(\App\BusinessSetting::where('type', 'mpesa')->first()->value == 1)
                                        <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                            <label class="sk-megabox d-block mb-3">
                                                <input value="mpesa" class="online_payment" type="radio" name="payment_option" checked>
                                                <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                    <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                    <span class="flex-grow-1 pl-2 text-black">{{ toUpper(translate('mpesa'))}}</span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                    @if(\App\BusinessSetting::where('type', 'flutterwave')->first()->value == 1)
                                        <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                            <label class="sk-megabox d-block mb-3">
                                                <input value="flutterwave" class="online_payment" type="radio" name="payment_option" checked>
                                                <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                    <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                    <span class="flex-grow-1 pl-2 text-black">{{ toUpper(translate('flutterwave'))}}</span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                    @if(\App\BusinessSetting::where('type', 'payfast')->first()->value == 1)
                                        <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                            <label class="sk-megabox d-block mb-3">
                                                <input value="payfast" class="online_payment" type="radio" name="payment_option" checked>
                                                <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                    <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                    <span class="flex-grow-1 pl-2 text-black">{{ toUpper(translate('payfast'))}}</span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                @endif
                                @if(\App\Addon::where('unique_identifier', 'paytm')->first() != null && \App\Addon::where('unique_identifier', 'paytm')->first()->activated)
                                    <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                        <label class="sk-megabox d-block mb-3">
                                            <input value="paytm" class="online_payment" type="radio" name="payment_option" checked>
                                            <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-2 text-black">{{ toUpper(translate('Paytm'))}}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                @if(\App\BusinessSetting::where('type', 'cash_payment')->first()->value == 1 && $pay_on_delivery)
                                    @php
                                        $digital = 0;
                                        $cod_on = 1;
                                        foreach(Session::get('cart') as $cartItem){
                                            if($cartItem['digital'] == 1){
                                                $digital = 1;
                                            }
                                            if($cartItem['cash_on_delivery'] == 0){
                                                $cod_on = 0;
                                            }
                                        }
                                    @endphp
                                    @if($digital != 1 && $cod_on == 1)
                                        <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                            <label class="sk-megabox d-block mb-3">
                                                <input value="cash_on_delivery" class="online_payment" type="radio" name="payment_option" checked>
                                                <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                    <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                    <span class="flex-grow-1 pl-2 text-black">{{toUpper(translate('Cash on Delivery'))}}</span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                @endif
                                @if (Auth::check())
                                    @if (\App\Addon::where('unique_identifier', 'offline_payment')->first() != null && \App\Addon::where('unique_identifier', 'offline_payment')->first()->activated)
                                        @foreach(\App\ManualPaymentMethod::all() as $method)
                                            <div class="col-sm-6 col-xxxl-4 mb-15px payment-method-item">
                                                <label class="sk-megabox d-block mb-3">
                                                    <input value="{{ $method->heading }}" type="radio" name="payment_option" onchange="toggleManualPaymentData({{ $method->id }})" data-id="{{ $method->id }}" checked>
                                                    <span class="d-flex px-10px py-15px sk-megabox-elem with-border rounded-0">
                                                        <span class="sk-rounded-check flex-shrink-0 mt-1"></span>
                                                            <span class="flex-grow-1 pl-2 text-black">{{ toUpper($method->heading) }}</span>
                                                        </span>
                                                </label>
                                            </div>
                                        @endforeach

                                        @foreach(\App\ManualPaymentMethod::all() as $method)
                                            <div id="manual_payment_info_{{ $method->id }}" class="d-none">
                                                @php echo $method->description @endphp
                                                @if ($method->bank_info != null)
                                                    <ul>
                                                        @foreach (json_decode($method->bank_info) as $key => $info)
                                                            <li>{{ translate('Bank Name') }} - {{ $info->bank_name }}, {{ translate('Account Name') }} - {{ $info->account_name }}, {{ translate('Account Number') }} - {{ $info->account_number}}, {{ translate('Routing Number') }} - {{ $info->routing_number }}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                @endif
                            </div>
                            @if (\App\Addon::where('unique_identifier', 'offline_payment')->first() != null && \App\Addon::where('unique_identifier', 'offline_payment')->first()->activated)
                                <div class="bg-white border mb-3 p-3 rounded text-left d-none">
                                    <div id="manual_payment_description">

                                    </div>
                                </div>
                            @endif
                            @if (Auth::check() && \App\BusinessSetting::where('type', 'wallet_system')->first()->value == 1)
                                <div class="separator mb-3">
                                        <span class="bg-white px-3">
                                            <span class="opacity-60">{{ translate('Or')}}</span>
                                        </span>
                                </div>
                                <div class="text-center py-4">
                                    <div class="h6 mb-3">
                                        <span class="opacity-80">{{ translate('Your wallet balance :')}}</span>
                                        <span class="fw-600">{{ single_price(Auth::user()->balance) }}</span>
                                    </div>
                                    @if(Auth::user()->balance < $total)
                                        <button type="button" class="btn btn-secondary" disabled>{{ translate('Insufficient balance')}}</button>
                                    @else
                                        <button  type="button" onclick="use_wallet()" class="btn btn-primary fw-600">{{ translate('Pay with wallet')}}</button>
                                    @endif
                                </div>
                            @endif
                        </form>
                    </div>
                    <div class="col-lg-5">
                        <div class="payment-select-summary">
                            @include('frontend.partials.cart_summary')
                            <div class="px-15px px-xl-30px pt-10px pb-15px pb-sm-30px">
                                <div class="row gutters-5 mb-10px align-items-end">
                                    @if ( get_setting('payment_method_images') !=  null )
                                        <div class="col-sm-auto col-lg-12 col-xxl-auto mb-10px">
                                            <div class="row gutters-5 justify-content-end">
                                                @foreach (explode(',', get_setting('payment_method_images')) as $key => $value)
                                                    <div class="col-auto">
                                                        <img src="{{ uploaded_asset($value) }}" height="20" class="mw-100 h-20px">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-sm col-lg-12 col-xxl">
                                        <div class="text-right text-default-50 fs-12 sm-fs-16 position-relative mb-10px">
                                            <label class="sk-checkbox m-0">
                                                <input type="checkbox" required id="agree_checkbox">
                                                <span class="sk-square-check"></span>
                                                {{ translate('I agree with the')}}
                                                <a class="text-reset hov-text-primary" href="{{ route('termspolicies') }}" target="_blank">{{ translate('Terms&Policies')}}</a>
                                            </label>
                                            <div id="checkout-error-agree" class="invalid-feedback absolute fs-10 d-block mb-0" role="alert"></div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" onclick="submitOrder(this)" class="btn btn-secondary btn-block fs-13 md-fs-18 py-10px py-md-13px mt-10px">{{toUpper(translate('Complete Order'))}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script type="text/javascript">

      $(document).ready(function(){
        $(".online_payment").click(function(){
          $('#manual_payment_description').parent().addClass('d-none');
        });
        toggleManualPaymentData($('input[name=payment_option]:checked').data('id'));
      });

      function use_wallet(){
        $('input[name=payment_option]').val('wallet');
        if($('#agree_checkbox').is(":checked")){
          $('#checkout-form').submit();
        }else{
          $('#checkout-error-agree').text('{{translate('You need to agree with our policies')}}');
        }
      }
      function submitOrder(el){
        $(el).prop('disabled', true);
        if($('#agree_checkbox').is(":checked")){
            let payment_method = $('input[name="payment_option"]:checked').val();
            addLoader();
            if (payment_method === 'viva_wallet') {
                $.ajax({
                    method:"POST",
                    url: "{{ route('viva.pay_order') }}",
                    dataType: "JSON",
                    data: {
                        _token: '{{ csrf_token() }}',
                        lng: '1'
                    },
                    success: function(data) {
                        window.location.href = data.RedirectUrl;
                        // removeLoader();
                    }
                });
            }
            else {
                $('#checkout-form').submit();
            }
        }else{
          $('#checkout-error-agree').text('{{translate('You need to agree with our policies')}}');
          $(el).prop('disabled', false);
        }
      }

      function toggleManualPaymentData(id){
        if(typeof id != 'undefined'){
          $('#manual_payment_description').parent().removeClass('d-none');
          $('#manual_payment_description').html($('#manual_payment_info_'+id).html());
        }
      }
    </script>
@endsection
