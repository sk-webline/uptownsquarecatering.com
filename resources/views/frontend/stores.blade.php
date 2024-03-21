@extends('frontend.layouts.app')

@section('meta_title'){{ translate('Store') }}@stop
@section('content')
<div id="stores" class="mt-40px mt-lg-80px mt-xxl-125px mb-80px mb-lg-125px mb-xxl-150px overflow-hidden">
    <div class="stores-top">
        <div class="container">
            <h1 class="fs-20 lg-fs-45 xxl-fs-70 fw-700 text-default-50 l-space-1-2 font-play mb-25px mb-lg-65px mb-xxl-100px">{{translate('Store')}} <span class="text-secondary">{{translate('Locator')}}</span></h1>
        </div>
        <div class="d-none d-md-block">
            <div class="stores-map">
                <img class="w-100" src="{{static_asset('assets/img/icons/cyprus-map.svg')}}" alt="">
                <div class="stores-map-over">
                    @foreach(\App\Store::all() as $store)
                        <div class="point-location" style="top:{{$store->y_pos}}%; left:{{$store->x_pos}}%;">
                            <div class="point-location-toggle"></div>
                            <a href="{{$store->google_map_url}}" target="_blank" class="d-block point-location-popup">
                            <span class="d-block point-location-popup-wrap">
                                <span class="d-block point-location-popup-image">
                                    <img class="img-fit absolute-full h-100 lazyload"
                                         src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                         data-src="{{uploaded_asset($store->thumbnail_img)}}"
                                         onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                         alt="">
                                </span>
                                <span class="d-block p-5px fs-12 fw-500 text-center">
                                    <span class="d-block m-0 fs-20 fw-700 text-black l-space-1-2 font-play">{{$store->getTranslation('name')}}</span>
                                    <span class="d-block my-5px border-top border-black-100"></span>
                                    <span class="text-secondary border-bottom border-inherit hov-text-primary">
                                        {{translate('View on Google Map')}}
                                    </span>
                                </span>
                            </span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="d-md-none">
            <div class="stores-map">
                <img class="w-100" src="{{static_asset('assets/img/icons/cyprus-map-mobile.svg')}}" alt="">
                <div class="stores-map-over">
                    @foreach(\App\Store::all() as $store)
                        <div class="point-location" style="top:{{$store->y_pos_phone}}%; left:{{$store->x_pos_phone}}%;">
                            <div class="point-location-toggle"></div>
                            <a href="{{$store->google_map_url}}" target="_blank" class="d-block point-location-popup">
                                <span class="d-block point-location-popup-wrap">
                                    <span class="d-block point-location-popup-image">
                                        <img class="img-fit absolute-full h-100 lazyload"
                                             src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                             data-src="{{uploaded_asset($store->thumbnail_img)}}"
                                             onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                             alt="">
                                    </span>
                                    <span class="d-block p-5px fs-12 fw-500 text-center">
                                        <span class="d-block m-0 fs-20 fw-700 text-black l-space-1-2 font-play">{{$store->getTranslation('name')}}</span>
                                        <span class="d-block my-5px border-top border-black-100"></span>
                                        <span class="text-secondary border-bottom border-inherit hov-text-primary">
                                            {{translate('View on Google Map')}}
                                        </span>
                                    </span>
                                </span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @if(count($cities) > 0)
        <div class="stores-bottom mt-30px mt-lg-60px mt-xxl-90px">
            <div class="container">
                <div class="store-tabs border-bottom border-width-3 border-black-100 mb-25px mb-lg-50px mb-xxl-80px font-play fs-18 lg-fs-24 xxl-fs-30 fw-700 text-default-50">
                    <div class="row gutters-10 lg-gutters-30 xxl-gutters-50">
                        @php
                            $counter=1;
                        @endphp
                        @foreach($cities as $city)
                            <div class="col-auto">
                                <div class="store-tab-res-item pb-20px @if($counter==1) active @endif " data-id="{{$city->id}}">
                                    {{toUpper(\App\StoreCity::find($city->id)->getTranslation('name'))}}
                                </div>
                            </div>
                            @php
                                $counter++;
                            @endphp
                        @endforeach
                    </div>
                </div>
            </div>
            @php
                $counter=1;
            @endphp
            @foreach($cities as $city)
                <div class="stores-city-container @if($counter==1) active @endif " data-id="{{$city->id}}">
                    <div class="row gutters-40">
                        <div class="col-lg-6 fs-13 lg-fs-14 xxl-fs-16 text-black-50 fw-500">
                            <div class="container-left">
                                <div class="pt-20px pt-lg-35px pt-xxl-50px">
                                    <div class="row xxl-gutters-30">
                                        @foreach(\App\Store::where('city_id', $city->id)->orderBy('order_level', 'desc')->get() as $store)
                                            <div class="col-sm-6 col-xxl-5 mb-30px store-res-item">
                                                <h2 class="font-play fs-16 lg-fs-18 xxl-fs-20 fw-700 text-black l-space-1-2">{{$store->getTranslation('name')}}</h2>
                                                <div class="mb-10px mb-lg-15px mb-xxl-20px mw-225px">
                                                    {{$store->getTranslation('address')}}
                                                    <div class="mt-5px fs-12 fw-600">
                                                        <a href="{{$store->google_map_url}}" target="_blank" class="text-black-80 border-bottom border-inherit hov-text-primary">
                                                            {{toUpper(translate('View on Map'))}}
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="mb-10px mb-lg-15px mb-xxl-20px">
                                                    <div>{{translate('Tel')}}: {{$store->phone}}</div>
                                                    @if($store->fax)
                                                        <div>{{translate('Fax')}}: {{$store->fax}}</div>
                                                    @endif
                                                </div>
                                                @if(($store->working_days_1 && $store->working_hours_1) || ($store->working_days_2 && $store->working_hours_2) || ($store->working_days_3 && $store->working_hours_3))
                                                    <div>{{translate('Opening Hours')}}</div>
                                                    @if($store->working_days_1 && $store->working_hours_1)
                                                        <div class="mb-10px mb-lg-15px mb-xxl-20px">
                                                            <div>{{$store->getTranslation('working_days_1')}}:</div>
                                                            <div>{{$store->working_hours_1}}</div>
                                                        </div>
                                                    @endif
                                                    @if($store->working_days_2 && $store->working_hours_2)
                                                        <div class="mb-10px mb-lg-15px mb-xxl-20px">
                                                            <div>{{$store->getTranslation('working_days_2')}}:</div>
                                                            <div>{{$store->working_hours_2}}</div>
                                                        </div>
                                                    @endif
                                                    @if($store->working_days_3 && $store->working_hours_3)
                                                        <div class="mb-10px mb-lg-15px mb-xxl-20px">
                                                            <div>{{$store->getTranslation('working_days_3')}}:</div>
                                                            <div>{{$store->working_hours_3}}</div>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="store-res-image">
                                <img class="img-fit absolute-full h-100 lazyload"
                                     src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                                     data-src="{{uploaded_asset(\App\StoreCity::find($city->id)->thumbnail_img)}}"
                                     alt="">
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    $counter++;
                @endphp
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('script')
    <script>
      $(document).on('click', '.store-tab-res-item', function () {
        $('.store-tab-res-item').not(this).removeClass('active');
        $(this).addClass('active');
        $('.stores-city-container').removeClass('active');
        $('.stores-city-container[data-id="' + $(this).data('id') + '"]').addClass('active');
      });
    </script>
@endsection