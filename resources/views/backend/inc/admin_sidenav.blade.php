<div class="sk-sidebar-wrap">
    <div class="sk-sidebar left c-scrollbar">
        <div class="sk-side-nav-logo-wrap">
            <a href="{{ route('admin.dashboard') }}" class="d-block text-left">
{{--                @if(get_setting('system_logo_white') != null)--}}
{{--                    <img class="mw-100" src="{{ uploaded_asset(get_setting('system_logo_white')) }}" class="brand-icon"--}}
{{--                         alt="{{ get_setting('site_name') }}">--}}
{{--                @else--}}
{{--                    <img class="mw-100" src="{{ static_asset('assets/img/logo.png') }}" class="brand-icon"--}}
{{--                         alt="{{ get_setting('site_name') }}">--}}
{{--                @endif--}}

                <svg class="" xmlns="http://www.w3.org/2000/svg" height="80" width="80"
                     viewBox="0 0 122.47 76.24">
                    <use xlink:href="{{static_asset('assets/img/icons/logo_admin.svg')}}#uptown-logo-admin"></use>
                </svg>
            </a>
        </div>
        <div class="sk-side-nav-wrap">
            <div class="px-20px mb-3">
                <input class="form-control bg-soft-secondary border-0 form-control-sm text-white" type="text" name=""
                       placeholder="{{ translate('Search in menu') }}" id="menu-search" onkeyup="menuSearch()">
            </div>
            <ul class="sk-side-nav-list" id="search-menu">
            </ul>
            <ul class="sk-side-nav-list" id="main-menu" data-toggle="sk-side-menu">
                <li class="sk-side-nav-item">
                    <a href="{{route('admin.dashboard')}}" class="sk-side-nav-link">
                        <i class="las la-home sk-side-nav-icon"></i>
                        <span class="sk-side-nav-text">{{translate('Dashboard')}}</span>
                    </a>
                </li>
                @if((Auth::user()->user_type == 'admin' || in_array('organisations', json_decode(Auth::user()->staff->role->permissions))))
                <li class="sk-side-nav-item">
                    <a href="{{route('organisations.index')}}" class="sk-side-nav-link {{ areActiveRoutes(['organisations.index', 'organisations.create', 'organisations.create', 'catering.index', 'organisation_settings.edit',
                                               'organisation_settings.create', 'catering.index', 'organisation_locations.create', 'organisation_locations.edit' ,
                                                'organisation_cards.index', 'organisation_prices.create', 'catering_plans.index','catering_plans.create', 'catering_plans.edit',
                                                 'canteen.index', 'canteen_settings.create', 'canteen_settings.edit','canteen_locations.create', 'canteen_locations.edit',
                                                 'canteen_menu.index', 'canteen_menu.edit'])}}" id="organisation-link">
                        <i class="las la-home sk-side-nav-icon"></i>
                        <span class="sk-side-nav-text">{{translate('Organisations')}}</span>
                    </a>
                </li>
                @endif


                @if(Auth::user()->user_type == 'admin' || in_array('reports', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="sk-side-nav-item">
                        <a href="#" class="sk-side-nav-link">
                            <i class="las la-file-alt sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Reports') }}</span>
                            <span class="sk-side-nav-arrow"></span>
                        </a>
                        <ul class="sk-side-nav-list level-2">
                            @if((Auth::user()->user_type == 'admin' || in_array('catering_reports', json_decode(Auth::user()->staff->role->permissions))))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('catering_reports.index')}}" class="sk-side-nav-link {{ areActiveRoutes(['catering_reports.index', 'catering_reports.show'])}}" >
                                        <i class="las la-file-alt sk-side-nav-icon"></i>
                                        <span class="sk-side-nav-text">{{translate('Catering Reports')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if((Auth::user()->user_type == 'admin' || in_array('meal_reports', json_decode(Auth::user()->staff->role->permissions))))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('meal_reports.index')}}" class="sk-side-nav-link {{ areActiveRoutes(['meal_reports.index', 'meal_reports.show'])}}" >
                                        <i class="las la-file-alt sk-side-nav-icon"></i>
                                        <span class="sk-side-nav-text">{{translate('Catering Meal Reports')}}</span>
                                    </a>
                                </li>
                            @endif

                                @if((Auth::user()->user_type == 'admin' || in_array('canteen_reports', json_decode(Auth::user()->staff->role->permissions))))
                                    <li class="sk-side-nav-item">
                                        <a href="{{route('canteen_reports.index')}}" class="sk-side-nav-link {{ areActiveRoutes(['canteen_reports.index', 'canteen_reports.show'])}}" >
                                            <i class="las la-file-alt sk-side-nav-icon"></i>
                                            <span class="sk-side-nav-text">{{translate('Canteen Reports')}}</span>
                                        </a>
                                    </li>
                                @endif
                                @if((Auth::user()->user_type == 'admin' || in_array('canteen_meal_reports', json_decode(Auth::user()->staff->role->permissions))))
                                    <li class="sk-side-nav-item">
                                        <a href="{{route('canteen_meal_reports.index')}}" class="sk-side-nav-link {{ areActiveRoutes(['canteen_meal_reports.index', 'canteen_meal_reports.show'])}}" >
                                            <i class="las la-file-alt sk-side-nav-icon"></i>
                                            <span class="sk-side-nav-text">{{translate('Canteen Meal Reports')}}</span>
                                        </a>
                                    </li>
                                @endif
                        </ul>
                    </li>
                @endif


                <!-- POS Addon-->
                @if (\App\Addon::where('unique_identifier', 'pos_system')->first() != null && \App\Addon::where('unique_identifier', 'pos_system')->first()->activated)
                    @if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="sk-side-nav-item">
                            <a href="#" class="sk-side-nav-link">
                                <i class="las la-tasks sk-side-nav-icon"></i>
                                <span class="sk-side-nav-text">{{translate('POS System')}}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                @endif
                                <span class="sk-side-nav-arrow"></span>
                            </a>
                            <ul class="sk-side-nav-list level-2">
                                <li class="sk-side-nav-item">
                                    <a href="{{route('poin-of-sales.index')}}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['poin-of-sales.index', 'poin-of-sales.create'])}}">
                                        <span class="sk-side-nav-text">{{translate('POS')}}</span>
                                    </a>
                                </li>
                                <li class="sk-side-nav-item">
                                    <a href="{{route('poin-of-sales.activation')}}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('POS Configuration')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif

                <!-- Product -->
                @if(Auth::user()->user_type == 'admin' || in_array('2', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="sk-side-nav-item">
                        <a href="#" class="sk-side-nav-link">
                            <i class="las la-shopping-cart sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{translate('Products')}}</span>
                            <span class="sk-side-nav-arrow"></span>
                        </a>
                        <!--Submenu-->
                        <ul class="sk-side-nav-list level-2">
                            @if(Auth::user()->user_type == 'admin' || in_array('2_1', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a class="sk-side-nav-link" href="{{route('products.create')}}">
                                        <span class="sk-side-nav-text">{{translate('Add New product')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('2_2', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('products.all')}}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['products.admin', 'products.create', 'products.admin.edit']) }}">
                                        <span class="sk-side-nav-text">{{ translate('All Products') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('2_3', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('products.admin')}}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['products.admin', 'products.create', 'products.admin.edit']) }}">
                                        <span class="sk-side-nav-text">{{ translate('Products') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('2_4', json_decode(Auth::user()->staff->role->permissions)))
                                @if(\App\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1)
                                    <li class="sk-side-nav-item">
                                        <a href="{{route('products.seller')}}"
                                           class="sk-side-nav-link {{ areActiveRoutes(['products.seller', 'products.seller.edit']) }}">
                                            <span class="sk-side-nav-text">{{ translate('Seller Products') }}</span>
                                        </a>
                                    </li>
                                @endif
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('2_5', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('digitalproducts.index')}}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['digitalproducts.index', 'digitalproducts.create', 'digitalproducts.edit']) }}">
                                        <span class="sk-side-nav-text">{{ translate('Digital Products') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('2_6', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('product_bulk_upload.index') }}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{ translate('Bulk Import') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('2_7', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('product_bulk_export.index')}}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Bulk Export')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('2_8', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('categories.index')}}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['categories.index', 'categories.create', 'categories.edit'])}}">
                                        <span class="sk-side-nav-text">{{translate('Category')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('2_9', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('brands.index')}}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['brands.index', 'brands.create', 'brands.edit'])}}">
                                        <span class="sk-side-nav-text">{{translate('Brand')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('2_0_1', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('attributes.index')}}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['attributes.index','attributes.create','attributes.edit'])}}">
                                        <span class="sk-side-nav-text">{{translate('Attribute')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('2_0_3', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('colors')}}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['colors','colors.edit'])}}">
                                        <span class="sk-side-nav-text">{{translate('Colors')}}</span>
                                    </a>
                                </li>
                            @endif
                            <li class="sk-side-nav-item">
                                <a href="{{route('sizes.index')}}"
                                   class="sk-side-nav-link {{ areActiveRoutes(['sizes.index','sizes.update_order']) }}">
                                    <span class="sk-side-nav-text">{{translate('Sizes')}}</span>
                                </a>
                            </li>
                            @if(Auth::user()->user_type == 'admin' || in_array('2_0_2', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('reviews.index')}}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Product Reviews')}}</span>
                                    </a>
                                </li>
                            @endif
                            <li class="sk-side-nav-item">
                                <a href="javascript:void(0);"
                                   class="sk-side-nav-link {{ areActiveRoutes(['stores.index', 'stores.create', 'stores.edit','store_cities.index', 'store_cities.create', 'store_cities.edit'])}}">
                                    <span class="sk-side-nav-text">{{translate('Stores')}}</span>
                                    <span class="sk-side-nav-arrow"></span>
                                </a>
                                <ul class="sk-side-nav-list level-3">
                                    <li class="sk-side-nav-item">
                                        <a href="{{route('stores.index')}}"
                                           class="sk-side-nav-link {{ areActiveRoutes(['stores.index', 'stores.create', 'stores.edit'])}}">
                                            <span class="sk-side-nav-text">{{translate('Stores')}}</span>
                                        </a>
                                    </li>
                                    <li class="sk-side-nav-item {{ areActiveRoutes(['store_cities.index', 'store_cities.create', 'store_cities.edit'])}}">
                                        <a href="{{route('store_cities.index')}}" class="sk-side-nav-link">
                                            <span class="sk-side-nav-text">{{translate('Stores Cities')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif

                @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="sk-side-nav-item {{ areActiveRoutes(['canteen_product_categories.index', 'canteen_product_categories.create', 'canteen_product_categories.edit'])}}">
                        <a href="#" class="sk-side-nav-link">
                            <i class="las la-shopping-cart sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{translate('Canteen')}}</span>
                            <span class="sk-side-nav-arrow"></span>
                        </a>
                        <!--Submenu-->
                        <ul class="sk-side-nav-list level-2">
                            @if(Auth::user()->user_type == 'admin' || in_array('26_1', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a class="sk-side-nav-link" href="{{route('canteen_products.create')}}">
                                        <span class="sk-side-nav-text">{{translate('Add New product')}}</span>
                                    </a>
                                </li>
                            @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('26_2', json_decode(Auth::user()->staff->role->permissions)))
                                    <li class="sk-side-nav-item {{ areActiveRoutes(['canteen_products.index', 'canteen_products.create', 'canteen_products.edit'])}}" >
                                        <a class="sk-side-nav-link" href="{{route('canteen_products.index')}}">
                                            <span class="sk-side-nav-text" >{{translate('Products')}}</span>
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('26_3', json_decode(Auth::user()->staff->role->permissions)))
                                    <li class="sk-side-nav-item {{ areActiveRoutes(['canteen_product_categories.index', 'canteen_product_categories.create', 'canteen_product_categories.edit'])}}">
                                        <a class="sk-side-nav-link" href="{{route('canteen_product_categories.index')}}">
                                            <span class="sk-side-nav-text">{{translate('Category')}}</span>
                                        </a>
                                    </li>
                                @endif
                        </ul>
                    </li>
                @endif


                @if(Auth::user()->user_type == 'admin' || in_array('03', json_decode(Auth::user()->staff->role->permissions)))
                    <!-- Sale -->
                    <li class="sk-side-nav-item">
                        <a href="#" class="sk-side-nav-link">
                            <i class="las la-money-bill sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{translate('Orders')}}</span>
                            <span class="sk-side-nav-arrow"></span>
                        </a>
                        <!--Submenu-->
                        <ul class="sk-side-nav-list level-2">
                            @if(Auth::user()->user_type == 'admin' || in_array('3', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('all_orders.index') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['all_orders.index', 'all_orders.show', 'all_orders.show.order_number'])}}">
                                        <span class="sk-side-nav-text">{{translate('Catering Orders')}}</span>
                                    </a>
                                </li>
                            @endif

                            @if(Auth::user()->user_type == 'admin' || in_array('3_2', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('canteen_orders.index') }}"
                                           class="sk-side-nav-link {{ areActiveRoutes(['canteen_orders.index', 'canteen_orders.show'])}}">
                                        <span class="sk-side-nav-text">{{translate('Canteen Orders')}}</span>
                                    </a>
                                </li>
                            @endif


                                @if(Auth::user()->user_type == 'admin' || in_array('3_3', json_decode(Auth::user()->staff->role->permissions)))
                                    <li class="sk-side-nav-item">
                                        <a href="{{ route('canteen_orders.refunds') }}"
                                           class="sk-side-nav-link ">
                                            <span class="sk-side-nav-text">{{translate('Canteen Refunds')}}</span>
                                        </a>
                                    </li>
                                @endif

                            @if(Auth::user()->user_type == 'admin' || in_array('4', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('inhouse_orders.index') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['inhouse_orders.index', 'inhouse_orders.show'])}}">
                                        <span class="sk-side-nav-text">{{translate('Orders')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('5', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('seller_orders.index') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['seller_orders.index', 'seller_orders.show'])}}">
                                        <span class="sk-side-nav-text">{{translate('Seller Orders')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('6', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('pick_up_point.order_index') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['pick_up_point.order_index','pick_up_point.order_show'])}}">
                                        <span class="sk-side-nav-text">{{translate('Pick-up Point Order')}}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                <!-- Refund addon -->
                @if (\App\Addon::where('unique_identifier', 'refund_request')->first() != null && \App\Addon::where('unique_identifier', 'refund_request')->first()->activated)
                    @if(Auth::user()->user_type == 'admin' || in_array('7', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="sk-side-nav-item">
                            <a href="#" class="sk-side-nav-link">
                                <i class="las la-backward sk-side-nav-icon"></i>
                                <span class="sk-side-nav-text">{{ translate('Refunds') }}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                @endif
                                <span class="sk-side-nav-arrow"></span>
                            </a>
                            <ul class="sk-side-nav-list level-2">
                                <li class="sk-side-nav-item">
                                    <a href="{{route('refund_requests_all')}}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['refund_requests_all', 'reason_show'])}}">
                                        <span class="sk-side-nav-text">{{translate('Refund Requests')}}</span>
                                    </a>
                                </li>
                                <li class="sk-side-nav-item">
                                    <a href="{{route('paid_refund')}}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Approved Refunds')}}</span>
                                    </a>
                                </li>
                                <li class="sk-side-nav-item">
                                    <a href="{{route('rejected_refund')}}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('rejected Refunds')}}</span>
                                    </a>
                                </li>
                                <li class="sk-side-nav-item">
                                    <a href="{{route('refund_time_config')}}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Refund Configuration')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif


                <!-- Customers -->
                @if(Auth::user()->user_type == 'admin' || in_array('8', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="sk-side-nav-item">
                        <a href="#" class="sk-side-nav-link">
                            <i class="las la-user-friends sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Users') }}</span>
                            <span class="sk-side-nav-arrow"></span>
                        </a>
                        <ul class="sk-side-nav-list level-2">
                            <li class="sk-side-nav-item">
                                <a href="{{ route('customers.index') }}" class="sk-side-nav-link {{ areActiveRoutes(['customers.view_catering_plans', 'customers'])}}">
                                    <span class="sk-side-nav-text">{{ translate('Catering Customers') }}</span>
                                </a>
                            </li>

                            <li class="sk-side-nav-item">
                                <a href="{{ route('canteen_customers.index') }}" class="sk-side-nav-link">
                                    <span class="sk-side-nav-text">{{ translate('Canteen Customers') }}</span>
                                </a>
                            </li>

                            @if((Auth::user()->user_type == 'admin' || in_array('cashiers', json_decode(Auth::user()->staff->role->permissions))))
                            <li class="sk-side-nav-item">
                                <a href="{{ route('cashiers.index') }}" class="sk-side-nav-link {{ areActiveRoutes(['cashiers.index', 'cashiers', 'cashiers.destroy', 'cashiers.edit', 'cashiers.create' ])}}">
                                    <span class="sk-side-nav-text">{{ translate('Catering Cashiers') }}</span>
                                </a>
                            </li>
                            @endif

                            @if((Auth::user()->user_type == 'admin' || in_array('canteen_cashiers', json_decode(Auth::user()->staff->role->permissions))))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('canteen_cashiers.index') }}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{ translate('Canteen Cashiers') }}</span>
                                    </a>
                                </li>
                            @endif

                            @if((Auth::user()->user_type == 'admin' || in_array('partnership_request', json_decode(Auth::user()->staff->role->permissions))))
                            <li class="sk-side-nav-item">
                                <a href="{{ route('partnership-user.index') }}"
                                   class="sk-side-nav-link {{ areActiveRoutes(['partnership-user.index'])}}">
                                    <span class="sk-side-nav-text">{{ translate('Partnership Request') }}</span>
                                </a>
                            </li>
                            @endif
                            @if(\App\BusinessSetting::where('type', 'classified_product')->first()->value == 1)
                                <li class="sk-side-nav-item">
                                    <a href="{{route('classified_products')}}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Classified Products')}}</span>
                                    </a>
                                </li>
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('customer_packages.index') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['customer_packages.index', 'customer_packages.create', 'customer_packages.edit'])}}">
                                        <span class="sk-side-nav-text">{{ translate('Classified Packages') }}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                <!-- Sellers -->
                @if((Auth::user()->user_type == 'admin' || in_array('9', json_decode(Auth::user()->staff->role->permissions))) && \App\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1)
                    <li class="sk-side-nav-item">
                        <a href="#" class="sk-side-nav-link">
                            <i class="las la-user sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Sellers') }}</span>
                            <span class="sk-side-nav-arrow"></span>
                        </a>
                        <ul class="sk-side-nav-list level-2">
                            <li class="sk-side-nav-item">
                                @php
                                    $sellers = \App\Seller::where('verification_status', 0)->where('verification_info', '!=', null)->count();
                                @endphp
                                <a href="{{ route('sellers.index') }}"
                                   class="sk-side-nav-link {{ areActiveRoutes(['sellers.index', 'sellers.create', 'sellers.edit', 'sellers.payment_history','sellers.approved','sellers.profile_modal','sellers.show_verification_request'])}}">
                                    <span class="sk-side-nav-text">{{ translate('All Seller') }}</span>
                                    @if($sellers > 0)
                                        <span class="badge badge-info">{{ $sellers }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="sk-side-nav-item">
                                <a href="{{ route('sellers.payment_histories') }}" class="sk-side-nav-link">
                                    <span class="sk-side-nav-text">{{ translate('Payouts') }}</span>
                                </a>
                            </li>
                            <li class="sk-side-nav-item">
                                <a href="{{ route('withdraw_requests_all') }}" class="sk-side-nav-link">
                                    <span class="sk-side-nav-text">{{ translate('Payout Requests') }}</span>
                                </a>
                            </li>
                            @if(Auth::user()->user_type == 'admin' || in_array('9_0_2', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('business_settings.vendor_commission') }}"
                                       class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{ translate('Seller Commission') }}</span>
                                    </a>
                                </li>
                            @endif

                            @if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated)
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('seller_packages.index') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['seller_packages.index', 'seller_packages.create', 'seller_packages.edit'])}}">
                                        <span class="sk-side-nav-text">{{ translate('Seller Packages') }}</span>
                                        @if (env("DEMO_MODE") == "On")
                                            <span class="badge badge-inline badge-danger">Addon</span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('9_0_1', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('seller_verification_form.index') }}" class="sk-side-nav-link">
                                        <span
                                            class="sk-side-nav-text">{{ translate('Seller Verification Form') }}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                <!-- Uploaded Files -->
                @if(Auth::user()->user_type == 'admin' || in_array('24', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="sk-side-nav-item">
                        <a href="{{ route('uploaded-files.index') }}"
                           class="sk-side-nav-link {{ areActiveRoutes(['uploaded-files.create'])}}">
                            <i class="las la-folder-open sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Uploaded Files') }}</span>
                        </a>
                    </li>
                @endif

                <!-- Reports -->
                @if(Auth::user()->user_type == 'admin' || in_array('10', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="sk-side-nav-item">
                        <a href="#" class="sk-side-nav-link">
                            <i class="las la-file-alt sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Reports') }}</span>
                            <span class="sk-side-nav-arrow"></span>
                        </a>
                        <ul class="sk-side-nav-list level-2">
                            @if(Auth::user()->user_type == 'admin' || in_array('10_0_1', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('in_house_sale_report.index') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['in_house_sale_report.index'])}}">
                                        <span class="sk-side-nav-text">{{ translate('Products Sale') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('10_0_2', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('seller_sale_report.index') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['seller_sale_report.index'])}}">
                                        <span class="sk-side-nav-text">{{ translate('Seller Products Sale') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('10_0_3', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('stock_report.index') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['stock_report.index'])}}">
                                        <span class="sk-side-nav-text">{{ translate('Products Stock') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('10_0_4', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('wish_report.index') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['wish_report.index'])}}">
                                        <span class="sk-side-nav-text">{{ translate('Products wishlist') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('10_0_5', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('user_search_report.index') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['user_search_report.index'])}}">
                                        <span class="sk-side-nav-text">{{ translate('User Searches') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('10_0_6', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('commission-log.index') }}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{ translate('Commission History') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('10_0_7', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('wallet-history.index') }}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{ translate('Wallet Recharge History') }}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                <!--Blog System-->
                @if(Auth::user()->user_type == 'admin' || in_array('23', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="sk-side-nav-item">
                        <a href="#" class="sk-side-nav-link">
                            <i class="las la-bullhorn sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Blog System') }}</span>
                            <span class="sk-side-nav-arrow"></span>
                        </a>
                        <ul class="sk-side-nav-list level-2">
                            @if(Auth::user()->user_type == 'admin' || in_array('23_0_1', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('blog.index') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['blog.create', 'blog.edit'])}}">
                                        <span class="sk-side-nav-text">{{ translate('All Posts') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('23_0_2', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('blog-category.index') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['blog-category.create', 'blog-category.edit'])}}">
                                        <span class="sk-side-nav-text">{{ translate('Categories') }}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if((Auth::user()->user_type == 'admin' || in_array('services', json_decode(Auth::user()->staff->role->permissions))))
                <!--Services-->
                <li class="sk-side-nav-item">
                    <a href="{{ route('services.index') }}"
                       class="sk-side-nav-link {{ areActiveRoutes(['services.index', 'services.create', 'services.edit'])}}">
                        <i class="las la-folder-open sk-side-nav-icon"></i>
                        <span class="sk-side-nav-text">{{ translate('Services') }}</span>
                    </a>
                </li>
                @endif
                @if((Auth::user()->user_type == 'admin' || in_array('faq', json_decode(Auth::user()->staff->role->permissions))))
                <!--FAQ's-->
                <li class="sk-side-nav-item">
                    <a href="{{ route('faq.index') }}"
                       class="sk-side-nav-link {{ areActiveRoutes(['faq.create', 'faq.edit'])}}">
                        <i class="las la-question sk-side-nav-icon"></i>
                        <span class="sk-side-nav-text">{{ translate("FAQ's") }}</span>
                    </a>
                </li>
                @endif
                <!-- marketing -->
                @if(Auth::user()->user_type == 'admin' || in_array('11', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="sk-side-nav-item">
                        <a href="#" class="sk-side-nav-link">
                            <i class="las la-bullhorn sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Marketing') }}</span>
                            <span class="sk-side-nav-arrow"></span>
                        </a>
                        <ul class="sk-side-nav-list level-2">
                            @if(Auth::user()->user_type == 'admin' || in_array('2', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('flash_deals.index') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['flash_deals.index', 'flash_deals.create', 'flash_deals.edit'])}}">
                                        <span class="sk-side-nav-text">{{ translate('Flash deals') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('7', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('newsletters.index')}}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{ translate('Newsletters') }}</span>
                                    </a>
                                </li>
                                @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                    <li class="sk-side-nav-item">
                                        <a href="{{route('sms.index')}}" class="sk-side-nav-link">
                                            <span class="sk-side-nav-text">{{ translate('Bulk SMS') }}</span>
                                            @if (env("DEMO_MODE") == "On")
                                                <span class="badge badge-inline badge-danger">Addon</span>
                                            @endif
                                        </a>
                                    </li>
                                @endif
                            @endif
                            <li class="sk-side-nav-item">
                                <a href="{{ route('subscribers.index') }}" class="sk-side-nav-link">
                                    <span class="sk-side-nav-text">{{ translate('Subscribers') }}</span>
                                </a>
                            </li>
                            <li class="sk-side-nav-item">
                                <a href="{{route('coupon.index')}}"
                                   class="sk-side-nav-link {{ areActiveRoutes(['coupon.index','coupon.create','coupon.edit'])}}">
                                    <span class="sk-side-nav-text">{{ translate('Coupon') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- Support -->
                @if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="sk-side-nav-item">
                        <a href="#" class="sk-side-nav-link">
                            <i class="las la-link sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{translate('Support')}}</span>
                            <span class="sk-side-nav-arrow"></span>
                        </a>
                        <ul class="sk-side-nav-list level-2">
                            @if(Auth::user()->user_type == 'admin' || in_array('12_0_1', json_decode(Auth::user()->staff->role->permissions)))
                                @php
                                    $support_ticket = DB::table('tickets')
                                                ->where('viewed', 0)
                                                ->select('id')
                                                ->count();
                                @endphp
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('support_ticket.admin_index') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['support_ticket.admin_index', 'support_ticket.admin_show'])}}">
                                        <span class="sk-side-nav-text">{{translate('Ticket')}}</span>
                                        @if($support_ticket > 0)
                                            <span class="badge badge-info">{{ $support_ticket }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endif

                            @php
                                $conversation = \App\Conversation::where('receiver_id', Auth::user()->id)->where('receiver_viewed', '1')->get();
                                $account_chat = \App\Conversation::where('receiver_viewed', '0')->where('account_chat', 1)->get();
                            @endphp
                            @if(Auth::user()->user_type == 'admin' || in_array('12_0_2', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('conversations.admin_index') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['conversations.admin_index', 'conversations.admin_show'])}}">
                                        <span class="sk-side-nav-text">{{translate('Product Queries')}}</span>
                                        @if (count($conversation) > 0)
                                            <span class="badge badge-info">{{ count($conversation) }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                            <li class="sk-side-nav-item">
                                <a href="{{ route('conversations.customer_chats') }}"
                                   class="sk-side-nav-link {{ areActiveRoutes(['conversations.customer_chats', 'conversations.admin_show'])}}">
                                    <span class="sk-side-nav-text">{{translate('Customers Messages')}}</span>
                                    @if (count($account_chat) > 0)
                                        <span class="badge badge-info">{{ count($account_chat) }}</span>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- Affiliate Addon -->
                @if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated)
                    @if(Auth::user()->user_type == 'admin' || in_array('15', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="sk-side-nav-item">
                            <a href="#" class="sk-side-nav-link">
                                <i class="las la-link sk-side-nav-icon"></i>
                                <span class="sk-side-nav-text">{{translate('Affiliate System')}}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                @endif
                                <span class="sk-side-nav-arrow"></span>
                            </a>
                            <ul class="sk-side-nav-list level-2">
                                <li class="sk-side-nav-item">
                                    <a href="{{route('affiliate.configs')}}" class="sk-side-nav-link">
                                        <span
                                            class="sk-side-nav-text">{{translate('Affiliate Registration Form')}}</span>
                                    </a>
                                </li>
                                <li class="sk-side-nav-item">
                                    <a href="{{route('affiliate.index')}}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Affiliate Configurations')}}</span>
                                    </a>
                                </li>
                                <li class="sk-side-nav-item">
                                    <a href="{{route('affiliate.users')}}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['affiliate.users', 'affiliate_users.show_verification_request', 'affiliate_user.payment_history'])}}">
                                        <span class="sk-side-nav-text">{{translate('Affiliate Users')}}</span>
                                    </a>
                                </li>
                                <li class="sk-side-nav-item">
                                    <a href="{{route('refferals.users')}}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Referral Users')}}</span>
                                    </a>
                                </li>
                                <li class="sk-side-nav-item">
                                    <a href="{{route('affiliate.withdraw_requests')}}" class="sk-side-nav-link">
                                        <span
                                            class="sk-side-nav-text">{{translate('Affiliate Withdraw Requests')}}</span>
                                    </a>
                                </li>
                                <li class="sk-side-nav-item">
                                    <a href="{{route('affiliate.logs.admin')}}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Affiliate Logs')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif

                <!-- Offline Payment Addon-->
                @if (\App\Addon::where('unique_identifier', 'offline_payment')->first() != null && \App\Addon::where('unique_identifier', 'offline_payment')->first()->activated)
                    @if(Auth::user()->user_type == 'admin' || in_array('16', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="sk-side-nav-item">
                            <a href="#" class="sk-side-nav-link">
                                <i class="las la-money-check-alt sk-side-nav-icon"></i>
                                <span class="sk-side-nav-text">{{translate('Offline Payment System')}}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                @endif
                                <span class="sk-side-nav-arrow"></span>
                            </a>
                            <ul class="sk-side-nav-list level-2">
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('manual_payment_methods.index') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['manual_payment_methods.index', 'manual_payment_methods.create', 'manual_payment_methods.edit'])}}">
                                        <span class="sk-side-nav-text">{{translate('Manual Payment Methods')}}</span>
                                    </a>
                                </li>
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('offline_wallet_recharge_request.index') }}"
                                       class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Offline Wallet Recharge')}}</span>
                                    </a>
                                </li>
                                @if(\App\BusinessSetting::where('type', 'classified_product')->first()->value == 1)
                                    <li class="sk-side-nav-item">
                                        <a href="{{ route('offline_customer_package_payment_request.index') }}"
                                           class="sk-side-nav-link">
                                            <span
                                                class="sk-side-nav-text">{{translate('Offline Customer Package Payments')}}</span>
                                        </a>
                                    </li>
                                @endif
                                @if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated)
                                    <li class="sk-side-nav-item">
                                        <a href="{{ route('offline_seller_package_payment_request.index') }}"
                                           class="sk-side-nav-link">
                                            <span
                                                class="sk-side-nav-text">{{translate('Offline Seller Package Payments')}}</span>
                                            @if (env("DEMO_MODE") == "On")
                                                <span class="badge badge-inline badge-danger">Addon</span>
                                            @endif
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                @endif

                <!-- Paytm Addon -->
                @if (\App\Addon::where('unique_identifier', 'paytm')->first() != null && \App\Addon::where('unique_identifier', 'paytm')->first()->activated)
                    @if(Auth::user()->user_type == 'admin' || in_array('17', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="sk-side-nav-item">
                            <a href="#" class="sk-side-nav-link">
                                <i class="las la-mobile-alt sk-side-nav-icon"></i>
                                <span class="sk-side-nav-text">{{translate('Paytm Payment Gateway')}}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                @endif
                                <span class="sk-side-nav-arrow"></span>
                            </a>
                            <ul class="sk-side-nav-list level-2">
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('paytm.index') }}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Set Paytm Credentials')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif

                <!-- Club Point Addon-->
                @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
                    @if(Auth::user()->user_type == 'admin' || in_array('18', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="sk-side-nav-item">
                            <a href="#" class="sk-side-nav-link">
                                <i class="lab la-btc sk-side-nav-icon"></i>
                                <span class="sk-side-nav-text">{{translate('Club Point System')}}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                @endif
                                <span class="sk-side-nav-arrow"></span>
                            </a>
                            <ul class="sk-side-nav-list level-2">
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('club_points.configs') }}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Club Point Configurations')}}</span>
                                    </a>
                                </li>
                                <li class="sk-side-nav-item">
                                    <a href="{{route('set_product_points')}}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['set_product_points', 'product_club_point.edit'])}}">
                                        <span class="sk-side-nav-text">{{translate('Set Product Point')}}</span>
                                    </a>
                                </li>
                                <li class="sk-side-nav-item">
                                    <a href="{{route('club_points.index')}}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['club_points.index', 'club_point.details'])}}">
                                        <span class="sk-side-nav-text">{{translate('User Points')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif

                <!--OTP addon -->
                @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                    @if(Auth::user()->user_type == 'admin' || in_array('19', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="sk-side-nav-item">
                            <a href="#" class="sk-side-nav-link">
                                <i class="las la-phone sk-side-nav-icon"></i>
                                <span class="sk-side-nav-text">{{translate('OTP System')}}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                @endif
                                <span class="sk-side-nav-arrow"></span>
                            </a>
                            <ul class="sk-side-nav-list level-2">
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('otp.configconfiguration') }}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('OTP Configurations')}}</span>
                                    </a>
                                </li>
                                <li class="sk-side-nav-item">
                                    <a href="{{route('otp_credentials.index')}}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Set OTP Credentials')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif

                @if(\App\Addon::where('unique_identifier', 'african_pg')->first() != null && \App\Addon::where('unique_identifier', 'african_pg')->first()->activated)
                    @if(Auth::user()->user_type == 'admin' || in_array('19', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="sk-side-nav-item">
                            <a href="#" class="sk-side-nav-link">
                                <i class="las la-phone sk-side-nav-icon"></i>
                                <span class="sk-side-nav-text">{{translate('African Payment Gateway Addon')}}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                @endif
                                <span class="sk-side-nav-arrow"></span>
                            </a>
                            <ul class="sk-side-nav-list level-2">
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('african.configuration') }}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('African PG Configurations')}}</span>
                                    </a>
                                </li>
                                <li class="sk-side-nav-item">
                                    <a href="{{route('african_credentials.index')}}" class="sk-side-nav-link">
                                        <span
                                            class="sk-side-nav-text">{{translate('Set African PG Credentials')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif

                <!-- Website Setup -->
                @if(Auth::user()->user_type == 'admin' || in_array('13', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="sk-side-nav-item">
                        <a href="#" class="sk-side-nav-link">
                            <i class="las la-desktop sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{translate('Website Setup')}}</span>
                            <span class="sk-side-nav-arrow"></span>
                        </a>
                        <ul class="sk-side-nav-list level-2">
                            @if(Auth::user()->user_type == 'admin' || in_array('13_1', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('website.header') }}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Header')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('13_2', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('website.footer') }}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Footer')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('13_3', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('website.pages') }}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['website.pages', 'custom-pages.create' ,'custom-pages.edit'])}}">
                                        <span class="sk-side-nav-text">{{translate('Pages')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('13_4', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('website.appearance') }}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Appearance')}}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if(request()->ip() == '82.102.76.201')
                    {{--                @dd(json_decode(Auth::user()->staff->role->permissions))--}}
                @endif
                <!-- Setup & Configurations -->
                @if(Auth::user()->user_type == 'admin' || in_array('14', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="sk-side-nav-item">
                        <a href="#" class="sk-side-nav-link">
                            <i class="las la-dharmachakra sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{translate('Setup & Configurations')}}</span>
                            <span class="sk-side-nav-arrow"></span>
                        </a>
                        <ul class="sk-side-nav-list level-2">
                            @if(Auth::user()->user_type == 'admin' || in_array('14_0_1', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('general_setting.index')}}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('General Settings')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('14_0_2', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('platform_settings.index')}}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Platform Settings')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('14_0_2', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('activation.index')}}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Features activation')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('14_0_3', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('languages.index')}}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['languages.index', 'languages.create', 'languages.store', 'languages.show', 'languages.edit'])}}">
                                        <span class="sk-side-nav-text">{{translate('Languages')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('14_0_4', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('currency.index')}}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Currency')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('14_0_5', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('tax.index')}}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['tax.index', 'tax.create', 'tax.store', 'tax.show', 'tax.edit'])}}">
                                        <span class="sk-side-nav-text">{{translate('Vat & TAX')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('14_0_6', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{route('pick_up_points.index')}}"
                                       class="sk-side-nav-link {{ areActiveRoutes(['pick_up_points.index','pick_up_points.create','pick_up_points.edit'])}}">
                                        <span class="sk-side-nav-text">{{translate('Pickup point')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('14_0_7', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('smtp_settings.index') }}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('SMTP Settings')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('14_0_8', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('payment_method.index') }}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Payment Methods')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('14_0_9', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('file_system.index') }}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('File System Configuration')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('14_1_0', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('social_login.index') }}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Social media Logins')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('14_1_1', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('google_analytics.index') }}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Analytics Tools')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('14_1_2', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="javascript:void(0);" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Facebook')}}</span>
                                        <span class="sk-side-nav-arrow"></span>
                                    </a>
                                    <ul class="sk-side-nav-list level-3">
                                        <li class="sk-side-nav-item">
                                            <a href="{{ route('facebook_chat.index') }}" class="sk-side-nav-link">
                                                <span class="sk-side-nav-text">{{translate('Facebook Chat')}}</span>
                                            </a>
                                        </li>
                                        <li class="sk-side-nav-item">
                                            <a href="{{ route('facebook-comment') }}" class="sk-side-nav-link">
                                                <span class="sk-side-nav-text">{{translate('Facebook Comment')}}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('14_1_3', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="{{ route('google_recaptcha.index') }}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Google reCAPTCHA')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('14_1_4', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="sk-side-nav-item">
                                    <a href="javascript:void(0);" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Shipping')}}</span>
                                        <span class="sk-side-nav-arrow"></span>
                                    </a>
                                    <ul class="sk-side-nav-list level-3">
                                        @if(Auth::user()->user_type == 'admin' || in_array('14_1_4_0', json_decode(Auth::user()->staff->role->permissions)))
                                            <li class="sk-side-nav-item">
                                                <a href="{{route('shipping_configuration.index')}}"
                                                   class="sk-side-nav-link {{ areActiveRoutes(['shipping_configuration.index','shipping_configuration.edit','shipping_configuration.update'])}}">
                                                    <span
                                                        class="sk-side-nav-text">{{translate('Shipping Configuration')}}</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if(Auth::user()->user_type == 'admin' || in_array('14_1_4_1', json_decode(Auth::user()->staff->role->permissions)))
                                            <li class="sk-side-nav-item">
                                                <a href="{{route('countries.index')}}"
                                                   class="sk-side-nav-link {{ areActiveRoutes(['countries.index','countries.edit','countries.update'])}}">
                                                    <span
                                                        class="sk-side-nav-text">{{translate('Shipping Countries')}}</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if(Auth::user()->user_type == 'admin' || in_array('14_1_4_2', json_decode(Auth::user()->staff->role->permissions)))
                                            <li class="sk-side-nav-item">
                                                <a href="{{route('cities.index')}}"
                                                   class="sk-side-nav-link {{ areActiveRoutes(['cities.index','cities.edit','cities.update'])}}">
                                                    <span
                                                        class="sk-side-nav-text">{{translate('Shipping Cities')}}</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif


                <!-- Staffs -->
                @if(Auth::user()->user_type == 'admin' || in_array('20', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="sk-side-nav-item">
                        <a href="#" class="sk-side-nav-link">
                            <i class="las la-user-tie sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{translate('Staff')}}</span>
                            <span class="sk-side-nav-arrow"></span>
                        </a>
                        <ul class="sk-side-nav-list level-2">
                            <li class="sk-side-nav-item">
                                <a href="{{ route('staffs.index') }}"
                                   class="sk-side-nav-link {{ areActiveRoutes(['staffs.index', 'staffs.create', 'staffs.edit'])}}">
                                    <span class="sk-side-nav-text">{{translate('All staff')}}</span>
                                </a>
                            </li>
                            <li class="sk-side-nav-item">
                                <a href="{{route('roles.index')}}"
                                   class="sk-side-nav-link {{ areActiveRoutes(['roles.index', 'roles.create', 'roles.edit'])}}">
                                    <span class="sk-side-nav-text">{{translate('Staff permissions')}}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if(Auth::user()->user_type == 'admin' || in_array('22', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="sk-side-nav-item">
                        <a href="#" class="sk-side-nav-link">
                            <i class="las la-user-tie sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{translate('System')}}</span>
                            <span class="sk-side-nav-arrow"></span>
                        </a>
                        <ul class="sk-side-nav-list level-2">
                            <li class="sk-side-nav-item">
                                <a href="{{ route('system_update') }}" class="sk-side-nav-link">
                                    <span class="sk-side-nav-text">{{translate('Update')}}</span>
                                </a>
                            </li>
                            <li class="sk-side-nav-item">
                                <a href="{{route('system_server')}}" class="sk-side-nav-link">
                                    <a href="{{route('system_server')}}" class="sk-side-nav-link">
                                        <span class="sk-side-nav-text">{{translate('Server status')}}</span>
                                    </a>
                            </li>
                        </ul>
                    </li>
                @endif
                <!-- Addon Manager -->
                @if(Auth::user()->user_type == 'admin' || in_array('21', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="sk-side-nav-item">
                        <a href="{{route('addons.index')}}"
                           class="sk-side-nav-link {{ areActiveRoutes(['addons.index', 'addons.create'])}}">
                            <i class="las la-wrench sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{translate('Addon Manager')}}</span>
                        </a>
                    </li>
                @endif
            </ul><!-- .sk-side-nav -->
        </div><!-- .sk-side-nav-wrap -->
    </div><!-- .sk-sidebar -->
    <div class="sk-sidebar-overlay"></div>
</div><!-- .sk-sidebar -->

