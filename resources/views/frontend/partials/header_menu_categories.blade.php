@php
    $used_products = null;
    if($type != null) {
      $menu_categories = getMainCategoriesByType($type->id, null, 1);
      $used_products = filter_products(\App\Product::where('published', 1)->where('type_id', $type->id)->where('used', '1'))->get();
    } else {
      $menu_categories = getWebshopMainCategories(null, 1);
    }
@endphp
@if(count($menu_categories) > 0)
    <div class="header-submenu pt-50px bg-white border-top border-default-100">
        <div class="container">
            <div class="swiper header-submenu-swiper without-pseudos">
                <div class="swiper-wrapper">
                    @foreach($menu_categories as $menu_category)
                        @php
                            if($type != null) {
                                $link = route('typecat.maincategory', ['category_slug'=>$menu_category->slug,'type_slug'=>$type->slug]);
                            } else {
                                $link = route('maincategory', $menu_category->slug);
                            }
                        @endphp
                        <div class="swiper-slide auto">
                            <a href="{{$link}}">
                                <span class="d-block mw-250px header-sub-cat-wrap">
                                    <span class="d-block header-sub-cat-image">
                                        <img class="img-contain h-130px lazyload"
                                             src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                             data-src="{{uploaded_asset($menu_category->icon)}}"
                                             onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                             alt="">
                                    </span>
                                    <span class="d-block fs-17 fw-500 mt-25px text-center header-sub-cat-title">
                                        <span class="d-inline-block header-sub-cat-title-wrap">
                                            {{$menu_category->getTranslation('name')}}
                                        </span>
                                    </span>
                                </span>
                            </a>
                        </div>
                    @endforeach
                    @if($used_products != null && count($used_products) > 0 && $type != null)
                        @php
                            $type_text = ($type->id==2) ? 'water' : 'land';
                        @endphp
                        <div class="swiper-slide auto">
                            <a href="{{route('used_page')}}?type={{$type_text}}">
                                <span class="d-block mw-250px header-sub-cat-wrap">
                                    <span class="d-block header-sub-cat-image">
                                        <img class="img-contain h-130px lazyload"
                                             src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                             data-src="{{uploaded_asset(get_setting('used_'.$type_text.'_icon'))}}"
                                             onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                             alt="">
                                    </span>
                                    <span class="d-block fs-17 fw-500 mt-25px text-center header-sub-cat-title">
                                        <span class="d-inline-block header-sub-cat-title-wrap">{{translate('Used')}}</span>
                                    </span>
                                </span>
                            </a>
                        </div>
                    @endif
                </div>
                <div class="mt-50px position-relative">
                    <div class="swiper-scrollbar"></div>
                </div>
            </div>
        </div>
    </div>
@endif
