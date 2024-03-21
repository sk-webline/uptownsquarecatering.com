@extends('application.layouts.app')

@section('meta_title'){{ translate('Choose Snack') }}@endsection

@section('content')

    @php

    use Carbon\Carbon;
    use Illuminate\Support\Facades\Session;
    use App\Models\CanteenPurchase;
    use App\Models\CanteenMenu;

        $carbon_date = Carbon::create($date);
        $categories = \App\Models\CanteenProductCategory::all();

        $day_name = Carbon::create($date)->format('l');

        $canteen_menu_for_today = $canteen_setting->canteen_menus->where('organisation_break_num', $break->break_num)->where('day',strtolower($day_name));

        $products_with_custom_prices = [];

        $category_ids = [];

        foreach($canteen_menu_for_today as $menu){
            $category_ids[] = \App\Models\CanteenProduct::find($menu->canteen_product_id)->canteen_product_category_id;
            if($menu->custom_price_status==1){
                $products_with_custom_prices[$menu->canteen_product_id] = $menu->custom_price;
            }
        }

        $category_ids = array_unique($category_ids);

        $categories = \App\Models\CanteenProductCategory::whereIn('id', $category_ids)->get();

        $products_for_today = $canteen_setting->canteen_menus->where('organisation_break_num', $break->break_num)->where('day',strtolower($day_name))->pluck('canteen_product_id')->toArray();

        $lang = \Illuminate\Support\Facades\Session::get('locale');

        $daily_limit = auth()->guard('application')->user()->daily_limit;

        $break_sort = $break->break_num;

        $purchases_price_for_today= CanteenPurchase::where('canteen_app_user_id', $user->id )->where('date', '=', $date)->sum('price');
        $cart_price_for_today = 0;
        $items_in_cart = [];
        $quantity_for_each_product = [];

        if(Session::has('app_cart')){
           foreach (Session::get('app_cart') as $key => $cartItem) {
                if($cartItem['date'] == $date ){
                    $cart_price_for_today+=$cartItem['price']*$cartItem['quantity'];
                }

                 if($cartItem['date'] == $date &&  $cartItem['break_id'] == $break->id){
                      $items_in_cart[] = $cartItem['product_id'];
                      $quantity_for_each_product[$cartItem['product_id']] = $cartItem['quantity'];
                 }
            }
        }

        $available_balance = $daily_limit-$purchases_price_for_today-$cart_price_for_today;


     @endphp

    <div id="choose-snack">

        <div class="container">
            <div class="my-25px text-green fs-11">
                {{translate('Selected Day & Break')}}: <span class="fs-14 fw-700">{{$carbon_date->format('l')}} {{$carbon_date->format('d/m')}} - {{ordinal($break_sort)}} {{translate('Break')}}</span>
            </div>
            <h1 class="fs-18 fw-700 mb-10px">{{toUpper(translate('Time to choose!'))}}</h1>
            <p>{{translate('What would you like to eat or drink?')}}</p>
        </div>

        <div class="my-20px overflow-hidden">
            <div class="border-bottom border-inherit">
                <div class="swiper snack-categories-swiper">
                    <div class="swiper-wrapper">
                        @foreach($categories as $key => $category)
                            @if(in_array($category->id, $category_ids))
                            <div class="swiper-slide auto">
                                <div class="snack-category-tab @if($key==0) active @endif" data-id="{{$category->id}}">{{$category->getTranslation('name')}}</div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="swiper-arrow swiper-prev">
                        <span></span>
                    </div>
                    <div class="swiper-arrow swiper-next">
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="snack-all-results">
                @foreach($categories as $key => $category)
                    @if(in_array($category->id, $category_ids))
                        <div class="snack-category-results active" data-id="{{$category->id}}">
                            <div class="row gutters-10">
                                @foreach($category->products as $product)
                                    @if(in_array($product->id,$products_for_today))

                                        @php

                                            $not_available = false;
                                            if(array_key_exists($product->id, $products_with_custom_prices)){
                                                $product_price = $products_with_custom_prices[$product->id];
                                            }else{
                                                 $product_price = $product->price;
                                            }

                                            if($product_price> $available_balance){
                                                $not_available = true;
                                            }


                                        @endphp
                                    <div class="col-6 mb-20px product-card @if($not_available) not-available @endif" data-productPrice="{{$product_price}}">
                                        <div class="snack-res-item">
                                            <div class="snack-res-image">
                                                <img src="{{ uploaded_asset($product->thumbnail_img) }}?width=400&qty=50"
                                                     alt="{{ $product->name }}" class="img-fit h-100 absolute-full">
                                                <div class="snack-res-add @if(in_array($product->id, $items_in_cart)) added-quantity @endif" data-productID="{{$product->id}}">
                                                    <div class="plus">+</div>
                                                    <div class="quantity">
                                                        <div class="row gutters-2">
                                                            <div class="col-auto">
                                                                <div class="control quantity-minus">-</div>
                                                            </div>
                                                            <div class="col">
                                                                <div class="quantity-total">
                                                                    @if(in_array($product->id, $items_in_cart)) {{$quantity_for_each_product[$product->id]}} @else 0 @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-auto">
                                                                <div class="control quantity-plus">+</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="added">
                                                        <span class="added_amount pr-2px"> @if(in_array($product->id, $items_in_cart)) {{$quantity_for_each_product[$product->id]}} @else 0 @endif </span>
                                                        <span class="arrow px-2px"></span></div>
                                                </div>
                                            </div>
                                            <div class="snack-res-text">
                                                <h3 class="snack-res-title fs-14 fw-400 text-black-50 mb-2px text-truncate">{{ $product->getTranslation('name') }}</h3>
                                                <div class="snack-res-price fs-12 fw-700">
                                                    @if(array_key_exists($product->id, $products_with_custom_prices))
                                                        {{single_price($products_with_custom_prices[$product->id])}}
                                                    @else
                                                        {{single_price($product->price)}}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach

                             </div>
                        </div>
                    @endif
                @endforeach

                <div class="snack-total-cart pb-60px">


                    <a class="btn btn-block btn-secondary fs-16 fw-700" href="{{ route('application.cart') }}">
                        <span class="row gutters-2 align-items-center">
                            <span class="d-block col-auto">
                                 <span class="d-block cart-totals">
                                  @if(Session::has('total_items') && Session::get('total_items')>0)
                                   {{Session::get('total_items')}}
                                  @else
                                      0
                                  @endif
                                  </span>
                            </span>
                            <span class="d-block col">{{translate('Cart')}}</span>
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
                    </a>
                    <div class="text-right mt-5px fs-10 text-black-50 fw-500">
                        {{translate('Available balance')}}: <span class="available_balance">{{single_price($available_balance)}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('notification-popup')
    <div id="limit-reached" class="notification-pop">
        <div class="notification-pop-scroll c-scrollbar">
            <div class="notification-pop-wrap">
                <div class="notification-pop-box fs-15 d-flex align-items-center">
                    <img class="h-25px mr-2" src="{{static_asset('assets/img/icons/sad-euro-face.svg')}}" alt="">
                    {{translate('You have reached your daily limit.')}}
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>

        const date = '{{$date}}';
        const break_id = '{{$break->id}}';
        const break_sort = '{{$break_sort}}';

        let available_balance = '{{$available_balance}}';

        /*Categories Slider*/
        var snack_categories_swiper = new Swiper('.snack-categories-swiper', {
            loop: false,
            slidesPerView: "auto",
            navigation: {
                nextEl: ".snack-categories-swiper .swiper-button-next",
                prevEl: ".snack-categories-swiper .swiper-button-prev",
            },
        });
        /*Categories Change Tab*/
        $(document).on('click', '.snack-category-tab', function (){
            $('.snack-category-tab, .snack-category-results').removeClass('active')
            $(this).addClass('active');
            $('.snack-category-results[data-id="' + $(this).data('id') + '"]').addClass('active');
        });

        /*Show Quantity Controls*/
        var inactivityTimeout;
        $(document).on('click', '.snack-res-item:not(.disabled) .snack-res-add .plus', function (){

            if($(this).parents('.product-card').first().hasClass('not-available')){
                return;
            }

            var product_id = $(this).parent('.snack-res-add').attr('data-productID');
            console.log('ela dame product_id: ', product_id);
            $('.snack-res-item:not(.disabled) .snack-res-add .plus, .snack-res-item:not(.disabled) .snack-res-add .added').not(this).parent('.snack-res-add').removeClass('open-quantity');

            $(this).parent('.snack-res-add').addClass('open-quantity');
            $(this).parent('.snack-res-add').addClass('added-quantity');

            var quantity_total_element = $(this).parent('.snack-res-add').find('.quantity-total');
            var added_amount_element = $(this).parents('.snack-res-add').find('.added .added_amount');
            var snack_res_add_element = $(this).parents('.snack-res-add').first();

            $(this).parent('.snack-res-add').find('.quantity-total').html('1');
            $(this).parents('.snack-res-add').find('.added .added_amount').text('1');
            clearTimeout(inactivityTimeout);

            // inactivityTimeout = setTimeout(function() {
            //     // $('.snack-res-add').removeClass('open-quantity');
            // }, 5000);

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
                    quantity: 1,
                },
                success: function(data){

                    console.log('addToCart: ', data);
                    quantity_total_element.text(data.product_quantity_in_cart);
                    added_amount_element.text(data.product_quantity_in_cart);

                    if(data.status == 0){
                        snack_res_add_element.removeClass('open-quantity');
                        snack_res_add_element.removeClass('added-quantity');
                    }

                    if(data.status == 1) {
                        $('.snack-total-cart .cart-total-price').html(data.total);
                        $('.snack-total-cart .cart-totals').html(data.total_items);
                        $('.available_balance').html(data.available_balance);
                        available_balance = data.available_balance_num;
                        $('.footer-bar .cart-number').html(data.total_items);
                    }
                    change_availability_status()
                }
            });

        });

        $(document).on('click', '.snack-res-item:not(.disabled) .snack-res-add .added', function (){
            var product_id = $(this).parent('.snack-res-add').attr('data-productID');
            console.log('ela dame product_id: ', product_id);
            $('.snack-res-item:not(.disabled) .snack-res-add .plus, .snack-res-item:not(.disabled) .snack-res-add .added').not(this).parent('.snack-res-add').removeClass('open-quantity');

            $(this).parent('.snack-res-add').addClass('open-quantity');
        });

        $(document).on('click', '.snack-res-add .quantity .control', function (){
            clearTimeout(inactivityTimeout);
            inactivityTimeout = setTimeout(function() {
                $('.snack-res-add').removeClass('open-quantity');
            }, 5000);
        });

        $(document).mouseup(function(e) {
            if(!$('.snack-res-add').is(e.target) && $('.snack-res-add').has(e.target).length === 0) {
                $('.snack-res-add').removeClass('open-quantity');
            }
        });

        $(document).on('click', '.snack-res-item.disabled .snack-res-add .plus, .snack-res-item.disabled .snack-res-add .added', function (){
            $('#limit-reached').addClass('active');
        });

        $(document).on('click', '.snack-res-add .quantity .quantity-plus', function (){

            if($(this).parents('.product-card').first().hasClass('not-available')){
                return;
            }
            var old_val = parseInt($(this).parents('.snack-res-add').find('.quantity-total').text(), 10);
            $(this).parents('.snack-res-add').find('.quantity-total').text(old_val+1);
            $(this).parents('.snack-res-add').find('.added .added_amount').text(old_val+1);

            console.log('allagi',  $(this).parents('.snack-res-add').find('input'),  $(this).parents('.snack-res-add').find('input').val());
        });


        $(document).on('click', '.snack-res-add .quantity .quantity-plus', debounce(function (){

            var quantity = parseInt($(this).parents('.snack-res-add').find('.quantity-total').text(), 10);
            var product_id = $(this).parents('.snack-res-add').attr('data-productID');

            var quantity_total_element = $(this).parents('.snack-res-add').find('.quantity-total');
            var added_amount_element = $(this).parents('.snack-res-add').find('.added .added_amount');

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
                        $('.available_balance').html(data.available_balance);
                        available_balance = data.available_balance_num;
                        $('.footer-bar .cart-number').html(data.total_items);
                        // console.log('sosto: ',data ,  quantity_total_element,  added_amount_element);
                    }
                    change_availability_status()
                }
            });

        }, 500));

        $(document).on('click', '.snack-res-add .quantity .quantity-minus', function (){

            var old_val = parseInt($(this).parents('.snack-res-add').find('.quantity-total').text(), 10);

            if(old_val>0){
                $(this).parents('.snack-res-add').find('.quantity-total').text(old_val-1);
                $(this).parents('.snack-res-add').find('.added .added_amount').text(old_val-1);

            }else{
                $(this).parents('.snack-res-add').find('.quantity-total').text('0');
                $(this).parents('.snack-res-add').find('.added .added_amount').text('0');
            }


            // console.log('allagi minus');
        });

        $(document).on('click', '.snack-res-add .quantity .quantity-minus', debounce(function (){

            // console.log('kamni to quantity:', $(this).parents('.snack-res-add').find('.quantity-total').text());

            var quantity = parseInt($(this).parents('.snack-res-add').find('.quantity-total').text(), 10);
            var product_id = $(this).parents('.snack-res-add').attr('data-productID');

            var quantity_total_element = $(this).parents('.snack-res-add').find('.quantity-total');
            var added_amount_element = $(this).parents('.snack-res-add').find('.added .added_amount');
            var snack_res_add_element = $(this).parents('.snack-res-add');

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

                    console.log('removeFromCart: ', data);
                    quantity_total_element.html(data.product_quantity_in_cart);
                    added_amount_element.html(data.product_quantity_in_cart);

                    if(data.product_quantity_in_cart == 0){
                        snack_res_add_element.removeClass('added-quantity');
                    }

                    if(data.status == 0){
                        console.log('Error: ',data );
                    }

                    if(data.status == 1) {
                        $('.snack-total-cart .cart-total-price').html(data.total);
                        $('.snack-total-cart .cart-totals').html(data.total_items);
                        $('.available_balance').html(data.available_balance);
                        available_balance = data.available_balance_num;
                        $('.footer-bar .cart-number').html(data.total_items);
                        console.log('sosto: ',data ,  quantity_total_element,  added_amount_element);
                    }
                    change_availability_status()
                }
            });

        }, 500));

        function change_availability_status(){

            // console.log('empike?', $('.product-card'));
            // Loop through each div with class 'hjj'
            $('.product-card').each(function(index, parentElement) {
                // Find the child div with class 'opa' within the current parent
                var price = $(parentElement).attr('data-productPrice');

                if(price > available_balance){
                    $(parentElement).addClass('not-available');
                }else{
                    $(parentElement).removeClass('not-available');
                }

            });

        }


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
