@php
    if($type_id != null){
        $type = \App\ProductType::find($type_id);
        $route = route('product.type', ['type_slug'=>$type->slug,'slug'=>$product->slug]);
    } elseif($brand_id != null){
        $brand = \App\Brand::find($brand_id);
        $route = route('product.brand', ['brand_slug'=>$brand->slug,'slug'=>$product->slug]);
    } else {
        $route = route('product', $product->slug);
    }
@endphp
<div class="position-relative notsale-product-res-wrap">
    <a href="{{ $route }}" class="d-block">
        <span class="d-block py-20px py-lg-40px notsale-product-res-container">
            <span class="d-block mb-10px mb-lg-20px notsale-product-res-image">
                <img
                        class="img-contain lazyload absolute-full h-100"
                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                        data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                        alt=""
                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                >
            </span>
            <span class="d-block text-center notsale-product-res-text">
                <span class="d-block fs-13 lg-fs-16 xxl-fs-20 fw-500 mb-lg-5px notsale-product-res-title">{{$product->getTranslation('name')}}</span>
                @if($product->getTranslation('subtitle'))
                    <span class="d-block fs-10 lg-fs-12 xxl-fs-14 text-primary notsale-product-res-slogan">{{$product->getTranslation('subtitle')}}</span>
                @endif
            </span>
        </span>
    </a>
</div>