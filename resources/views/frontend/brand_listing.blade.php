@extends('frontend.layouts.app')

@section('meta_title'){{ $brand->meta_title }}@stop
@section('meta_description'){{ $brand->meta_description }}@stop

@section('content')
    <div class="line-slider-item overflow-hidden">
        <div class="line-slider-image">
            @if($brand->type == 'video' && $brand->video_link)
                <div id="player" class="absolute-full"></div>
            @else
                <img
                    class="absolute-full h-100 img-fit"
                    src="{{ uploaded_asset($brand->header) }}"
                    alt=""
                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
                >
            @endif
            <div class="line-slider-over">
                <div class="row no-gutters">
                    <div class="col-lg-4">
                        <div class="line-slider-over-left">
                            <div class="line-slider-over-box"></div>
                            <div class="line-slider-over-box slogan d-lg-flex justify-content-center">
                                <div class="line-slider-over-box-inner p-xxl-20px">
                                    <h1 class="font-play fs-25 md-fs-30 xl-fs-45 xxl-fs-60 fw-700 m-0">{{ $brand->getTranslation('name') }}</h1>
                                </div>
                            </div>
                            @if($brand->getTranslation('banner_desc'))
                                <div class="line-slider-over-box bg-white text-default-50 fs-16 xl-fs-18 xxl-fs-23 xxxl-fs-28 font-play l-space-1 px-xxl-50px d-none d-lg-flex flex-column justify-content-center lh-1-4">
                                    <div class="mw-850px">
                                        <p class="text-truncate-6">{{$brand->getTranslation('banner_desc')}}</p>
                                    </div>
                                </div>
                            @else
                                <div class="line-slider-over-box d-none d-lg-block"></div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-8 d-none d-lg-block">
                        <div class="line-slider-over-right">
                            <div class="line-slider-over-box"></div>
                            <div class="line-slider-over-box slogan"></div>
                            <div class="line-slider-over-box d-flex align-items-end justify-content-end">
                                <?php /*
                                @if($brand->logo)
                                    <div class="line-slider-over-box-inner p-xxl-20px">
                                        <img class="img-fit h-60px h-xxl-85px line-slider-logo" src="{{ uploaded_asset($brand->logo) }}"
                                             alt=""
                                             onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';">
                                    </div>
                                @endif
                                */ ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($brand->getTranslation('banner_desc'))
        <div class="my-30px my-md-50px d-lg-none">
            <div class="container">
                <div class="text-default-50 fs-16 font-play l-space-1-2 lh-1-7 mw-850px">
                    <p>{{$brand->getTranslation('banner_desc')}}</p>
                </div>
            </div>
        </div>
    @endif
    @if(count($categories) > 0)
        <div id="brands-categories" class="mt-20px mt-lg-150px mt-xxl-275px mb-60px mb-lg-100px mb-xxl-150px fs-11 sm-fs-14 xl-fs-16 xxxl-fs-18 position-relative">
            <div class="services-results-top position-relative overflow-hidden">
                <div class="container">
                    <div class="row lg-gutters-20 xxxl-gutters-30">
                        @php
                            $counter=1;
                        @endphp
                        @foreach($categories as $category)
                            @if($counter <= 2)
                                @php
                                    $description = \App\CategoryBrand::where('category_id', $category->id)->where('brand_id', $brand->id)->first();
                                @endphp
                                <div class="col-md-6 mb-30px mb-md-0 services-res-item">
                                    <div class="services-res-wrap fw-500 l-space-1-2 text-white-80">
                                        <a href="{{ route('brand.maincategory', ['category_slug'=>$category->slug,'brand_slug'=>$brand->slug]) }}">
                                            <span class="d-block services-res-image">
                                                <img class="img-fit absolute-full h-100"
                                                     src="{{ uploaded_asset($category->banner) }}"
                                                     alt=""
                                                     onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
                                                >
                                                <span class="services-res-over px-15px px-sm-30px px-xxl-50px pb-15px pb-sm-30px pt-35px d-flex flex-column justify-content-between">
                                                    @if($description)
                                                        <span class="d-block services-res-over-desc c-scrollbar">
                                                            {{$description->getTranslation('description')}}
                                                        </span>
                                                    @endif
                                                    <span class="services-res-title fs-14 sm-fs-17 lg-fs-22 xxl-fs-30 fw-700 text-white font-play text-truncate-2">{{\App\Category::find($category->id)->getTranslation('name')}}</span>
                                                </span>
                                            </span>
                                        </a>
                                        @if($description)
                                            <div class="services-res-toggle fs-25 fw-700 font-play"></div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @php
                                $counter++;
                            @endphp
                        @endforeach
                    </div>
                </div>
            </div>
            @if(count($categories) >= 3)
                <div class="services-results-middle my-md-30px my-lg-40px my-xxxl-60px overflow-hidden">
                    <div class="container">
                        <div class="row lg-gutters-20 xxxl-gutters-30 @if($brand->getTranslation('slogan_description') || $brand->getTranslation('slogan_title')) align-items-end @endif ">
                            @if($brand->getTranslation('slogan_description') || $brand->getTranslation('slogan_title'))
                                <div class="col-md-6 pt-20px pt-md-0 pb-50px pb-md-60px fs-11 lg-fs-18 xxl-fs-25 l-space-1-2 text-default-50">
                                    @if($brand->getTranslation('slogan_description'))
                                        <p class="m-0">{{$brand->getTranslation('slogan_description')}}</p>
                                    @endif
                                    @if($brand->getTranslation('slogan_title'))
                                        <h2 class="d-inline-block text-secondary fs-30 lg-fs-50 xxl-fs-70 fw-700 font-play m-0 lh-1">{{$brand->getTranslation('slogan_title')}}</h2>
                                    @endif
                                </div>
                            @else
                                <div class="col-md-6 pt-20px py-md-0 pb-50px fs-11 lg-fs-18 xxl-fs-25 l-space-1-2 text-default-50 d-flex align-items-center justify-content-center">
                                    @if($brand->logo)
                                        <img class="img-contain h-40px h-sm-60px h-md-40px h-lg-60px h-xxl-85px" src="{{ uploaded_asset($brand->logo) }}"
                                             alt=""
                                             onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';">
                                    @else
                                        <h2 class="d-inline-block text-secondary fs-30 lg-fs-50 xxl-fs-70 fw-700 font-play m-0 lh-1">{{$brand->getTranslation('name')}}</h2>
                                    @endif
                                </div>
                            @endif
                            @php
                                $counter=1;
                            @endphp
                            @foreach($categories as $category)
                                @if($counter == 3)
                                    @php
                                        $description = \App\CategoryBrand::where('category_id', $category->id)->where('brand_id', $brand->id)->first();
                                    @endphp
                                    <div class="col-md-6 mb-30px mb-md-0 services-res-item">
                                        <div class="services-res-wrap fw-500 l-space-1-2 text-white-80">
                                            <a href="{{ route('brand.maincategory', ['category_slug'=>$category->slug,'brand_slug'=>$brand->slug]) }}">
                                            <span class="d-block services-res-image">
                                                <img class="img-fit absolute-full h-100"
                                                     src="{{ uploaded_asset($category->banner) }}"
                                                     alt=""
                                                     onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
                                                >
                                                <span class="services-res-over px-15px px-sm-30px px-xxl-50px pb-15px pb-sm-30px pt-35px d-flex flex-column justify-content-between">
                                                    @if($description)
                                                        <span class="d-block services-res-over-desc c-scrollbar">
                                                            {{$description->getTranslation('description')}}
                                                        </span>
                                                    @endif
                                                    <span class="services-res-title fs-14 sm-fs-17 lg-fs-22 xxl-fs-30 fw-700 text-white font-play text-truncate-2">{{\App\Category::find($category->id)->getTranslation('name')}}</span>
                                                </span>
                                            </span>
                                            </a>
                                            @if($description)
                                                <div class="services-res-toggle fs-25 fw-700 font-play"></div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                @php
                                    $counter++;
                                @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            @if(count($categories) >= 4)
                @php
                    $counter=1;
                    $inside_counter=1;
                @endphp
                <div class="services-results-bottom overflow-hidden">
                    <div class="container">
                        <div class="row lg-gutters-20 xxxl-gutters-30">
                            @foreach($categories as $category)
                                @php
                                    $description = \App\CategoryBrand::where('category_id', $category->id)->where('brand_id', $brand->id)->first();
                                @endphp
                                @if($counter >= 4)
                                    @php
                                        $class = ($inside_counter==1) ? 'col-12': 'col-md-6';
                                    @endphp
                                    @if($counter == 7 && ($brand->getTranslation('slogan_title_2') || $brand->getTranslation('slogan_description_2')))
                                        <div class="{{$class}} mb-30px mb-lg-40px mb-xxxl-60px">
                                            <div class="row lg-gutters-20 xxxl-gutters-30 align-items-end">
                                                <div class="col-md-6 pt-20px pt-md-0 pb-50px pb-md-60px fs-11 lg-fs-18 xxl-fs-25 l-space-1-2 text-default-50">
                                                    @if($brand->getTranslation('slogan_description_2'))
                                                        <p class="m-0">{{$brand->getTranslation('slogan_description_2')}}</p>
                                                    @endif
                                                    @if($brand->getTranslation('slogan_title_2'))
                                                        <h2 class="d-inline-block text-secondary fs-30 lg-fs-50 xxl-fs-70 fw-700 font-play m-0 lh-1">{{$brand->getTranslation('slogan_title_2')}}</h2>
                                                    @endif
                                                </div>
                                                <div class="col-md-6 services-res-item">
                                                    <div class="services-res-wrap fw-500 l-space-1-2 text-white-80">
                                                        <a href="{{ route('brand.maincategory', ['category_slug'=>$category->slug,'brand_slug'=>$brand->slug]) }}">
                                                            <span class="d-block services-res-image">
                                                                <img class="img-fit absolute-full h-100"
                                                                     src="{{ uploaded_asset($category->banner) }}"
                                                                     alt=""
                                                                     onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
                                                                >
                                                                <span class="services-res-over px-15px px-sm-30px px-xxl-50px pb-15px pb-sm-30px pt-35px d-flex flex-column justify-content-between">
                                                                    @if($description)
                                                                        <span class="d-block services-res-over-desc c-scrollbar">
                                                                            {{$description->getTranslation('description')}}
                                                                        </span>
                                                                    @endif
                                                                    <span class="services-res-title fs-14 sm-fs-17 lg-fs-22 xxl-fs-30 fw-700 text-white font-play text-truncate-2">{{\App\Category::find($category->id)->getTranslation('name')}}</span>
                                                                </span>
                                                            </span>
                                                        </a>
                                                        @if($description)
                                                            <div class="services-res-toggle fs-25 fw-700 font-play"></div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="{{$class}} mb-30px mb-lg-40px mb-xxxl-60px services-res-item">
                                            <div class="services-res-wrap fw-500 l-space-1-2 text-white-80">
                                                <a href="{{ route('brand.maincategory', ['category_slug'=>$category->slug,'brand_slug'=>$brand->slug]) }}">
                                                <span class="d-block services-res-image">
                                                    <img class="img-fit absolute-full h-100"
                                                         src="{{ uploaded_asset($category->banner) }}"
                                                         alt=""
                                                         onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
                                                    >
                                                    <span class="services-res-over px-15px px-sm-30px px-xxl-50px pb-15px pb-sm-30px pt-35px d-flex flex-column justify-content-between">
                                                        @if($description)
                                                            <span class="d-block services-res-over-desc c-scrollbar">
                                                                {{$description->getTranslation('description')}}
                                                            </span>
                                                        @endif
                                                        <span class="services-res-title fs-14 sm-fs-17 lg-fs-22 xxl-fs-30 fw-700 text-white font-play text-truncate-2">{{\App\Category::find($category->id)->getTranslation('name')}}</span>
                                                    </span>
                                                </span>
                                                </a>
                                                @if($description)
                                                    <div class="services-res-toggle fs-25 fw-700 font-play"></div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    @php
                                        $inside_counter++;
                                        if($inside_counter == 4) {
                                            $inside_counter=1;
                                        }
                                    @endphp
                                @endif
                                @php
                                    $counter++;
                                @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            <div class="d-none d-md-block sticky-column">
                <div class="side-right-bar sticky-top" data-rel=".services-results-top">
                    <a href="{{route('contact')}}" class="side-right-bar-link side-popup-toggle lg-fs-25">
                        <span class="d-block side-right-bar-arrow"></span>
                        <span class="d-block side-right-bar-text-wrap">
                        <span class="side-right-bar-text">{{toUpper(translate('Contact Us'))}}</span>
                    </span>
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="my-50px my-lg-100px my-xxl-150px overflow-hidden">
            <div class="container">
                <div class="alert alert-grey m-0 text-center fw-500">{{translate("We don't have any products under this brand yet")}}</div>
            </div>
        </div>
    @endif
@endsection

@section('script')
    <script type="text/javascript">
        @if($brand->type == 'video' && $brand->video_link)
            // 2. This code loads the IFrame Player API code asynchronously.
            var tag = document.createElement('script');

            tag.src = "https://www.youtube.com/iframe_api";
            var firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

            // 3. This function creates an <iframe> (and YouTube player)
            //    after the API code downloads.
            var player;
        function onYouTubeIframeAPIReady() {
            player = new YT.Player('player', {
                height: '390',
                width: '640',
                videoId: "{{ explode('=', $brand->video_link)[1] }}",
                playerVars: {
                    'playsinline': 1,
                    'autoplay': 1,
                    'showinfo': 0,
                    'controls': 0,
                    'loop': 1,
                    'playlist': "{{ explode('=', $brand->video_link)[1] }}",
                    'mute': 1,
                    'enablejsapi': 1,
                    'version': 3,
                    'playerapiid': 'ytplayer',
                    'rel': 0
                },
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange':
                        function(e) {
                            if (e.data === YT.PlayerState.ENDED) {
                                player.playVideo();
                            }
                        }
                }
            });
        }

        // 4. The API will call this function when the video player is ready.
        function onPlayerReady(event) {
            event.target.playVideo();
        }
        function onPlayerReadyPhone(event) {
            event.target.playVideo();
        }

        // 5. The API calls this function when the player's state changes.
        //    The function indicates that when playing a video (state=1),
        //    the player should play for six seconds and then stop.
        var done = false;
        function onPlayerStateChange(event) {
            if (event.data == YT.PlayerState.PLAYING && !done) {
                setTimeout(stopVideo, 6000);
                done = true;
            }
        }
        function stopVideo() {
            player.stopVideo();
        }
        @endif
    </script>
@endsection
