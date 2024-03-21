<div class="position-relative used-product-res-wrap">
    <a href="{{ route('product', $product->slug) }}" class="d-block">
        <span class="d-block mb-15px mb-lg-20px mb-xxl-25px used-product-res-image">
            <img
                    class="img-fit lazyload absolute-full h-100"
                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                    data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                    alt="{{  $product->getTranslation('name')  }}"
                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
            >
        </span>
        <span class="d-block used-product-res-text">
            <span class="row gutters-5">
                <span class="d-block col">
                    <span class="d-block fw-500 mb-5px fs-12 lg-fs-14 xxl-fs-16 used-product-res-title">{{$product->getTranslation('name')}}</span>
                </span>
                <span class="d-block text-right col-auto fs-14 lg-fs-16 xxl-fs-18 fw-700">
                    <span class="@if(home_base_price($product->id) != home_discounted_base_price($product->id)) text-primary  @endif ">{{ home_discounted_base_price($product->id) }}</span>
                    @if(home_base_price($product->id) != home_discounted_base_price($product->id))
                        <span class="used-product-res-price-del">{{ home_base_price($product->id) }}</span>
                    @endif
                </span>
            </span>
        </span>
    </a>
</div>