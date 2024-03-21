@extends('frontend.layouts.app')

@section('meta_title'){{ translate('News') }}@stop

@section('content')
<div id="news-feature" class="my-50px my-lg-90px my-xxl-125px overflow-hidden">
    <div class="container">
        <h1 class="fs-30 lg-fs-50 xxl-fs-70 text-default-50 l-space-1-2 fw-700 font-play mb-35px mb-lg-80px mb-xxl-125px">
            {{translate('News in the')}} <span class="text-secondary">{{translate('adventure world')}}</span>
        </h1>
    </div>
    @php
        $feat_blogs = \App\Blog::where('featured', 1)->get();
    @endphp
    <div class="container-left">
        <div class="blog-carousel-container mb-10px mb-lg-20px mb-xxl-30px">
            <div class="sk-carousel blog-carousel gutters-5 lg-gutters-10 xxl-gutters-15" data-auto-width="true" data-center="false" data-items="2" data-xs-items="1" data-arrows='false' data-infinite='true'>
                @php
                    $counter = 1;
                @endphp
                @foreach($feat_blogs as $feat_blog)
                    @if($counter % 3 != 0)
                        <div class="carousel-box blog-car-res-item blog-small">
                            <a href="{{ url("blog").'/'. $feat_blog->slug }}">
                                    <span class="d-block blog-car-res-wrap">
                                        <span class="d-block blog-car-res-image">
                                            <img
                                                    src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                                                    data-src="{{ uploaded_asset($feat_blog->banner) }}"
                                                    alt=""
                                                    class="img-fit absolute-full h-100 lazyload"
                                            >
                                        </span>
                                        <span class="d-flex flex-column justify-content-between p-15px p-lg-25px px-xl-40px py-xl-30px text-white-80 l-space-1-2 blog-car-res-over">
                                            <span class="d-block fs-11 sm-fs-14 fw-500 blog-car-res-date">
                                                {{ toUpper(date('d M Y', strtotime($feat_blog->created_at))) }}
                                            </span>
                                            <span class="d-block mt-10px">
                                                <span class="d-block fs-14 md-fs-32 xxl-fs-50 fw-700 font-play text-white lh-1 mb-lg-15px blog-car-res-title">{{ $feat_blog->title }}</span>
                                                <span class="d-none d-lg-block blog-car-res-desc">{{ $feat_blog->short_description }}</span>
                                            </span>
                                        </span>
                                    </span>
                            </a>
                        </div>
                    @endif
                    @php
                        $counter++;
                    @endphp
                @endforeach
            </div>
        </div>
        @if(count($feat_blogs) > 2)
            <div class="blog-carousel-container">
                <div class="sk-carousel blog-carousel gutters-5 lg-gutters-10 xxl-gutters-15" data-auto-width="true" data-center="false" data-items="1" data-arrows='false' data-infinite='true'>
                    @php
                        $counter = 1;
                    @endphp
                    @foreach($feat_blogs as $feat_blog)
                        @if($counter % 3 == 0)
                            <div class="carousel-box blog-car-res-item">
                                <a href="{{ url("blog").'/'. $feat_blog->slug }}">
                                        <span class="d-block blog-car-res-wrap">
                                            <span class="d-block blog-car-res-image">
                                                <img
                                                        src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                                                        data-src="{{ uploaded_asset($feat_blog->banner) }}"
                                                        alt=""
                                                        class="img-fit absolute-full h-100 lazyload"
                                                >
                                            </span>
                                            <span class="d-flex flex-column justify-content-between p-15px p-lg-25px px-xl-40px py-xl-30px text-white-80 l-space-1-2 blog-car-res-over">
                                                <span class="d-block fs-11 sm-fs-14 fw-500 blog-car-res-date">
                                                    {{ toUpper(date('d M Y', strtotime($feat_blog->created_at))) }}
                                                </span>
                                                <span class="d-block mt-10px">
                                                    <span class="d-block fs-14 md-fs-32 xxl-fs-50 fw-700 font-play text-white lh-1 mb-lg-15px blog-car-res-title">{{ $feat_blog->title }}</span>
                                                    <span class="d-none d-lg-block blog-car-res-desc">{{ $feat_blog->short_description }}</span>
                                                </span>
                                            </span>
                                        </span>
                                </a>
                            </div>
                        @endif
                        @php
                            $counter++;
                        @endphp
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
<div id="news-results" class="bg-default-10 pb-50px py-lg-50px py-xxl-100px overflow-hidden">
    <div class="container">
        <div class="row xxl-gutters-20 news-results-load">
            @foreach($blogs as $blog)
                <div class="col-sm-6 col-lg-4 my-25px my-lg-35px my-xxl-45px blog-res-item">
                    <a href="{{ url("blog").'/'. $blog->slug }}">
                        <span class="d-block blog-res-wrap">
                            <span class="d-block blog-res-image">
                                <img
                                        src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                                        data-src="{{ uploaded_asset($blog->banner) }}"
                                        alt="{{ $blog->title }}"
                                        class="img-fit absolute-full h-100 lazyload "
                                >
                                <span class="d-block fs-11 sm-fs-14 fw-500 px-10px px-lg-15px px-xxl-25px py-5px py-lg-10px py-xxl-15px blog-res-date">
                                    {{ toUpper(date('d M Y', strtotime($blog->created_at))) }}
                                </span>
                            </span>
                            <span class="d-block bg-white p-10px p-lg-15px p-xxl-25px blog-res-text">
                                 <span class="fs-13 xl-fs-19 xxl-fs-25 fw-700 font-play l-space-1-2 text-truncate-5 blog-res-title">{{ $blog->title }}</span>
                            </span>
                        </span>
                    </a>
                </div>
            @endforeach
        </div>
        <div class="row gutters-20 justify-content-center mt-15px mt-sm-0">
            <div class="col-sm-6 col-lg-4">
                @if($has_next_blogs)
                    <div id="load-more" class="btn btn-block btn-outline-black fs-13 lg-fs-15 xxl-fs-18 py-10px py-lg-15px py-xxl-20px l-space-1-2" data-page="1">{{toUpper(translate('Load More'))}}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        $(document).on('click', '#load-more:not(.no-more)', function () {
          $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:"POST",
            url: '{{ route('blog.load_blog') }}',
            data: {
              page: $('#load-more').data('page')
            },
            beforeSend: function() {
              $('#load-more').addClass('loading');
            },
            success: function(data){
              var page = $('#load-more').data('page') + 1;
              $('#load-more').removeClass('loading');
              $('#load-more').data('page', page);
              if(data.has_next == false) {
                $('#load-more').addClass('no-more');
                $('#load-more').html('{{toUpper(translate("No More Items"))}}');
              }
              $('.news-results-load').append(data.view);
            }
          });
        });
    </script>
@endsection