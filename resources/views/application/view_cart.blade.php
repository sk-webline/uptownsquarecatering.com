@extends('application.layouts.app')

@section('meta_title'){{ translate('Cart') }}@endsection

@section('content')

    @php

        use Illuminate\Support\Facades\Session;

        \App\Http\Controllers\ApplicationController::appCartRefresh();

         $user = auth()->guard('application')->user(); // canteen user
         $rfid_card = $user->card;
         $organisation = $rfid_card->organisation;
         $daily_limit = $user->daily_limit;

         $cart_count = 0;

         if(Session::has('app_cart')){
             $cart_count = count(Session::get('app_cart')->toArray());
         }


    @endphp

    <div id="cart-review">
        <div class="container">
            <h1 class="fs-14 fw-300 text-black-50 my-15px border-bottom border-black-100 pb-5px">{{toUpper(translate('Cart'))}}</h1>
            <h2 class="fs-18 fw-700 mb-15px">{{toUpper(translate('Order'))}}</h2>
            <h3 class="fs-12 fw-400 pb-2px text-black-50 border-bottom border-login-box border-width-2 mb-10px">{{translate('Payment Method')}}</h3>
        </div>
        <form @if($cart_count>0) action="{{route('application.checkout')}}" method="POST" @endif>
            @csrf
            <div class="container">

{{--             if there is preselected visa cart   --}}
                @if($user->credit_card_token_id !=null)
                    @php
                        $credit_card = \App\Models\CreditCard::find($user->credit_card_token_id);
                    @endphp

                    @if($credit_card !=null)
                        <div class="mb-10px">
                            <label class="cart-payment-option">
                                <input type="radio" name="payment" value="current-card">
                                <div class="cart-payment-option-box">
                                    <div class="row gutters-5 align-items-center">
                                        <div class="col-auto">
                                            <div class="check">
                                                <svg class="h-10px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14.31 10.22">
                                                    <use xlink:href="{{static_asset('assets/img/icons/check-break.svg')}}#content"></use>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="text">
                                                <div class="title">{{translate('Selected Credit Card')}}</div>
                                                <div class="visa">Visa *****{{substr($credit_card->credit_card_number, -4)}}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @endif
                @endif
                <div class="mb-5px">
                    <label class="cart-payment-option">
                        <input type="radio" name="payment" value="other-card">
                        <div class="cart-payment-option-box">
                            <div class="row gutters-5 align-items-center">
                                <div class="col-auto">
                                    <div class="check">
                                        <svg class="h-10px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14.31 10.22">
                                            <use xlink:href="{{static_asset('assets/img/icons/check-break.svg')}}#content"></use>
                                        </svg>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="text">
                                        <div class="title">{{translate('Instant pay with Another Card')}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
                <div class="row">
                    <span class="col-auto error-msg-payment fs-13 text-danger"></span>
                    <div class="text-right mb-30px col">
                        <svg class="h-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 106.72 15.96">
                            <use xlink:href="{{static_asset('assets/img/icons/viva_wallet.svg')}}#viva_wallet_svg"></use>
                        </svg>
                    </div>
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

              <div class="cart-results">
                  @include('application.partials.cart_results')
              </div>

                <div class="snack-total-cart pb-70px">
                    <div class="container">
                        <button class="btn btn-block btn-secondary fs-16 fw-700" @if($cart_count>0)  type="submit" @else type="button" @endif>
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
                                <span class="d-block col">{{translate('Continue')}}</span>
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
        </form>
    </div>
@endsection

@section('modal')
    @include('application.modals.delete_modal')
@endsection

@section('script')

    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

    <script>
        /*Show Quantity Controls*/
        var inactivityTimeout;
        $(document).on('click', '.cart-break-add .added', function (){
            $('.cart-break-add .added').not(this).parent('.cart-break-add').removeClass('open-quantity');
            $(this).parent('.cart-break-add').addClass('open-quantity');
            clearTimeout(inactivityTimeout);
            inactivityTimeout = setTimeout(function() {
                $('.cart-break-add').removeClass('open-quantity');
            }, 5000);
        });
        $(document).on('click', '.cart-break-add .quantity .control', function (){
            clearTimeout(inactivityTimeout);
            inactivityTimeout = setTimeout(function() {
                $('.cart-break-add').removeClass('open-quantity');
            }, 5000);
        });
        $(document).mouseup(function(e) {
            if(!$('.cart-break-add').is(e.target) && $('.cart-break-add').has(e.target).length === 0) {
                $('.cart-break-add').removeClass('open-quantity');
            }
        });

        $(document).on('click', '.cart-break-add .quantity .quantity-plus', function (){
            var old_val = parseInt($(this).parents('.quantity').find('.quantity-total').text(), 10);
            $(this).parents('.cart-break-add').find('.quantity-total').text(old_val+1);
            $(this).parents('.cart-break-add').find('.added .added_amount').text(old_val+1);
            // console.log('allagi old_val:',  old_val);

        });

        $(document).on('click', '.cart-break-add .quantity .quantity-plus', debounce(function (){

            var quantity = parseInt($(this).parents('.cart-break-add').find('.quantity-total').text(), 10);
            var product_id = $(this).parents('.cart-break-add').attr('data-productID');
            var date = $(this).parents('.cart-break-add').attr('data-date');
            var break_id = $(this).parents('.cart-break-add').attr('data-breakID');
            var break_sort = $(this).parents('.cart-break-add').attr('data-breakSort');

            var quantity_total_element = $(this).parents('.cart-break-add').find('.quantity-total');
            var added_amount_element = $(this).parents('.cart-break-add').find('.added .added_amount');

            console.log('send ajax with quantity: ', quantity, ' product: ', product_id, ' date: ', date, ' break_sort: ', break_sort);

            // return;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:"POST",
                url: '{{ route('application.addToCart') }}',
                data: {
                    product_id: product_id,
                    date: date,
                    break_id: break_id,
                    break_sort: break_sort,
                    quantity: quantity,
                },
                success: function(data){

                    console.log('addToCart: ', data);
                    quantity_total_element.html(data.product_quantity_in_cart);
                    added_amount_element.html(data.product_quantity_in_cart);

                    if(data.status == 0){
                        // console.log('Error: ',data );
                    }
                    if(data.status == 1) {
                        $('.snack-total-cart .cart-total-price').html(data.total);
                        $('.snack-total-cart .cart-totals').html(data.total_items);
                        $('.footer-bar .cart-number').html(data.total_items);
                        // console.log('sosto: ',data ,  quantity_total_element,  added_amount_element);
                    }
                }
            });

        }, 500));

        $(document).on('click', '.cart-break-add .quantity .quantity-minus', function (){

            var old_val = parseInt($(this).parents('.cart-break-add').find('.quantity-total').text(), 10);

            if(old_val>1){
                $(this).parents('.cart-break-add').find('.quantity-total').text(old_val-1);
                $(this).parents('.cart-break-add').find('.added .added_amount').text(old_val-1);

            }else{
                $(this).parents('.cart-break-add').find('.quantity-total').text('0');
                $(this).parents('.cart-break-add').find('.added .added_amount').text('0');
            }

        });

        $(document).on('click', '.cart-break-add .quantity .quantity-minus', debounce(function (){


            var quantity = parseInt($(this).parents('.cart-break-add').find('.quantity-total').text(), 10);
            var product_id = $(this).parents('.cart-break-add').attr('data-productID');
            var date = $(this).parents('.cart-break-add').attr('data-date');
            var break_id = $(this).parents('.cart-break-add').attr('data-breakID');
            var break_sort = $(this).parents('.cart-break-add').attr('data-breakSort');

            var quantity_total_element = $(this).parents('.cart-break-add').find('.quantity-total');
            var added_amount_element = $(this).parents('.cart-break-add').find('.added .added_amount');
            var snack_res_add_element = $(this).parents('.cart-break-add');

            console.log('send remove with quantity: ', quantity, ' product: ', product_id, ' date: ', date, ' break_sort: ', break_sort);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:"POST",
                url: '{{ route('application.removeFromCart') }}',
                data: {
                    product_id: product_id,
                    date: date,
                    break_id: break_id,
                    break_sort: break_sort,
                    quantity: quantity,
                },
                success: function(data){

                    // console.log('removeFromCart: ', data);
                    quantity_total_element.html(data.product_quantity_in_cart);
                    added_amount_element.html(data.product_quantity_in_cart);

                    if(data.product_quantity_in_cart == 0){
                        snack_res_add_element.removeClass('added-quantity');
                    }

                    if(data.status == 0){
                        // console.log('Error: ',data );
                    }

                    if(data.status == 1) {
                        $('.snack-total-cart .cart-total-price').html(data.total);
                        $('.snack-total-cart .cart-totals').html(data.total_items);
                        $('.footer-bar .cart-number').html(data.total_items);
                        // console.log('sosto to remove: ',data ,  quantity_total_element,  added_amount_element);
                    }
                }
            });

        }, 500));

        $('form').validate({
            errorClass: 'is-invalid',
            rules: {
                payment: {
                    required: true
                }
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "payment") {
                    $(".error-msg-payment").html('{{translate('Please select payment method')}}');
                }
            }
        });

        $(document).on('click', 'input[name=payment]', function (){
            $(".error-msg-payment").html('');
        });




        // Returns a function, that, as long as it continues to be invoked, will not
        // be triggered. The function will be called after it stops being called for
        // N milliseconds. If `immediate` is passed, trigger the function on the
        // leading edge, instead of the trailing.
        function debounce(func, wait, immediate) {
            var timeout;
            return function () {
                var context = this, args = arguments;
                var later = function () {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        };
    </script>
@endsection
