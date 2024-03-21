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
    @php
        $services = \App\Service::get();
    @endphp
    @if(count($services) > 0)
        <div id="services-results" class="mt-20px mt-lg-150px mt-xxl-275px mb-60px mb-lg-100px mb-xxl-150px fs-11 sm-fs-14 xl-fs-16 xxxl-fs-18 position-relative">
            <div class="container">
                <h2 class="font-play fs-30 lg-fs-50 xxl-fs-70 fw-700 l-space-1-2 text-default-50 mb-15px mb-lg-45px mb-xxl-75px">
                    {{translate('Covering your')}} <span class="text-secondary">{{translate('needs')}}</span>
                </h2>
            </div>
            <div class="position-relative">
                <div class="services-results-top position-relative overflow-hidden">
                    <div class="container">
                        <div class="row lg-gutters-20 xxxl-gutters-30">
                            @php
                                $count=1;
                            @endphp
                            @foreach($services as $service)
                                @if($count <= 2)
                                    <div class="col-md-6 mb-30px mb-md-0 services-res-item">
                                        <div class="services-res-wrap clickable fw-500 l-space-1-2 text-white-80">
                                            <div class="services-res-image">
                                                <img
                                                        class="img-fit absolute-full h-100"
                                                        src="{{ uploaded_asset($service->banner) }}"
                                                        alt=""
                                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
                                                >
                                                <div class="services-res-over px-15px px-sm-30px px-xxl-50px pb-15px pb-sm-30px pt-35px d-flex flex-column justify-content-between">
                                                    <div class="services-res-toggle fs-25 fw-700 font-play"></div>
                                                    <div class="services-res-over-desc c-scrollbar">{{$service->getTranslation('short_description')}}</div>
                                                    <h3 class="services-res-title fs-14 sm-fs-17 lg-fs-22 xxl-fs-30 fw-700 text-white font-play m-0 text-truncate-2">{{$service->getTranslation('name')}}</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @php
                                    $count++;
                                @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
                @if(count($services) >= 3)
                    <div class="services-results-middle my-md-30px my-lg-40px my-xxxl-60px overflow-hidden">
                        <div class="container">
                            <div class="row lg-gutters-20 xxxl-gutters-30 align-items-end">
                                <div class="col-md-6 pt-20px pt-md-0 pb-50px pb-md-60px fs-11 lg-fs-18 xxl-fs-25 l-space-1-2 text-default-50">
                                    <p class="m-0">{{translate('For questions and info don’t hesitate to')}}</p>
                                    <a href="{{route('contact')}}" class="d-inline-block text-secondary hov-text-primary fs-30 lg-fs-50 xxl-fs-70 fw-700 font-play m-0 border-bottom border-width-3 border-lg-width-5 border-inherit pb-5px pb-lg-15px lh-1">{{translate('get in touch')}}</a>
                                </div>
                                @php
                                    $count=1;
                                @endphp
                                @foreach($services as $service)
                                    @if($count == 3)
                                        <div class="col-md-6 mb-30px mb-md-0 services-res-item">
                                            <div class="services-res-wrap clickable fw-500 l-space-1-2 text-white-80">
                                                <div class="services-res-image">
                                                    <img
                                                            class="img-fit absolute-full h-100"
                                                            src="{{ uploaded_asset($service->banner) }}"
                                                            alt=""
                                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
                                                    >
                                                    <div class="services-res-over px-15px px-sm-30px px-xxl-50px pb-15px pb-sm-30px pt-35px d-flex flex-column justify-content-between">
                                                        <div class="services-res-toggle fs-25 fw-700 font-play"></div>
                                                        <div class="services-res-over-desc c-scrollbar">{{$service->getTranslation('short_description')}}</div>
                                                        <h3 class="services-res-title fs-14 sm-fs-17 lg-fs-22 xxl-fs-30 fw-700 text-white font-play m-0 text-truncate-2">{{$service->getTranslation('name')}}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @php
                                        $count++;
                                    @endphp
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                @if(count($services) >= 4)
                    <div class="services-results-bottom overflow-hidden">
                        <div class="container">
                            <div class="row lg-gutters-20 xxxl-gutters-30">
                                @php
                                    $count=1;
                                    $service_count=1;
                                @endphp
                                @foreach($services as $service)
                                    @if($count >= 4)
                                        @php
                                            $service_class = ($service_count == 1) ? 'col-12' : 'col-md-6';

                                        @endphp
                                        <div class="{{$service_class}} mb-30px mb-lg-40px mb-xxxl-60px services-res-item">
                                            <div class="services-res-wrap clickable fw-500 l-space-1-2 text-white-80">
                                                <div class="services-res-image">
                                                    <img
                                                            class="img-fit absolute-full h-100"
                                                            src="{{ uploaded_asset($service->banner) }}"
                                                            alt=""
                                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
                                                    >
                                                    <div class="services-res-over px-15px px-sm-30px px-xxl-50px pb-15px pb-sm-30px pt-35px d-flex flex-column justify-content-between">
                                                        <div class="services-res-toggle fs-25 fw-700 font-play"></div>
                                                        <div class="services-res-over-desc c-scrollbar">{{$service->getTranslation('short_description')}}</div>
                                                        <h3 class="services-res-title fs-14 sm-fs-17 lg-fs-22 xxl-fs-30 fw-700 text-white font-play m-0 text-truncate-2">{{$service->getTranslation('name')}}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $service_count++;
                                            if($service_count == 4) {
                                                $service_count = 1;
                                            }
                                        @endphp
                                    @endif
                                    @php
                                        $count++;
                                    @endphp
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                <div class="d-none d-md-block sticky-column">
                    <div class="side-right-bar sticky-top" data-rel=".services-results-top">
                        <a href="{{route('contact')}}" class="side-right-bar-link side-popup-toggle lg-fs-25">
                            <span class="d-block side-right-bar-arrow"></span>
                            <span class="d-block side-right-bar-text-wrap">
                                <span class="side-right-bar-text">{{toUpper(translate('Contact Us'))}}</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @include('frontend.partials.feature_brands')
@endsection