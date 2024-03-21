<!DOCTYPE html>
@if(\App\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
    <html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @else
        <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
        @endif
        <head>
            <script src="https://www.googleoptimize.com/optimize.js?id=OPT-54KNCS3"></script>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <meta name="app-url" content="{{ getBaseURL() }}">
            <meta name="file-base-url" content="{{ getFileBaseURL() }}">

            <title>@yield('meta_title', get_setting('website_name').' | '.get_setting('site_motto'))</title>

            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="robots" content="index, follow">
            <meta name="description" content="@yield('meta_description', get_setting('meta_description') )" />
            <meta name="keywords" content="@yield('meta_keywords', get_setting('meta_keywords') )">

            @yield('meta')

            @if(!isset($detailedProduct) && !isset($customer_product) && !isset($shop) && !isset($page) && !isset($blog))
                <!-- Schema.org markup for Google+ -->
                <meta itemprop="name" content="{{ get_setting('meta_title') }}">
                <meta itemprop="description" content="{{ get_setting('meta_description') }}">
                <meta itemprop="image" content="{{ uploaded_asset(get_setting('meta_image')) }}">

                <!-- Twitter Card data -->
                <meta name="twitter:card" content="product">
                <meta name="twitter:site" content="@publisher_handle">
                <meta name="twitter:title" content="{{ get_setting('meta_title') }}">
                <meta name="twitter:description" content="{{ get_setting('meta_description') }}">
                <meta name="twitter:creator" content="@author_handle">
                <meta name="twitter:image" content="{{ uploaded_asset(get_setting('meta_image')) }}">

                <!-- Open Graph data -->
                <meta property="og:title" content="{{ get_setting('meta_title') }}" />
                <meta property="og:type" content="website" />
                <meta property="og:url" content="{{ route('home') }}" />
                <meta property="og:image" content="{{ uploaded_asset(get_setting('meta_image')) }}" />
                <meta property="og:description" content="{{ get_setting('meta_description') }}" />
                <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
                <meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">
            @endif

            <!-- Favicon -->
            <link rel="icon" href="{{ uploaded_asset(get_setting('site_icon')) }}">

            <!-- Google Fonts -->
            <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap" rel="stylesheet">

            <!-- CSS Files -->
            <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
            <link rel="stylesheet" href="{{ static_asset('assets/js/sly/sly.css') }}">
            <link rel="stylesheet" href="{{ static_asset('assets/js/simplebar/simplebar.min.css') }}">
            <link rel="stylesheet" href="{{ static_asset('assets/css/jquery-ui.min.css') }}">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
            @if(\App\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
                <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-rtl.min.css') }}">
            @endif
            <link rel="stylesheet" href="{{ static_asset('assets/css/master.css') }}">
            <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-adds.css') }}">
            <link rel="stylesheet" href="{{ static_asset('assets/css/custom.css') }}">

            <link rel="stylesheet" href="{{ static_asset('assets/css/canteen_cashier.css') }}">




            <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>



            <script>
                var SK = SK || {};
                SK.local = {
                    nothing_found: '{{ translate('Nothing found') }}',
                    choose_file: '{{ translate('Choose file') }}',
                    file_selected: '{{ translate('File selected') }}',
                    files_selected: '{{ translate('Files selected') }}',
                    add_more_files: '{{ translate('Add more files') }}',
                    adding_more_files: '{{ translate('Adding more files') }}',
                    drop_files_here_paste_or: '{{ translate('Drop files here, paste or') }}',
                    browse: '{{ translate('Browse') }}',
                    upload_complete: '{{ translate('Upload complete') }}',
                    upload_paused: '{{ translate('Upload paused') }}',
                    resume_upload: '{{ translate('Resume upload') }}',
                    pause_upload: '{{ translate('Pause upload') }}',
                    retry_upload: '{{ translate('Retry upload') }}',
                    cancel_upload: '{{ translate('Cancel upload') }}',
                    uploading: '{{ translate('Uploading') }}',
                    processing: '{{ translate('Processing') }}',
                    complete: '{{ translate('Complete') }}',
                    file: '{{ translate('File') }}',
                    files: '{{ translate('Files') }}',
                }
            </script>

            <style>
                :root{
                    --primary: {{ get_setting('base_color', '#e62d04') }};
                    --hov-primary: {{ get_setting('base_hov_color', '#c52907') }};
                    --soft-primary: {{ hex2rgba(get_setting('base_color','#e62d04'),.15) }};
                }
            </style>

            @if (\App\BusinessSetting::where('type', 'google_analytics')->first()->value == 1)
                <!-- Global site tag (gtag.js) - Google Analytics -->
                <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('TRACKING_ID') }}"></script>

                <script>
                    window.dataLayer = window.dataLayer || [];
                    function gtag(){dataLayer.push(arguments);}
                    gtag('js', new Date());
                    gtag('config', '{{ env('TRACKING_ID') }}');
                </script>
            @endif

            @if (\App\BusinessSetting::where('type', 'facebook_pixel')->first()->value == 1)
                <!-- Facebook Pixel Code -->
                <script>
                    !function(f,b,e,v,n,t,s)
                    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                        n.queue=[];t=b.createElement(e);t.async=!0;
                        t.src=v;s=b.getElementsByTagName(e)[0];
                        s.parentNode.insertBefore(t,s)}(window, document,'script',
                        'https://connect.facebook.net/en_US/fbevents.js');
                    fbq('init', '{{ env('FACEBOOK_PIXEL_ID') }}');
                    fbq('track', 'PageView');
                </script>
                <noscript>
                    <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ env('FACEBOOK_PIXEL_ID') }}&ev=PageView&noscript=1"/>
                </noscript>
                <!-- End Facebook Pixel Code -->
            @endif

            @php
                echo get_setting('header_script');
            @endphp
        </head>
        <body>
        <!-- sk-main-wrapper -->
        <div class="sk-main-wrapper d-flex flex-column">
            <div class="background-fixed-popup"></div>

            <!-- Header -->
{{--            @include('frontend.inc.nav')--}}

            @yield('content')



        </div>

        @if (get_setting('show_cookies_agreement') == 'on')
            <div class="sk-cookie-alert shadow-xl">
                <div class="p-3 bg-dark rounded">
                    <div class="text-white mb-3">
                        @php
                            echo get_setting('cookies_agreement_text');
                        @endphp
                    </div>
                    <button class="btn btn-primary sk-cookie-accepet">
                        {{ translate('Ok. I Understood') }}
                    </button>
                </div>
            </div>
        @endif

        @include('frontend.partials.modal')

        <div class="modal fade" id="addToCart">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
                <div class="modal-content position-relative">
                    <div class="c-preloader text-center p-3">
                        <i class="las la-spinner la-spin la-3x"></i>
                    </div>
                    <button type="button" class="close absolute-top-right btn-icon close z-1" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="la-2x">&times;</span>
                    </button>
                    <div id="addToCart-modal-body">

                    </div>
                </div>
            </div>
        </div>

        @yield('modal')

        <!-- SCRIPTS -->
        <script src="{{ static_asset('assets/js/vendors.js') }}"></script>
        <script>
            let _tooltip = jQuery.fn.tooltip; // <--- Cache this
        </script>
        <script src="{{ static_asset('assets/js/jquery-ui.min.js') }}"></script>
        <script>
            jQuery.fn.tooltip = _tooltip; // <--- Then restore it here
        </script>
        <script src="{{ static_asset('assets/js/master.js') }}"></script>
        <script src="{{ static_asset('assets/js/sly/sly.min.js') }}"></script>
        <script src="{{ static_asset('assets/js/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ static_asset('assets/js/custom.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

        @if (get_setting('facebook_chat') == 1)
            <script type="text/javascript">
                window.fbAsyncInit = function() {
                    FB.init({
                        xfbml            : true,
                        version          : 'v3.3'
                    });
                };

                (function(d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) return;
                    js = d.createElement(s); js.id = id;
                    js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));
            </script>
            <div id="fb-root"></div>
            <!-- Your customer chat code -->
            <div class="fb-customerchat"
                 attribution=setup_tool
                 page_id="{{ env('FACEBOOK_PAGE_ID') }}">
            </div>
        @endif

        <script>
            @foreach (session('flash_notification', collect())->toArray() as $message)
            SK.plugins.notify('{{ $message['level'] }}', '{{ $message['message'] }}');
            @endforeach
        </script>

        <script>

            $(document).ready(function() {

                if ($('#lang-change').length > 0) {
                    $('#lang-change .dropdown-menu a').each(function() {
                        $(this).on('click', function(e){
                            e.preventDefault();
                            var $this = $(this);
                            var locale = $this.data('flag');
                            $.post('{{ route('language.change') }}',{_token: SK.data.csrf, locale:locale}, function(data){
                                location.reload();
                            });

                        });
                    });
                }

                if ($('#currency-change').length > 0) {
                    $('#currency-change .dropdown-menu a').each(function() {
                        $(this).on('click', function(e){
                            e.preventDefault();
                            var $this = $(this);
                            var currency_code = $this.data('currency');
                            $.post('{{ route('currency.change') }}',{_token: SK.data.csrf, currency_code:currency_code}, function(data){
                                location.reload();
                            });

                        });
                    });
                }
            });

            $('#search').on('keyup', function(){
                search();
            });

            $('#search').on('focus', function(){
                search();
            });

            function search(){
                var searchKey = $('#search').val();
                if(searchKey.length > 4){
                    $('body').addClass("typed-search-box-shown");

                    $('.typed-search-box').removeClass('d-none');
                    $('.search-preloader').removeClass('d-none');
                    $.post('{{ route('search.ajax') }}', { _token: SK.data.csrf, search:searchKey}, function(data){
                        if(data == '0'){
                            // $('.typed-search-box').addClass('d-none');
                            $('#search-content').html(null);
                            $('.typed-search-box .search-nothing').removeClass('d-none').html('Sorry, nothing found for <strong>"'+searchKey+'"</strong>');
                            $('.search-preloader').addClass('d-none');

                        }
                        else{
                            $('.typed-search-box .search-nothing').addClass('d-none').html(null);
                            $('#search-content').html(data);
                            $('.search-preloader').addClass('d-none');
                        }
                    });
                }
                else {
                    $('.typed-search-box').addClass('d-none');
                    $('body').removeClass("typed-search-box-shown");
                }
            }


            $('#search-megamenu').on('keyup', function(){
                search_megamenu();
            });

            $('#search-megamenu').on('focus', function(){
                search_megamenu();
            });

            function search_megamenu(){
                var searchKey = $('#search-megamenu').val();
                if(searchKey.length > 4){
                    $('body').addClass("typed-search-box-shown-megamenu");

                    $('.typed-search-box-megamenu').removeClass('d-none');
                    $('.search-preloader-megamenu').removeClass('d-none');
                    $.post('{{ route('search.ajax') }}', { _token: SK.data.csrf, search:searchKey}, function(data){
                        if(data == '0'){
                            // $('.typed-search-box').addClass('d-none');
                            $('#search-content-megamenu').html(null);
                            $('.typed-search-box-megamenu .search-nothing-megamenu').removeClass('d-none').html('Sorry, nothing found for <strong>"'+searchKey+'"</strong>');
                            $('.search-preloader-megamenu').addClass('d-none');

                        }
                        else{
                            $('.typed-search-box-megamenu .search-nothing-megamenu').addClass('d-none').html(null);
                            $('#search-content-megamenu').html(data);
                            $('.search-preloader-megamenu').addClass('d-none');
                        }
                    });
                }
                else {
                    $('.typed-search-box-megamenu').addClass('d-none');
                    $('body').removeClass("typed-search-box-shown-megamenu");
                }
            }


            $('#search-fixed').on('keyup', function(){
                search_fixed();
            });

            $('#search-fixed').on('focus', function(){
                search_fixed();
            });

            function search_fixed(){
                var searchKey = $('#search-fixed').val();
                if(searchKey.length > 4){
                    $('body').addClass("typed-search-box-shown-fixed");

                    $('.typed-search-box-fixed').removeClass('d-none');
                    $('.search-preloader-fixed').removeClass('d-none');
                    $.post('{{ route('search.ajax') }}', { _token: SK.data.csrf, search:searchKey}, function(data){
                        if(data == '0'){
                            // $('.typed-search-box').addClass('d-none');
                            $('#search-content-fixed').html(null);
                            $('.typed-search-box-fixed .search-nothing-fixed').removeClass('d-none').html('Sorry, nothing found for <strong>"'+searchKey+'"</strong>');
                            $('.search-preloader-fixed').addClass('d-none');

                        }
                        else{
                            $('.typed-search-box-fixed .search-nothing-fixed').addClass('d-none').html(null);
                            $('#search-content-fixed').html(data);
                            $('.search-preloader-fixed').addClass('d-none');
                        }
                    });
                }
                else {
                    $('.typed-search-box-fixed').addClass('d-none');
                    $('body').removeClass("typed-search-box-shown-fixed");
                }
            }


            function updateNavCart(){
                $.post('{{ route('cart.nav_cart') }}', {_token: SK.data.csrf }, function(data){
                    $('#cart_items').html(data);
                    $('#cart_items_megamenu').html(data);
                    $('#fixed_cart_items').html(data);
                });
            }

            function removeFromCart(key){
                $('body').addClass('loader');
                $.post('{{ route('cart.removeFromCart') }}', {_token: SK.data.csrf, key:key}, function(data){
                    updateNavCart();
                    $('#cart-summary').html(data);
                    SK.plugins.notify('success', 'Item has been removed from cart');
                    $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html())-1);
                    $('body').removeClass('loader');
                });
            }

            function addToCompare(id){
                $.post('{{ route('compare.addToCompare') }}', {_token: SK.data.csrf, id:id}, function(data){
                    $('#compare').html(data);
                    SK.plugins.notify('success', "{{ translate('Item has been added to compare list') }}");
                    $('#compare_items_sidenav').html(parseInt($('#compare_items_sidenav').html())+1);
                });
            }

            function addToWishList(id){
                @if (Auth::check() && (Auth::user()->user_type == 'customer' || Auth::user()->user_type == 'seller'))
                $.post('{{ route('wishlists.store') }}', {_token: SK.data.csrf, id:id}, function(data){
                    if(data != 0){
                        $('#wishlist').html(data);
                        SK.plugins.notify('success', "{{ translate('Item has been added to wishlist') }}");
                    }
                    else{
                        SK.plugins.notify('warning', "{{ translate('Please login first') }}");
                    }
                });
                @else
                SK.plugins.notify('warning', "{{ translate('Please login first') }}");
                @endif
            }

            function showAddToCartModal(id){
                if(!$('#modal-size').hasClass('modal-lg')){
                    $('#modal-size').addClass('modal-lg').removeClass('modal-sm');
                }
                $('#addToCart-modal-body').html('');
                $('#addToCart').modal();
                $('.c-preloader').show();
                $.post('{{ route('cart.showCartModal') }}', {_token: SK.data.csrf, id:id}, function(data){
                    $('.c-preloader').hide();
                    $('#addToCart-modal-body').html(data);
                    SK.plugins.slickCarousel();
                    SK.plugins.zoom();
                    SK.extra.plusMinus();
                    getVariantPrice();
                });
            }

            $('#option-choice-form input').on('change', function(){
                getVariantPrice();
            });

            function getVariantPrice(){
                if($('#option-choice-form input[name=quantity]').val() > 0 && checkAddToCartValidity()){
                    $.ajax({
                        type:"POST",
                        url: '{{ route('products.variant_price') }}',
                        data: $('#option-choice-form').serializeArray(),
                        beforeSend: function() {
                            $('#publish-right-form').addClass('loader');
                        },
                        success: function(data){
                            var option_quantity = $('#option-choice-form input[name=quantity]').val();
                            $('.product-gallery-thumb .carousel-box').each(function (i) {
                                if($(this).data('variation') && data.variation == $(this).data('variation')){
                                    $('.product-gallery-thumb').slick('slickGoTo', i);
                                }
                            });
                            $('#part-number').html(data.part_number);
                            $('#part-number-phone').html(data.part_number);
                            $('#chosen_price_div').removeClass('d-none');
                            $('#chosen_price_div #chosen_price').html(data.price);
                            $('#side_chosen_price').html(data.price);
                            $('#available-quantity').html(data.quantity);
                            $('.input-number').prop('max', data.max_quantity);
                            $('#side_chosen_quantity').text(option_quantity);
                            $('#product-request-side-popup input[name="quantity"]').val(option_quantity);
                            if(data.color){
                                $('#product-request-side-popup input[name="color"]').val(data.color);
                                $('#product-request-side-popup .color-megabox-elem').css('background', data.color_code);
                            }

                            for(let i = 0; i < data.attributes.length; i++) {
                                $('#product-request-side-popup input[name="attribute_id_' + data.attributes[i]['id'] + '"]').val(data.attributes[i]['value']);
                                $('#product-request-side-popup input[name="attribute_id_' + data.attributes[i]['id'] + '"] + .sk-megabox-elem').text(data.attributes[i]['value']);
                            }

                            if(parseInt(data.quantity) < 1 && data.digital  == 0){
                                $('.card-actions, #publish-quantity').hide();
                                $('.out-of-stock, .out-of-stock-label').show();
                            } else {
                                $('.card-actions, #publish-quantity').show();
                                $('.out-of-stock, .out-of-stock-label').hide();
                            }
                            $('#publish-right-form').removeClass('loader');
                        }
                    });
                }
            }

            function checkAddToCartValidity(){
                var names = {};
                $('#option-choice-form input:radio').each(function() { // find unique names
                    names[$(this).attr('name')] = true;
                });
                var count = 0;
                $.each(names, function() { // then count them
                    count++;
                });

                if($('#option-choice-form input:radio:checked').length == count){
                    return true;
                }

                return false;
            }

            function addToCart(){
                if(checkAddToCartValidity()) {
                    $('#addToCart-modal-body').html('');
                    $('#addToCart').modal();
                    $('.c-preloader').show();
                    $.ajax({
                        type:"POST",
                        url: '{{ route('cart.addToCart') }}',
                        data: $('#option-choice-form').serializeArray(),
                        success: function(data){
                            $('#addToCart-modal-body').html(null);
                            $('.c-preloader').hide();
                            $('#modal-size').removeClass('modal-lg').addClass('modal-sm');
                            $('#addToCart-modal-body').html(data.view);
                            updateNavCart();
                            $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html())+1);
                        }
                    });
                }
                else{
                    SK.plugins.notify('warning', 'Please choose all the options');
                }
            }

            function buyNow(){
                if(checkAddToCartValidity()) {
                    $('#publish-right-form').addClass('loader');
                    $('#addToCart-modal-body').html('');
                    $.ajax({
                        type:"POST",
                        url: '{{ route('cart.addToCart') }}',
                        data: $('#option-choice-form').serializeArray(),
                        success: function(data){
                            if(data.status == 1){
                                updateNavCart();
                                $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html())+1);
                                window.location.replace("{{ route('cart') }}");
                            }
                            else{
                                $('#modal-size').removeClass('modal-lg').addClass('modal-sm');
                                $('#addToCart-modal-body').html(data.view);
                                $('.c-preloader').hide();
                                $('#addToCart').modal();
                                $('#publish-right-form').removeClass('loader');
                            }
                        }
                    });
                }
                else{
                    SK.plugins.notify('warning', 'Please choose all the options');
                }
            }

            function show_purchase_history_details(order_id)
            {
                $('#order-details-modal-body').html(null);

                if(!$('#modal-size').hasClass('modal-lg')){
                    $('#modal-size').addClass('modal-lg').removeClass('modal-sm');
                }

                $.post('{{ route('purchase_history.details') }}', { _token : SK.data.csrf, order_id : order_id}, function(data){
                    $('#order-details-modal-body').html(data);
                    $('#order_details').modal();
                    $('.c-preloader').hide();
                });
            }

            function show_order_details(order_id)
            {
                $('#order-details-modal-body').html(null);

                if(!$('#modal-size').hasClass('modal-lg')){
                    $('#modal-size').addClass('modal-lg').removeClass('modal-sm');
                }

                $.post('{{ route('orders.details') }}', { _token : SK.data.csrf, order_id : order_id}, function(data){
                    $('#order-details-modal-body').html(data);
                    $('#order_details').modal();
                    $('.c-preloader').hide();
                });
            }

            function cartQuantityInitialize(){
                $('.btn-number').click(function(e) {
                    e.preventDefault();

                    fieldName = $(this).attr('data-field');
                    type = $(this).attr('data-type');
                    var input = $("input[name='" + fieldName + "']");
                    var currentVal = parseInt(input.val());

                    if (!isNaN(currentVal)) {
                        if (type == 'minus') {

                            if (currentVal > input.attr('min')) {
                                input.val(currentVal - 1).change();
                            }
                            if (parseInt(input.val()) == input.attr('min')) {
                                $(this).attr('disabled', true);
                            }

                        } else if (type == 'plus') {

                            if (currentVal < input.attr('max')) {
                                input.val(currentVal + 1).change();
                            }
                            if (parseInt(input.val()) == input.attr('max')) {
                                $(this).attr('disabled', true);
                            }

                        }
                    } else {
                        input.val(0);
                    }
                });

                $('.input-number').focusin(function() {
                    $(this).data('oldValue', $(this).val());
                });

                $('.input-number').change(function() {

                    minValue = parseInt($(this).attr('min'));
                    maxValue = parseInt($(this).attr('max'));
                    valueCurrent = parseInt($(this).val());

                    name = $(this).attr('name');
                    if (valueCurrent >= minValue) {
                        $(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
                    } else {
                        // alert('Sorry, the minimum value was reached');
                        // $(this).val($(this).data('oldValue'));
                        $(this).val(maxValue);
                    }
                    if (valueCurrent <= maxValue) {
                        $(".btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled')
                    } else {
                        $(this).val(maxValue);
                        // alert('Sorry, the maximum value was reached!');
                        // $(this).val($(this).data('oldValue'));
                    }


                });
                $(".input-number").keydown(function(e) {
                    // Allow: backspace, delete, tab, escape, enter and .
                    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
                        // Allow: Ctrl+A
                        (e.keyCode == 65 && e.ctrlKey === true) ||
                        // Allow: home, end, left, right
                        (e.keyCode >= 35 && e.keyCode <= 39)) {
                        // let it happen, don't do anything
                        return;
                    }
                    // Ensure that it is a number and stop the keypress
                    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                        e.preventDefault();
                    }
                });
            }

            function imageInputInitialize(){
                $('.custom-input-file').each(function() {
                    var $input = $(this),
                        $label = $input.next('label'),
                        labelVal = $label.html();

                    $input.on('change', function(e) {
                        var fileName = '';

                        if (this.files && this.files.length > 1)
                            fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
                        else if (e.target.value)
                            fileName = e.target.value.split('\\').pop();

                        if (fileName)
                            $label.find('span').html(fileName);
                        else
                            $label.html(labelVal);
                    });

                    // Firefox bug fix
                    $input
                        .on('focus', function() {
                            $input.addClass('has-focus');
                        })
                        .on('blur', function() {
                            $input.removeClass('has-focus');
                        });
                });
            }

            $(document).on('click', '.megamenu-toggle', function(){
                $('.megamenu-toggle').toggleClass('open');
                if($('.megamenu-toggle').hasClass('open')){
                    $('html, body').addClass('megamenu-opened');
                } else {
                    $('html, body').removeClass('megamenu-opened');
                }
            });
            $(document).on('click', '.header-search-toggle', function () {
                $('.front-header-search').toggleClass('active');
            });
            $(document).on('click', '.header-search-toggle-megamenu', function () {
                $('.front-header-search-megamenu').toggleClass('active');
            });
            $(document).on('click', '.header-search-toggle-fixed', function () {
                $('.front-header-search-fixed').toggleClass('active');
            });
            $(document).mouseup(function(e) {
                var container = $('.front-header-search');
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    $('.front-header-search').removeClass('active');
                }
            });
            $(document).mouseup(function(e) {
                var container = $('.front-header-search-megamenu');
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    $('.front-header-search-megamenu').removeClass('active');
                }
            });
            $(document).mouseup(function(e) {
                var container = $('.front-header-search-fixed');
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    $('.front-header-search-fixed').removeClass('active');
                }
            });
            setHeightofSideText();
            setOffsettoButtons()
            function setHeightofSideText(){
                $('.side-right-bar.sticky-top').each(function () {
                    var height = $($(this).data('rel')).outerHeight();
                    $(this).css('height', height + 'px');
                });
                $('.side-right-bar-text').each(function () {
                    var width = $(this).outerWidth();
                    $(this).parent('.side-right-bar-text-wrap').css('height', width + 'px');
                });
                $('.side-popup-close-text').each(function () {
                    var width = $(this).outerWidth();
                    $(this).parent('.side-popup-close-text-wrap').css('height', width + 'px');
                });
                $('.side-modal-close-text').each(function () {
                    var width = $(this).outerWidth();
                    $(this).parent('.side-modal-close-text-wrap').css('height', width + 'px');
                });
                if($('.publish-container-target').length > 0) {
                    if($('.publish-container-target').outerWidth() >= 1350) {
                        var body_width = $('body').outerWidth();
                        var calc = -((body_width - 1350) / 2);
                        $('.container-left-1350px').css('margin-right', calc + 'px');
                        $('.publish-container-adjustable').removeClass('container-left').addClass('container');
                        $('.publish-container-adjustable > .mw-1350px').addClass('mx-auto');
                    } else {
                        $('.container-left-1350px').css('margin-right', 0);
                        $('.publish-container-adjustable').addClass('container-left').removeClass('container');
                        $('.publish-container-adjustable > .mw-1350px').removeClass('mx-auto');
                    }
                }
                if($('.color-megabox-elem').length > 0){
                    var color_box_height = $('.color-megabox-elem').outerHeight();
                    $('.megabox-size-element > span').css('height', color_box_height + 'px');
                }
                if($('.contain-without-footer').length > 0){
                    if ($(window).width() >= 992) {
                        var header_height = $('header').outerHeight();
                        $('.contain-without-footer').css('min-height', 'calc(100vh - ' + header_height + 'px)');
                    } else {
                        $('.contain-without-footer').css('min-height', '');
                    }
                }
                if($('.cashier-dashboard-box').length > 0 && $('.cashier-body-dashboard-boxes').length > 0){
                    $('.cashier-dashboard-box, .cashier-dashboard-box-wrap').css('height', '');
                    $('.cashier-dashboard-box, .cashier-dashboard-box-wrap').css('min-height', '');
                    if($(window).width() >= 992) {
                        var dashboard_container_height = $('.cashier-body-dashboard-boxes').outerHeight();
                        $('.cashier-dashboard-box, .cashier-dashboard-box-wrap').css('height', dashboard_container_height + 'px');
                    } else {
                        var dashboard_container_height = ($('.cashier-body-dashboard-boxes').outerHeight() / 2) - 10;
                        $('.cashier-dashboard-box, .cashier-dashboard-box-wrap').css('min-height', dashboard_container_height + 'px');
                    }
                }

                if($('.cashier-buffet-box').length > 0 && $('.cashier-body-buffet-boxes').length > 0){
                    $('.cashier-buffet-box, .cashier-buffet-box-wrap').css('height', '');
                    $('.cashier-buffet-box, .cashier-buffet-box-wrap').css('min-height', '');
                    if($(window).width() >= 992) {
                        var buffet_container_height = $('.cashier-body-buffet-boxes').outerHeight();
                        $('.cashier-buffet-box, .cashier-buffet-box-wrap').css('height', buffet_container_height + 'px');
                    } else {
                        var buffet_container_height = ($('.cashier-body-buffet-boxes').outerHeight() / 2) - 10;
                        $('.cashier-buffet-box, .cashier-buffet-box-wrap').css('min-height', buffet_container_height + 'px');
                    }
                }

                if($('.cashier-meal-plan-box').length > 0 && $('.cashier-body-meal-plans-boxes').length > 0){
                    $('.cashier-meal-plan-box, .cashier-meal-plan-box-wrap').css('height', '');
                    $('.cashier-meal-plan-box, .cashier-meal-plan-box-wrap').css('min-height', '');
                    if($(window).width() >= 992) {
                        var meal_container_height = $('.cashier-body-meal-plans-boxes').outerHeight();
                        $('.cashier-meal-plan-box, .cashier-meal-plan-box-wrap').css('height', meal_container_height + 'px');
                    } else {
                        var meal_container_height = ($('.cashier-body-meal-plans-boxes').outerHeight() / 2) - 10;
                        $('.cashier-meal-plan-box, .cashier-meal-plan-box-wrap').css('min-height', meal_container_height + 'px');
                    }
                }
            }
            function setOffsettoButtons(){
                $('.about-brands-btn').each(function () {
                    var offset = $(this).offset().left;
                    $(this).attr('data-offset', offset);
                });
            }
            $(window).resize(function() {
                setTimeout(function(){
                    setHeightofSideText();
                    setOffsettoButtons();
                }, 500);
            });

            $('.modal').on('shown.bs.modal', function (e) {
                setHeightofSideText();
            });

            @if(get_setting('header_stikcy') == 'on')
            /*Header Fixed with scroll*/
            var didScroll;// on scroll, let the interval function know the user has scrolled
            var lastScrollTop = 0;
            var delta = 5;
            var navbarHeight = $('header').outerHeight();

            $(window).scroll(function(event){
                didScroll = true;
            });// run hasScrolled() and reset didScroll status
            setInterval(function() {
                if (didScroll) {
                    hasScrolled();
                    didScroll = false;
                }
            }, 250);
            function hasScrolled() {
                var st = $(this).scrollTop();
                if (Math.abs(lastScrollTop  - st) <= delta) {
                    return;
                }
                // If current position > last position AND scrolled past navbar...
                if (st > lastScrollTop && st > navbarHeight){  // Scroll Down
                    $('.fixed-header').removeClass('header-down').addClass('header-up');
                    $('body').removeClass('fixed-header-showed');
                } else {  // Scroll Up
                    // If did not scroll past the document (possible on mac)...
                    if(st + $(window).height() < $(document).height() && lastScrollTop > st) {
                        $('.fixed-header').removeClass('header-up').addClass('header-down');
                        $('body').addClass('fixed-header-showed');
                        if(st <= navbarHeight) {
                            $('.fixed-header').removeClass('header-down');
                            $('body').removeClass('fixed-header-showed');
                        }
                    }
                }
                lastScrollTop = st;
            }
            @endif

            function doFooter() {
                var footer = $(".fixed-footer");
                if($(document.body).height() + footer.outerHeight() < $(window).height() && "fixed" === footer.css("position") || $(document.body).height() < $(window).height() && "fixed" !== footer.css("position")){
                    footer.css({
                        position: "fixed",
                        bottom: 0,
                        width: "100%"
                    });
                } else {
                    footer.css({
                        position: "static"
                    });
                }
            }
            $(window).resize(function() {
                doFooter();
            });
            $(window).on("load", function() {
                doFooter();
            });

            $(document).ready(function(){
                $("#newsletter_form").on("submit", function() {
                    $('#newsletter-form-error-email,#newsletter-form-error-agree').removeClass('d-block').text('');
                    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                    if($('#newsletter_form input[name="newsletter_email"]').val()=="" || ($('#newsletter_form input[name="newsletter_email"]').val()!="" && !emailReg.test($('#newsletter_form input[name="newsletter_email"]').val())) || $('#newsletter_form input[name="newsletter_agree_policies"]').prop('checked')==false){
                        if($('#newsletter_form input[name="newsletter_email"]').val()==""){
                            $('#newsletter-form-error-email').addClass('d-block').text('{{translate('Please enter your email')}}');
                        }
                        if($('#newsletter_form input[name="newsletter_email"]').val()!="" && !emailReg.test($('#newsletter_form input[name="newsletter_email"]').val())) {
                            $('#newsletter-form-error-email').addClass('d-block').text('{{translate('Invalid email!')}}');
                        }
                        if($('#newsletter_form input[name="newsletter_agree_policies"]').prop('checked')==false){
                            $('#newsletter-form-error-agree').addClass('d-block').text('{{translate('You need to agree with our policies')}}');
                        }
                        return false;
                    } else {
                        $.ajax({
                            type: "POST",
                            url: '{{ route('subscribers_store_ajax') }}',
                            data: $('#newsletter_form').serializeArray(),
                            beforeSend: function() {
                                $('#newsletter-button').addClass('loader');
                            },
                            success: function (data) {
                                if(data.status == 1) {
                                    SK.plugins.notify('success', '{{ translate('You have subscribed successfully.') }} {{translate('Thanks for subscribing to our newsletter!')}}');
                                    $('#newsletter-button').text('{{toUpper(translate('You have Subscribed'))}}').css('pointer-events', 'none');
                                    $('#newsletter_form input[name="email"]').val('');
                                } else {
                                    SK.plugins.notify('warning', '{{ translate('You are already a subscriber')}}');
                                    $('#newsletter-button').text('{{toUpper(translate('Subscribe to Newsletter'))}}');
                                }
                                $('#newsletter-button').removeClass('loader');
                            }
                        });
                        return false;
                    }
                    //captcha verified
                    //do the rest of your validations here
                    /*$("#newsletter_form").submit();*/
                });
            });

            if($('.header-submenu-swiper').length > 0) {
                var header_submenu_swiper = new Swiper('.header-submenu-swiper', {
                    loop: false,
                    slidesPerView: "auto",
                    spaceBetween: 20,
                    pauseOnMouseEnter: true,
                    disableOnInteraction: true,
                    scrollbar: {
                        el: ".header-submenu-swiper .swiper-scrollbar",
                        hide: false,
                        draggable: true,
                    },
                    breakpoints: {
                        768: {
                            spaceBetween: 40,
                        },
                        1200: {
                            spaceBetween: 80,
                        },
                    },
                });
            }
        </script>

        @yield('script')

        @php
            echo get_setting('footer_script');
        @endphp

        </body>
        </html>
