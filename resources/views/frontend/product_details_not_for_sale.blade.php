@extends('frontend.layouts.app')

@section('meta_title'){{ $detailedProduct->meta_title }}@stop

@section('meta_description'){{ $detailedProduct->meta_description }}@stop

@section('meta_keywords'){{ $detailedProduct->tags }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $detailedProduct->meta_title }}">
    <meta itemprop="description" content="{{ $detailedProduct->meta_description }}">
    <meta itemprop="image" content="{{ uploaded_asset($detailedProduct->meta_img) }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ $detailedProduct->meta_title }}">
    <meta name="twitter:description" content="{{ $detailedProduct->meta_description }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ uploaded_asset($detailedProduct->meta_img) }}">
    <meta name="twitter:data1" content="{{ single_price($detailedProduct->unit_price) }}">
    <meta name="twitter:label1" content="Price">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $detailedProduct->meta_title }}" />
    <meta property="og:type" content="og:product" />
    <meta property="og:url" content="{{ route('product', $detailedProduct->slug) }}" />
    <meta property="og:image" content="{{ uploaded_asset($detailedProduct->meta_img) }}" />
    <meta property="og:description" content="{{ $detailedProduct->meta_description }}" />
    <meta property="og:site_name" content="{{ get_setting('meta_title') }}" />
    <meta property="og:price:amount" content="{{ single_price($detailedProduct->unit_price) }}" />
    <meta property="product:price:currency" content="{{ \App\Currency::findOrFail(\App\BusinessSetting::where('type', 'system_default_currency')->first()->value)->code }}" />
    <meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">
@endsection

@section('content')
    @php
        $qty = 0;
        if($detailedProduct->variant_product){
            foreach ($detailedProduct->stocks as $key => $stock) {
                $qty += $stock->qty;
            }
        }
        else{
            $qty = $detailedProduct->current_stock;
        }

        $photos = explode(',', $detailedProduct->photos);
    @endphp
    <div id="slider-publish" class="position-relative overflow-hidden">
        <div class="sk-carousel line-slider" data-fade='true' data-arrows="true" data-dots="false" data-autoplay="false" data-infinite="true" data-nav-for='.line-gallery-thumb'>
            @php
                $counter_slider = 0;
            @endphp
            @if($detailedProduct->video_provider == 'youtube' && isset(explode('=', $detailedProduct->video_link)[1]))
                <div class="carousel-box">
                    <div class="line-slider-item">
                        <div class="line-slider-image">
                            <div id="player" class="absolute-full"></div>
                            <div class="line-slider-over">
                                <div class="row no-gutters">
                                    <div class="col-lg-4">
                                        <div class="line-slider-over-left">
                                            <div class="line-slider-over-box breadcrumb-line d-lg-flex justify-content-center">
                                                <div class="line-slider-over-box-inner p-xxl-20px">
                                                    @if(isset($brand_id))
                                                        @php
                                                            $brand = \App\Brand::find($brand_id);
                                                        @endphp
                                                        <ul class="breadcrumb fs-10 md-fs-12">
                                                            <li class="breadcrumb-item">
                                                                <a class="hov-text-primary" href="{{route('brand_page', $brand->slug)}}">{{ $brand->getTranslation('name') }}</a>
                                                            </li>
                                                            @php
                                                                $breadcrumb_categories = array_reverse(getCategoryParents(\App\Category::findOrFail($detailedProduct->category_id)->parent_id));
                                                            @endphp
                                                            @foreach($breadcrumb_categories as $breadcrumb_category)
                                                                @php
                                                                    if($breadcrumb_category['parent_id'] == 0){
                                                                        $route = route('brand.maincategory', ['category_slug'=>$breadcrumb_category['slug'],'brand_slug'=>$brand->slug]);
                                                                    } else {
                                                                        $route = route('products.brand_category', ['category_slug'=>$breadcrumb_category['slug'],'brand_slug'=>$brand->slug]).'?outlet='.$detailedProduct->outlet;
                                                                    }
                                                                @endphp
                                                                <li class="breadcrumb-item">
                                                                    <a class="hov-text-primary" href="{{$route}}">
                                                                        {{ $breadcrumb_category['name'] }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                            <li class="breadcrumb-item">
                                                                <a class="hov-text-primary" href="{{ route('products.brand_category', ['category_slug'=>\App\Category::findOrFail($detailedProduct->category_id)->slug,'brand_slug'=>$brand->slug]) }}?outlet={{$detailedProduct->outlet}}">
                                                                    {{ \App\Category::find($detailedProduct->category_id)->getTranslation('name') }}
                                                                </a>
                                                            </li>
                                                            <li class="breadcrumb-item">
                                                                <a class="hov-text-primary" href="{{ route('product.brand', ['brand_slug'=>$brand->slug,'slug'=>$detailedProduct->slug]) }}">
                                                                    {{ $detailedProduct->getTranslation('name') }}
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    @elseif(isset($type_id))
                                                        @php
                                                            $type = \App\ProductType::find($type_id);
                                                        @endphp
                                                        <ul class="breadcrumb fs-10 md-fs-12">
                                                            <li class="breadcrumb-item">
                                                                <a class="hov-text-primary" href="{{route('type.maincategory', $type->slug)}}">{{ $type->getTranslation('name') }}</a>
                                                            </li>
                                                            @php
                                                                $breadcrumb_categories = array_reverse(getCategoryParents(\App\Category::findOrFail($detailedProduct->category_id)->parent_id));
                                                            @endphp
                                                            @foreach($breadcrumb_categories as $breadcrumb_category)
                                                                @php
                                                                    if($breadcrumb_category['parent_id'] == 0){
                                                                        $route = route('typecat.maincategory', ['category_slug'=>$breadcrumb_category['slug'],'type_slug'=>$type->slug]);
                                                                    } else {
                                                                        $route = route('products.type_category', ['category_slug'=>$breadcrumb_category['slug'],'type_slug'=>$type->slug]).'?outlet='.$detailedProduct->outlet;
                                                                    }
                                                                @endphp
                                                                <li class="breadcrumb-item">
                                                                    <a class="hov-text-primary" href="{{$route}}">
                                                                        {{ $breadcrumb_category['name'] }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                            <li class="breadcrumb-item">
                                                                <a class="hov-text-primary" href="{{ route('products.type_category', ['category_slug'=>\App\Category::findOrFail($detailedProduct->category_id)->slug,'type_slug'=>$type->slug]) }}?outlet={{$detailedProduct->outlet}}">
                                                                    {{ \App\Category::find($detailedProduct->category_id)->getTranslation('name') }}
                                                                </a>
                                                            </li>
                                                            <li class="breadcrumb-item">
                                                                <a class="hov-text-primary" href="{{ route('product.type', ['type_slug'=>$type->slug,'slug'=>$detailedProduct->slug]) }}">
                                                                    {{ $detailedProduct->getTranslation('name') }}
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    @else
                                                        <ul class="breadcrumb fs-10 md-fs-12">
                                                            @php
                                                                $breadcrumb_categories = array_reverse(getCategoryParents(\App\Category::findOrFail($detailedProduct->category_id)->parent_id));
                                                            @endphp
                                                            @foreach($breadcrumb_categories as $breadcrumb_category)
                                                                @php
                                                                    if($breadcrumb_category['parent_id'] == 0){
                                                                        $route = route('maincategory', $breadcrumb_category['slug']);
                                                                    } else {
                                                                        $route = route('products.category', $breadcrumb_category['slug']).'?outlet='.$detailedProduct->outlet;
                                                                    }
                                                                @endphp
                                                                <li class="breadcrumb-item">
                                                                    <a class="hov-text-primary" href="{{$route}}">
                                                                        {{ $breadcrumb_category['name'] }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                            <li class="breadcrumb-item">
                                                                <a class="hov-text-primary" href="{{ route('products.category', \App\Category::findOrFail($detailedProduct->category_id)->slug) }}?outlet={{$detailedProduct->outlet}}">
                                                                    {{ \App\Category::find($detailedProduct->category_id)->getTranslation('name') }}
                                                                </a>
                                                            </li>
                                                            <li class="breadcrumb-item">
                                                                <a class="hov-text-primary" href="{{ route('product', $detailedProduct->slug) }}">
                                                                    {{ $detailedProduct->getTranslation('name') }}
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="line-slider-over-box slogan d-lg-flex justify-content-center">
                                                <div class="line-slider-over-box-inner p-0">
                                                    <h2 class="fs-11 md-fs-14 xxl-fs-16 fw-400">
                                                        @if(isset($brand_id))
                                                            <a class="hov-text-primary" href="{{ route('products.brand_category', ['category_slug'=>\App\Category::findOrFail($detailedProduct->category_id)->slug,'brand_slug'=>$brand->slug]) }}?outlet={{$detailedProduct->outlet}}">
                                                                {{ \App\Category::find($detailedProduct->category_id)->getTranslation('name') }}
                                                            </a>
                                                        @elseif(isset($type_id))
                                                            <a class="hov-text-primary" href="{{ route('products.type_category', ['category_slug'=>\App\Category::findOrFail($detailedProduct->category_id)->slug,'type_slug'=>$type->slug]) }}?outlet={{$detailedProduct->outlet}}">
                                                                {{ \App\Category::find($detailedProduct->category_id)->getTranslation('name') }}
                                                            </a>
                                                        @else
                                                            <a class="hov-text-primary" href="{{ route('products.category', \App\Category::findOrFail($detailedProduct->category_id)->slug) }}?outlet={{$detailedProduct->outlet}}">
                                                                {{ \App\Category::find($detailedProduct->category_id)->getTranslation('name') }}
                                                            </a>
                                                        @endif
                                                    </h2>
                                                    <h2 class="font-play fs-25 md-fs-30 xl-fs-40 xxl-fs-50 fw-700 m-0">{{ $detailedProduct->getTranslation('name') }}</h2>
                                                </div>
                                            </div>
                                            <div class="line-slider-over-box d-none d-lg-block"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="fullscreen-icon" data-id="{{$counter_slider}}">
                                    <svg class="w-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <use xlink:href="{{static_asset('assets/img/icons/fullscreen-icon.svg')}}#content"></use>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    $counter_slider++;
                @endphp
            @endif
            @foreach ($detailedProduct->stocks as $key => $stock)
                @if ($stock->image != null)
                    <div class="carousel-box">
                        <div class="line-slider-item">
                            <div class="line-slider-image">
                                <img
                                        class="absolute-full h-100 img-fit lazyload"
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                        data-src="{{ uploaded_asset($stock->image) }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                >
                                <div class="line-slider-over">
                                    <div class="row no-gutters">
                                        <div class="col-lg-4">
                                            <div class="line-slider-over-left">
                                                <div class="line-slider-over-box breadcrumb-line d-lg-flex justify-content-center">
                                                    <div class="line-slider-over-box-inner p-xxl-20px">
                                                        <ul class="breadcrumb fs-10 md-fs-12">
                                                            @php
                                                                $breadcrumb_categories = array_reverse(getCategoryParents(\App\Category::findOrFail($detailedProduct->category_id)->parent_id));
                                                            @endphp
                                                            @foreach($breadcrumb_categories as $breadcrumb_category)
                                                                @php
                                                                    if($breadcrumb_category['parent_id'] == 0){
                                                                        $route = route('maincategory', $breadcrumb_category['slug']);
                                                                    } else {
                                                                        $route = route('products.category', $breadcrumb_category['slug']).'?outlet='.$detailedProduct->outlet;
                                                                    }
                                                                @endphp
                                                                <li class="breadcrumb-item">
                                                                    <a class="hov-text-primary" href="{{$route}}">
                                                                        {{ $breadcrumb_category['name'] }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                            <li class="breadcrumb-item">
                                                                <a class="hov-text-primary" href="{{ route('products.category', \App\Category::findOrFail($detailedProduct->category_id)->slug) }}?outlet={{$detailedProduct->outlet}}">
                                                                    {{ \App\Category::find($detailedProduct->category_id)->getTranslation('name') }}
                                                                </a>
                                                            </li>
                                                            <li class="breadcrumb-item">
                                                                <a class="hov-text-primary" href="{{ route('product', $detailedProduct->slug) }}">
                                                                    {{ $detailedProduct->getTranslation('name') }}
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="line-slider-over-box slogan d-lg-flex justify-content-center">
                                                    <div class="line-slider-over-box-inner p-0">
                                                        <h2 class="fs-11 md-fs-14 xxl-fs-16 fw-400">{{\App\Category::find($detailedProduct->category_id)->getTranslation('name')}}</h2>
                                                        <h2 class="font-play fs-25 md-fs-30 xl-fs-40 xxl-fs-50 fw-700 m-0">{{ $detailedProduct->getTranslation('name') }}</h2>
                                                    </div>
                                                </div>
                                                <div class="line-slider-over-box d-none d-lg-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fullscreen-icon" data-id="{{$counter_slider}}">
                                        <svg class="w-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <use xlink:href="{{static_asset('assets/img/icons/fullscreen-icon.svg')}}#content"></use>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        $counter_slider++;
                    @endphp
                @endif
            @endforeach
            @foreach ($photos as $key => $photo)
                @if($photo)
                    <div class="carousel-box">
                        <div class="line-slider-item">
                            <div class="line-slider-image">
                                <img
                                        class="absolute-full h-100 img-fit lazyload"
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                        data-src="{{ uploaded_asset($photo) }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                >
                                <div class="line-slider-over">
                                    <div class="row no-gutters">
                                        <div class="col-lg-4">
                                            <div class="line-slider-over-left">
                                                <div class="line-slider-over-box breadcrumb-line d-lg-flex justify-content-center">
                                                    <div class="line-slider-over-box-inner p-xxl-20px">
                                                        <ul class="breadcrumb fs-10 md-fs-12">
                                                            @php
                                                                $breadcrumb_categories = array_reverse(getCategoryParents(\App\Category::findOrFail($detailedProduct->category_id)->parent_id));
                                                            @endphp
                                                            @foreach($breadcrumb_categories as $breadcrumb_category)
                                                                @php
                                                                    if($breadcrumb_category['parent_id'] == 0){
                                                                        $route = route('maincategory', $breadcrumb_category['slug']);
                                                                    } else {
                                                                        $route = route('products.category', $breadcrumb_category['slug']).'?outlet='.$detailedProduct->outlet;
                                                                    }
                                                                @endphp
                                                                <li class="breadcrumb-item">
                                                                    <a class="hov-text-primary" href="{{$route}}">
                                                                        {{ $breadcrumb_category['name'] }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                            <li class="breadcrumb-item">
                                                                <a class="hov-text-primary" href="{{ route('products.category', \App\Category::findOrFail($detailedProduct->category_id)->slug) }}?outlet={{$detailedProduct->outlet}}">
                                                                    {{ \App\Category::find($detailedProduct->category_id)->getTranslation('name') }}
                                                                </a>
                                                            </li>
                                                            <li class="breadcrumb-item">
                                                                <a class="hov-text-primary" href="{{ route('product', $detailedProduct->slug) }}">
                                                                    {{ $detailedProduct->getTranslation('name') }}
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="line-slider-over-box slogan d-lg-flex justify-content-center">
                                                    <div class="line-slider-over-box-inner p-0">
                                                        <h2 class="fs-11 md-fs-14 xxl-fs-16 fw-400">{{\App\Category::find($detailedProduct->category_id)->getTranslation('name')}}</h2>
                                                        <h2 class="font-play fs-25 md-fs-30 xl-fs-40 xxl-fs-50 fw-700 m-0">{{ $detailedProduct->getTranslation('name') }}</h2>
                                                    </div>
                                                </div>
                                                <div class="line-slider-over-box d-none d-lg-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fullscreen-icon" data-id="{{$counter_slider}}">
                                        <svg class="w-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <use xlink:href="{{static_asset('assets/img/icons/fullscreen-icon.svg')}}#content"></use>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        $counter_slider++;
                    @endphp
                @endif
            @endforeach
        </div>
    </div>
    <div class="pl-10px pl-lg-20px pt-10px pt-lg-15px pb-10px pb-lg-20px overflow-hidden">
        <div class="slider-publish-thumbs">
            <div class="sk-carousel gutters-5 lg-gutters-10 line-gallery-thumb" data-items='5' data-xl-items="4" data-lg-items="4" data-md-items="3" data-sm-items="3" data-xs-items="2" data-nav-for='.line-slider' data-focus-select='true' data-arrows='false' data-infinite="true">
                @if($detailedProduct->video_provider == 'youtube' && isset(explode('=', $detailedProduct->video_link)[1]))
                    <div class="carousel-box c-pointer" data-variation="{{ $stock->variant }}">
                        <div class="quick-view-thumb-wrap">
                            <img
                                    class="img-fit lazyload h-100 absolute-full"
                                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                    data-src="https://img.youtube.com/vi/{{explode('=', $detailedProduct->video_link)[1]}}/maxresdefault.jpg"
                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                            >
                        </div>
                    </div>
                @endif
                @foreach ($detailedProduct->stocks as $key => $stock)
                    @if ($stock->image != null)
                        <div class="carousel-box c-pointer" data-variation="{{ $stock->variant }}">
                            <div class="quick-view-thumb-wrap">
                                <img
                                        class="img-fit lazyload h-100 absolute-full"
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                        data-src="{{ uploaded_asset($stock->image) }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                >
                            </div>
                        </div>
                    @endif
                @endforeach
                @foreach ($photos as $key => $photo)
                    @if($photo)
                        <div class="carousel-box c-pointer">
                            <div class="quick-view-thumb-wrap">
                                <img
                                        class="img-fit lazyload h-100 absolute-full"
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                        data-src="{{ uploaded_asset($photo) }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                >
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @php
    $price_with_discount = home_discounted_price($detailedProduct->id);
    $price_without_discount = home_price($detailedProduct->id);
    @endphp
    <div class="bg-black-10 py-35px py-lg-90px py-xxl-130px position-relative overflow-hidden min-h-320px lg-min-h-420px d-flex flex-column justify-content-center">
        <div class="container">
            <div class="mw-1150px mx-auto pr-40px pr-sm-30px pr-lg-0">
                @if($detailedProduct->getTranslation('short_description'))
                    <div class="mb-20px mb-lg-35px mb-xxl-55px fs-14 lg-fs-17 xxl-fs-20 l-space-1-2 font-play text-default-50 lg-lh-2">
                        <p>{{$detailedProduct->getTranslation('short_description')}}</p>
                    </div>
                @endif
                @if(format_price(0) != $price_with_discount)
                <div class="row align-items-center">
                    <div class="col-sm">
                        <div class="publish-price font-play mb-10px">
                            <span class="fs-20 lg-fs-25 xxl-fs-35 fw-700 text-secondary">{{toUpper(translate('From'))}} {{ $price_with_discount }}</span>
                            @if($price_without_discount != $price_with_discount)
                                <span class="text-black product-res-price-del">{{ $price_without_discount }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-auto text-sm-right text-default-50 fs-10 md-fs-12 fw-500">
                        {{includeVatText()}}
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div class="side-right-bar">
            <a href="{{route('contact')}}" class="side-right-bar-link lg-fs-25">
                <span class="d-block side-right-bar-arrow"></span>
                <span class="d-block side-right-bar-text-wrap">
                    <span class="side-right-bar-text">{{toUpper(translate('Contact us for More'))}}</span>
                </span>
            </a>
        </div>
    </div>
    <div class="mt-45px mt-lg-85px mt-xxl-125px overflow-hidden">
        <div class="container">
            <div class="mw-1350px mx-auto">
                <div class="nav border-bottom border-black-100 border-width-2 sk-nav-tabs fs-16 lg-fs-19 xxl-fs-22 font-play">
                    <a href="#tab_default_1" data-toggle="tab" class="pb-10px pb-lg-15px active show">
                        {{toUpper(translate('Product Description'))}}
                    </a>
                </div>
                <div class="tab-content pt-20px pt-lg-35px fw-500 l-space-1-2 fs-11 lg-fs-15 xxl-fs-18 text-default-50">
                    <div class="tab-pane fade active show" id="tab_default_1">
                        <div class="mw-100 overflow-hidden text-left">
                            @if($detailedProduct->getTranslation('description'))
                            <?php echo $detailedProduct->getTranslation('description'); ?>
                            @else
                                <p>{{translate('There is no description of this product.')}}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
        $related_products = filter_products(\App\Product::where('category_id', $detailedProduct->category_id)->where('id', '!=', $detailedProduct->id))->limit(10)->get();
    @endphp
    @if(count($related_products) > 0)
        <div class="mt-45px mt-lg-85px mt-xxl-175px overflow-hidden">
            <div class="container">
                <div class="mw-1350px mx-auto publish-container-target">
                    <h3 class="fs-13 md-fs-18 xxl-fs-23 fw-50 l-space-1-2 text-default-50 lh-1 m-0">{{translate('You may also like these')}}</h3>
                    <h2 class="fs-40 md-fs-55 xxl-fs-70 fw-700 mb-25px mb-md-50px mb-xxl-75px font-play lh-1">{{toUpper(translate('Suggested Products'))}}</h2>
                </div>
            </div>
            <div class="container publish-container-adjustable">
                <div class="mw-1350px mx-auto">
                    <div class="container-left-1350px">
                        <div class="product-carousel-container">
                            <div class="swiper featured-products-swiper product-carousel-arrows">
                                <div class="swiper-wrapper" data-center="false" data-items="5" data-xl-items="4" data-lg-items="3"  data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows='true' data-infinite='true'>
                                    @foreach ($related_products as $key => $related_product)
                                        <div class="swiper-slide">
                                            @include('frontend.partials.product_listing.forsale_listing',['product' => $related_product, 'type_id' => null , 'brand_id' => null])
                                        </div>
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
                </div>
            </div>
        </div>
    @endif
    <div id="publish-banners-popup">
        <div class="d-flex flex-column justify-content-center publish-banners-popup-container">
            <div class="publish-banners-popup-close">
                <svg class="w-30px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
                    <use xlink:href="{{static_asset('assets/img/icons/close-icon.svg')}}#content"></use>
                </svg>
            </div>
            <div class="py-70px publish-banners-popup-wrap">
                <div class="container">
                    <h2 class="fs-30 md-fs-40 fw-700 font-play l-space-1-2 mb-50px mb-sm-125px text-default-50">{{ $detailedProduct->getTranslation('name') }}</h2>
                </div>
                <div class="mb-50px mb-sm-125px">
                    <div class="sk-carousel" data-arrows="true" data-dots="false" data-autoplay="false" data-infinite="true">
                        @if($detailedProduct->video_provider == 'youtube' && isset(explode('=', $detailedProduct->video_link)[1]))
                            <div class="carousel-box">
                                <div class="publish-banner-pop-image">
                                    <div id="player-phone" class="absolute-full"></div>
                                </div>
                            </div>
                        @endif
                        @foreach ($detailedProduct->stocks as $key => $stock)
                            @if ($stock->image != null)
                                <div class="carousel-box">
                                    <div class="publish-banner-pop-image">
                                        <img
                                                class="absolute-full h-100 img-fit lazyload"
                                                src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                data-src="{{ uploaded_asset($stock->image) }}"
                                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                        >
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        @foreach ($photos as $key => $photo)
                            @if($photo)
                                <div class="carousel-box">
                                    <div class="publish-banner-pop-image">
                                        <img
                                                class="absolute-full h-100 img-fit lazyload"
                                                src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                data-src="{{ uploaded_asset($photo) }}"
                                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                        >
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('frontend.partials.feature_brands')
@endsection

@section('modal')
    <div class="modal fade" id="out_of_stock_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-lg" role="document">
            <div class="modal-content position-relative">
                <div class="modal-body pt-40px px-40px pb-70px fs-16 fw-500 l-space-1-2 text-default-50">
                    <h2 class="text-secondary fs-45 fw-700 font-play mb-55px">{{translate('Order item explanation')}}</h2>
                    <p>{{translate('We do not have the desired product in stock, but we can order it for you from our supplier in the quantity you require.')}}</p>
                    <p>{{translate('As soon as we will receive the delivery from our supplier, we will send the order to you on the same day.')}}</p>
                </div>
                <div class="side-modal-close" data-dismiss="modal" aria-label="Close">
                    <div class="side-modal-close-icon"></div>
                    <div class="side-modal-close-text-wrap">
                        <div class="side-modal-close-text">{{toUpper(translate('Close'))}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="chat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="modal-header">
                    <h5 class="modal-title fw-600 h5">{{ translate('Any query about this product')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="" action="{{ route('conversations.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $detailedProduct->id }}">
                    <div class="modal-body gry-bg px-3 pt-3">
                        <div class="form-group">
                            <input type="text" class="form-control mb-3" name="title" value="{{ $detailedProduct->name }}" placeholder="{{ translate('Product Name') }}" required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="8" name="message" required placeholder="{{ translate('Your Question') }}">{{ route('product', $detailedProduct->slug) }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary fw-600" data-dismiss="modal">{{ translate('Cancel')}}</button>
                        <button type="submit" class="btn btn-primary fw-600">{{ translate('Send')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="login_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">{{ translate('Login')}}</h6>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-3">
                        <form class="form-default" role="form" action="{{ route('cart.login.submit') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                    <input type="text" class="form-control h-auto form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{ translate('Email Or Phone')}}" name="email" id="email">
                                @else
                                    <input type="email" class="form-control h-auto form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{  translate('Email') }}" name="email">
                                @endif
                                @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                    <span class="opacity-60">{{  translate('Use country code before number') }}</span>
                                @endif
                            </div>

                            <div class="form-group">
                                <input type="password" name="password" class="form-control h-auto form-control-lg" placeholder="{{ translate('Password')}}">
                            </div>

                            <div class="row mb-2">
                                <div class="col-6">
                                    <label class="sk-checkbox">
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <span class=opacity-60>{{  translate('Remember Me') }}</span>
                                        <span class="sk-square-check"></span>
                                    </label>
                                </div>
                                <div class="col-6 text-right">
                                    <a href="{{ route('password.request') }}" class="text-reset opacity-60 fs-14">{{ translate('Forgot password?')}}</a>
                                </div>
                            </div>

                            <div class="mb-5">
                                <button type="submit" class="btn btn-primary btn-block fw-600">{{  translate('Login') }}</button>
                            </div>
                        </form>

                        <div class="text-center mb-3">
                            <p class="text-muted mb-0">{{ translate('Dont have an account?')}}</p>
                            <a href="{{ route('user.registration') }}">{{ translate('Register Now')}}</a>
                        </div>
                        @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                            <div class="separator mb-3">
                                <span class="bg-white px-3 opacity-60">{{ translate('Or Login With')}}</span>
                            </div>
                            <ul class="list-inline social colored text-center mb-5">
                                @if (\App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1)
                                    <li class="list-inline-item">
                                        <a href="{{ route('social.login', ['provider' => 'facebook']) }}" class="facebook">
                                            <i class="lab la-facebook-f"></i>
                                        </a>
                                    </li>
                                @endif
                                @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1)
                                    <li class="list-inline-item">
                                        <a href="{{ route('social.login', ['provider' => 'google']) }}" class="google">
                                            <i class="lab la-google"></i>
                                        </a>
                                    </li>
                                @endif
                                @if (\App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                                    <li class="list-inline-item">
                                        <a href="{{ route('social.login', ['provider' => 'twitter']) }}" class="twitter">
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
@endsection

@section('script')
    <script type="text/javascript">
      $(document).ready(function() {
        getVariantPrice();
      });

      $(document).on('click', '.fullscreen-icon', function () {
        $('#publish-banners-popup').addClass('active');
        $('#publish-banners-popup .sk-carousel').slick('slickGoTo', $(this).data('id'));
      });

      $(document).on('click', '.publish-banners-popup-close', function () {
        $('#publish-banners-popup').removeClass('active');
      });

      function CopyToClipboard(e) {
        var url = $(e).data('url');
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(url).select();
        try {
          document.execCommand("copy");
          SK.plugins.notify('success', '{{ translate('Link copied to clipboard') }}');
        } catch (err) {
          SK.plugins.notify('danger', '{{ translate('Oops, unable to copy') }}');
        }
        $temp.remove();
        // if (document.selection) {
        //     var range = document.body.createTextRange();
        //     range.moveToElementText(document.getElementById(containerid));
        //     range.select().createTextRange();
        //     document.execCommand("Copy");

        // } else if (window.getSelection) {
        //     var range = document.createRange();
        //     document.getElementById(containerid).style.display = "block";
        //     range.selectNode(document.getElementById(containerid));
        //     window.getSelection().addRange(range);
        //     document.execCommand("Copy");
        //     document.getElementById(containerid).style.display = "none";

        // }
        // SK.plugins.notify('success', 'Copied');
      }
      function show_chat_modal(){
          @if (Auth::check())
          $('#chat_modal').modal('show');
          @else
          $('#login_modal').modal('show');
          @endif
      }
      @if($detailedProduct->video_provider == 'youtube' && isset(explode('=', $detailedProduct->video_link)[1]))
      // 2. This code loads the IFrame Player API code asynchronously.
      var tag = document.createElement('script');

      tag.src = "https://www.youtube.com/iframe_api";
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

      // 3. This function creates an <iframe> (and YouTube player)
      //    after the API code downloads.
      var player;
      var player_phone;
      function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
          height: '390',
          width: '640',
          videoId: "{{ explode('=', $detailedProduct->video_link)[1] }}",
          playerVars: {
            'playsinline': 1,
            'autoplay': 1,
            'showinfo': 0,
            'controls': 0,
            'loop': 1,
            'playlist': "{{ explode('=', $detailedProduct->video_link)[1] }}",
            'mute': 1,
            'enablejsapi': 1,
            'version': 3,
            'playerapiid': 'ytplayer',
            'rel': 0
          },
          events: {
            'onReady': onPlayerReady,
            'onStateChange':
              function(e) {
                if (e.data === YT.PlayerState.ENDED) {
                  player.playVideo();
                }
              }
          }
        });
        player_phone = new YT.Player('player-phone', {
          height: '390',
          width: '640',
          videoId: "{{ explode('=', $detailedProduct->video_link)[1] }}",
          playerVars: {
            'playsinline': 1,
            'autoplay': 1,
            'showinfo': 0,
            'controls': 0,
            'loop': 1,
            'playlist': "{{ explode('=', $detailedProduct->video_link)[1] }}",
            'mute': 1,
            'enablejsapi': 1,
            'version': 3,
            'playerapiid': 'ytplayer',
            'rel': 0
          },
          events: {
            'onReady': onPlayerReady,
            'onStateChange':
              function(e) {
                if (e.data === YT.PlayerState.ENDED) {
                  player_phone.playVideo();
                }
              }
          }
        });
      }

      // 4. The API will call this function when the video player is ready.
      function onPlayerReady(event) {
        event.target.playVideo();
      }
      function onPlayerReadyPhone(event) {
        event.target.playVideo();
      }

      // 5. The API calls this function when the player's state changes.
      //    The function indicates that when playing a video (state=1),
      //    the player should play for six seconds and then stop.
      var done = false;
      function onPlayerStateChange(event) {
        if (event.data == YT.PlayerState.PLAYING && !done) {
          setTimeout(stopVideo, 6000);
          done = true;
        }
      }
      function stopVideo() {
        player.stopVideo();
        player_phone.stopVideo();
      }
      @endif

      var numberOfSlides = document.querySelectorAll('.featured-products-swiper .swiper-slide').length;
      var featured_swiper = undefined;
      function initSwiper() {
        if(featured_swiper != undefined) {
          featured_swiper.destroy();
        }
        var screen = $('body').outerWidth();
        var navigation = false;
        if(screen >= 1500 && numberOfSlides > 4) {
          navigation = true;
        } else if(screen >= 1200 && numberOfSlides > 3) {
          navigation = true;
        } else if(screen >= 768 && numberOfSlides > 2) {
          navigation = true;
        } else if(screen <= 767 && numberOfSlides > 1) {
          navigation = true;
        }
        if(navigation == true) {
          $('.featured-products-swiper .swiper-prev, .featured-products-swiper .swiper-next').removeClass('d-none');
        } else {
          $('.featured-products-swiper .swiper-prev, .featured-products-swiper .swiper-next').addClass('d-none');
        }
        featured_swiper = new Swiper('.featured-products-swiper', {
          loop: numberOfSlides > 1 ? true : false,
          slidesPerView: 2,
          spaceBetween: 40,
          navigation: {
            nextEl: '.swiper-next',
            prevEl: '.swiper-prev',
            enabled: navigation,
          },
          breakpoints: {
            768: {
              slidesPerView: 3,
              spaceBetween: 40,
              loop: numberOfSlides > 2 ? true : false,
            },
            992: {
              slidesPerView: 3,
              spaceBetween: 60,
              loop: numberOfSlides > 2 ? true : false,
            },
            1200: {
              slidesPerView: 4,
              spaceBetween: 60,
              loop: numberOfSlides > 3 ? true : false,
            },
            1500: {
              slidesPerView: 5,
              spaceBetween: 60,
              loop: numberOfSlides > 4 ? true : false,
            },
            1750: {
              slidesPerView: 5,
              spaceBetween: 80,
              loop: numberOfSlides > 4 ? true : false,
            },
          },
        });
      }
      initSwiper();
      //Swiper plugin initialization on window resize
      $(window).on('resize', function(){
        initSwiper();
      });
    </script>
@endsection
