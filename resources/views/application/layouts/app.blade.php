<!DOCTYPE html>
@if(\App\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
    <html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @else
        <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
        @endif
        <head>
            @if(request()->server('HTTP_HOST') == 'www.uptownsquarecatering.com')
                <script id="Cookiebot" src="https://consent.cookiebot.com/uc.js" data-cbid="27f4c985-1c11-4696-8a8a-4c52a4967ae2" data-blockingmode="auto" type="text/javascript"></script>
                <script id="CookieDeclaration" src="https://consent.cookiebot.com/27f4c985-1c11-4696-8a8a-4c52a4967ae2/cd.js" type="text/javascript" async></script>
            @endif
            <script src="https://www.googleoptimize.com/optimize.js?id=OPT-54KNCS3"></script>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <meta name="app-url" content="{{ getBaseURL() }}">
            <meta name="file-base-url" content="{{ getFileBaseURL() }}">

            <title>@yield('meta_title') | {{get_setting('website_name')}}</title>

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
            <link rel="stylesheet" href="{{ static_asset('assets/js/simplebar/simplebar.min.css') }}">
            <link rel="stylesheet" href="{{ static_asset('assets/css/jquery-ui.min.css') }}">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
            @if(\App\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
                <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-rtl.min.css') }}">
            @endif
            <link rel="stylesheet" href="{{ static_asset('assets/css/master.css') }}">
            <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-adds.css') }}">
            <link rel="stylesheet" href="{{ static_asset('assets/css/custom.css') }}">
            <link rel="stylesheet" href="{{ static_asset('assets/css/application.css') }}">


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


{{--            PWA--}}
{{--            Link the Service Worker in your HTML file --}}
            <script>
                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.register('{{ static_asset('assets/js/service-worker.js') }}')
                        .then((registration) => {
                            console.log('Service Worker registered with scope:', registration.scope);
                        })
                        .catch((error) => {
                            console.error('Service Worker registration failed:', error);
                        });
                }


            </script>

{{--            Add the Manifest to your HTML file --}}
            <link rel="manifest" href="{{ static_asset('assets/json/manifest.json') }}">



        </head>
        <body>
        <!-- sk-main-wrapper -->
        <div class="sk-main-wrapper d-flex flex-column">
            <div class="background-fixed-popup"></div>

            <!-- Header -->
            @include('application.inc.nav')

            @yield('content')

        </div>

        @include('application.inc.footer-bar')

        @include('application.inc.footer')

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

        @yield('notification-popup')

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
        <script src="{{ static_asset('assets/js/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ static_asset('assets/js/custom.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js'></script>

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
            $(document).on('click', '.megamenu-toggle', function(){
                $('.megamenu-toggle').toggleClass('open');
                if($('.megamenu-toggle').hasClass('open')){
                    $('html, body').addClass('megamenu-opened');
                } else {
                    $('html, body').removeClass('megamenu-opened');
                }
            });

            setCssOnResize();
            function setCssOnResize(){
                setHeightOnBreakTaple();
                setWidthOnCartQuantityBoxes();
                setScrollHeightOnBottomPopup();
                if($('.sk-main-wrapper').length > 0) {
                    var main_min_height = $(window).height() - $('.fixed-footer').height() - $('.footer-bar').height() - 1;
                    $('.sk-main-wrapper').css('min-height',  main_min_height + 'px');
                }
                if($('.megamenu').length > 0) {
                    var megamenu_body_height = $(window).height() - $('.megamenu-header').height() - $('.megamenu-footer').height();
                    $('.megamenu-body').css('min-height',  megamenu_body_height + 'px');
                }
            }
            $(window).resize(function() {
                setTimeout(function(){
                    setCssOnResize();
                }, 500);
            });

            $('.modal').on('shown.bs.modal', function (e) {
                setCssOnResize();
            });

            function setHeightOnBreakTaple() {
                if($('.break-table').length > 0) {
                    $('.break-table-col').css('height', '');
                    $('.break-table-left .break-table-row').each(function (){
                        var row = $(this).data('row');
                        var row_height = $(this).outerHeight();
                        $('.break-table-right .break-table-row[data-row="' + row + '"]').each(function (){
                            if(row_height < $(this).outerHeight()) {
                                row_height = $(this).outerHeight();
                            }
                        });
                        $('.break-table-row[data-row="' + row + '"] .break-table-col').css('height', row_height + 'px');
                    });
                }
            }

            function setWidthOnCartQuantityBoxes(){
                var cart_added_element = $('.cart-break-add .added');
                if(cart_added_element.length > 0){
                    cart_added_element.css('min-width', '');
                    var cart_quantity_box_width = 0;
                    cart_added_element.each(function (){
                        var this_width =  $(this).outerWidth();
                        if(cart_quantity_box_width < this_width){
                            cart_quantity_box_width = this_width;
                        }
                    });
                    cart_added_element.css('min-width', cart_quantity_box_width + 'px');
                }

                var cart_total_quantity_element = $('.total-quantity');
                if(cart_total_quantity_element.length > 0){
                    cart_total_quantity_element.css('min-width', '');
                    var cart_total_quantity_box_width = 0;
                    cart_total_quantity_element.each(function (){
                        var this_width =  $(this).outerWidth();
                        if(cart_total_quantity_box_width < this_width){
                            cart_total_quantity_box_width = this_width;
                        }
                    });
                    cart_total_quantity_element.css('min-width', cart_total_quantity_box_width + 'px');
                }
            }

            function setScrollHeightOnBottomPopup(){
                if($('.bottom-popup-scroll.no-scroll').length > 0 && $('.bottom-popup .fix-area').length > 0 && $('.bottom-popup .scroll-area').length > 0) {
                    $('.bottom-popup-scroll.no-scroll').each(function(){
                        var $this = $(this);
                        $this.find('.bottom-popup-container').css('max-height', $(window).height() + 'px');
                        var scroll_height = $this.find('.bottom-popup-container').height() - $this.find('.fix-area').outerHeight(true);
                        $this.find('.scroll-area').css('max-height', scroll_height + 'px');
                    })

                }
            }

            function addToCart(date, product_id, break_id){

                // console.log('in addToCart date: ', date, ' product_id: ', product_id,  ' break_id: ', break_id);

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
                        quantity: 1,
                    },
                    success: function(data){

                        console.log('addToCart: ', data);

                        if(data.status == 0){


                        }

                        if(data.status == 1) {
                            $(this).parent('.snack-res-add').addClass('added-quantity');
                            updateNavCart();
                        }
                    }
                });

            }

            function updateNavCart(){

                // console.log('in nav cart');
                $.post('{{ route('application.nav_cart') }}', {_token: SK.data.csrf }, function(data){
                    // console.log('data in nav cart: ', data);
                    // $('#cart-table').html(data.cart_table);
                    // $('#cart_items, #cart_items_phone').html(data.view);
                    // $('#cart-popup .side-popup-body').html(data.popup);
                    // $('#cart-summary').html(data.cart_summary);
                    // $('#cart-popup .side-popup-container').removeClass('loader');
                });
            }

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

            /*Sticky Cart Button*/
            $(document).ready(function() {
                stickyButtonActions();
                $(window).scroll(function() {
                    stickyButtonActions();
                });
            });
            function stickyButtonActions(){
                if($('.snack-all-results').length > 0 && $('.snack-total-cart').length > 0) {
                    // Get the scroll position and window height
                    var scrollPos = $(window).scrollTop();
                    var windowHeight = $(window).height();

                    // Get the position and height of the sticky element
                    var containerPos = $('.snack-all-results').offset().top;
                    var containerHeight = $('.snack-all-results').height();

                    // Check if the sticky element is at the bottom of its container
                    if (scrollPos + windowHeight >= containerPos + containerHeight) {
                        $('.snack-total-cart').removeClass('gradient');
                    } else {
                        $('.snack-total-cart').addClass('gradient');
                    }
                }

                if($('.history-table-container').length > 0 && $('.history-table-sticky').length > 0) {
                    // Get the scroll position and window height
                    var history_scrollPos = $(window).scrollTop();
                    var history_windowHeight = $(window).height();

                    // Get the position and height of the sticky element
                    var history_containerPos = $('.history-table-container').offset().top;
                    var history_containerHeight = $('.history-table-container').height();

                    // Check if the sticky element is at the bottom of its container
                    if (history_scrollPos + history_windowHeight >= history_containerPos + history_containerHeight) {
                        $('.history-table-sticky').removeClass('gradient');
                    } else {
                        $('.history-table-sticky').addClass('gradient');
                    }
                }

                if($('.remaining-balance-table').length > 0 && $('.remaining-balance-sticky').length > 0) {
                    // Get the scroll position and window height
                    var remaining_scrollPos = $(window).scrollTop();
                    var remaining_windowHeight = $(window).height();

                    // Get the position and height of the sticky element
                    var remaining_containerPos = $('.remaining-balance-table').offset().top;
                    var remaining_containerHeight = $('.remaining-balance-table').height();

                    // Check if the sticky element is at the bottom of its container
                    if (remaining_scrollPos + remaining_windowHeight >= remaining_containerPos + remaining_containerHeight) {
                        $('.remaining-balance-sticky').removeClass('gradient');
                    } else {
                        $('.remaining-balance-sticky').addClass('gradient');
                    }
                }
            }

            let flag = false;

            $(document).on("click", function(event) {

                var bottom_popup = $('div.bottom-popup');
                var history_table_container = $('div.history-table-container');
                var delete_item = $('.cart-break-product-item .delete');

                // Check if the clicked element is not inside the history-popup
                if (bottom_popup.hasClass('active') && $('html,body').hasClass('bottom-popup-opened')
                    && !bottom_popup.is(event.target) && bottom_popup.has(event.target).length === 0
                    && !history_table_container.is(event.target) && history_table_container.has(event.target).length === 0
                    && !delete_item.is(event.target) && delete_item.has(event.target).length === 0

                ) {
                    // Trigger your function here
                    $('html,body').removeClass('bottom-popup-opened');
                    $('.bottom-popup').removeClass('active');

                    $('#refund-popup input[name=product_id]').val('');
                    $('#refund-popup input[name=date]').val('');
                    $('#refund-popup input[name=break_num]').val('');
                    $('#refund-popup input[name=quantity]').val('');

                    $('#delete-popup input[name=product_id]').val('');
                    $('#delete-popup input[name=break_id]').val('');
                    $('#delete-popup input[name=date]').val('');
                }
            });

            /*Bottom Popup*/
            $(document).on('click', '.bottom-popup-close', function (){
                $('html,body').removeClass('bottom-popup-opened');
                $('.bottom-popup').removeClass('active');
            });

            $(document).on('click', '#delete-popup .popup-close-link', function (){
                $('html,body').removeClass('bottom-popup-opened');
                $('#delete-popup').removeClass('active');
                $('#delete-popup input[name=product_id]').val('');
                $('#delete-popup input[name=break_id]').val('');
                $('#delete-popup input[name=date]').val('');

            });

            $(document).on('click', '#refund-popup .popup-close-link', function (){
                $('html,body').removeClass('bottom-popup-opened');
                $('#refund-popup').removeClass('active');
                $('#refund-popup input[name=product_id]').val('');
                $('#refund-popup input[name=date]').val('');
                $('#refund-popup input[name=break_num]').val('');
                $('#refund-popup input[name=quantity]').val('');

                $('#refund-popup .one-item').addClass('d-none');
                $('#refund-popup .multiple-items').addClass('d-none');

            });

            /*Cart Delete Modal*/
            $(document).on('click', '#cart-review .cart-break-product-item .delete', function (){

                var element = $(this).parents('.cart-break-product-item').first().find('.cart-break-add').first();

                var product_id =element.attr('data-productID');
                var break_id =element.attr('data-breakID');
                var date =element.attr('data-date');

                console.log(element, product_id, break_id,date );

                $('#delete-popup input[name=product_id]').val(product_id);
                $('#delete-popup input[name=break_id]').val(break_id);
                $('#delete-popup input[name=date]').val(date);

                $('html,body').addClass('bottom-popup-opened');
                $('#delete-popup').addClass('active');

            });

            $(document).on('click', '#delete-popup .delete-item', function (){

                var product_id = $('#delete-popup input[name=product_id]').val();
                var break_id = $('#delete-popup input[name=break_id]').val();
                var date = $('#delete-popup input[name=date]').val();

                if(product_id==null || break_id==null || date==null){
                    return;
                }

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
                        break_sort: 0,
                        quantity: 0,
                        type: 'delete',
                    },
                    success: function(data){

                        // console.log('removeFromCart: ', data);

                        if(data.status == 0){
                            // console.log('Error: ',data );
                        }

                        if(data.status == 1) {
                            $('.snack-total-cart .cart-total-price').html(data.total);
                            $('.snack-total-cart .cart-totals').html(data.total_items);
                            $('.footer-bar .cart-number').html(data.total_items);
                            $('.cart-results').html(data.cart_table_view);
                            // console.log('sosto: ',data);
                        }

                        $('#delete-popup .popup-close-link').click();

                    }

                });

            });

            /*Cart Delete Modal*/
            $(document).on('click', '#upcoming-meals .cart-break-product-item .delete', function (){

                var element = $(this).parents('.cart-break-product-item').first();
                var product_id =element.attr('data-productID');
                var break_num =element.attr('data-breakNum');
                var date =element.attr('data-date');
                var quantity =element.attr('data-quantity');

                // console.log('ola tou', element, product_id, break_num, date);

                $('#refund-popup input[name=product_id]').val(product_id);
                $('#refund-popup input[name=break_num]').val(break_num);
                $('#refund-popup input[name=date]').val(date);
                $('#refund-popup input[name=quantity]').val(quantity);

                $('#refund-popup .quantity-total').html(quantity);

                $('#refund-popup .multiple-items').addClass('d-none');
                $('#refund-popup .one-item').addClass('d-none');

                if(quantity>1){

                    $('#refund-popup .quantity-total').attr('data-minQuantity', 1);
                    $('#refund-popup .quantity-total').attr('data-maxQuantity', quantity);
                    $('#refund-popup .multiple-items').removeClass('d-none');
                }else{
                    $('#refund-popup .one-item').removeClass('d-none');
                }


                $('html,body').addClass('bottom-popup-opened');
                $('#refund-popup').addClass('active');

            });

            $(document).on('click', '#refund-popup .quantity .quantity-plus', function (){

                var element = $('#refund-popup .quantity-total');
                var temp_quantity = parseInt($('#refund-popup .quantity-total').text());
                var max = parseInt(element.attr('data-maxQuantity'));
                var min = parseInt(element.attr('data-minQuantity'));

                console.log(temp_quantity, min, max);

                if(temp_quantity + 1 <= max){
                    $('#refund-popup .quantity-total').text(temp_quantity + 1);
                    $('#refund-popup input[name=quantity]').val(temp_quantity + 1);
                }

            });

            $(document).on('click', '#refund-popup .quantity .quantity-minus', function (){

                // var element = $('#refund-popup .quantity-total');
                var temp_quantity = parseInt($('#refund-popup .quantity-total').text());

                if(temp_quantity - 1 >= 1){
                    $('#refund-popup .quantity-total').text(temp_quantity - 1);
                    $('#refund-popup input[name=quantity]').val(temp_quantity - 1);
                }

            });

            function refundItem(){

                $('#refund-popup').addClass('loader');
                console.log('refund Item');

                var date = $('#refund-popup input[name=date]').val();
                var break_num = $('#refund-popup input[name=break_num]').val();
                var product_id = $('#refund-popup input[name=product_id]').val();
                var quantity = $('#refund-popup input[name=quantity]').val();
                // var quantity = 1;

                // return;

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('app_viva.refund_order')}}",
                    type: 'post',
                    data: {
                        date: date,
                        break_num: break_num,
                        user_id: '{{auth()->guard('application')->user()->id}}',
                        product_id: product_id,
                        quantity: quantity
                    },
                    success: function (data) {

                        console.log('data: ', data);
                        $('#refund-popup').removeClass('loader');

                        if (data.status == 1) {
                            $('#upcoming-meals .snack-all-results').html(data.view);
                            $('.icon.upcoming-icon .footer-number').html(data.upcoming_purchases_count);
                            $('#refund-popup').removeClass('loader');

                        }
                            $('#refund-popup').removeClass('loader');
                            $('#refund-popup .bottom-popup-close').click();


                    }
                });

            }


        </script>

        @yield('script')

        @php
            echo get_setting('footer_script');
        @endphp

</body>
</html>
