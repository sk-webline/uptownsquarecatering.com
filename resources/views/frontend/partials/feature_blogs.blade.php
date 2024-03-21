@php
    $feat_blogs = \App\Blog::where('featured', 1)->get();
@endphp
@if(count($feat_blogs) > 0)
    <div id="feature-blogs" class="mt-50px mt-lg-100px mt-xxl-150px overflow-hidden">
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
@endif