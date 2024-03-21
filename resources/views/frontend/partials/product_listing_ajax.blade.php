@php
    $data_category_id = (isset($category_id)) ? $category_id : null;
    $data_brand_id = (isset($brand_id)) ? $brand_id : null;
    $data_type_id = (isset($type_id)) ? $type_id : null;
    $category = (isset($category_id)) ? \App\Category::find($category_id) : 0;
    $main_category = (isset($category_id) && $category->parent_id > 0) ? \App\Category::find(getMainCategory($category->id)->id) : null;
@endphp
<div class="line-slider-item overflow-hidden">
    <div class="line-slider-image line-slider-small">
        @if (isset($category_id))
            <img
                class="absolute-full h-100 img-fit"
                src="{{ uploaded_asset(\App\Category::find($category_id)->header) }}"
                alt=""
                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
            >
        @elseif($outlet==1)
            <img
                class="absolute-full h-100 img-fit"
                src="{{ static_asset('assets/img/outlet-header.jpg') }}"
                alt=""
                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
            >
        @else
            <img
                class="absolute-full h-100 img-fit"
                src="{{ static_asset('assets/img/webshop-header.jpg') }}"
                alt=""
                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
            >
        @endif
        <div class="line-slider-over">
            <div class="row no-gutters">
                <div class="col-lg-4">
                    <div class="line-slider-over-left">
                        <div class="line-slider-over-box breadcrumb-line d-lg-flex justify-content-center">
                            <div class="line-slider-over-box-inner p-xxl-20px">
                                @if(isset($brand_id))
                                    @php
                                        $brand = \App\Brand::find($brand_id);
                                    @endphp

                                    <ul class="breadcrumb fs-10 md-fs-12">
                                        <li class="breadcrumb-item">
                                            <a class="hov-text-primary" href="{{route('brand_page', $brand->slug)}}">{{ $brand->getTranslation('name') }}</a>
                                        </li>
                                        @if($main_category)
                                            <li class="breadcrumb-item">
                                                <a class="hov-text-primary" href="{{ route('brand.maincategory', ['category_slug'=>$main_category->slug,'brand_slug'=>$brand->slug]) }}">{{ $main_category->getTranslation('name') }}</a>
                                            </li>
                                        @endif
                                        <li class="breadcrumb-item">
                                            <a class="hov-text-primary" href="{{ route('products.brand_category', ['category_slug'=>$category->slug,'brand_slug'=>$brand->slug]) }}">{{ $category->getTranslation('name') }}</a>
                                        </li>
                                    </ul>
                                @elseif(isset($type_id))
                                    @php
                                        $type = \App\ProductType::find($type_id);
                                    @endphp
                                    <ul class="breadcrumb fs-10 md-fs-12">
                                        <li class="breadcrumb-item">
                                            <a class="hov-text-primary" href="{{route('type.maincategory', $type->slug)}}">{{ $type->getTranslation('name') }}</a>
                                        </li>
                                        @if($main_category)
                                            <li class="breadcrumb-item">
                                                <a class="hov-text-primary" href="{{ route('typecat.maincategory', ['category_slug'=>$main_category->slug,'type_slug'=>$type->slug]) }}">{{ $main_category->getTranslation('name') }}</a>
                                            </li>
                                        @endif
                                        <li class="breadcrumb-item">
                                            <a class="hov-text-primary" href="{{ route('products.type_category', ['category_slug'=>$category->slug,'type_slug'=>$type->slug]) }}">{{ $category->getTranslation('name') }}</a>
                                        </li>
                                    </ul>
                                @elseif(isset($category_id))
                                    <ul class="breadcrumb fs-10 md-fs-12">
                                        <li class="breadcrumb-item">
                                            <a class="hov-text-primary" href="{{route('search')}}">{{ translate('Webshop') }}</a>
                                        </li>
                                        @php
                                            $breadcrumb_categories = array_reverse(getCategoryParents(\App\Category::findOrFail($category_id)->parent_id));
                                        @endphp
                                        @foreach($breadcrumb_categories as $breadcrumb_category)
                                            <li class="breadcrumb-item">
                                                <a class="hov-text-primary" href="{{ route('products.category', $breadcrumb_category['slug']) }}?outlet={{$outlet}}">{{ $breadcrumb_category['name'] }}</a>
                                            </li>
                                        @endforeach
                                        <li class="breadcrumb-item">
                                            <a class="hov-text-primary" href="{{ route('products.category', \App\Category::findOrFail($category_id)->slug) }}?outlet={{$outlet}}"> {{ \App\Category::find($category_id)->getTranslation('name') }}</a>
                                        </li>
                                    </ul>
                                @endif
                            </div>
                        </div>
                        <div class="line-slider-over-box slogan d-lg-flex justify-content-center">
                            <div class="line-slider-over-box-inner p-xxl-20px">
                                <h1 class="font-play fs-25 md-fs-30 xl-fs-45 xxl-fs-60 fw-700 m-0">
                                    @if(isset($category_id))
                                        {{ \App\Category::find($category_id)->getTranslation('name') }}
                                    @elseif(isset($query))
                                        {{ translate('Search result for ') }}{{ $query }}
                                    @else
                                        @if($outlet==1)
                                            {{translate('Outlet')}}
                                        @else
                                            {{translate('Webshop')}}
                                        @endif
                                    @endif
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="product-results">
    <form class="" id="search-form" action="" method="GET">
        <input type="hidden" name="q" value="{{$query}}">
        <input type="hidden" name="data_category_id" value="{{$data_category_id}}">
        <input type="hidden" name="data_brand_id" value="{{$data_brand_id}}">
        <input type="hidden" name="data_type_id" value="{{$data_type_id}}">
        <input type="hidden" name="products_per_page" value="{{$products_per_page}}">
        <input type="hidden" name="page_products_count" value="{{$page_products_count}}">
        <input type="hidden" name="outlet" value="{{$outlet}}">
        <input type="hidden" name="page" value="{{$page}}">
        <input type="hidden" name="all_products" value="{{$all_products}}">
        @csrf
        <div class="row no-gutters">
            <div class="col-lg-4">
                <div class="bg-black-10 container-left pt-20px pt-md-60px pt-xxl-100px pb-10px pb-md-40px sticky-top z-0 overflow-hidden">
                    <h2 class="fs-16 md-fs-18 fw-500 text-default-30 pb-15px pr-15px pr-sm-25px border-bottom border-black-100 results-line-height mb-15px mb-md-45px">
                        @if($outlet==1)
                            {{toUpper(translate('Outlet'))}}
                        @else
                            {{toUpper(translate('Webshop'))}}
                        @endif
                    </h2>
                    <div class="pr-15px pr-sm-25px">
                        <div class="mw-300px">
                            <ul class="list-unstyled fw-700 fs-16 xl-fs-18 xxl-fs-20 results-categories-list">
                                @php
                                    $side_categories = getSidebarCategories($outlet);
                                @endphp
                                @if(count($side_categories) > 0)
                                    @foreach ($side_categories as $category)
                                        @php
                                            $side_subcategories = getSidebarCategories($outlet, $category->id);
                                            $active_category = (isset($category_id) && $category_id == $category->id) ? true : false;
                                        @endphp
                                        <li @if($active_category) class="active" @endif >
                                            @if(count($side_subcategories) > 0)
                                                <div class="results-category-toggle">
                                                    <div class="results-categories-link" data-outlet="{{$outlet}}" data-category="{{ $category->id }}" data-href="{{ route('products.category', $category->slug) }}?outlet={{$outlet}}">
                                                        {{ $category->getTranslation('name') }}
                                                    </div>
                                                </div>
                                                <ul>
                                                    @foreach($side_subcategories as $side_subcategory)
                                                        @php
                                                            $active_subcategory = (isset($category_id) && $category_id == $side_subcategory->id) ? true : false;
                                                        @endphp
                                                        <li @if($active_subcategory) class="active" @endif>
                                                            <div class="results-categories-link" data-outlet="{{$outlet}}" data-category="{{ $side_subcategory->id }}" data-href="{{ route('products.category', $side_subcategory->slug) }}?outlet={{$outlet}}">
                                                                {{ $side_subcategory->getTranslation('name') }}
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="results-categories-link" data-outlet="{{$outlet}}" data-category="{{ $category->id }}" data-href="{{ route('products.category', $category->slug) }}?outlet={{$outlet}}">
                                                    {{ $category->getTranslation('name') }}
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                @endif
                                <li>
                                    @if($outlet==1)
                                        <div class="results-categories-link" data-outlet="0" data-category="" data-href="{{route('search')}}?outlet=0">{{translate('Webshop')}}</div>
                                    @else
                                        @php
                                            $outlet_products = getSidebarCategories(1);
                                        @endphp
                                        @if(count($outlet_products) > 0)
                                            <div class="results-categories-link" data-outlet="1" data-category="" data-href="{{route('search')}}?outlet=1">{{translate('Outlet')}}</div>
                                        @endif
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 overflow-hidden">
                <div class="container-left pl-lg-0 container-right">
                    <div class="pt-20px pt-md-60px pt-xxl-100px pb-70px pb-lg-100px pb-xxl-125px">
                        <div class="border-lg-bottom border-black-100 results-line-height pb-lg-5px pl-lg-20px pl-xxl-50px mb-40px mb-md-45px">
                            <div class="row align-items-end">
                                <div class="col">
                                    <h2 class="fs-11 lg-fs-13 xxl-fs-16 fw-500 text-default-50 m-0 lh-1">
                                        @if(isset($category_id))
                                            {{toUpper(translate('All'))}} {{ toUpper(\App\Category::find($category_id)->getTranslation('name')) }}
                                        @elseif(isset($query))
                                            {{ toUpper(translate('Searches for ')) }}{{ toUpper($query) }}
                                        @else
                                            {{ toUpper(translate('All Products')) }}
                                        @endif
                                    </h2>
                                </div>
                                <div class="col-auto fs-11 lg-fs-13 xxl-fs-16 fw-500 text-default-50 d-none d-lg-block">
                                    <div class="row gutters-2 align-items-center justify-content-end">
                                        <div class="col-auto">
                                            {{ translate('Sort by')}}:
                                        </div>
                                        <div class="col-auto">
                                            <div class="form-group w-200px m-0">
                                                <select class="form-control form-control-sm sk-selectpicker form-control-unstyled select-unstyled" name="sort_by" onchange="filter()">
                                                    <option value="newest" @isset($sort_by) @if ($sort_by == 'newest') selected @endif @endisset>
                                                        {{ translate('Newest to oldest')}}
                                                    </option>
                                                    <option value="oldest" @isset($sort_by) @if ($sort_by == 'oldest') selected @endif @endisset>
                                                        {{ translate('Oldest to newest')}}
                                                    </option>
                                                    <option value="price-asc" @isset($sort_by) @if ($sort_by == 'price-asc') selected @endif @endisset>
                                                        {{ translate('Price low to high')}}
                                                    </option>
                                                    <option value="price-desc" @isset($sort_by) @if ($sort_by == 'price-desc') selected @endif @endisset>
                                                        {{ translate('Price high to low')}}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pl-lg-20px pl-xxl-50px">
                            <input type="hidden" name="min_price" value="">
                            <input type="hidden" name="max_price" value="">
                            @if(count($products) > 0)
                                <div class="row gutters-10 xxl-gutters-25 row-cols-xl-3 row-cols-lg-2 row-cols-md-3 row-cols-sm-2 row-cols-1 products-results-load">
                                    @foreach ($products as $key => $product)
                                        <div class="col mb-50px mb-lg-70px mb-xxl-90px product-res-item">
                                            @include('frontend.partials.product_listing.forsale_listing',['product' => $product, 'type_id' => $type_id , 'brand_id' => $brand_id])
                                        </div>
                                    @endforeach
                                </div>
                                <div class="row gutters-10 xxl-gutters-25 row-cols-xl-3 row-cols-lg-2 row-cols-md-3 row-cols-sm-2 row-cols-1 justify-content-center">
                                    @if($has_next_products)
                                        <div class="col">
                                            <div id="load-more" class="btn btn-block btn-outline-black fs-18 py-10px l-space-1-2" data-page="1">
                                                {{toUpper(translate('Load More'))}}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-grey text-center fw-700 my-25px my-md-50px">{{translate('There are no products under this category')}}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $('.results-categories-list .active').parent('ul').parent('li').addClass('active');
</script>
