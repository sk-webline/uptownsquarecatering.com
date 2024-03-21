@extends('frontend.layouts.app')

@section('meta_title'){{ $page->meta_title }}@stop

@section('meta_description'){{ $page->meta_description }}@stop

@section('meta_keywords'){{ $page->tags }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $page->meta_title }}">
    <meta itemprop="description" content="{{ $page->meta_description }}">
    <meta itemprop="image" content="{{ uploaded_asset($page->meta_img) }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ $page->meta_title }}">
    <meta name="twitter:description" content="{{ $page->meta_description }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ uploaded_asset($page->meta_img) }}">
    <meta name="twitter:data1" content="{{ single_price($page->unit_price) }}">
    <meta name="twitter:label1" content="Price">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $page->meta_title }}" />
    <meta property="og:type" content="product" />
    <meta property="og:url" content="{{ route('product', $page->slug) }}" />
    <meta property="og:image" content="{{ uploaded_asset($page->meta_img) }}" />
    <meta property="og:description" content="{{ $page->meta_description }}" />
    <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
    <meta property="og:price:amount" content="{{ single_price($page->unit_price) }}" />
@endsection

@section('content')
    <div class="line-slider-item overflow-hidden">
        <div class="line-slider-image">
            <img
                    class="absolute-full h-100 img-fit"
                    src="{{ uploaded_asset($page->banner) }}"
                    alt=""
                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
            >
            <div class="line-slider-over">
                <div class="row no-gutters">
                    <div class="col-lg-4">
                        <div class="line-slider-over-left">
                            <div class="line-slider-over-box"></div>
                            <div class="line-slider-over-box slogan d-lg-flex justify-content-center">
                                <div class="line-slider-over-box-inner">
                                    <h1 class="font-play fs-25 md-fs-30 xl-fs-45 xxl-fs-60 fw-700 m-0">{{ $page->getTranslation('title') }}</h1>
                                </div>
                            </div>
                            <div class="line-slider-over-box d-none d-lg-block"></div>
                        </div>
                    </div>
                    <div class="col-lg-8 d-none d-lg-block">
                        <div class="line-slider-over-right">
                            <div class="line-slider-over-box"></div>
                            <div class="line-slider-over-box slogan"></div>
                            @if($page->getTranslation('banner_desc'))
                                <div class="line-slider-over-box bg-white text-default-50 fs-16 xl-fs-18 xxl-fs-23 xxxl-fs-28 font-play l-space-1 px-xxl-50px d-flex flex-column justify-content-end lh-1-4">
                                    <p>{{$page->getTranslation('banner_desc')}}</p>
                                </div>
                            @else
                                <div class="line-slider-over-box"></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($page->getTranslation('banner_desc'))
    <div class="my-30px my-md-50px d-lg-none">
        <div class="container">
            <div class="text-default-50 fs-16 font-play l-space-1-2 lh-1-7 mw-850px">
                <p>{{$page->getTranslation('banner_desc')}}</p>
            </div>
        </div>
    </div>
    @endif
    <div id="mission-vision" class="mt-30px mt-lg-125px mt-xxl-175px overflow-hidden">
        <div class="container">
            <div class="row gutters-5 lg-gutters-15 xxxl-gutters-30">
                @if(get_setting('mission_banner'))
                    <div class="col-md-4 mb-30px mb-md-0 mission-res-item">
                        <div class="mission-res-wrap">
                            <div class="mission-res-image">
                                <img class="img-fit h-100 absolute-full lazyload"
                                     src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                     data-src="{{uploaded_asset(get_setting('mission_banner'))}}"
                                     onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                     alt="">
                            </div>
                            <div class="mission-res-over md-fs-12 xl-fs-16 xxxl-fs-18 p-20px p-xxl-40px fw-500 l-space-1-2 d-flex flex-column justify-content-between">
                                @if(get_setting('mission_description'))
                                    <p>{{get_setting('mission_description')}}</p>
                                @endif
                                <h2 class="fs-18 sm-fs-24 md-fs-14 lg-fs-20 xl-fs-24 xxl-fs-30 font-play fw-700 text-white m-0">{{translate('Our Goal')}}</h2>
                            </div>
                        </div>
                    </div>
                @endif
                @if(get_setting('vision_banner'))
                    <div class="col-md-4 mb-30px mb-md-0 mission-res-item">
                        <div class="mission-res-wrap">
                            <div class="mission-res-image">
                                <img class="img-fit h-100 absolute-full lazyload"
                                     src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                     data-src="{{uploaded_asset(get_setting('vision_banner'))}}"
                                     onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                     alt="">
                            </div>
                            <div class="mission-res-over md-fs-12 xl-fs-16 xxxl-fs-18 p-20px p-xxl-40px fw-500 l-space-1-2 d-flex flex-column justify-content-between">
                                @if(get_setting('vision_description'))
                                    <p>{{get_setting('vision_description')}}</p>
                                @endif
                                <h2 class="fs-18 sm-fs-24 md-fs-14 lg-fs-20 xl-fs-24 xxl-fs-30 font-play fw-700 text-white m-0">{{translate('Our Vision')}}</h2>
                            </div>
                        </div>
                    </div>
                @endif
                @if(get_setting('integrity_banner'))
                    <div class="col-md-4 mb-30px mb-md-0 mission-res-item">
                        <div class="mission-res-wrap">
                            <div class="mission-res-image">
                                <img class="img-fit h-100 absolute-full lazyload"
                                     src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                     data-src="{{uploaded_asset(get_setting('integrity_banner'))}}"
                                     onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                     alt="">
                            </div>
                            <div class="mission-res-over md-fs-12 xl-fs-16 xxxl-fs-18 p-20px p-xxl-40px fw-500 l-space-1-2 d-flex flex-column justify-content-between">
                                @if(get_setting('integrity_description'))
                                    <p>{{get_setting('integrity_description')}}</p>
                                @endif
                                <h2 class="fs-18 sm-fs-24 md-fs-14 lg-fs-20 xl-fs-24 xxl-fs-30 font-play fw-700 text-white m-0">{{translate('Integrity & Reliability')}}</h2>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @php
        $brands = \App\Brand::orderBy('order_level', 'desc')->get();
    @endphp
    @if(count($brands) > 0)
        <div id="about-brands" class="mt-50px mt-lg-125px mt-xxl-175px overflow-hidden">
            <div class="about-brands-top">
                @php
                    $counter = 0;
                @endphp
                @foreach($brands as $brand)
                    <div class="about-brand-res-item @if($counter==0) active @endif " data-id="{{$brand->id}}">
                        <a href="{{route('brand_page', $brand->slug)}}" class="btn btn-primary btn-block fw-500 l-space-1 fs-14 md-fs-16 py-5px d-lg-none">
                            {{toUpper(translate('View Products'))}}
                            <span class="btn-arrow right ml-3"></span>
                        </a>
                        <div class="position-relative">
                            <div class="about-brand-res-image">
                                <img class="img-fit h-100 absolute-full lazyload"
                                     src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                     data-src="{{uploaded_asset($brand->banner)}}"
                                     onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                     alt="">
                            </div>
                            <div class="absolute-full about-brand-res-over">
                                <div class="row align-items-center no-gutters">
                                    <div class="col-lg-8 d-none d-lg-block">
                                        <div class="position-relative bg-white min-h-250px">
                                            <div class="font-play fs-20 xxl-fs-25 l-space-1-2 text-default-50 container py-15px lh-2">
                                                <p>{{$brand->getTranslation('about_desc')}}</p>
                                            </div>
                                            <div class="side-right-bar">
                                                <a href="{{route('brand_page', $brand->slug)}}" class="side-right-bar-link side-popup-toggle">
                                                    <span class="d-block side-right-bar-arrow"></span>
                                                    <span class="d-block side-right-bar-text-wrap">
                                                    <span class="side-right-bar-text">{{toUpper(translate('View Products'))}}</span>
                                                </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 ml-auto">
                                        <?php /*
                                        <div class="p-15px text-right text-lg-center">
                                            <img class="img-contain h-80px h-lg-140px w-180px w-lg-250px about-brand-res-logo" src="{{uploaded_asset($brand->logo)}}" alt="">
                                        </div>*/ ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        $counter++;
                    @endphp
                @endforeach
            </div>
            <div class="about-brands-bottom bg-default text-white-50 fw-500 l-space-1-2 fs-12 sm-fs-14 lg-fs-16 xxl-fs-18 text-nowrap">
                <div class="hor-swipe" data-simplebar>
                    <div class="row no-gutters flex-nowrap">
                        @php
                            $counter = 0;
                        @endphp
                        @foreach($brands as $brand)
                            <div class="col about-brands-btn-item">
                                <div class="about-brands-btn py-5px py-sm-15px py-lg-25px px-15px @if($counter==0) active @endif " data-id="{{$brand->id}}" data-offset="">
                                    <div class="about-brands-btn-back"></div>
                                    {{toUpper($brand->getTranslation('name'))}}
                                </div>
                            </div>
                            @php
                                $counter++;
                            @endphp
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (get_setting('home_slider_images') != null)
        @php $history_images = json_decode(get_setting('history_slider_images'), true);  @endphp
        <div id="about-history" class="my-50px my-lg-90px my-xxl-125px overflow-hidden">
            <div class="container">
                <h2 class="fs-30 lg-fs-50 xxl-fs-70 font-play l-space-1-2 fw-700 text-default-50 mb-30px mb-xxl-50px">
                    {{translate('Experience through')}} <span class="text-secondary">{{translate('history')}}</span>
                </h2>
            </div>
            <div class="container-left">
                <div class="about-history-container">
                    <div class="swiper about-history-swiper">
                        <div class="swiper-wrapper" data-items="4" data-xl-items="3" data-md-items="3" data-sm-items="2" data-arrows="false" data-dots="false" data-infinite="true">
                            @foreach ($history_images as $key => $value)
                                <div class="swiper-slide">
                                    <div class="about-history-res-item">
                                        <div class="about-history-res-wrap l-space-1-2 text-default-50 fw-500 fs-11 sm-fs-14 xl-fs-16 xxxl-fs-18">
                                            <div class="about-history-res-image">
                                                <img
                                                        class="absolute-full h-100 img-fit"
                                                        src="{{ uploaded_asset($history_images[$key]) }}"
                                                        alt=""
                                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
                                                >
                                                <div class="about-history-res-over">
                                                    <div class="about-history-res-toggle fs-35 font-play fw-700 lh-1"></div>
                                                    <div class="about-history-res-over-desc pt-40px pb-15px pb-xxl-40px">
                                                        <div class="px-15px px-xxxl-35px c-scrollbar about-history-res-over-desc-scroll">
                                                            {{json_decode(get_setting('history_slider_full_desc'), true)[$key]}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <h3 class="about-history-res-year my-5px my-lg-10px my-xxl-15px lh-1 fw-700 font-play fs-18 lg-fs-26 xxl-fs-35 text-default">{{json_decode(get_setting('history_slider_years'), true)[$key]}}</h3>
                                            <p class="about-history-res-desc">{{json_decode(get_setting('history_slider_short_desc'), true)[$key]}}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-arrows d-flex justify-content-center">
                            <div class="swiper-button-prev swiper-arrow">
                                <i></i>
                            </div>
                            <div class="swiper-button-next swiper-arrow">
                                <i></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('script')
    <script>
        $(document).on('click', '.about-brands-btn', function () {
          var offset = $(this).data('offset');
          var width = $(this).outerWidth();
          $('.about-brands-btn').not(this).removeClass('active');
          $(this).addClass('active');
          $('.about-brand-res-item').removeClass('active');
          $('.about-brand-res-item[data-id="' + $(this).data('id') + '"]').addClass('active');
          setHeightofSideText();
          if($('body').outerWidth() < offset + width) {
            console.log(offset);
            $('.about-brands-bottom .hor-swipe .simplebar-content-wrapper').animate({
              scrollLeft: offset - $('body').outerWidth() + width
            }, 350);
          } else if($(this).offset().left < 0) {
            $('.about-brands-bottom .hor-swipe .simplebar-content-wrapper').animate({
              scrollLeft: offset
            }, 350);
          }
        });
        $(document).on('click', '.about-history-res-toggle', function () {
          $('.about-history-res-toggle').not(this).closest('.about-history-res-item').removeClass('active');
          $(this).closest('.about-history-res-item').toggleClass('active');
        });

        var about_swiper = new Swiper('.about-history-swiper', {
          loop: true,
          slidesPerView: 2,
          spaceBetween: 20,
          navigation: {
            nextEl: ".about-history-swiper .swiper-button-next",
            prevEl: ".about-history-swiper .swiper-button-prev",
          },
          breakpoints: {
            768: {
              slidesPerView: 3,
              spaceBetween: 20,
            },
            992: {
              slidesPerView: 3,
              spaceBetween: 40,
            },
            1500: {
              slidesPerView: 4,
              spaceBetween: 40,
            },
            1750: {
              slidesPerView: 4,
              spaceBetween: 60,
            },
          },
        });

    </script>
@endsection
