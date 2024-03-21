@extends('frontend.layouts.app')

@section('meta_title'){{ $blog->meta_title }}@stop

@section('meta_description'){{ $blog->meta_description }}@stop

@section('meta_keywords'){{ $blog->meta_keywords }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $blog->meta_title }}">
    <meta itemprop="description" content="{{ $blog->meta_description }}">
    <meta itemprop="image" content="{{ uploaded_asset($blog->meta_img) }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ $blog->meta_title }}">
    <meta name="twitter:description" content="{{ $blog->meta_description }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ uploaded_asset($blog->meta_img) }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $blog->meta_title }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('product', $blog->slug) }}" />
    <meta property="og:image" content="{{ uploaded_asset($blog->meta_img) }}" />
    <meta property="og:description" content="{{ $blog->meta_description }}" />
    <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
@endsection

@section('content')
    <div id="news-publish" class="mt-20px mt-md-40px mb-100px mb-lg-150px mb-xxl-175px overflow-hidden">
        <div class="container">
            <div class="mx-auto mw-1350px">
                <div class="mb-30px mb-lg-65px mb-xxl-100px">
                    <ul class="breadcrumb fs-10 md-fs-12">
                        <li class="breadcrumb-item">
                            <a class="hov-text-primary" href="{{ route('blog') }}">{{ translate('News') }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a class="hov-text-primary" href="{{ url("blog").'/'. $blog->slug }}">{{ $blog->title }}</a>
                        </li>
                    </ul>
                </div>
                <h1 class="text-default-50 fs-30 lg-fs-50 xxl-fs-70 fw-700 font-play l-space-1-2">{{ $blog->title }}</h1>
                @if($blog->category != null)
                    <div class="mb-2 opacity-50 fw-700">{{ $blog->category->category_name }}</div>
                @endif
                <div class="blog-publish-image mt-30px mt-lg-65px mt-xxl-100px">
                    <img
                            src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                            data-src="{{ uploaded_asset($blog->banner) }}"
                            alt="{{ $blog->title }}"
                            class="img-fit lazyload absolute-full h-100"
                    >
                    <div class="blog-publish-over absolute-full d-flex justify-content-between flex-column l-space-1-2 text-white fs-11 lg-fs-15 xxl-fs-18">
                        <div class="blog-publish-date fw-700 p-5px px-lg-20px px-xxl-40px py-lg-15px py-xxl-20px">{{ toUpper(date('d M Y', strtotime($blog->created_at))) }}</div>
                        <div class="blog-publish-short fs-11 lg-fs-18 xxl-fs-25 fw-500 d-none d-lg-block p-5px px-lg-20px px-xxl-40px py-lg-15px py-xxl-20px">
                            <p>{{ $blog->short_description }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-10px mt-md-20px text-default-50 l-space-1-2 fw-500 d-lg-none">
                    <p>{{ $blog->short_description }}</p>
                </div>
                <div class="blog-publish-text my-25px my-lg-75px my-xxl-125px text-default-50 l-space-1-2 fw-500">
                    <div class="blog-publish-description">
                        {!! $blog->description !!}
                    </div>
                    @if (get_setting('facebook_comment') == 1)
                        <div class="mt-10px mt-md-20px">
                            <div class="fb-comments" data-href="{{ route("blog",$blog->slug) }}" data-width="" data-numposts="5"></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @php
            $photos = explode(',', $blog->photos);
            $counter=1;
        @endphp
        @if(count($photos) > 0)
            <div class="sly-frame-content gutters-5 lg-gutters-10 xxl-gutters-15">
                <div class="sly-frame blog-publish-gallery-carousel">
                    <ul class="d-flex h-auto">
                        @foreach ($photos as $key => $photo)
                            <li>
                                <div class="px-5px px-lg-10px px-xxl-15px">
                                    <div class="blog-publish-gallery-car-image">
                                        <img class="img-fit h-100" src="{{ uploaded_asset($photo) }}">
                                    </div>
                                </div>
                            </li>
                            @php
                                $counter++;
                            @endphp
                        @endforeach
                    </ul>
                </div>
            </div>
            <style>
                :root {
                    --gallerynumbs: {{$counter}};
                }
            </style>
        @endif
    </div>
@endsection

@section('script')
    <script>
      $(window).on("load", function() {
        $('.blog-publish-gallery-carousel').sly({
          horizontal: 1,
          itemNav: 'basic',
          smart: 1,
          activateMiddle: 1,
          activateOn: 'click',
          mouseDragging: 1,
          touchDragging: 1,
          releaseSwing: 1,
          startAt: 0,
          scrollBy: 0,
          speed: 300,
          elasticBounds: 1,
          easing: 'easeOutExpo',
          dragHandle: 1,
          dynamicHandle: 1,
          clickBar: 1,
        });
      });
    </script>
    @if (get_setting('facebook_comment') == 1)
        <div id="fb-root"></div>
        <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v9.0&appId={{ env('FACEBOOK_APP_ID') }}&autoLogAppEvents=1" nonce="ji6tXwgZ"></script>
    @endif
@endsection