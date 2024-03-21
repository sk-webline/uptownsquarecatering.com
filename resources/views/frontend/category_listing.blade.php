@extends('frontend.layouts.app')

@if($category != null)
    @section('meta_title'){{ $category->meta_title }}@stop
    @section('meta_description'){{ $category->meta_description }}@stop
@elseif($type != null)
    @section('meta_title'){{ $type->meta_title }}@stop
    @section('meta_description'){{ $type->meta_description }}@stop
@endif

@section('content')
    @if($category != null)
        <div class="line-slider-item overflow-hidden">
            <div class="line-slider-image">
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
                                    @if(isset($brand_id))
                                        @php
                                            $brand = \App\Brand::find($brand_id);
                                        @endphp
                                        <div class="line-slider-over-box-inner p-xxl-20px">
                                            <ul class="breadcrumb fs-10 md-fs-12">
                                                <li class="breadcrumb-item">
                                                    <a class="hov-text-primary" href="{{route('brand_page', $brand->slug)}}">{{ $brand->getTranslation('name') }}</a>
                                                </li>
                                                <li class="breadcrumb-item">
                                                    <a class="hov-text-primary" href="{{ route('brand.maincategory', ['category_slug'=>$category->slug,'brand_slug'=>$brand->slug]) }}">{{ $category->getTranslation('name') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    @elseif(isset($type_id))
                                        <div class="line-slider-over-box-inner p-xxl-20px">
                                            <ul class="breadcrumb fs-10 md-fs-12">
                                                <li class="breadcrumb-item">
                                                    <a class="hov-text-primary" href="{{route('type.maincategory', $type->slug)}}">{{ $type->getTranslation('name') }}</a>
                                                </li>
                                                <li class="breadcrumb-item">
                                                    <a class="hov-text-primary" href="{{ route('typecat.maincategory', ['category_slug'=>$category->slug,'type_slug'=>$type->slug]) }}">{{ $category->getTranslation('name') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                <div class="line-slider-over-box slogan d-lg-flex justify-content-center">
                                    <div class="line-slider-over-box-inner p-xxl-20px">
                                        <h1 class="font-play fs-25 md-fs-30 xl-fs-45 xxl-fs-60 fw-700 m-0">{{ $category->getTranslation('name') }}</h1>
                                    </div>
                                </div>
                                @if($category->getTranslation('short_description'))
                                    <div class="line-slider-over-box bg-white text-default-50 fs-16 xl-fs-18 xxl-fs-23 xxxl-fs-28 font-play l-space-1 px-xxl-50px d-none d-lg-flex flex-column justify-content-center lh-1-4">
                                        <div class="mw-850px">
                                            <p class="text-truncate-6">{{$category->getTranslation('short_description')}}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="line-slider-over-box d-none d-lg-block"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($category->getTranslation('short_description'))
            <div class="my-30px my-md-50px d-lg-none">
                <div class="container">
                    <div class="text-default-50 fs-16 font-play l-space-1-2 lh-1-7 mw-850px">
                        <p>{{$category->getTranslation('short_description')}}</p>
                    </div>
                </div>
            </div>
        @endif
    @elseif($type != null)
        <div class="line-slider-item overflow-hidden">
            <div class="line-slider-image">
                <img
                        class="absolute-full h-100 img-fit"
                        src="{{ uploaded_asset($type->banner) }}"
                        alt=""
                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
                >
                <div class="line-slider-over">
                    <div class="row no-gutters">
                        <div class="col-lg-4">
                            <div class="line-slider-over-left">
                                <div class="line-slider-over-box breadcrumb-line d-lg-flex justify-content-center"></div>
                                <div class="line-slider-over-box slogan d-flex justify-content-center">
                                    <div class="line-slider-over-box-inner p-xxl-20px">
                                        <h1 class="font-play fs-25 md-fs-30 xl-fs-45 xxl-fs-60 fw-700 m-0">{{ $type->getTranslation('name') }}</h1>
                                    </div>
                                </div>
                                @if($type->getTranslation('short_description'))
                                    <div class="line-slider-over-box bg-white text-default-50 fs-16 xl-fs-18 xxl-fs-23 xxxl-fs-28 font-play l-space-1 px-xxl-50px d-none d-lg-flex flex-column justify-content-center lh-1-4">
                                        <div class="mw-850px">
                                            <p class="text-truncate-6">{{$type->getTranslation('short_description')}}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="line-slider-over-box d-none d-lg-block"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($type->getTranslation('short_description'))
            <div class="my-30px my-md-50px d-lg-none">
                <div class="container">
                    <div class="text-default-50 fs-16 font-play l-space-1-2 lh-1-7 mw-850px">
                        <p>{{$type->getTranslation('short_description')}}</p>
                    </div>
                </div>
            </div>
        @endif
    @endif
    @if(count($categories) > 0)
        <div class="mt-20px mb-75px mb-100px overflow-hidden">
            <div class="row gutters-3 lg-gutters-5 xxl-gutters-10">
                @foreach($categories as $subcat)
                    @php
                        if(isset($brand_id)) {
                            $link = route('products.brand_category', ['category_slug'=>$subcat->slug,'brand_slug'=>$brand->slug]);
                        } elseif (isset($type_id) && isset($category)) {
                            $link = route('products.type_category', ['category_slug'=>$subcat->slug,'type_slug'=>$type->slug]);
                        } elseif (isset($type_id)) {
                            $link = route('typecat.maincategory', ['category_slug'=>$subcat->slug,'type_slug'=>$type->slug]);
                        } else {
                            $link = route('products.category', $subcat->slug);
                        }
                    @endphp
                    <div class="col-6 mb-5px mb-lg-10px mb-xxl-20px subcat-res-item">
                        <a href="{{ $link }}">
                            <span class="d-block subcat-res-wrap">
                                <span class="d-flex align-items-center justify-content-center flex-column p-10px p-md-20px absolute-full subcat-res-over">
                                    <span class="d-block feature-cat-res-image">
                                        <img class="img-contain h-70px h-sm-125px h-md-200px h-lg-125px h-xl-170px h-xxxl-270px lazyload"
                                             src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                             data-src="{{ uploaded_asset($subcat->icon) }}"
                                             onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                             alt="">
                                    </span>
                                    <span class="mt-5px mt-lg-10px mt-xxl-25px fs-13 lg-fs-16 xxl-fs-20 fw-500 subcat-res-title text-center text-truncate-2">{{\App\Category::find($subcat->id)->getTranslation('name')}}</span>
                                </span>
                            </span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="my-100px">
            <div class="container">
                <div class="alert alert-grey m-0 text-center fw-500">{{translate("We don't have any sub-categories under this category")}}</div>
            </div>
        </div>
    @endif
@endsection