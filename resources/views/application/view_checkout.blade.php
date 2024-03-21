@extends('application.layouts.app')

@section('meta_title'){{ translate('Checkout') }}@endsection

@section('content')

    @php

        use Carbon\Carbon;
        use App\Models\CanteenProduct;

        use Illuminate\Support\Facades\Session;

         $user = auth()->guard('application')->user(); // canteen user
         $rfid_card = $user->card;
         $organisation = $rfid_card->organisation;
         $daily_limit = $user->daily_limit;

         $payment_type = Session::get('payment_type');

         $cart_count = 0;

         if(Session::has('app_cart')){
             $cart_count = count(Session::get('app_cart')->toArray());
         }

        $cart = [];

        if(Session::has('app_cart')){
            $cart = Session::get('app_cart')->toArray();
        }

        // Custom comparison function for sorting by date and then by break
        function sortByDateAndBreak($a, $b) {
            $dateComparison = strtotime($a['date']) - strtotime($b['date']);

            // If dates are equal, compare by 'break'
            if ($dateComparison == 0) {
            return $a['break_id'] - $b['break_id'];
            }

            return $dateComparison;
        }

        // Use usort to sort the array using the custom function
        usort($cart, 'sortByDateAndBreak');

        $cart_days = [];
        $break_keys = [];
        $dates = [];
        $breaks = [];
        $titles = [];
        $cart_title = [];

        foreach($cart as $key => $cart_item){

            $title = ucfirst($cart_item['day']) . ' ' . Carbon::create($cart_item['date'])->format('d/m') . ' - ' . ordinal($cart_item['break_sort']) . ' ' . translate('Break');
            $titles[] = $title;
            $cart_title[$key] = $title;

            $dates[$title] = $cart_item['date'];
            $breaks[$title] = $cart_item['break_id'];

            $break_keys[$cart_item['break_id']] = $cart_item['break_sort'];

        }

        $titles = array_unique($titles);

        if(isset($errorMessage)){
            dd($errorMessage);
        }

    @endphp

    <div id="cart-review">
        <div class="container">
            <h1 class="fs-14 fw-300 text-black-50 my-15px border-bottom border-black-100 pb-5px">{{toUpper(translate('Checkout'))}}</h1>
            <h2 class="fs-18 fw-700 mb-15px">{{toUpper(translate('Order'))}}</h2>
            <h3 class="fs-12 fw-400 pb-2px text-black-50 border-bottom border-login-box border-width-2 mb-10px">{{translate('Payment Method')}}</h3>
        </div>

            <div class="container">
                <div class="mb-5px">
                    <div class="cart-payment-option">
                        <div class="cart-payment-option-box p-5px">
                            <div class="text">
                                @if($payment_type=='current-card')
                                    <div class="title">{{translate('Selected Credit Card')}}</div>
                                    <div class="visa">Visa *****1234</div>

                                    <form name="pay_now" method="POST" action="{{route('app_viva.preauth_pay_order')}}">
                                        @csrf
                                        <input type="hidden" name="payment" value="{{$payment_type}}">

                                    </form>
                                @elseif($payment_type=='other-card')
                                    <div class="title">{{translate('Instant pay with Another Card')}}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


                <div class="text-right mb-30px">
                    <svg class="h-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 106.72 15.96">
                        <use xlink:href="{{static_asset('assets/img/icons/viva_wallet.svg')}}#viva_wallet_svg"></use>
                    </svg>
                </div>
                <div class="border-bottom border-width-2 border-login-box pb-5px mb-10px">
                    <div class="row align-items-end">
                        <div class="col fs-12 fs-12 text-black-50">
                            {{translate('Daily Limit')}}
                        </div>
                        <div class="col-auto fw-700">{{single_price($daily_limit)}}</div>
                    </div>
                </div>
            </div>

            <div class="snack-all-results">
                @foreach($titles as $title)
                <div class="cart-break-item">
                    <div class="container">
                        <div class="mb-2px text-green fw-700">{{$title}}</div>
                    </div>
                    <div class="cart-break-products">
                        @foreach($cart as $key => $cart_item)
                            @if($cart_title[$key] == $title)
                                @php
                                    $product = CanteenProduct::find($cart_item['product_id']);
                                @endphp
                                <div class="cart-break-product-item preview">
                                    <div class="row gutters-10 align-items-center">
                                        <div class="col-auto">
                                            <div class="total-quantity">
                                                {{$cart_item['quantity']}}
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="title">{{$product->getTranslation('name')}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="price">{{single_price($cart_item['price'])}}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div class="container">
                    <div class="position-relative mb-15px">
                        <label class="sk-checkbox fs-12 text-black-50 mb-5px">
                            <input type="checkbox" name="agree_policies">
                            <span class="sk-square-check"></span>
                            {{ translate('I agree with the')}}
                            <a class="text-reset hov-text-primary" href="{{ route('termspolicies') }}" target="_blank">{{ translate('Terms&Policies')}}</a>
                        </label>
                        <div id="contact-form-error-agree" class="invalid-feedback absolute fs-10 d-block" role="alert"></div>
                    </div>
                </div>
                <div class="snack-total-cart pb-70px">
                    <div class="container">
                        <button class="btn btn-block btn-secondary fs-16 fw-700" type="button" onclick="submitOrder(this)">
                            <span class="row gutters-2 align-items-center">
                                <span class="d-block col-auto">
                                    <span class="d-block cart-totals">
                                        @if(Session::has('total_items') && Session::get('total_items')>0)
                                            <span class="d-block cart-totals">{{Session::get('total_items')}}</span>
                                        @else
                                            0
                                        @endif
                                    </span>
                                </span>
                                <span class="d-block col">{{translate('Pay Now')}}</span>
                                <span class="d-block col-auto">
                                    <span class="d-block fs-15 cart-total-price">
                                         @if(Session::has('app_total'))
                                            {{single_price(Session::get('app_total'))}}
                                        @else
                                            {{single_price(0)}}
                                        @endif
                                    </span>
                                </span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>

{{--        </form>--}}
    </div>
@endsection

@section('script')

    <script type="text/javascript">
        function submitOrder(param) {
            $(param).prop('disabled', true);
            if($('input[name=agree_policies]').is(":checked")){
                // let payment_method = $('input[name="payment_option"]:checked').val();
                addLoader();
                if (payment_type === 'other-card') {
                    $.ajax({
                        method:"POST",
                        url: "{{ route('app_viva.pay_order') }}",
                        dataType: "JSON",
                        data: {
                            _token: '{{ csrf_token() }}',
                            lng: '1',
                            application_order: 'true'
                        },
                        success: function(data) {

                            console.log('viva ajax data: ', data);
                            window.location.href = data.RedirectUrl;
                            // removeLoader();
                        }
                    });
                }
                else {

                    $('form[name=pay_now]').submit();

                }
            }else{
                $('#contact-form-error-agree').text('{{translate('You need to agree with our policies')}}');
                $(param).prop('disabled', false);
            }
        }


        const payment_type = '{{$payment_type}}';

        $(document).on('click', 'input[name=agree_policies]', function (){
            $('#contact-form-error-agree').html('');
        });



    </script>
@endsection
