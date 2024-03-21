@extends('frontend.layouts.app')

@section('meta_title'){{ translate('Second Hand Products') }}@stop

@section('content')
    @php
        $current_type = (isset($_GET['type']) && $_GET['type'] == 'water') ? 'water' : 'land';
    @endphp
    <div class="line-slider-item overflow-hidden">
        <div class="line-slider-image line-slider-small">
            <img
                    class="absolute-full h-100 img-fit"
                    src="{{ static_asset('assets/img/used-header.jpg') }}"
                    alt=""
                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
            >
            <div class="line-slider-over">
                <div class="row no-gutters">
                    <div class="col-lg-4">
                        <div class="line-slider-over-left">
                            <div class="line-slider-over-box d-flex justify-content-center"></div>
                            <div class="line-slider-over-box slogan d-lg-flex justify-content-center">
                                <div class="line-slider-over-box-inner p-0">
                                    <h2 class="mb-0 fs-11 md-fs-14 xxl-fs-16 fw-400 type-title-change">
                                        <span data-rel="1" @if($current_type == 'land') class="active" @endif >{{ translate('Land') }}</span>
                                        <span data-rel="2" @if($current_type == 'water') class="active" @endif >{{ translate('Water') }}</span>
                                    </h2>
                                    <h1 class="font-play fs-25 md-fs-30 xl-fs-45 xxl-fs-60 fw-700 m-0">{{ translate('Used') }}</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="used-results" class="mt-50px mt-lg-75px mt-xxl-100px mb-20px overflow-hidden">
        <div class="container">
            <div class="row gutters-10 lg-gutters-15 xxl-gutters-20 mb-20px mb-lg-65px mb-xxl-90px">
                <div class="col-6">
                    <div class="type-toggle fs-12 md-fs-15 lg-fs-20 xxl-fs-25 font-play @if($current_type == 'land') active @endif " data-rel="1">{{toUpper(translate('Land Products'))}}</div>
                </div>
                <div class="col-6">
                    <div class="type-toggle fs-12 md-fs-15 lg-fs-20 xxl-fs-25 font-play @if($current_type == 'water') active @endif " data-rel="2">{{toUpper(translate('Water Products'))}}</div>
                </div>
            </div>
            <div class="type-toggle-tab @if($current_type == 'land') active @endif " data-rel="1">
                @if(count($land_products) > 0)
                    <div class="row gutters-10 lg-gutters-15 xxl-gutters-20 xxxl-gutters-35">
                        @foreach($land_products as $land_product)
                            <div class="col-sm-6 col-xl-4 mb-35px mb-lg-80px mb-xxl-125px used-product-res-item">
                                @include('frontend.partials.product_listing.used_listing',['product' => $land_product])
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="my-50px my-lg-75px my-xxl-100px">
                        <div class="alert alert-grey m-0 text-center fw-500">{{translate('There are no used items under this category')}}</div>
                    </div>
                @endif
            </div>
            <div class="type-toggle-tab @if($current_type == 'water') active @endif " data-rel="2">
                @if(count($water_products) > 0)
                    <div class="row gutters-10 lg-gutters-15 xxl-gutters-20 xxxl-gutters-35">
                        @foreach($water_products as $water_product)
                            <div class="col-sm-6 col-xl-4 mb-35px mb-lg-80px mb-xxl-125px used-product-res-item">
                                @include('frontend.partials.product_listing.used_listing',['product' => $water_product])
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="my-50px my-lg-75px my-xxl-100px">
                        <div class="alert alert-grey m-0 text-center fw-500">{{translate('There are no used items under this category')}}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on('click', '.type-toggle', function () {
          $('.type-toggle').not(this).removeClass('active');
          $('.type-toggle-tab, .type-title-change span').removeClass('active');
          $(this).addClass('active');
          $('.type-toggle-tab[data-rel="' + $(this).data('rel') + '"], .type-title-change span[data-rel="' + $(this).data('rel') + '"]').addClass('active');
        });
    </script>
@endsection