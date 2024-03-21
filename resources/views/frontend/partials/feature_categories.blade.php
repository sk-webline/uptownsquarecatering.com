@php
    $feat_categories = getAvailableMainFeatureCategories();
    $counter = 0;
    $cat_row = 1;
    $tablet_counter = 0;
    $tablet_cat_row = 1;
@endphp
@if(count($feat_categories) > 0)
    <div id="feature-categories" class="mt-50px mt-lg-100px mt-xxl-150px position-relative overflow-hidden">
        <div class="row gutters-2 md-gutters-5">
            @foreach($feat_categories as $feat_category)
                @php
                    $feat_sub_cats = getAvailableSubCategoriesByCategory($feat_category->id);
                @endphp
                <div class="col-6 col-lg-4 mb-5px mb-md-10px feature-cat-res-item">
                    @if(count($feat_sub_cats) > 0)
                        <div class="text-center position-relative feature-cat-res-wrap" data-row="{{$cat_row}}" data-tablet-row="{{$tablet_cat_row}}" data-id="{{$feat_category->id}}">
                            <div class="d-flex align-items-center justify-content-center flex-column p-10px p-md-15px p-lg-20px absolute-full feature-cat-res-over">
                                <div class="feature-cat-res-image">
                                    <img class="img-contain h-65px h-sm-100px h-md-125px h-lg-100px h-xl-150px h-xxl-200px h-xxxl-250px lazyload"
                                         src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>"
                                         data-src="{{uploaded_asset($feat_category->icon)}}"
                                         onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>';"
                                         alt=""
                                    >
                                </div>
                                <h3 class="mb-0 mt-5px mt-md-10px mt-lg-25px fs-12 sm-fs-14 lg-fs-20 xxl-fs-25 font-play fw-400 feature-cat-res-title">{{toUpper($feat_category->getTranslation('name'))}}</h3>
                            </div>
                        </div>
                    @else
                        <a href="{{route('products.category', $feat_category->slug)}}">
                            <span class="d-block feature-cat-res-wrap-no-click" data-row="{{$cat_row}}" data-tablet-row="{{$tablet_cat_row}}" data-id="{{$feat_category->id}}">
                                <span class="d-flex align-items-center justify-content-center flex-column p-10px p-md-15px p-lg-20px absolute-full feature-cat-res-over">
                                    <span class="d-block feature-cat-res-image">
                                        <img class="img-contain h-65px h-sm-100px h-md-125px h-lg-100px h-xl-150px h-xxl-200px h-xxxl-250px lazyload"
                                             src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>"
                                             data-src="{{uploaded_asset($feat_category->icon)}}"
                                             onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>';"
                                             alt=""
                                        >
                                    </span>
                                    <span class="d-block mb-0 mt-5px mt-md-10px mt-lg-25px fs-12 sm-fs-14 lg-fs-20 xxl-fs-25 font-play fw-400 feature-cat-res-title">{{toUpper($feat_category->getTranslation('name'))}}</span>
                                </span>
                            </span>
                        </a>
                    @endif
                </div>
                @php
                    $counter++;
                    $tablet_counter++;
                @endphp
                @if($tablet_counter==2)
                    <div class="col-12 mb-5px mb-md-10px feature-cat-res-row-tablet" data-tablet-row="{{$tablet_cat_row}}"></div>
                    @php
                        $tablet_counter = 0;
                        $tablet_cat_row++;
                    @endphp
                @endif
                @if($counter==3)
                    <div class="col-12 mb-5px mb-md-10px feature-cat-res-row" data-row="{{$cat_row}}"></div>
                    @php
                        $counter = 0;
                        $cat_row++;
                    @endphp
                @endif
            @endforeach
            @if($tablet_counter!=0)
                <div class="col-12 mb-5px mb-md-10px feature-cat-res-row-table" data-row="{{$tablet_cat_row}}"></div>
            @endif
            @if($counter!=0)
                <div class="col-12 mb-5px mb-md-10px feature-cat-res-row" data-row="{{$cat_row}}"></div>
            @endif

        </div>
    </div>
    <div id="feature-categories-clones" class="d-none">
        @foreach($feat_categories as $feat_category)
            @php
                $feat_sub_cats = getAvailableSubCategoriesByCategory($feat_category->id);
            @endphp
            @if(count($feat_sub_cats) > 0)
                <div class="feature-cat-res-dropdown desktop py-10px py-lg-30px" data-id="{{$feat_category->id}}">
                    <div class="container-left">
                        <div class="overflow-hidden">
                            <div class="sly-frame-content gutters-10 xl-gutters-40">
                                <div class="sly-frame feature-carousel">
                                    <ul class="d-flex">
                                        @foreach($feat_sub_cats as $feat_sub_cat)
                                            <li>
                                                <div class="carousel-box">
                                                    <div class="w-150px w-sm-220px w-md-250px w-xl-330px w-xxl-420px w-xxxl-450px">
                                                        <a href="{{route('products.category', $feat_sub_cat->slug)}}">
                                                            <span class="d-block text-center position-relative feature-subcat-res-wrap">
                                                                <span class="d-flex align-items-center justify-content-center flex-column p-10px p-md-15px p-lg-20px absolute-full feature-cat-res-over">
                                                                    <span class="d-block feature-cat-res-image">
                                                                        <img class="img-contain h-65px h-sm-100px h-md-125px h-lg-100px h-xl-150px h-xxl-200px h-xxxl-250px lazyload"
                                                                             src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>"
                                                                             data-src="{{uploaded_asset($feat_sub_cat->icon)}}"
                                                                             onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>';"
                                                                             alt=""
                                                                        >
                                                                    </span>
                                                                    <span class="d-block mt-5px mt-md-10px mt-lg-25px fs-12 sm-fs-14 lg-fs-20 fw-500 feature-cat-res-title">{{$feat_sub_cat->getTranslation('name')}}</span>
                                                                </span>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="mt-10px mt-lg-30px sly-scrollbar feature-carousel-scrollbar">
                            <div class="sly-handle">
                                <div class="sly-mousearea"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="feature-cat-res-dropdown tablet py-10px py-lg-30px" data-id="{{$feat_category->id}}">
                    <div class="container-left">
                        <div class="overflow-hidden">
                            <div class="sly-frame-content gutters-10 xl-gutters-40">
                                <div class="sly-frame feature-carousel">
                                    <ul class="d-flex">
                                        @foreach($feat_sub_cats as $feat_sub_cat)
                                            <li>
                                                <div class="carousel-box">
                                                    <div class="w-150px w-sm-220px w-md-250px w-xl-330px w-xxl-420px w-xxxl-450px">
                                                        <a href="{{route('products.category', $feat_sub_cat->slug)}}">
                                                            <span class="d-block text-center position-relative feature-subcat-res-wrap">
                                                                <span class="d-flex align-items-center justify-content-center flex-column p-10px p-md-15px p-lg-20px absolute-full feature-cat-res-over">
                                                                    <span class="d-block feature-cat-res-image">
                                                                        <img class="img-contain h-65px h-sm-100px h-md-125px h-lg-100px h-xl-150px h-xxl-200px h-xxxl-250px lazyload"
                                                                             src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>"
                                                                             data-src="{{uploaded_asset($feat_sub_cat->icon)}}"
                                                                             onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>';"
                                                                             alt=""
                                                                        >
                                                                    </span>
                                                                    <span class="d-block mt-5px mt-md-10px mt-lg-25px fs-12 sm-fs-14 lg-fs-20 fw-500 feature-cat-res-title">{{$feat_sub_cat->getTranslation('name')}}</span>
                                                                </span>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="mt-10px mt-lg-30px sly-scrollbar feature-carousel-scrollbar">
                            <div class="sly-handle">
                                <div class="sly-mousearea"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endif
