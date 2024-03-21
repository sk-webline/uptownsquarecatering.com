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
        $spare_parts = ($detailedProduct->brand != null && env('YAMAHA_BRAND') == $detailedProduct->brand->id && env('SPARE_PART_CATEGORY') == $detailedProduct->category_id) ? true : false;
        $qty = 0;
        if($detailedProduct->variant_product){
            foreach ($detailedProduct->stocks as $key => $stock) {
                $qty += $stock->qty;
            }
        }
        else{
            $qty = $detailedProduct->current_stock;
        }
        $srp_price = srp_price($detailedProduct->id);
    @endphp
    <div class="mt-20px mt-md-40px overflow-hidden">
        <div class="container">
            <div class="mw-1350px mx-auto">
                <div class="mb-30px mb-md-55px">
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
                            @if($detailedProduct->category->for_sale==1)
                                <li class="breadcrumb-item">
                                    <a class="hov-text-primary" href="{{route('search')}}">
                                        {{ translate('Webshop') }}
                                    </a>
                                </li>
                            @endif
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
                <div class="row lg-gutters-30 xxl-gutters-60">
                    <div class="col-12 col-lg-45per order-lg-1">
                        <div class="row gutters-10 align-items-start mb-5px mb-md-15px">
                            @if($detailedProduct->brand != null)
                                <div class="col">
                                    <img class="h-10px h-md-20px product-res-brand" src="{{uploaded_asset($detailedProduct->brand->logo)}}" alt="">
                                </div>
                            @endif
                            @if(!isPartner() && ($price_with_discount != $price_without_discount))
                                @php
                                    $discount_icon = ($detailedProduct->discount_type == 'percent') ? '%': currency_symbol();
                                @endphp
                                <div class="col d-flex justify-content-end overflow-hidden">
                                    <div class="d-flex align-items-center font-play fs-10 lg-fs-12 xxl-fs-14 fw-700 l-space-1-2 product-res-offer">
                                        <span>
                                            {{ toUpper(translate('Special Price')) }}
{{--                                            <span class="d-none d-md-inline"> - {{$detailedProduct->discount}}{{$discount_icon}}</span>--}}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @php
                            $photos = explode(',', $detailedProduct->photos);
                        @endphp
                        <div class="position-relative">
                            <div class="sk-carousel product-gallery" data-nav-for='.product-gallery-thumb' data-fade='true' data-auto-height='true'>
                                <div class="carousel-box img-zoom">
                                    <div class="quick-view-thumb-wrap">
                                        <img
                                                class="img-contain lazyload h-100 absolute-full"
                                                src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                data-src="{{ uploaded_asset($detailedProduct->thumbnail_img) }}"
                                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                        >
                                    </div>
                                </div>
                                @foreach ($detailedProduct->stocks as $key => $stock)
                                    @if ($stock->image != null)
                                        <div class="carousel-box img-zoom">
                                            <div class="quick-view-thumb-wrap">
                                                <img
                                                        class="img-contain lazyload h-100 absolute-full"
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
                                        <div class="carousel-box img-zoom">
                                            <div class="quick-view-thumb-wrap">
                                                <img
                                                        class="img-contain lazyload h-100 absolute-full"
                                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                        data-src="{{ uploaded_asset($photo) }}"
                                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                                >
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @if(isPartner() && $srp_price != $price_with_discount)
                                <div class="product-res-srp text-right lh-1 p-2px fs-14 @if($detailedProduct->category->for_sale==0) visibility-hidden @endif">
                                    <div class="text-primary fs-10 fw-700">{{translate('SRP')}}</div>
                                    <div class="">{{ $srp_price }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12 order-lg-3">
                        <div class="my-10px my-lg-20px border-bottom border-default-200">
                            <div class="d-lg-none">
                                @if($detailedProduct->category->for_sale == 1)
                                    <h2 class="l-space-1-2 fs-10 sm-fs-12 fw-500 text-default-50 mb-0 pb-2px">{{translate('Part Number')}}: <span id="part-number-phone"></span></h2>
                                @else
                                    @if($detailedProduct->part_number)
                                        <h2 class="l-space-1-2 fs-10 sm-fs-12 fw-500 text-default-50 mb-0 pb-2px">{{translate('Part Number')}}: {{$detailedProduct->part_number}}</h2>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-45per order-lg-4">
                        <div class="sk-carousel gutters-5 lg-gutters-10 xxl-gutters-15 product-gallery-thumb" data-items='5' data-xl-items="4" data-md-items="5" data-sm-items="4" data-nav-for='.product-gallery' data-focus-select='true' data-arrows='false'>
                            <div class="carousel-box c-pointer" data-variation="{{ $stock->variant }}">
                                <div class="quick-view-thumb-wrap">
                                    <img
                                            class="img-contain lazyload h-100 absolute-full"
                                            src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                            data-src="{{ uploaded_asset($detailedProduct->thumbnail_img) }}"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                    >
                                </div>
                            </div>
                            @foreach ($detailedProduct->stocks as $key => $stock)
                                @if ($stock->image != null)
                                    <div class="carousel-box c-pointer" data-variation="{{ $stock->variant }}">
                                        <div class="quick-view-thumb-wrap">
                                            <img
                                                    class="img-contain lazyload h-100 absolute-full"
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
                                                    class="img-contain lazyload h-100 absolute-full"
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
                    <div id="publish-right-form" class="col-12 col-lg-55per order-lg-2 d-flex flex-column justify-content-between mt-25px mt-md-50px mt-lg-0">
                        <div class="publish-right-top">
                            @if($detailedProduct->category->for_sale == 1)
                                <h2 class="l-space-1-2 fs-11 sm-fs-12 fw-500 text-default-80 mb-15px mb-md-35px d-none d-lg-block">{{translate('Part Number')}}: <span id="part-number"></span></h2>
                            @else
                                <h2 class="l-space-1-2 fs-11 sm-fs-12 fw-500 text-default-80 mb-15px mb-md-35px d-none d-lg-block" @if(!$detailedProduct->part_number) style="visibility: hidden" @endif>{{translate('Part Number')}}: {{$detailedProduct->part_number}}</h2>
                            @endif
                            <h3 class="font-play text-secondary fs-11 sm-fs-13 l-space-1-2 fw-400 mb-0 mb-sm-5px">{{toUpper(\App\Category::find($detailedProduct->category_id)->getTranslation('name'))}}</h3>
                            <h1 class="text-default-50 fs-18 lg-fs-24 xxl-fs-30 font-play l-space-1-2 fw-700 lh-1">{{ $detailedProduct->getTranslation('name') }}</h1>
                            @if($detailedProduct->category->for_sale == 1)
                                <div class="publish-price font-play mb-20px mb-lg-10px fs-10 lg-fs-14 xxl-fs-18">
                                    <span class="fs-18 lg-fs-26 xxl-fs-35 fw-700">{{ $price_with_discount }}</span>
                                    @if($price_with_discount != $price_without_discount)
                                        <span class="product-res-price-del">{{ $price_without_discount }}</span>
                                    @endif
                                </div>
                                <?php /*
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        @php
                                            $total = 0;
                                            $total += $detailedProduct->reviews->count();
                                        @endphp
                                        <span class="rating">
                                                {{ renderStarRating($detailedProduct->rating) }}
                                            </span>
                                        <span class="ml-1 opacity-50">({{ $total }} {{ translate('reviews')}})</span>
                                    </div>
                                    <div class="col-6 text-right">

                                        @if ($qty > 0)
                                            <span class="badge badge-md badge-inline badge-pill badge-success">{{ translate('In stock')}}</span>
                                        @else
                                            <span class="badge badge-md badge-inline badge-pill badge-danger">{{ translate('Out of stock')}}</span>
                                        @endif
                                    </div>
                                    <div class="col-auto">
                                        <small class="mr-2 opacity-50">{{ translate('Estimate Shipping Time')}}: </small>
                                        @if ($detailedProduct->est_shipping_days)
                                            {{ $detailedProduct->est_shipping_days }} {{  translate('Days') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <small class="mr-2 opacity-50">{{ translate('Sold by')}}: </small><br>
                                        @if ($detailedProduct->added_by == 'seller' && \App\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1)
                                            <a href="{{ route('shop.visit', $detailedProduct->user->shop->slug) }}" class="text-reset">{{ $detailedProduct->user->shop->name }}</a>
                                        @else
                                            {{  translate('Inhouse product') }}
                                        @endif
                                    </div>
                                    @if (\App\BusinessSetting::where('type', 'conversation_system')->first()->value == 1)
                                        <div class="col-auto">
                                            <button class="btn btn-sm btn-soft-primary" onclick="show_chat_modal()">{{ translate('Message Seller')}}</button>
                                        </div>
                                    @endif
                                </div>*/ ?>
                                @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated && $detailedProduct->earn_point > 0)
                                    <div class="publish-product-section mb-10px mb-md-20px">
                                        <h4 class="fs-12 fw-500 text-default-50 l-space-1-2 lh-1 mb-5px">{{translate('Club Point')}}</h4>
                                        <div class="d-inline-block rounded px-2 bg-soft-primary border-soft-primary border">
                                            <span class="strong-700">{{ $detailedProduct->earn_point }}</span>
                                        </div>
                                    </div>
                                @endif
                                <form id="option-choice-form">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $detailedProduct->id }}">

                                    @if (count(json_decode($detailedProduct->colors)) > 0)
                                        <div class="publish-product-section mb-10px mb-md-15px">
                                            <h4 class="fs-12 fw-500 text-default-50 l-space-1-2 lh-1 mb-5px">{{ translate('Color')}}</h4>
                                            <div class="row gutters-3 row-cols-7 row-cols-sm-10 row-cols-md-12 row-cols-lg-10 row-cols-xl-12">
                                                @foreach (json_decode($detailedProduct->colors) as $key => $color)
                                                    <div class="col mb-5px">
                                                        <label class="sk-megabox pl-0 m-0 d-block" data-toggle="tooltip" data-title="{{ getColorName($color) }}">
                                                            <input
                                                                    type="radio"
                                                                    name="color"
                                                                    value="{{ $color }}"
                                                                    @if($key == 0) checked @endif
                                                            >
                                                            <span class="sk-megabox-elem color-megabox-elem" style="{{ \App\Models\Color::getColorOrImage($color, true) }}"></span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if ($detailedProduct->choice_options != null)
                                        @foreach (json_decode($detailedProduct->choice_options) as $key => $choice)
                                            <div class="publish-product-section mb-10px mb-md-15px">
                                                <h4 class="fs-12 fw-500 text-default-50 l-space-1-2 lh-1 mb-5px">{{ \App\Attribute::find($choice->attribute_id)->getTranslation('name') }}</h4>
                                                <div class="row gutters-3 publish-product-section-size-section">
                                                    @php
                                                        $count = 0;
                                                    @endphp
                                                    @foreach (sortSizes($choice->values) as $key => $value)
                                                        <div class="col-auto mb-5px">
                                                            <label class="sk-megabox pl-0 m-0 d-block">
                                                                <input
                                                                        type="radio"
                                                                        name="attribute_id_{{ $choice->attribute_id }}"
                                                                        value="{{ $value['btms_id'] }}"
                                                                        @if($count == 0) checked @endif
                                                                >
                                                                <span class="sk-megabox-elem dark-megabox-elem megabox-size-element d-block">
                                                                    <span class="d-flex align-items-center justify-content-center p-1">
                                                                        {{ $value['name'] }}
                                                                    </span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        @php
                                                            $count++;
                                                        @endphp
                                                    @endforeach
                                                </div>
                                            </div>
                                    @endforeach
                                @endif

                                <!-- Quantity + Add to cart -->
                                    <div class="publish-product-section mb-10px mb-md-20px">
                                        <div class="row align-items-end gutters-5">
                                            <div class="col">
                                                <h4 class="fs-12 fw-500 text-default-50 l-space-1-2 lh-1 mb-5px">{{ translate('Quantity')}}</h4>
                                                <div class="product-quantity d-flex align-items-center">
                                                    <div class="mr-4">
                                                        <div class="row no-gutters align-items-center sk-plus-minus border border-default w-100px w-sm-140px fs-20 fw-500">
                                                            <button class="btn col-auto fs-20 fw-500 lh-1 p-2" type="button" data-type="minus" data-field="quantity" disabled="">
                                                                -
                                                            </button>
                                                            <input type="text" name="quantity" class="col border-0 text-center flex-grow-1 input-number" placeholder="1" value="{{ $detailedProduct->min_qty }}" min="{{ $detailedProduct->min_qty }}" max="10" readonly>
                                                            <button class="btn col-auto fs-20 fw-500 lh-1 p-2" type="button" data-type="plus" data-field="quantity">
                                                                +
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="out-of-stock-label text-primary fw-500 fs-14" style="display: none">
                                                        {{toUpper(translate('Out of Stock'))}}
                                                        <img class="size-15px ml-2" src="{{static_asset('assets/img/icons/warning-icon.svg')}}" alt="">
                                                    </div>
                                                        <?php /*
                                                <div class="avialable-amount opacity-60">
                                                    @if($detailedProduct->stock_visibility_state != 'hide')
                                                        (<span id="available-quantity">{{ $qty }}</span> {{ translate('available')}})
                                                    @endif
                                                </div>*/ ?>
                                                </div>
                                            </div>
                                            @if($detailedProduct->cyprus_shipping_only)
                                                <div class="col-auto">
                                                    <div class="cyprus-shipping-label fs-12 sm-fs-14">
                                                        <img src="{{static_asset('assets/img/icons/red-location.svg')}}" alt=""> {{translate('Delivery in Cyprus ONLY')}}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            @else
                                <div class="publish-not-for-sale-desc mt-40px text-default-50 l-space-1-2 fs-11 lg-fs-15 xxl-fs-18">
                                    <div class="c-scrollbar h-lg-355px h-xxl-445px">
                                        <div class="pb-lg-90px fw-500">
                                            @if($detailedProduct->getTranslation('description'))
                                            <?php echo $detailedProduct->getTranslation('description'); ?>
                                            @else
                                                <p>{{translate('There is no description of this product.')}}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{route('contact')}}" class="btn btn-outline-default-50 btn-b-width-2 btn-block fs-16 lg-fs-18 py-15px publish-not-for-sale-contact mt-20px mt-lg-0">
                                        {{toUpper(translate('Contact us for more'))}}
                                    </a>
                                </div>
                            @endif
                        </div>
                        @if($detailedProduct->category->for_sale == 1)
                            <div class="publish-right-bottom">
                                <div class="border-top border-default-200 pt-5px fs-16 sm-fs-18">
                                    <div class="row justify-content-between">
                                        <div class="col-auto">
                                            <div class="d-none" id="chosen_price_div">
                                                <h4 class="fs-10 sm-fs-12 fw-500 text-default-50 l-space-1-2 lh-1 mb-5px">{{ translate('Subtotal')}}</h4>
                                                <div id="chosen_price" class="font-play"></div>
                                            </div>
                                        </div>
                                        <div class="col-auto text-right text-default-50 fs-10 sm-fs-12 fw-500">
                                            {{includeVatText()}}
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-10px">
                                    @if ($qty > 0)
                                        <div class="card-actions" style="display: none">
                                            <div class="row gutters-3 sm-gutters-5 xl-gutters-10">
                                                <div class="col-12 col-sm col-lg-auto col-xl">
                                                    <button type="button" class="btn btn-primary btn-block fs-18 lg-fs-21 xxl-fs-25 l-space-1-2 p-5px py-lg-20px px-lg-10px px-xl-30px add-to-cart" onclick="addToCart()">
                                                        <div class="row no-gutters sm-gutters-15 align-items-center">
                                                            <div class="col-auto">
                                                                <img class="h-30px h-lg-35px" src="{{static_asset('assets/img/icons/header-cart.svg')}}" alt="">
                                                            </div>
                                                            <div class="col">
                                                                {{toUpper(translate('Add to cart'))}}
                                                            </div>
                                                        </div>
                                                    </button>
                                                </div>
                                                <div class="col-12 col-sm-auto col-lg col-xl-auto mt-10px mt-sm-0">
                                                    <button type="button" class="btn btn-outline-primary btn-b-width-2 btn-block fs-18 lg-fs-21 xxl-fs-25 l-space-1-2 px-10px py-5px py-lg-20px lh-1-6 xl-lh-1-5 buy-now" onclick="buyNow()">
                                                        {{toUpper(translate('Buy Now'))}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-b-width-2 btn-block fs-18 lg-fs-21 xxl-fs-25 l-space-1-2 py-5px py-lg-20px lh-1-6 xl-lh-1-5 out-of-stock side-popup-toggle" data-rel="product-request-side-popup" style="display: none">{{toUpper(translate('Request this Item'))}}</button>
                                    @else
                                        <button type="button" class="btn btn-outline-primary btn-b-width-2 btn-block fs-18 lg-fs-21 xxl-fs-25 l-space-1-2 py-5px py-lg-20px lh-1-6 xl-lh-1-5 out-of-stock side-popup-toggle" data-rel="product-request-side-popup">{{toUpper(translate('Request this Item'))}}</button>
                                    @endif
                                </div>
                              <?php /*
                            <div class="d-table width-100 mt-3">
                                <div class="d-table-cell">
                                    <!-- Add to wishlist button -->
                                    <button type="button" class="btn pl-0 btn-link fw-600" onclick="addToWishList({{ $detailedProduct->id }})">
                                        {{ translate('Add to wishlist')}}
                                    </button>
                                    <!-- Add to compare button -->
                                    <button type="button" class="btn btn-link btn-icon-left fw-600" onclick="addToCompare({{ $detailedProduct->id }})">
                                        {{ translate('Add to compare')}}
                                    </button>
                                    @if(Auth::check() && \App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated && (\App\AffiliateOption::where('type', 'product_sharing')->first()->status || \App\AffiliateOption::where('type', 'category_wise_affiliate')->first()->status) && Auth::user()->affiliate_user != null && Auth::user()->affiliate_user->status)
                                        @php
                                            if(Auth::check()){
                                                if(Auth::user()->referral_code == null){
                                                    Auth::user()->referral_code = substr(Auth::user()->id.Str::random(10), 0, 10);
                                                    Auth::user()->save();
                                                }
                                                $referral_code = Auth::user()->referral_code;
                                                $referral_code_url = URL::to('/product').'/'.$detailedProduct->slug."?product_referral_code=$referral_code";
                                            }
                                        @endphp
                                        <div>
                                            <button type=button id="ref-cpurl-btn" class="btn btn-sm btn-secondary" data-attrcpy="{{ translate('Copied')}}" onclick="CopyToClipboard(this)" data-url="{{$referral_code_url}}">{{ translate('Copy the Promote Link')}}</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @php
                                $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
                                $refund_sticker = \App\BusinessSetting::where('type', 'refund_sticker')->first();
                            @endphp
                            @if ($refund_request_addon != null && $refund_request_addon->activated == 1 && $detailedProduct->refundable)
                                <div class="row no-gutters mt-4">
                                    <div class="col-sm-2">
                                        <div class="opacity-50 my-2">{{ translate('Refund')}}:</div>
                                    </div>
                                    <div class="col-sm-10">
                                        <a href="{{ route('returnpolicy') }}" target="_blank">
                                            @if ($refund_sticker != null && $refund_sticker->value != null)
                                                <img src="{{ uploaded_asset($refund_sticker->value) }}" height="36">
                                            @else
                                                <img src="{{ static_asset('assets/img/refund-sticker.jpg') }}" height="36">
                                            @endif
                                        </a>
                                        <a href="{{ route('returnpolicy') }}" class="ml-2" target="_blank">{{ translate('View Policy') }}</a>
                                    </div>
                                </div>
                            @endif
                            <div class="row no-gutters mt-4">
                                <div class="col-sm-2">
                                    <div class="opacity-50 my-2">{{ translate('Share')}}:</div>
                                </div>
                                <div class="col-sm-10">
                                    <div class="sk-share"></div>
                                </div>
                            </div>*/ ?>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($detailedProduct->category->for_sale == 1)
        <div class="bg-black-10 py-20px py-lg-25px mt-50px overflow-hidden">
            <div class="container">
                <div class="mw-1350px mx-auto">
                    <div class="nav border-bottom border-black-100 border-width-2 sk-nav-tabs fs-13 sm-fs-16 lg-fs-19 xxl-fs-22 font-play">
                        @if($spare_parts)
                            <a href="#tab_default_parts" data-toggle="tab" class="pb-10px pb-lg-15px active show mr-2 mr-lg-3 mr-xxl-5">
                                {{toUpper(translate('Yamaha Spare Parts'))}}
                            </a>
                        @endif
                        <a href="#tab_default_1" data-toggle="tab" class="pb-10px pb-lg-15px @if(!$spare_parts) active show @endif ">
                            {{toUpper(translate('Product Description'))}}
                        </a>
                    </div>
                    <div class="tab-content pt-20px pt-lg-35px fw-500 l-space-1-2 fs-11 lg-fs-15 xxl-fs-18 text-default-50">
                        @if($spare_parts)
                            <div class="tab-pane fade active show" id="tab_default_parts">
                                <div class="mw-100 overflow-hidden text-left fw-700 text-default">
                                    <p>{{translate("These are new original spare parts of the brand Yamaha. In case you can't find your desired Yamaha spare part here, please make use of our inquiry form for original spare parts.")}}</p>
                                    <a href="javascript:void(0);" class="btn btn-outline-primary side-popup-toggle spare-parts-form-link fs-18 fw-400 btn-b-width-2 py-10px py-lg-13px mt-10px mb-40px" data-rel="spare-parts-side-popup">
                                        {{toUpper(translate('Inquiry Form'))}}
                                    </a>
                                </div>
                            </div>
                        @endif
                        <div class="tab-pane fade @if(!$spare_parts) active show @endif " id="tab_default_1">
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
    @endif
    @php
        $related_products = filter_products(\App\Product::where('category_id', $detailedProduct->category_id)->where('id', '!=', $detailedProduct->id))->limit(10)->get();
    @endphp
    @if(count($related_products) > 0)
        <div class="mt-45px mt-lg-85px mt-xxl-175px overflow-hidden">
            <div class="container">
                <div class="mw-1350px mx-auto publish-container-target">
                    <h3 class="fs-13 md-fs-18 xxl-fs-23 fw-50 l-space-1-2 text-default-50 lh-1 m-0">{{translate('You may also like these')}}</h3>
                    <h2 class="fs-40 md-fs-55 xxl-fs-70 fw-700 mb-25px mb-md-50px mb-xxl-75px font-play lh-1">{{toUpper(translate('Similar Products'))}}</h2>
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
    @include('frontend.partials.feature_brands')
    @if($detailedProduct->category->for_sale == 1)
        <div id="product-request-side-popup" class="side-popup">
            <div class="side-popup-box">
                <div class="side-popup-close">
                    <div class="side-popup-close-icon"></div>
                    <div class="side-popup-close-text-wrap">
                        <div class="side-popup-close-text">{{toUpper(translate('Close'))}}</div>
                    </div>
                </div>
                <div class="side-popup-container">
                    <div class="side-popup-scroll c-scrollbar">
                        <div class="px-20px px-sm-25px py-20px py-sm-40px fs-13 sm-fs-16 fw-500">
                            <form id="product_request_form" method="POST" action="{{ route('product.request') }}">
                                @csrf
                                <input type="hidden" name="product" value="{{$detailedProduct->id}}">
                                <div class="l-space-1-2 mb-20px text-default-50 fs-11 sm-fs-16">
                                    <h2 class="fs-32 sm-fs-45 font-play mb-10px mb-sm-20px fw-700 lh-1 text-secondary">
                                        {{translate('Request this Item')}}
                                    </h2>
                                    <p>{{translate('Request for the items you love. We will get back to you in a few hours, to inform you for the stock and the delivery info.')}}</p>
                                </div>
                                <div class="border-top border-bottom border-default-200 py-10px">
                                    <div class="row gutters-5 align-items-center">
                                        <div class="col-auto">
                                            <img
                                                    class="size-40px size-sm-65px img-contain lazyload"
                                                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                    data-src="{{ uploaded_asset($detailedProduct->thumbnail_img) }}"
                                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                            >
                                        </div>
                                        <div class="col font-play">
                                            <h3 class="fs-11 sm-fs-16 fw-700 l-space-1-2 text-default-50 mb-5px">{{ $detailedProduct->getTranslation('name') }}</h3>
                                            <p id="side_chosen_price" class="fs-10 sm-fs-14 fw-700"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-bottom border-default-200 py-10px mb-20px mb-sm-25px">
                                    <div class="row gutters-5">
                                        @if (count(json_decode($detailedProduct->colors)) > 0)
                                            <div class="col-4">
                                                <h4 class="fs-12 fw-500 text-default-50 l-space-1-2 lh-1 mb-10px">{{ translate('Color')}}</h4>
                                                <div class="w-25px">
                                                    <label class="sk-megabox pl-0 m-0 d-block">
                                                        <input type="radio" name="color" value="" checked>
                                                        <span class="sk-megabox-elem color-megabox-elem" style="background: {{ $color }};"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($detailedProduct->choice_options != null)
                                            @foreach (json_decode($detailedProduct->choice_options) as $key => $choice)
                                                <div class="col-4">
                                                    <h4 class="fs-12 fw-500 text-default-50 l-space-1-2 lh-1 mb-10px">{{ \App\Attribute::find($choice->attribute_id)->getTranslation('name') }}</h4>
                                                    <div class="w-25px fs-12">
                                                        <label class="sk-megabox pl-0 m-0 d-block">
                                                            <input type="radio" name="attribute_id_{{ $choice->attribute_id }}" value="" checked>
                                                            <span class="sk-megabox-elem dark-megabox-elem d-flex align-items-center justify-content-center p-5px lh-1"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                        <div class="col-4">
                                            <h4 class="fs-12 fw-500 text-default-50 l-space-1-2 lh-1 mb-10px">{{ translate('Quantity')}}</h4>
                                            <input type="hidden" value="" name="quantity">
                                            <p id="side_chosen_quantity"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-10px mb-sm-15px">
                                    <div class="form-control-with-label small-focus small-field @if(old('name')) focused @endif ">
                                        <label>{{ translate('Full Name') }}</label>
                                        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}">
                                    </div>
                                    @if ($errors->has('name'))
                                        <div class="invalid-feedback fs-10 d-block" role="alert">
                                            {{ $errors->first('name') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group mb-10px mb-sm-15px">
                                    <div class="form-control-with-label small-focus small-field @if(old('email')) focused @endif ">
                                        <label>{{ translate('Email') }}</label>
                                        <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}">
                                    </div>
                                    @if ($errors->has('email'))
                                        <div class="invalid-feedback fs-10 d-block" role="alert">
                                            {{ $errors->first('email') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group mb-10px mb-sm-15px">
                                    <div class="form-control-with-label small-focus small-field always-focused">
                                        <label>{{ translate('Country') }}</label>
                                        <select class="form-control sk-selectpicker {{ $errors->has('country') ? ' is-invalid' : '' }}" data-live-search="true" name="country">
                                            @foreach (\App\Country::getActiveCountriesForShipping() as $key => $country)
                                                <option value="{{ $country->id }}" @if(old('country') == $country->id | (!old('country') && $country->id == 54)) selected @endif >{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if ($errors->has('country'))
                                        <div class="invalid-feedback fs-10 d-block" role="alert">
                                            {{ $errors->first('country') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group mb-10px mb-sm-15px">
                                    <div class="form-control-with-label small-focus small-field textarea-label @if(old('comments')) focused @endif ">
                                        <label>{{ translate('Comments') }}</label>
                                        <textarea name="comments" rows="3" class="form-control resize-off {{ $errors->has('comments') ? ' is-invalid' : '' }}">{{ old('comments') }}</textarea>
                                    </div>
                                    @if ($errors->has('comments'))
                                        <div class="invalid-feedback fs-10 d-block" role="alert">
                                            {{ $errors->first('comments') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-25px mb-sm-40px text-right text-black-50 position-relative">
                                    <label class="sk-checkbox fs-11 sm-fs-14 mb-5px">
                                        <input type="checkbox" name="agree_policies">
                                        <span class="sk-square-check"></span>
                                        {{ translate('I agree with the')}}
                                        <a class="text-reset hov-text-primary" href="{{ route('custom-pages.show_custom_page', 'terms-policies' ) }}" target="_blank">{{ translate('Terms&Policies')}}</a>
                                    </label>
                                    <div id="product-request-form-error-agree" class="invalid-feedback absolute fs-10 d-block mt-0" role="alert"></div>
                                </div>
                                @if(\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
                                    <div id="recaptcha" class="g-recaptcha" data-sitekey="{{ getRecaptchaKeys()->public }}" data-callback="onSubmit" data-size="invisible"></div>
                                    <div id="product-request-form-error-recaptcha" class="invalid-feedback fs-10 text-right" role="alert"></div>
                                @endif
                                <button id="product-request-form-btn" class="btn btn-outline-primary btn-block fs-16 fw-500 py-10px">
                                    {{ toUpper(translate('Request this Item')) }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($spare_parts)
            <div id="spare-parts-side-popup" class="side-popup">
                <div class="side-popup-box">
                    <div class="side-popup-close">
                        <div class="side-popup-close-icon"></div>
                        <div class="side-popup-close-text-wrap">
                            <div class="side-popup-close-text">{{toUpper(translate('Close'))}}</div>
                        </div>
                    </div>
                    <div class="side-popup-container">
                        <div class="side-popup-scroll c-scrollbar">
                            <div class="px-20px px-sm-25px py-20px py-sm-40px fs-13 sm-fs-16 fw-500">
                                <form id="spare_parts_form" method="POST" action="{{ route('product.spare_parts') }}">
                                    @csrf
                                    <div class="l-space-1-2 mb-20px text-default-50 fs-11 sm-fs-16">
                                        <h2 class="fs-32 sm-fs-45 font-play mb-10px mb-sm-20px fw-700 lh-1 text-secondary">
                                            {{translate('Request spare parts')}}
                                        </h2>
                                        <p>{{translate('With this inquiry form for original parts we would like to help you to get what you need for your machine.')}}</p>
                                    </div>
                                    <h2 class="mb-5px lh-1 py-10px fs-17 sm-fs-22 fw-400 font-play border-top border-default-200">{{toUpper(translate('Bike Info'))}}</h2>
                                    <div class="form-group mb-15px">
                                        <div class="form-control-with-label small-focus small-field always-focused">
                                            <label>{{translate('Brand')}}</label>
                                            <div class="form-control">{{\App\Brand::where('id', env('YAMAHA_BRAND'))->first()->getTranslation('name')}}</div>
                                        </div>
                                        <input type="hidden" value="{{env('YAMAHA_BRAND')}}" name="brand">
                                        @if ($errors->has('brand'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('brand') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group mb-15px">
                                        <div class="form-control-with-label small-focus small-field @if(old('model_code')) focused @endif ">
                                            <label>{{translate('Model / Code')}}</label>
                                            <input type="text" class="form-control {{ $errors->has('model_code') ? ' is-invalid' : '' }}" name="model_code" value="{{ old('model_code') }}">
                                        </div>
                                        @if ($errors->has('model_code'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('model_code') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group mb-15px">
                                        <div class="form-control-with-label small-focus small-field @if(old('model_year')) focused @endif ">
                                            <label>{{translate('Model / Year')}}</label>
                                            <input type="text" class="form-control {{ $errors->has('model_year') ? ' is-invalid' : '' }}" name="model_year" value="{{ old('model_year') }}">
                                        </div>
                                        @if ($errors->has('model_year'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('model_year') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group mb-15px">
                                        <div class="form-control-with-label small-focus small-field @if(old('chassis_no')) focused @endif ">
                                            <label>{{translate('Chassis No.')}}</label>
                                            <input type="text" class="form-control {{ $errors->has('chassis_no') ? ' is-invalid' : '' }}" name="chassis_no" value="{{ old('chassis_no') }}">
                                        </div>
                                        @if ($errors->has('chassis_no'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('chassis_no') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group mb-15px">
                                        <div class="form-control-with-label small-focus small-field @if(old('color_code')) focused @endif ">
                                            <label>{{translate('Color Code')}}</label>
                                            <input type="text" class="form-control {{ $errors->has('color_code') ? ' is-invalid' : '' }}" name="color_code" value="{{ old('color_code') }}">
                                        </div>
                                        @if ($errors->has('color_code'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('color_code') }}
                                            </div>
                                        @endif
                                    </div>
                                    <h2 class="mb-5px lh-1 py-10px fs-17 sm-fs-22 fw-400 font-play border-top border-default-200">{{toUpper(translate('Personal Info'))}}</h2>
                                    <div class="form-group mb-15px">
                                        <div class="form-control-with-label small-focus small-field @if(old('part_name')) focused @endif ">
                                            <label>{{translate('Full Name')}}</label>
                                            <input type="text" class="form-control {{ $errors->has('part_name') ? ' is-invalid' : '' }}" name="part_name" value="{{ old('part_name') }}">
                                        </div>
                                        @if ($errors->has('part_name'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('part_name') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group mb-15px">
                                        <div class="form-control-with-label small-focus small-field @if(old('part_email')) focused @endif ">
                                            <label>{{translate('Email')}}</label>
                                            <input type="text" class="form-control {{ $errors->has('part_email') ? ' is-invalid' : '' }}" name="part_email" value="{{ old('part_email') }}">
                                        </div>
                                        @if ($errors->has('part_email'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('part_email') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group mb-15px">
                                        <div class="form-control-with-label small-focus small-field always-focused">
                                            <label>{{translate('Country')}}</label>
                                            <select class="form-control sk-selectpicker {{ $errors->has('part_country') ? ' is-invalid' : '' }}" data-live-search="true" name="part_country">
                                                @foreach (\App\Country::getActiveCountriesForShipping() as $key => $country)
                                                    <option value="{{ $country->id }}" @if(old('part_country') == $country->id || (!old('part_country') && $country->id == 54)) selected @endif >{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if ($errors->has('part_country'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('part_country') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group mb-5px">
                                        <div class="form-control-with-label small-focus small-field textarea-label @if(old('part_comments')) focused @endif ">
                                            <label>{{translate('Comments')}}</label>
                                            <textarea name="part_comments" rows="3" class="form-control resize-off {{ $errors->has('part_comments') ? ' is-invalid' : '' }}">{{ old('part_comments') }}</textarea>
                                        </div>
                                        @if ($errors->has('part_comments'))
                                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                                {{ $errors->first('part_comments') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-25px mb-sm-40px text-right text-black-50 position-relative">
                                        <label class="sk-checkbox fs-11 sm-fs-14 mb-5px">
                                            <input type="checkbox" name="agree_policies_parts">
                                            <span class="sk-square-check"></span>
                                            {{ translate('I agree with the')}}
                                            <a class="text-reset hov-text-primary" href="{{ route('custom-pages.show_custom_page', 'terms-policies' ) }}" target="_blank">{{ translate('Terms&Policies')}}</a>
                                        </label>
                                        <div id="spare-parts-form-error-agree" class="invalid-feedback absolute fs-10 d-block mt-0" role="alert"></div>
                                    </div>
                                    @if(\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
                                        <div id="recaptcha-parts" class="g-recaptcha" data-sitekey="{{ getRecaptchaKeys()->public }}" data-callback="onSubmitPart" data-size="invisible"></div>
                                        <div id="spare-parts-form-error-recaptcha" class="invalid-feedback fs-10 text-right" role="alert"></div>
                                    @endif
                                    <button id="spare-parts-form-btn" class="btn btn-outline-primary btn-block fs-16 fw-500 py-10px">
                                        {{ toUpper(translate('Request this Item')) }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
@endsection

@section('modal')
    <div class="modal fade" id="out_of_stock_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-lg" role="document">
            <div class="modal-content position-relative with-side-close">
                <div class="modal-body pt-20px pt-lg-40px px-20px px-lg-40px pb-40px pb-lg-70px fs-13 lg-fs-16 fw-500 l-space-1-2 text-default-50">
                    <h2 class="text-secondary fs-32 lg-fs-45 fw-700 font-play mb-20px mb-lg-55px">{{translate('Order item explanation')}}</h2>
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
    @if(\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
        <script src="https://www.google.com/recaptcha/api.js?onload=CaptchaCallback&render=explicit" async defer></script>
        <script type="text/javascript">
          function CaptchaCallback() {
            $('.g-recaptcha').each(function(index, el) {
              grecaptcha.render(el, {
                'sitekey' : $(el).attr('data-sitekey'),
                'size' : $(el).attr('data-size'),
                'bind' : $(el).attr('data-bind'),
                'callback' : $(el).attr('data-callback')
              });
            });
          }
        </script>
    @endif
    <script type="text/javascript">
        @if($detailedProduct->category->for_sale == 1)
          // making the CAPTCHA  a required field for form submission
          $(document).ready(function(){
              @if(\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
              function resetRecaptchas(){
                var count = 0;
                $('.g-recaptcha').each(function () {
                  grecaptcha.reset(count);
                });
                count++;
              }
              function onSubmit(token) {
                if (token.length > 0) {
                  $('#product_request_form').submit();
                }
              }
              function onSubmitPart(token) {
                if (token.length > 0) {
                  $('#spare_parts_form').submit();
                }
              }


                window.CaptchaCallback = CaptchaCallback;
                window.onSubmit = onSubmit;
                window.onSubmitPart = onSubmitPart;


            $('#product-request-form-btn').on("click", function(evt){
              evt.preventDefault();
              if($('input[name="agree_policies"]').prop('checked')==false) {
                $('#product-request-form-error-agree').addClass('d-block').text('{{translate('You need to agree with our policies')}}');
                return false;
              } else {
                $('#product-request-form-btn').addClass('loader');
                grecaptcha.execute(0);
                return true;
              }
            });
            $('#spare-parts-form-btn').on("click", function(evt){
              evt.preventDefault();
              if($('input[name="agree_policies_parts"]').prop('checked')==false) {
                $('#spare-parts-form-error-agree').addClass('d-block').text('{{translate('You need to agree with our policies')}}');
                return false;
              } else {
                $('#spare-parts-form-btn').addClass('loader');
                grecaptcha.execute(1);
                return true;
              }
            });
              @else
              $("#product_request_form").on("submit", function(){
                if($('input[name="agree_policies"]').prop('checked')==false){
                  $('#product-request-form-error-agree').addClass('d-block').text('{{translate("You need to agree with our policies")}}');
                  return false;
                } else {
                  $('#product-request-form-btn').addClass('loader');
                }
              });
            $("#spare_parts_form").on("submit", function(){
              if($('input[name="agree_policies_parts"]').prop('checked')==false){
                $('#spare-parts-form-error-agree').addClass('d-block').text('{{translate("You need to agree with our policies")}}');
                return false;
              } else {
                $('#spare-parts-form-btn').addClass('loader');
              }
            });
              @endif
          });
        @endif
      @if($detailedProduct->category->for_sale == 1)
          $(document).ready(function() {
            getVariantPrice();
          });
      @endif

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
    @if($detailedProduct->category->for_sale == 1)
          @if(old('opened_request') == true)
              $('.out-of-stock').trigger('click');
              @if(old('color'))
                $('#option-choice-form input[name="color"][value="{{old('color')}}"]').prop("checked", true);
              @endif
              @if(old('attributes'))
                  @foreach(old('attributes') as $key => $attr)
                    $('#option-choice-form input[name="{{ $key }}"][value="{{$attr}}"]').prop("checked", true);
                  @endforeach
              @endif
              @if(old('quantity'))
                $('#option-choice-form input[name="quantity"]').val({{old('quantity')}});
              @endif
          @endif
          @if(old('opened_spare_parts') == true)
              $('.spare-parts-form-link').trigger('click');
          @endif
          $(document).on('click', '.out-of-stock-label', function () {
            $('#out_of_stock_modal').modal('show');
          });
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
            loop: numberOfSlides > 1 ? true : false,
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
