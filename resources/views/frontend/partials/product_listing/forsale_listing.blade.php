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
    $srp_price = srp_price($product->id);
    $price_with_discount = home_discounted_base_price($product->id);
    $price_without_discount = home_base_price($product->id);
@endphp
<div class="position-relative product-res-wrap">
    <a href="{{ $route }}" class="d-block">
        <span class="d-block product-res-top">
            <span class="row gutters-5">
                <span class="col-auto">
                    @if($product->brand_id)
                        <img class="h-10px h-xxl-15px product-res-brand" src="{{uploaded_asset($product->brand->logo)}}" alt="">
                    @else
                        <span class="d-inline-block h-10px h-xxl-15px"></span>
                    @endif
                </span>
                <span class="col d-flex flex-column align-items-end">
                    @if($price_without_discount != $price_with_discount)
                        @php
                            $discount_icon = ($product->discount_type == 'percent') ? '%': currency_symbol();
                        @endphp
                        <span class="@if($product->category->for_sale==0) d-none @else d-flex @endif mb-5px align-items-center font-play fs-10 md-fs-12 xxxl-fs-14 fw-700 l-space-05 lg-l-space-1-2 product-res-offer">
                           <span>{{ toUpper(translate('Special Price')) }}
    {{--                               <span class="d-none d-md-inline"> - {{$product->discount}}{{$discount_icon}}</span>--}}
                           </span>
                        </span>
                    @endif
                    @if($product->cyprus_shipping_only)
                        <div class="cyprus-shipping-label fs-10 xxl-fs-11">
                            <img src="{{static_asset('assets/img/icons/red-location.svg')}}" alt=""> {{translate('Delivery in Cyprus ONLY')}}
                        </div>
                    @endif
                </span>
            </span>
        </span>
        <span class="d-block my-10px my-md-20px product-res-image">
            <img
                    class="img-fit lazyload absolute-full h-100"
                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                    data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                    alt="{{  $product->getTranslation('name')  }}"
                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
            >
            @if(isPartner() && $srp_price != $price_with_discount)
                <span class="d-block product-res-srp text-right lh-1 p-2px fs-14 @if($product->category->for_sale==0) visibility-hidden @endif">
                    <span class="d-block text-primary fs-10 fw-700">{{translate('SRP')}}</span>
                    <span class="d-block">{{ $srp_price }}</span>
                </span>
            @endif
        </span>
        <span class="d-block product-res-text">
            <span class="d-block font-play text-secondary fs-10 md-fs-13 l-space-1-2 product-res-category">
                {{ toUpper($product->category->getTranslation('name')) }}
            </span>
            <span class="d-block fs-11 md-fs-14 xxl-fs-16 fw-500 l-space-1-2 text-default-50 my-md-5px product-res-title">{{$product->getTranslation('name')}}</span>
            <span class="d-block font-play fs-10 md-fs-14 xxl-fs-18 product-res-price @if($product->category->for_sale==0) visibility-hidden @endif">
                <span class="fw-700 fs-18 md-fs-24 xxl-fs-30 lh-1 @if($price_without_discount != $price_with_discount) text-primary  @endif ">
                    {{ $price_with_discount }}
                </span>
                @if($price_without_discount != $price_with_discount)
                    <span class="product-res-price-del">{{ $price_without_discount }}</span>
                @endif
            </span>
            @if($product->category->for_sale==0)
                <span class="d-block font-play fw-700 fs-11 md-fs-14 xxl-fs-16 product-res-enquire">{{toUpper(translate('Enquire for this one'))}}</span>
            @endif
            {{--@if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
                <span class="d-block rounded px-2 mt-2 bg-soft-primary border-soft-primary border">
                    {{ translate('Club Point') }}:
                    <span class="fw-700 float-right">{{ $product->earn_point }}</span>
                </span>
            @endif--}}
        </span>
    </a>
</div>
