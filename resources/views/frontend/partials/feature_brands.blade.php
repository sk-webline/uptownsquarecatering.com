@php
    $brands = \App\Brand::orderBy('order_level', 'desc')->get();
@endphp
@if(count($brands) > 0)
    <div id="feature-brands" class="mt-60px mt-lg-90px mt-xxl-125px px-20px px-lg-0 py-10px py-lg-75px py-xxl-150px overflow-hidden">
        <div class="container">
            <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-4 row-cols-xxl-6 gutters-25 justify-content-center">
                @foreach($brands as $brand)
                    <div class="col my-15px my-xxl-20px feature-res-brand-item">
                        <a href="{{route('brand_page', $brand->slug)}}">
                            <span class="d-block feature-res-brand-wrap">
                                <span class="d-flex flex-column absolute-full justify-content-center feature-res-brand-image">
                                    <img class="img-contain h-100 mh-25px lg-mh-45px" src="{{uploaded_asset($brand->logo)}}" alt="">
                                </span>
                            </span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
