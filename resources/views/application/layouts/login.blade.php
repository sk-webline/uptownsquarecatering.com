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

            <!-- CSS Files -->
            <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
            <link rel="stylesheet" href="{{ static_asset('assets/css/jquery-ui.min.css') }}">
            @if(\App\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
                <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-rtl.min.css') }}">
            @endif
            <link rel="stylesheet" href="{{ static_asset('assets/css/master.css') }}">
            <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-adds.css') }}">
            <link rel="stylesheet" href="{{ static_asset('assets/css/custom.css') }}">
            <link rel="stylesheet" href="{{ static_asset('assets/css/application.css') }}">
            <link rel="stylesheet" href="{{ static_asset('assets/css/tutorial.css') }}">


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

{{--                        Add the Manifest to your HTML file--}}
            <link rel="manifest" href="{{ static_asset('assets/json/manifest.json') }}">


        </head>
        <body>
        <!-- sk-main-wrapper -->
        <div class="sk-main-wrapper d-flex flex-column">
            <div class="background-fixed-popup"></div>

            <!-- Header -->
            @include('application.inc.login_nav')

            @yield('content')

            @include('application.inc.login_footer')

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
        <script src="{{ static_asset('assets/js/custom.js') }}"></script>

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
            setCssOnResize();
            function setCssOnResize(){

            }
            $(window).resize(function() {
                setTimeout(function(){
                    setCssOnResize();
                }, 500);
            });

            $('.modal').on('shown.bs.modal', function (e) {
                setCssOnResize();
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


            let deferredPrompt;

            $(document).ready(function() {
                $(window).on('beforeinstallprompt', function(event) {
                    // Prevent Chrome 76 and earlier from automatically showing the prompt
                    event.preventDefault();
                    // Stash the event so it can be triggered later
                    deferredPrompt = event;
                    // Make the install button visible
                    $('#install-btn').removeClass('d-none');
                });

                // For example, in a button click event
                $('#install-btn').on('click', function() {
                    console.log('clicked');
                    if (deferredPrompt) {
                        // Show the prompt
                        deferredPrompt.prompt();
                        // Wait for the user to respond to the prompt
                        deferredPrompt.userChoice.then(function(choiceResult) {
                            if (choiceResult.outcome === 'accepted') {
                                console.log('User accepted the install prompt');
                            } else {
                                console.log('User dismissed the install prompt');
                            }
                            // Clear the deferred prompt variable
                            deferredPrompt = null;
                        });
                    }
                });
            });





        </script>
        @yield('script')
        @php
            echo get_setting('footer_script');
        @endphp
</body>
</html>
