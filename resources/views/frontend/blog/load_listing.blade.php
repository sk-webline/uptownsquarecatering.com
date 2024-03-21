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