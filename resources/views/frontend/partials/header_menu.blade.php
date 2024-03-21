@php
    $land = \App\ProductType::find(1);
    $water = \App\ProductType::find(2);
    $webshop = null;
@endphp
<ul class="header-menu-list d-flex justify-content-center">
    <li>
        <a href="javascript:void(0);">{{toUpper(translate('Land'))}}</a>
        @include('frontend.partials.header_menu_categories', ['type' => $land])
    </li>
    <li>
        <a href="javascript:void(0);">{{toUpper(translate('Water'))}}</a>
        @include('frontend.partials.header_menu_categories', ['type' => $water])
    </li>
    @if( get_setting('show_webshop') == 'on')
        <li>
            <a href="javascript:void(0);">{{toUpper(translate('Webshop'))}}</a>
            @include('frontend.partials.header_menu_categories', ['type' => $webshop])
        </li>
    @endif
</ul>
