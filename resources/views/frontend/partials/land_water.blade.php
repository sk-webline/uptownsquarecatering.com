@php
    $types = \App\ProductType::all();
    $webshop_categories = getWebshopMainCategories();
@endphp
@if(count($types) > 0)
    <div id="land-water" class="mt-70px mt-md-100px mt-xxl-125px overflow-hidden">
        <div class="container d-none d-md-block">
            <h1 class="fs-40 lg-fs-50 xxl-fs-70 fw-700 font-play text-default-50 l-space-1-2 mb-50px mb-xxl-90px">
                {{translate('Whatever your')}} <span class="text-secondary">{{translate('style')}}</span> {{translate('is')}}
            </h1>
        </div>
        <div class="land-water-bottom position-relative">
            <div class="container">
                <div class="row lg-gutters-20 xxl-gutters-30">
                    @foreach($types as $type)
                        <div class="col-md-6 mb-15px mb-sm-40px mb-md-0 land-water-res-item">
                            <a href="{{route('type.maincategory', $type->slug)}}">
                                <span class="d-block fw-500 land-water-res-wrap">
                                    <span class="d-block land-water-res-image">
                                        <img class="img-fit absolute-full h-100"
                                             src="{{uploaded_asset($type->banner)}}"
                                             onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                             alt="">
                                        <span class="d-flex flex-column justify-content-end justify-content-xl-between align-items-start px-15px px-md-35px px-xl-60px pt-15px pt-xl-40px text-white-80 land-water-res-over">
                                            <span class="d-none d-xl-block land-water-res-desc">{{$type->getTranslation('short_description')}}</span>
                                            <span class="d-inline-block pb-10px pb-xl-30px fs-22 lg-fs-30 fw-700 font-play l-space-1-2 text-white land-water-res-title">{{$type->getTranslation('slogan')}}</span>
                                        </span>
                                    </span>
                                </span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @if( get_setting('show_webshop') == 'on')
                @if(count($webshop_categories) > 0)
                    <div class="side-right-bar d-none d-md-block">
                        <a href="javascript:void(0);" class="side-right-bar-link side-popup-toggle lg-fs-25" data-rel="webshop-side-popup">
                            <span class="d-block side-right-bar-arrow"></span>
                            <span class="d-block side-right-bar-text-wrap">
                                <span class="side-right-bar-text">{{toUpper(translate('Webshop'))}}</span>
                            </span>
                        </a>
                    </div>
                @endif
            @endif
        </div>
    </div>
    @if( get_setting('show_webshop') == 'on')
        @if(count($webshop_categories) > 0)
            <div id="webshop-side-popup" class="side-popup">
                <div class="side-popup-box">
                    <div class="side-popup-close">
                        <div class="side-popup-close-icon"></div>
                        <div class="side-popup-close-text-wrap">
                            <div class="side-popup-close-text">{{toUpper(translate('Close'))}}</div>
                        </div>
                    </div>
                    <div class="side-popup-container">
                        <div class="side-popup-scroll c-scrollbar">
                            <div class="p-10px">
                                @foreach($webshop_categories as $webshop_category)
                                    <div class="mb-10px webshop-cat-res-item">
                                        <a href="{{route('maincategory', $webshop_category->slug)}}">
                                            <span class="d-flex justify-content-center align-items-center text-center min-h-170px webshop-cat-res-wrap">
                                                <span class="d-block webshop-cat-res-over">
                                                    <span class="d-block webshop-cat-res-image">
                                                        <img class="img-contain h-90px"
                                                             src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                             data-src="{{uploaded_asset($webshop_category->icon)}}"
                                                             onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                                             alt=""
                                                        >
                                                    </span>
                                                    <span class="d-block mt-10px font-play webshop-cat-res-title">{{toUpper($webshop_category->getTranslation('name'))}}</span>
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
@endif
