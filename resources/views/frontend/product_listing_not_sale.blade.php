@extends('frontend.layouts.app')

@if (isset($category_id))
    @php
        $meta_title = \App\Category::find($category_id)->meta_title;
        $meta_description = \App\Category::find($category_id)->meta_description;
    @endphp
@elseif (isset($brand_id))
    @php
        $meta_title = \App\Brand::find($brand_id)->meta_title;
        $meta_description = \App\Brand::find($brand_id)->meta_description;
    @endphp
@else
    @php
        $meta_title         = get_setting('meta_title');
        $meta_description   = get_setting('meta_description');
    @endphp
@endif

@section('meta_title'){{ $meta_title }}@stop
@section('meta_description'){{ $meta_description }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $meta_title }}">
    <meta itemprop="description" content="{{ $meta_description }}">

    <!-- Twitter Card data -->
    <meta name="twitter:title" content="{{ $meta_title }}">
    <meta name="twitter:description" content="{{ $meta_description }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $meta_title }}" />
    <meta property="og:description" content="{{ $meta_description }}" />
@endsection

@section('content')
    @php
        $category = \App\Category::find($category_id);
        $main_category = ($category->parent_id > 0) ? \App\Category::find(getMainCategory($category->id)->id) : null;
    @endphp
    <div class="line-slider-item overflow-hidden">
        <div class="line-slider-image line-slider-small">
            <img
                    class="absolute-full h-100 img-fit"
                    src="{{ uploaded_asset($category->header) }}"
                    alt=""
                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
            >
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
                                            @if($main_category)
                                                <li class="breadcrumb-item">
                                                    <a class="hov-text-primary" href="{{ route('brand.maincategory', ['category_slug'=>$main_category->slug,'brand_slug'=>$brand->slug]) }}">{{ $main_category->getTranslation('name') }}</a>
                                                </li>
                                            @endif
                                            <li class="breadcrumb-item">
                                                <a class="hov-text-primary" href="{{ route('products.brand_category', ['category_slug'=>$category->slug,'brand_slug'=>$brand->slug]) }}">{{ $category->getTranslation('name') }}</a>
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
                                            @if($main_category)
                                                <li class="breadcrumb-item">
                                                    <a class="hov-text-primary" href="{{ route('typecat.maincategory', ['category_slug'=>$main_category->slug,'type_slug'=>$type->slug]) }}">{{ $main_category->getTranslation('name') }}</a>
                                                </li>
                                            @endif
                                            <li class="breadcrumb-item">
                                                <a class="hov-text-primary" href="{{ route('products.type_category', ['category_slug'=>$category->slug,'type_slug'=>$type->slug]) }}">{{ $category->getTranslation('name') }}</a>
                                            </li>
                                        </ul>
                                    @elseif($main_category)
                                        <ul class="breadcrumb fs-10 md-fs-12">
                                            <li class="breadcrumb-item">
                                                <a class="hov-text-primary" href="{{route('maincategory', $main_category->slug)}}">{{ $main_category->getTranslation('name') }}</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a class="hov-text-primary" href="{{ route('products.category', $category->slug) }}">{{ $category->getTranslation('name') }}</a>
                                            </li>
                                        </ul>
                                    @endif
                                </div>
                            </div>
                            <div class="line-slider-over-box slogan d-lg-flex justify-content-center">
                                <div class="line-slider-over-box-inner p-0">
                                    @if($main_category)
                                        <h2 class="mb-0 fs-11 md-fs-14 xxl-fs-16 fw-400">
                                            @if(isset($brand_id))
                                                <a class="hov-text-primary" href="{{ route('brand.maincategory', ['category_slug'=>$main_category->slug,'brand_slug'=>$brand->slug]) }}">{{ $main_category->getTranslation('name') }}</a>
                                            @elseif(isset($type_id))
                                                <a class="hov-text-primary" href="{{ route('typecat.maincategory', ['category_slug'=>$main_category->slug,'type_slug'=>$type->slug]) }}">{{ $main_category->getTranslation('name') }}</a>
                                            @elseif($main_category)
                                                <a class="hov-text-primary" href="{{route('maincategory', $main_category->slug)}}">{{ $main_category->getTranslation('name') }}</a>
                                            @endif
                                        </h2>
                                    @endif
                                    <h1 class="font-play fs-25 md-fs-30 xl-fs-45 xxl-fs-60 fw-700 m-0">{{ $category->getTranslation('name') }}</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-30px mt-lg-55px mt-xxl-80px mb-100px mb-lg-125px mb-xxl-175px overflow-hidden">
        <div class="container">
            <div class="row">
                @php
                    $counter = 1;
                    $tablet_counter = 1;
                @endphp
                @foreach ($products as $key => $product)
                    <div class="col-6 col-lg-4 notsale-product-res-item">
                        @include('frontend.partials.product_listing.notforsale_listing',['product' => $product, 'type_id' => $type_id , 'brand_id' => $brand_id])
                    </div>
                    @if($counter == 3)
                        <div class="col-12 mb-15px mb-lg-20px d-none d-lg-block">
                            <div class="border-bottom border-black-100 border-width-2"></div>
                        </div>
                    @endif
                    @if($tablet_counter == 2)
                        <div class="col-12 mb-15px mb-lg-20px d-lg-none">
                            <div class="border-bottom border-black-100 border-width-2"></div>
                        </div>
                    @endif
                    @php
                        $counter++;
                        if($counter == 4){
                            $counter = 1;
                        }

                        $tablet_counter++;
                        if($tablet_counter == 3){
                            $tablet_counter = 1;
                        }
                    @endphp
                @endforeach
                @if($counter != 1)
                    <div class="col-12 mb-15px mb-lg-20px d-none d-lg-block">
                        <div class="border-bottom border-black-100 border-width-2"></div>
                    </div>
                @endif
                @if($tablet_counter != 1)
                    <div class="col-12 mb-15px mb-lg-20px d-lg-none">
                        <div class="border-bottom border-black-100 border-width-2"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
      function filter(){
        $('#search-form').submit();
      }
      function rangefilter(arg){
        $('input[name=min_price]').val(arg[0]);
        $('input[name=max_price]').val(arg[1]);
        filter();
      }
    </script>
@endsection