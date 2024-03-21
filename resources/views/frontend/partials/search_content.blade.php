<?php /*
<div class="">
    @if (sizeof($keywords) > 0)
        <div class="px-2 py-1 text-uppercase fs-10 text-right text-muted bg-soft-secondary">{{translate('Popular Suggestions')}}</div>
        <ul class="list-group list-group-raw">
            @foreach ($keywords as $key => $keyword)
                <li class="list-group-item py-1">
                    <a class="text-reset hov-text-primary" href="{{ route('suggestion.search', $keyword) }}">{{ $keyword }}</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
<div class="">
    @if (count($categories) > 0)
        <div class="px-2 py-1 text-uppercase fs-10 text-right text-muted bg-soft-secondary">{{translate('Category Suggestions')}}</div>
        <ul class="list-group list-group-raw">
            @foreach ($categories as $key => $category)
                <li class="list-group-item py-1">
                    <a class="text-reset hov-text-primary" href="{{ route('products.category', $category->slug) }}">{{ $category->getTranslation('name') }}</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>*/ ?>
<div class="">
    @if (count($products) > 0)
        <div class="px-2 py-1 text-uppercase fs-10 text-white bg-secondary">{{translate('Products')}}</div>
        <ul class="list-group list-group-raw">
            @foreach ($products as $key => $product)
                <li class="list-group-item px-10px">
                    <a class="text-reset" href="{{ route('product', $product->slug) }}">
                        <div class="d-flex search-product align-items-center">
                            <div class="mr-2">
                                <img class="size-45px img-contain" src="{{ uploaded_asset($product->thumbnail_img) }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                            </div>
                            <div class="flex-grow-1 minw-0">
                                <div class="product-name text-truncate fs-14 mb-5px text-truncate">
                                    {{  $product->getTranslation('name')  }}
                                </div>
                                <div class="fs-10">
                                    @if(home_base_price($product->id) != home_discounted_base_price($product->id))
                                        <del class="opacity-60">{{ home_base_price($product->id) }}</del>
                                    @endif
                                    <span class="fw-600 text-primary">{{ home_discounted_base_price($product->id) }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
<?php /*
@if(\App\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1)
    <div class="">
        @if (count($shops) > 0)
            <div class="px-2 py-1 text-uppercase fs-10 text-right text-muted bg-soft-secondary">{{translate('Shops')}}</div>
            <ul class="list-group list-group-raw">
                @foreach ($shops as $key => $shop)
                    <li class="list-group-item">
                        <a class="text-reset" href="{{ route('shop.visit', $shop->slug) }}">
                            <div class="d-flex search-product align-items-center">
                                <div class="mr-3">
                                    <img class="size-40px img-fit rounded" src="{{ uploaded_asset($shop->logo) }}">
                                </div>
                                <div class="flex-grow-1 overflow--hidden">
                                    <div class="product-name text-truncate fs-14 mb-5px">
                                        {{ $shop->name }}
                                    </div>
                                    <div class="opacity-60">
                                        {{ $shop->address }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endif */?>
