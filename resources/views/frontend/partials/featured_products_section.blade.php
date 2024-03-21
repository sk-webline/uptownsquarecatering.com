@php
    $feat_products = filter_products(\App\Product::where('published', 1)->where('featured', '1'))->limit(12)->get();
@endphp
@if(count($feat_products) > 0)
    <div id="feature-products" class="mt-70px mt-lg-100px mt-xxl-150px overflow-hidden">
        <div class="container">
            <h3 class="fs-13 md-fs-18 xxl-fs-23 fw-50 l-space-1-2 text-default-50 lh-1 m-0">{{ get_setting('feat_prods_slogan') }}</h3>
            <h2 class="fs-40 md-fs-55 xxl-fs-70 fw-700 mb-25px mb-md-50px mb-xxl-75px font-play lh-1">{{ toUpper(get_setting('feat_prods_title')) }}</h2>
        </div>
        <div class="container-left">
            <div class="product-carousel-container">
                <div class="swiper featured-products-swiper product-carousel-arrows">
                    <div class="swiper-wrapper" data-center="false" data-items="5" data-xl-items="4" data-lg-items="3"  data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows='true' data-infinite='true'>
                        @foreach ($feat_products as $key => $product)
                            <div class="swiper-slide">
                                @include('frontend.partials.product_listing.forsale_listing',['product' => $product, 'type_id' => null , 'brand_id' => null])
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
@endif
