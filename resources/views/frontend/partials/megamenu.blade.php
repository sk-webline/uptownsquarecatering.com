@php
    $mega_brands = \App\Brand::orderBy('order_level', 'desc')->limit(6)->get();
    $all_categories = getAllAvailableMainCategories();
    $webshop_categories = getWebshopMainCategories();
    $used_products = filter_products(\App\Product::where('published', 1)->where('used', '1'))->get();
    $outlet_products = getSidebarCategories(1);
@endphp
<div id="megamenu">
    <div class="megamenu-scroll c-scrollbar-light">
        <div class="pr-lg-20px megamenu-container">
            <div class="megamenu-top h-65px h-lg-130px">
                <div class="row no-gutters lg-gutters-5 align-items-lg-end justify-content-end">
                    <div class="col-auto">
                        <div class="position-relative d-flex d-lg-block">
                            <div class="header-small-btn header-bottom-btn">
                                <div class="flex-grow-1 front-header-search-megamenu d-flex align-items-center bg-white">
                                    <div class="position-relative flex-grow-1">
                                        <div class="d-flex position-relative align-items-center">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="btn header-small-btn header-search-toggle-megamenu">
                                                        <img class="size-20px size-lg-30px" src="{{static_asset('assets/img/icons/header-search.svg')}}" alt="">
                                                    </div>
                                                </div>
                                                <input type="text" class="border-0 fs-12 lg-fs-14 fw-500 lg-l-space-1 pl-5px py-5px form-control" id="search-megamenu" name="q" placeholder="{{translate('Search Part Number...')}}" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="typed-search-box-megamenu stop-propagation document-click-d-none d-none bg-white shadow-lg position-absolute left-0 top-100 w-100 c-scrollbar-light" style="min-height: 200px">
                                            <div class="search-preloader-megamenu absolute-top-center">
                                                <div class="dot-loader"><div></div><div></div><div></div></div>
                                            </div>
                                            <div class="search-nothing-megamenu d-none p-3 text-center fs-16">

                                            </div>
                                            <div id="search-content-megamenu" class="text-left">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="header-login">
                                @auth
                                    @if(isAdmin())
                                        <a href="{{ route('admin.dashboard') }}" class="header-small-btn">
                                            <img class="size-20px size-lg-30px" src="{{static_asset('assets/img/icons/header-login.svg')}}" alt="">
                                            <span class="login-dot"></span>
                                        </a>
                                    @else
                                        <a href="{{ route('dashboard') }}" class="header-small-btn">
                                            <img class="size-20px size-lg-30px" src="{{static_asset('assets/img/icons/header-login.svg')}}" alt="">
                                            <span class="login-dot"></span>
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('user.login') }}" class="header-small-btn">
                                        <img class="size-20px size-lg-30px" src="{{static_asset('assets/img/icons/header-login.svg')}}" alt="">
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="position-relative d-flex d-lg-block">
                            <div class="header-bottom-btn">
                                <div id="cart_items_megamenu">
                                    @include('frontend.partials.cart')
                                </div>
                            </div>
                            <div class="header-large-btn megamenu-toggle">
                                <div class="icon">
                                    <div class="top"></div>
                                    <div class="bottom"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="megamenu-bottom d-none d-lg-block">
                <div class="row no-gutters">
                    <div class="col-12 col-lg-25per">
                        <div class="megamenu-sidebar">
                            <div class="row no-gutters fw-500 fs-12 sm-fs-14 lg-fs-12 xxl-fs-16 xxxl-fs-18">
                                <div class="col-6">
                                    <div class="megamenu-categories-btn megamenu-type-toggle active" data-type="all">
                                        {{toUpper(translate('All'))}}
                                    </div>
                                </div>
                                @if( get_setting('show_webshop') == 'on')
                                    <div class="col-6">
                                        <div class="megamenu-categories-btn megamenu-type-toggle" data-type="webshop">
                                            {{toUpper(translate('Webshop'))}}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="megamenu-sidebar-scroll c-scrollbar-light">
                                <div class="megamenu-sidebar-cat-contents py-20px py-xl-40px fw-700 fs-12 sm-fs-14 md-fs-16 xl-fs-18 xxxl-fs-20">
                                    <div class="megamenu-sidebar-cat-content active" data-type="all">
                                        @if(count($all_categories) > 0)
                                            @foreach($all_categories as $all_category)
                                                <div class="megamenu-sidebar-cat-item" data-id="{{$all_category->id}}">
                                                    <a href="{{route('maincategory', $all_category->slug)}}">{{$all_category->getTranslation('name')}}</a>
                                                </div>
                                            @endforeach
                                        @endif
                                        @if(count($used_products) > 0)
                                            <div class="megamenu-sidebar-cat-item" data-id="used">
                                                <a href="{{route('used_page')}}">{{translate('Used')}}</a>
                                            </div>
                                        @endif
                                    </div>
                                    @if( get_setting('show_webshop') == 'on')
                                    <div class="megamenu-sidebar-cat-content" data-type="webshop">
                                        @if(count($webshop_categories) > 0)
                                            @foreach($webshop_categories as $webshop_category)
                                                <div class="megamenu-sidebar-cat-item" data-id="{{$webshop_category->id}}">
                                                    <a href="{{route('maincategory', $webshop_category->slug)}}">{{$webshop_category->getTranslation('name')}}</a>
                                                </div>
                                            @endforeach
                                        @endif
                                        @if(count($outlet_products) > 0)
                                            <div class="megamenu-sidebar-cat-item" data-id="outlet">
                                                <a href="{{route('search')}}?outlet=1">{{translate('Outlet')}}</a>
                                            </div>
                                        @endif
                                    </div>
                                    @endif
                                    @foreach($mega_brands as $mega_brand)
                                        <div class="megamenu-sidebar-cat-content" data-type="brand-{{$mega_brand->id}}">
                                            @php
                                                $brand_categories = getMainCategoriesByBrand($mega_brand->id);
                                            @endphp
                                            @if(count($brand_categories) > 0)
                                                @foreach($brand_categories as $brand_category)
                                                    <div class="megamenu-sidebar-cat-item" data-id="{{$brand_category->id}}">
                                                        <a href="{{ route('brand.maincategory', ['category_slug'=>$brand_category->slug,'brand_slug'=>$mega_brand->slug]) }}">
                                                            {{$brand_category->getTranslation('name')}}
                                                        </a>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-75per d-flex flex-column">
                        <div class="megamenu-body fs-12 sm-fs-14 lg-fs-12 xxl-fs-16 xxxl-fs-18">
                            @if(count($mega_brands) > 0)
                                <div class="row no-gutters row-cols-6 fw-800 bg-default text-white overflow-hidden">
                                    @foreach($mega_brands as $mega_brand)
                                        <div class="col">
                                            <div class="megamenu-categories-btn megamenu-brand-toggle" data-type="brand-{{$mega_brand->id}}">
                                                {{toUpper($mega_brand->getTranslation('name'))}}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="megamenu-body-container d-flex flex-column flex-grow-1">
                            <div class="flex-grow-1">
                                <div class="megamenu-body-scroll c-scrollbar-light">
                                    <div class="megamenu-body-cat-contents pl-lg-35px pt-lg-35px pl-xxl-70px pt-xxl-70px">
                                        <div class="megamenu-body-cat-content active" data-type="all">
                                            @if(count($all_categories) > 0)
                                                @foreach($all_categories as $all_category)
                                                    <div class="megamenu-body-subcat-content" data-id="{{$all_category->id}}">
                                                        @php
                                                            $sub_categories = getAvailableSubCategoriesByCategory($all_category->id);
                                                        @endphp
                                                        @if(count($sub_categories) > 0)
                                                            <div class="row row-cols-3 row-cols-sm-4 row-cols-md-5 row-cols-xl-6 row-cols-xxxl-7">
                                                                @foreach($sub_categories as $sub_category)
                                                                    <div class="col mb-20px mb-lg-35px mb-xxl-70px megamenu-sub-cat-item">
                                                                        <a href="{{route('products.category', $sub_category->slug)}}">
                                                                            <span class="d-block megamenu-sub-cat-wrap">
                                                                                <span class="d-block megamenu-sub-cat-image">
                                                                                    <img class="img-contain h-50px h-sm-80px"
                                                                                         src="{{ uploaded_asset($sub_category->icon) }}"
                                                                                         onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                                                                         alt=""
                                                                                    >
                                                                                </span>
                                                                                <span class="d-block fs-10 sm-fs-13 fw-500 mt-5px mt-sm-10px text-center megamenu-sub-cat-title">
                                                                                    <span class="d-inline-block megamenu-sub-cat-title-wrap">
                                                                                        {{$sub_category->getTranslation('name')}}
                                                                                    </span>
                                                                                </span>
                                                                            </span>
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        @if( get_setting('show_webshop') == 'on')
                                            <div class="megamenu-body-cat-content" data-type="webshop">
                                                @if(count($webshop_categories) > 0)
                                                    @foreach($webshop_categories as $webshop_category)
                                                        <div class="megamenu-body-subcat-content" data-id="{{$webshop_category->id}}">
                                                            @php
                                                                $sub_categories = getAvailableWebSubCategoriesByCategory($webshop_category->id);
                                                            @endphp
                                                            @if(count($sub_categories) > 0)
                                                                <div class="row row-cols-3 row-cols-sm-4 row-cols-md-5 row-cols-xl-6 row-cols-xxxl-7">
                                                                    @foreach($sub_categories as $sub_category)
                                                                        <div class="col mb-20px mb-lg-35px mb-xxl-70px megamenu-sub-cat-item">
                                                                            <a href="{{route('products.category', $sub_category->slug)}}">
                                                                                <span class="d-block megamenu-sub-cat-wrap">
                                                                                    <span class="d-block megamenu-sub-cat-image">
                                                                                        <img class="img-contain h-50px h-sm-80px"
                                                                                             src="{{ uploaded_asset($sub_category->icon) }}"
                                                                                             onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                                                                             alt=""
                                                                                        >
                                                                                    </span>
                                                                                    <span class="d-block fs-10 sm-fs-13 fw-500 mt-5px mt-sm-10px text-center megamenu-sub-cat-title">
                                                                                        <span class="d-inline-block megamenu-sub-cat-title-wrap">
                                                                                            {{$sub_category->getTranslation('name')}}
                                                                                        </span>
                                                                                    </span>
                                                                                </span>
                                                                            </a>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif
                                                @if(count($outlet_products) > 0)
                                                    <div class="megamenu-body-subcat-content" data-id="outlet">
                                                        <div class="row row-cols-3 row-cols-sm-4 row-cols-md-5 row-cols-xl-6 row-cols-xxxl-7">
                                                            @foreach($outlet_products as $outlet_product)
                                                                <div class="col mb-20px mb-lg-35px mb-xxl-70px megamenu-sub-cat-item">
                                                                    <a href="{{route('products.category', $outlet_product->slug)}}?outlet=1">
                                                                        <span class="d-block megamenu-sub-cat-wrap">
                                                                            <span class="d-block megamenu-sub-cat-image">
                                                                                <img class="img-contain h-50px h-sm-80px"
                                                                                     src="{{ uploaded_asset($outlet_product->icon) }}"
                                                                                     onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                                                                     alt=""
                                                                                >
                                                                            </span>
                                                                            <span class="d-block fs-10 sm-fs-13 fw-500 mt-5px mt-sm-10px text-center megamenu-sub-cat-title">
                                                                                <span class="d-inline-block megamenu-sub-cat-title-wrap">
                                                                                    {{$outlet_product->getTranslation('name')}}
                                                                                </span>
                                                                            </span>
                                                                        </span>
                                                                    </a>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        @foreach($mega_brands as $mega_brand)
                                            @php
                                                $brand_categories = getMainCategoriesByBrand($mega_brand->id);
                                            @endphp
                                            <div class="megamenu-body-cat-content" data-type="brand-{{$mega_brand->id}}">
                                                @if(count($brand_categories) > 0)
                                                    @foreach($brand_categories as $brand_category)
                                                        <div class="megamenu-body-subcat-content" data-id="{{$brand_category->id}}">
                                                            @php
                                                                $sub_categories = getSubCategoriesByBrand($brand_category->id, $mega_brand->id);
                                                            @endphp
                                                            @if(count($sub_categories) > 0)
                                                                <div class="row row-cols-3 row-cols-sm-4 row-cols-md-5 row-cols-xl-6 row-cols-xxxl-7">
                                                                    @foreach($sub_categories as $sub_category)
                                                                        <div class="col mb-20px mb-lg-35px mb-xxl-70px megamenu-sub-cat-item">
                                                                            <a href="{{route('products.brand_category', ['category_slug'=>$sub_category->slug,'brand_slug'=>$mega_brand->slug])}}">
                                                                            <span class="d-block megamenu-sub-cat-wrap">
                                                                                <span class="d-block megamenu-sub-cat-image">
                                                                                    <img class="img-contain h-50px h-sm-80px"
                                                                                         src="{{ uploaded_asset($sub_category->icon) }}"
                                                                                         onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                                                                         alt=""
                                                                                    >
                                                                                </span>
                                                                                <span class="d-block fs-10 sm-fs-13 fw-500 mt-5px mt-sm-10px text-center megamenu-sub-cat-title">
                                                                                    <span class="d-inline-block megamenu-sub-cat-title-wrap">
                                                                                        {{$sub_category->getTranslation('name')}}
                                                                                    </span>
                                                                                </span>
                                                                            </span>
                                                                            </a>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="megamenu-body-footer text-default-40 py-20px">
                                <div class="row no-gutters justify-content-end">
                                    <div class="col-auto">
                                        <div class="border-bottom border-default-200 fs-14 fw-500">
                                            <div class="row gutters-10">
                                                @foreach (json_decode( get_setting('widget_one_labels'), true) as $key => $value)
                                                    <div class="col-auto pb-5px">
                                                        <a href="{{ json_decode( get_setting('widget_one_links'), true)[$key] }}" class="hov-text-primary">
                                                            {{ $value }}
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-end align-items-end gutters-3 mt-5px">
                                    <div class="col-auto fs-12">
                                        <a href="{{ route('termspolicies') }}" class="hov-text-primary">{{translate('Terms & Policies')}}</a>
                                    </div>
                                    <div class="col-auto">
                                        <ul class="list-inline my-0 social">
                                            @if ( get_setting('facebook_link') !=  null )
                                                <li class="list-inline-item">
                                                    <a href="{{ get_setting('facebook_link') }}" target="_blank" class="facebook"><i class="lab la-facebook-f"></i></a>
                                                </li>
                                            @endif
                                            @if ( get_setting('twitter_link') !=  null )
                                                <li class="list-inline-item">
                                                    <a href="{{ get_setting('twitter_link') }}" target="_blank" class="twitter"><i class="lab la-twitter"></i></a>
                                                </li>
                                            @endif
                                            @if ( get_setting('instagram_link') !=  null )
                                                <li class="list-inline-item">
                                                    <a href="{{ get_setting('instagram_link') }}" target="_blank" class="instagram"><i class="lab la-instagram"></i></a>
                                                </li>
                                            @endif
                                            @if ( get_setting('youtube_link') !=  null )
                                                <li class="list-inline-item">
                                                    <a href="{{ get_setting('youtube_link') }}" target="_blank" class="youtube"><i class="lab la-youtube"></i></a>
                                                </li>
                                            @endif
                                            @if ( get_setting('linkedin_link') !=  null )
                                                <li class="list-inline-item">
                                                    <a href="{{ get_setting('linkedin_link') }}" target="_blank" class="linkedin"><i class="lab la-linkedin-in"></i></a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="megamenu-bottom-phone d-lg-none">
                <div class="d-flex flex-column justify-content-between megamenu-bottom-phone-height">
                    <div class="megamenu-body-container-phone">
                        @if(count($mega_brands) > 0)
                            <div class="mobile-hor-swipe bg-default" data-simplebar>
                                <div class="megamenu-body fs-12 sm-fs-14 lg-fs-12 xxl-fs-16 xxxl-fs-18 pr-15px">
                                    <div class="row flex-nowrap no-gutters text-white fw-800 justify-content-between">
                                        @foreach($mega_brands as $mega_brand)
                                            <div class="col-auto">
                                                <div class="megamenu-categories-btn megamenu-brand-toggle" data-type="brand-{{$mega_brand->id}}">
                                                    {{toUpper($mega_brand->getTranslation('name'))}}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="megamenu-phone-brand-categories px-15px py-20px">
                                @foreach($mega_brands as $mega_brand)
                                    <div class="megamenu-sidebar-cat-content" data-type="brand-{{$mega_brand->id}}">
                                        @php
                                            $brand_categories = getMainCategoriesByBrand($mega_brand->id);
                                        @endphp
                                        @if(count($brand_categories) > 0)
                                            @foreach($brand_categories as $brand_category)
                                                @php
                                                    $sub_categories = getSubCategoriesByBrand($brand_category->id, $mega_brand->id);
                                                @endphp
                                                <div class="megamenu-sidebar-cat-item-phone" data-id="{{$brand_category->id}}">
                                                    <div class="megamenu-sidebar-cat-item-title @if(count($sub_categories) > 0) has-submenu @endif ">
                                                        <a href="{{ route('brand.maincategory', ['category_slug'=>$brand_category->slug,'brand_slug'=>$mega_brand->slug]) }}">{{$brand_category->getTranslation('name')}}</a>
                                                        @if(count($sub_categories) > 0)
                                                            <div class="toggle"></div>
                                                        @endif
                                                    </div>
                                                    @if(count($sub_categories) > 0)
                                                        <div class="megamenu-body-subcat-content-phone">
                                                            <div class="row row-cols-3 row-cols-sm-4 row-cols-md-5 row-cols-xl-6 row-cols-xxxl-7">
                                                                @foreach($sub_categories as $sub_category)
                                                                    <div class="col mb-20px mb-lg-35px mb-xxl-70px megamenu-sub-cat-item">
                                                                        <a href="{{route('products.brand_category', ['category_slug'=>$sub_category->slug,'brand_slug'=>$mega_brand->slug])}}">
                                                                            <span class="d-block megamenu-sub-cat-wrap">
                                                                                <span class="d-block megamenu-sub-cat-image">
                                                                                    <img class="img-contain h-50px h-sm-80px"
                                                                                         src="{{ uploaded_asset($sub_category->icon) }}"
                                                                                         onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                                                                         alt=""
                                                                                    >
                                                                                </span>
                                                                                <span class="d-block fs-10 sm-fs-13 fw-500 mt-5px mt-sm-10px text-center megamenu-sub-cat-title">
                                                                                    <span class="d-inline-block megamenu-sub-cat-title-wrap">
                                                                                        {{$sub_category->getTranslation('name')}}
                                                                                    </span>
                                                                                </span>
                                                                            </span>
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div class="megamenu-phone-general-tabs">
                            <div class="row no-gutters fw-500 fs-12 sm-fs-14 lg-fs-12 xxl-fs-16 xxxl-fs-18 justify-content-center">
                                <div class="col">
                                    <div class="megamenu-categories-btn megamenu-type-toggle" data-type="all">
                                        {{toUpper(translate('All'))}}
                                    </div>
                                </div>
                                @if( get_setting('show_webshop') == 'on')
                                    <div class="col">
                                        <div class="megamenu-categories-btn megamenu-type-toggle" data-type="webshop">
                                            {{toUpper(translate('Webshop'))}}
                                        </div>
                                    </div>
                                    @if($outlet_products)
                                        <div class="col">
                                            <div class="megamenu-categories-btn megamenu-type-toggle" data-type="outlet">
                                                {{toUpper(translate('Outlet'))}}
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                @if(count($used_products) > 0)
                                    <div class="col">
                                        <div class="megamenu-categories-link megamenu-type-toggle">
                                            <a href="{{route('used_page')}}">{{toUpper(translate('Used'))}}</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="megamenu-phone-general-categories px-15px py-20px">
                            <div class="megamenu-sidebar-cat-content" data-type="all">
                                @if(count($all_categories) > 0)
                                    @foreach($all_categories as $all_category)
                                        @php
                                            $sub_categories = getAvailableSubCategoriesByCategory($all_category->id);
                                        @endphp
                                        <div class="megamenu-sidebar-cat-item-phone" data-id="{{$all_category->id}}">
                                            <div class="megamenu-sidebar-cat-item-title @if(count($sub_categories) > 0) has-submenu @endif ">
                                                <a href="{{route('maincategory', $all_category->slug)}}">{{$all_category->getTranslation('name')}}</a>
                                                @if(count($sub_categories) > 0)
                                                    <div class="toggle"></div>
                                                @endif
                                            </div>
                                            @if(count($sub_categories) > 0)
                                                <div class="megamenu-body-subcat-content-phone">
                                                    <div class="row row-cols-3 row-cols-sm-4 row-cols-md-5 row-cols-xl-6 row-cols-xxxl-7">
                                                        @foreach($sub_categories as $sub_category)
                                                            <div class="col mb-20px mb-lg-35px mb-xxl-70px megamenu-sub-cat-item">
                                                                <a href="{{route('products.category', $sub_category->slug)}}">
                                                                    <span class="d-block megamenu-sub-cat-wrap">
                                                                        <span class="d-block megamenu-sub-cat-image">
                                                                            <img class="img-contain h-50px h-sm-80px"
                                                                                 src="{{ uploaded_asset($sub_category->icon) }}"
                                                                                 onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                                                                 alt=""
                                                                            >
                                                                        </span>
                                                                        <span class="d-block fs-10 sm-fs-13 fw-500 mt-5px mt-sm-10px text-center megamenu-sub-cat-title">
                                                                            <span class="d-inline-block megamenu-sub-cat-title-wrap">
                                                                                {{$sub_category->getTranslation('name')}}
                                                                            </span>
                                                                        </span>
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            @if( get_setting('show_webshop') == 'on')
                                <div class="megamenu-sidebar-cat-content" data-type="webshop">
                                    @if(count($webshop_categories) > 0)
                                        @foreach($webshop_categories as $webshop_category)
                                            @php
                                                $sub_categories = getAvailableWebSubCategoriesByCategory($webshop_category->id);
                                            @endphp
                                            <div class="megamenu-sidebar-cat-item-phone" data-id="{{$webshop_category->id}}">
                                                <div class="megamenu-sidebar-cat-item-title @if(count($sub_categories) > 0) has-submenu @endif ">
                                                    <a href="{{route('maincategory', $webshop_category->slug)}}">{{$webshop_category->getTranslation('name')}}</a>
                                                    @if(count($sub_categories) > 0)
                                                        <div class="toggle"></div>
                                                    @endif
                                                </div>
                                                @if(count($sub_categories) > 0)
                                                    <div class="megamenu-body-subcat-content-phone">
                                                        <div class="row row-cols-3 row-cols-sm-4 row-cols-md-5 row-cols-xl-6 row-cols-xxxl-7">
                                                            @foreach($sub_categories as $sub_category)
                                                                <div class="col mb-20px mb-lg-35px mb-xxl-70px megamenu-sub-cat-item">
                                                                    <a href="{{route('products.category', $sub_category->slug)}}">
                                                                        <span class="d-block megamenu-sub-cat-wrap">
                                                                            <span class="d-block megamenu-sub-cat-image">
                                                                                <img class="img-contain h-50px h-sm-80px"
                                                                                     src="{{ uploaded_asset($sub_category->icon) }}"
                                                                                     onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                                                                     alt=""
                                                                                >
                                                                            </span>
                                                                            <span class="d-block fs-10 sm-fs-13 fw-500 mt-5px mt-sm-10px text-center megamenu-sub-cat-title">
                                                                                <span class="d-inline-block megamenu-sub-cat-title-wrap">
                                                                                    {{$sub_category->getTranslation('name')}}
                                                                                </span>
                                                                            </span>
                                                                        </span>
                                                                    </a>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="megamenu-sidebar-cat-content" data-type="outlet">
                                    @if(count($outlet_products) > 0)
                                        @foreach($outlet_products as $outlet_product)
                                            @php
                                                $sub_categories = getSidebarCategories(1, $outlet_product->id);
                                            @endphp
                                            <div class="megamenu-sidebar-cat-item-phone" data-id="{{$outlet_product->id}}">
                                                <div class="megamenu-sidebar-cat-item-title @if(count($sub_categories) > 0) has-submenu @endif ">
                                                    <a href="{{route('products.category', $outlet_product->slug)}}?outlet=1">{{$outlet_product->getTranslation('name')}}</a>
                                                    @if(count($sub_categories) > 0)
                                                        <div class="toggle"></div>
                                                    @endif
                                                </div>
                                                @if(count($sub_categories) > 0)
                                                    <div class="megamenu-body-subcat-content-phone">
                                                        <div class="row row-cols-3 row-cols-sm-4 row-cols-md-5 row-cols-xl-6 row-cols-xxxl-7">
                                                            @foreach($sub_categories as $sub_category)
                                                                <div class="col mb-20px mb-lg-35px mb-xxl-70px megamenu-sub-cat-item">
                                                                    <a href="{{route('products.category', $sub_category->slug)}}?outlet=1">
                                                                        <span class="d-block megamenu-sub-cat-wrap">
                                                                            <span class="d-block megamenu-sub-cat-image">
                                                                                <img class="img-contain h-50px h-sm-80px"
                                                                                     src="{{ uploaded_asset($sub_category->icon) }}"
                                                                                     onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                                                                     alt=""
                                                                                >
                                                                            </span>
                                                                            <span class="d-block fs-10 sm-fs-13 fw-500 mt-5px mt-sm-10px text-center megamenu-sub-cat-title">
                                                                                <span class="d-inline-block megamenu-sub-cat-title-wrap">
                                                                                    {{$sub_category->getTranslation('name')}}
                                                                                </span>
                                                                            </span>
                                                                        </span>
                                                                    </a>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="megamenu-body-footer text-default-40 py-5px px-15px mt-15px">
                        <div class="border-bottom border-default-200 fs-12 sm-fs-14 fw-500">
                            <div class="row no-gutters">
                                @foreach (json_decode( get_setting('widget_one_labels'), true) as $key => $value)
                                    <div class="col-12 mb-5px">
                                        <a href="{{ json_decode( get_setting('widget_one_links'), true)[$key] }}" class="hov-text-primary">
                                            {{ $value }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="row justify-content-end align-items-end gutters-3 mt-5px">
                            <div class="col-auto fs-10 sm-fs-12">
                                <a href="{{ route('termspolicies') }}" class="hov-text-primary">{{translate('Terms & Policies')}}</a>
                            </div>
                            <div class="col-auto">
                                <ul class="list-inline my-0 social">
                                    @if ( get_setting('facebook_link') !=  null )
                                        <li class="list-inline-item">
                                            <a href="{{ get_setting('facebook_link') }}" target="_blank" class="facebook"><i class="lab la-facebook-f"></i></a>
                                        </li>
                                    @endif
                                    @if ( get_setting('twitter_link') !=  null )
                                        <li class="list-inline-item">
                                            <a href="{{ get_setting('twitter_link') }}" target="_blank" class="twitter"><i class="lab la-twitter"></i></a>
                                        </li>
                                    @endif
                                    @if ( get_setting('instagram_link') !=  null )
                                        <li class="list-inline-item">
                                            <a href="{{ get_setting('instagram_link') }}" target="_blank" class="instagram"><i class="lab la-instagram"></i></a>
                                        </li>
                                    @endif
                                    @if ( get_setting('youtube_link') !=  null )
                                        <li class="list-inline-item">
                                            <a href="{{ get_setting('youtube_link') }}" target="_blank" class="youtube"><i class="lab la-youtube"></i></a>
                                        </li>
                                    @endif
                                    @if ( get_setting('linkedin_link') !=  null )
                                        <li class="list-inline-item">
                                            <a href="{{ get_setting('linkedin_link') }}" target="_blank" class="linkedin"><i class="lab la-linkedin-in"></i></a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
