@php
    $full_name = getAccountName(Auth::user()->name);
@endphp
<div class="sk-user-sidenav-wrap position-relative z-1 fs-16" data-aos="fade-right">
    <div class="sk-user-sidenav border border-primary-100 border-width-2">
        <div class="sidemnenu py-15px py-md-40px">
            <ul class="sk-side-nav-list" data-toggle="sk-side-menu">
                <li class="sk-side-nav-item ">
                    <a href="{{ route('dashboard') }}" class="sk-side-nav-link {{ areActiveRoutes(['dashboard'])}} {{ areActiveRoutes(['dashboard.subscription_history'])}}
                    {{ areActiveRoutes(['dashboard.meals_history'])}}">
                        <div class="sk-side-nav-icon">
                            <svg class="w-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 21">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/account-dashboard.svg')}}#content"></use>
                            </svg>
                        </div>
                        <span class="sk-side-nav-text">{{ translate('Dashboard') }}</span>
                    </a>
                </li>
                @php
                    $delivery_viewed = App\Order::where('user_id', Auth::user()->id)->where('delivery_viewed', 0)->get()->count();
                    $payment_status_viewed = App\Order::where('user_id', Auth::user()->id)->where('payment_status_viewed', 0)->get()->count();
                @endphp
{{--                <li class="sk-side-nav-item ">--}}
{{--                    <a href="{{ route('purchase_history.index') }}"--}}
{{--                       class="sk-side-nav-link {{ areActiveRoutes(['purchase_history.index'])}}">--}}
{{--                        <div class="sk-side-nav-icon">--}}
{{--                            <svg class="w-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 21">--}}
{{--                                <use xlink:href="{{static_asset('assets/img/icons/account-orders.svg')}}#content"></use>--}}
{{--                            </svg>--}}
{{--                        </div>--}}
{{--                        <span class="sk-side-nav-text">{{ translate('Card History') }}</span>--}}
{{--                        @if($delivery_viewed > 0 || $payment_status_viewed > 0)--}}
{{--                            <span class="badge badge-inline badge-secondary">{{ translate('New') }}</span>--}}
{{--                        @endif--}}
{{--                    </a>--}}
{{--                </li>--}}
                <?php /*
                <li class="sk-side-nav-item">
                    <a href="{{ route('digital_purchase_history.index') }}" class="sk-side-nav-link {{ areActiveRoutes(['digital_purchase_history.index'])}}">
                        <i class="las la-download sk-side-nav-icon"></i>
                        <span class="sk-side-nav-text">{{ translate('Downloads') }}</span>
                    </a>
                </li>*/ ?>
                @php
                    $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
                    $club_point_addon = \App\Addon::where('unique_identifier', 'club_point')->first();
                @endphp
                @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                    <li class="sk-side-nav-item">
                        <a href="{{ route('customer_refund_request') }}"
                           class="sk-side-nav-link {{ areActiveRoutes(['customer_refund_request'])}}">
                            <i class="las la-backward sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Sent Refund Request') }}</span>
                        </a>
                    </li>
                @endif
                <?php /*
                <li class="sk-side-nav-item">
                    <a href="{{ route('wishlists.index') }}" class="sk-side-nav-link {{ areActiveRoutes(['wishlists.index'])}}">
                        <i class="la la-heart-o sk-side-nav-icon"></i>
                        <span class="sk-side-nav-text">{{ translate('Wishlist') }}</span>
                    </a>
                </li>*/ ?>

                @if(Auth::user()->user_type == 'seller')
                    <li class="sk-side-nav-item">
                        <a href="{{ route('seller.products') }}"
                           class="sk-side-nav-link {{ areActiveRoutes(['seller.products', 'seller.products.upload', 'seller.products.edit'])}}">
                            <i class="lab la-sketch sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Products') }}</span>
                        </a>
                    </li>
                    <li class="sk-side-nav-item">
                        <a href="{{route('product_bulk_upload.index')}}"
                           class="sk-side-nav-link {{ areActiveRoutes(['product_bulk_upload.index'])}}">
                            <i class="las la-upload sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Product Bulk Upload') }}</span>
                        </a>
                    </li>
                    <li class="sk-side-nav-item">
                        <a href="{{ route('seller.digitalproducts') }}"
                           class="sk-side-nav-link {{ areActiveRoutes(['seller.digitalproducts', 'seller.digitalproducts.upload', 'seller.digitalproducts.edit'])}}">
                            <i class="lab la-sketch sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Digital Products') }}</span>
                        </a>
                    </li>
                @endif

                @if(\App\BusinessSetting::where('type', 'classified_product')->first()->value == 1)
                    <li class="sk-side-nav-item">
                        <a href="{{ route('customer_products.index') }}"
                           class="sk-side-nav-link {{ areActiveRoutes(['customer_products.index', 'customer_products.create', 'customer_products.edit'])}}">
                            <i class="lab la-sketch sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Classified Products') }}</span>
                        </a>
                    </li>
                @endif

                @if(Auth::user()->user_type == 'seller')
                    @if (\App\Addon::where('unique_identifier', 'pos_system')->first() != null && \App\Addon::where('unique_identifier', 'pos_system')->first()->activated)
                        @if (\App\BusinessSetting::where('type', 'pos_activation_for_seller')->first() != null && \App\BusinessSetting::where('type', 'pos_activation_for_seller')->first()->value != 0)
                            <li class="sk-side-nav-item">
                                <a href="{{ route('poin-of-sales.seller_index') }}"
                                   class="sk-side-nav-link {{ areActiveRoutes(['poin-of-sales.seller_index'])}}">
                                    <i class="las la-fax sk-side-nav-icon"></i>
                                    <span class="sk-side-nav-text">{{ translate('POS Manager') }}</span>
                                </a>
                            </li>
                        @endif
                    @endif

                    @php
                        $orders = DB::table('orders')
                                    ->orderBy('code', 'desc')
                                    ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                                    ->where('order_details.seller_id', Auth::user()->id)
                                    ->where('orders.viewed', 0)
                                    ->select('orders.id')
                                    ->distinct()
                                    ->count();
                    @endphp
                    <li class="sk-side-nav-item">
                        <a href="{{ route('orders.index') }}"
                           class="sk-side-nav-link {{ areActiveRoutes(['orders.index'])}}">
                            <i class="las la-money-bill sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Orders') }}</span>
                            @if($orders > 0)
                                <span class="badge badge-inline badge-secondary">{{ $orders }}</span>
                            @endif
                        </a>
                    </li>

                    @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                        <li class="sk-side-nav-item">
                            <a href="{{ route('vendor_refund_request') }}"
                               class="sk-side-nav-link {{ areActiveRoutes(['vendor_refund_request','reason_show'])}}">
                                <i class="las la-backward sk-side-nav-icon"></i>
                                <span class="sk-side-nav-text">{{ translate('Received Refund Request') }}</span>
                            </a>
                        </li>
                    @endif

                    @php
                        $review_count = DB::table('reviews')
                                    ->orderBy('code', 'desc')
                                    ->join('products', 'products.id', '=', 'reviews.product_id')
                                    ->where('products.user_id', Auth::user()->id)
                                    ->where('reviews.viewed', 0)
                                    ->select('reviews.id')
                                    ->distinct()
                                    ->count();
                    @endphp
                    <li class="sk-side-nav-item">
                        <a href="{{ route('reviews.seller') }}"
                           class="sk-side-nav-link {{ areActiveRoutes(['reviews.seller'])}}">
                            <i class="las la-star-half-alt sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Product Reviews') }}</span>
                            @if($review_count > 0)
                                <span class="badge badge-inline badge-secondary">{{ $review_count }}</span>
                            @endif
                        </a>
                    </li>

                    <li class="sk-side-nav-item">
                        <a href="{{ route('shops.index') }}"
                           class="sk-side-nav-link {{ areActiveRoutes(['shops.index'])}}">
                            <i class="las la-cog sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Shop Setting') }}</span>
                        </a>
                    </li>

                    <li class="sk-side-nav-item">
                        <a href="{{ route('payments.index') }}"
                           class="sk-side-nav-link {{ areActiveRoutes(['payments.index'])}}">
                            <i class="las la-history sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Payment History') }}</span>
                        </a>
                    </li>

                    <li class="sk-side-nav-item">
                        <a href="{{ route('withdraw_requests.index') }}"
                           class="sk-side-nav-link {{ areActiveRoutes(['withdraw_requests.index'])}}">
                            <i class="las la-money-bill-wave-alt sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Money Withdraw') }}</span>
                        </a>
                    </li>

                    <li class="sk-side-nav-item">
                        <a href="{{ route('commission-log.index') }}" class="sk-side-nav-link">
                            <i class="las la-file-alt sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Commission History') }}</span>
                        </a>
                    </li>

                @endif


                @if (\App\BusinessSetting::where('type', 'wallet_system')->first()->value == 1)
                    <li class="sk-side-nav-item">
                        <a href="{{ route('wallet.index') }}"
                           class="sk-side-nav-link {{ areActiveRoutes(['wallet.index'])}}">
                            <i class="las la-dollar-sign sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{translate('My Wallet')}}</span>
                        </a>
                    </li>
                @endif

                @if ($club_point_addon != null && $club_point_addon->activated == 1)
                    <li class="sk-side-nav-item">
                        <a href="{{ route('earnng_point_for_user') }}"
                           class="sk-side-nav-link {{ areActiveRoutes(['earnng_point_for_user'])}}">
                            <i class="las la-dollar-sign sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{translate('Earning Points')}}</span>
                        </a>
                    </li>
                @endif

                @if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated && Auth::user()->affiliate_user != null && Auth::user()->affiliate_user->status)
                    <li class="sk-side-nav-item">
                        <a href="javascript:void(0);"
                           class="sk-side-nav-link {{ areActiveRoutes(['affiliate.user.index', 'affiliate.payment_settings'])}}">
                            <i class="las la-dollar-sign sk-side-nav-icon"></i>
                            <span class="sk-side-nav-text">{{ translate('Affiliate') }}</span>
                            <span class="sk-side-nav-arrow"></span>
                        </a>
                        <ul class="sk-side-nav-list level-2">
                            <li class="sk-side-nav-item">
                                <a href="{{ route('affiliate.user.index') }}" class="sk-side-nav-link">
                                    <span class="sk-side-nav-text">{{ translate('Affiliate System') }}</span>
                                </a>
                            </li>
                            <li class="sk-side-nav-item">
                                <a href="{{ route('affiliate.user.payment_history') }}" class="sk-side-nav-link">
                                    <span class="sk-side-nav-text">{{ translate('Payment History') }}</span>
                                </a>
                            </li>
                            <li class="sk-side-nav-item">
                                <a href="{{ route('affiliate.user.withdraw_request_history') }}"
                                   class="sk-side-nav-link">
                                    <span class="sk-side-nav-text">{{ translate('Withdraw request history') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @php
                    $support_ticket = DB::table('tickets')
                                ->where('client_viewed', 0)
                                ->where('user_id', Auth::user()->id)
                                ->count();
                @endphp
                <?php /*
                <li class="sk-side-nav-item">
                    <a href="{{ route('support_ticket.index') }}" class="sk-side-nav-link {{ areActiveRoutes(['support_ticket.index'])}}">
                        <i class="las la-atom sk-side-nav-icon"></i>
                        <span class="sk-side-nav-text">{{translate('Support Ticket')}}</span>
                        @if($support_ticket > 0)<span class="badge badge-inline badge-secondary">{{ $support_ticket }}</span> @endif
                    </a>
                </li>*/ ?>

                <li class="sk-side-nav-item">
                    <a href="{{ route('profile') }}" class="sk-side-nav-link {{ areActiveRoutes(['profile'])}}">
                        <div class="sk-side-nav-icon">
                            <svg class="w-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 21">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/account-profile.svg')}}#content"></use>
                            </svg>
                        </div>
                        <span class="sk-side-nav-text">{{translate('Manage Profile')}}</span>
                    </a>
                </li>

                <li class="sk-side-nav-item">
                    <a href="{{ route('credit_cards') }}" class="sk-side-nav-link {{ areActiveRoutes(['credit_cards'])}}">
                        <div class="sk-side-nav-icon">
                            <svg class="w-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22.68 14.83">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/credit_card_icon.svg')}}#content"></use>
                            </svg>
                        </div>
                        <span class="sk-side-nav-text">{{translate('Credit Cards')}}</span>
                    </a>
                </li>

                @if (\App\BusinessSetting::where('type', 'conversation_system')->first()->value == 1)
                    @php
                        $conversation = \App\Conversation::where('sender_id', Auth::user()->id)->where('sender_viewed', 0)->get();
                    @endphp
                    {{--                    <li class="sk-side-nav-item">--}}
                    {{--                        <a href="{{ route('conversations.index') }}" class="sk-side-nav-link {{ areActiveRoutes(['conversations.index', 'conversations.show'])}}">--}}
                    {{--                            <div class="sk-side-nav-icon">--}}
                    {{--                                <svg class="w-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">--}}
                    {{--                                    <use xlink:href="{{static_asset('assets/img/icons/account-messages.svg')}}#content"></use>--}}
                    {{--                                </svg>--}}
                    {{--                            </div>--}}
                    {{--                            <span class="sk-side-nav-text">{{ translate('Messages') }}</span>--}}
                    {{--                            @if (count($conversation) > 0)--}}
                    {{--                                <span class="badge badge-primary">({{ count($conversation) }})</span>--}}
                    {{--                            @endif--}}
                    {{--                        </a>--}}
                    {{--                    </li>--}}
                @endif

            </ul>
        </div>

        @if (\App\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1 && Auth::user()->user_type == 'customer')
            <div>
                <a href="{{ route('shops.create') }}" class="btn btn-block btn-soft-primary rounded-0">
                    </i>{{ translate('Be A Seller') }}
                </a>
            </div>
        @endif
        @if(Auth::user()->user_type == 'seller')
            <hr>
            <h4 class="h5 fw-600 text-center">{{ translate('Sold Amount')}}</h4>
            <!-- <div class="sidebar-widget-title py-3">
                <span></span>
            </div> -->
            @php
                $date = date("Y-m-d");
                $days_ago_30 = date('Y-m-d', strtotime('-30 days', strtotime($date)));
                $days_ago_60 = date('Y-m-d', strtotime('-60 days', strtotime($date)));
            @endphp
            <div class="widget-balance pb-3 pt-1">
                <div class="text-center">
                    <div class="heading-4 strong-700 mb-4">
                        @php
                            $orderTotal = \App\Order::where('seller_id', Auth::user()->id)->where("payment_status", 'paid')->where('created_at', '>=', $days_ago_30)->sum('grand_total');
                            //$orderDetails = \App\OrderDetail::where('seller_id', Auth::user()->id)->where('created_at', '>=', $days_ago_30)->get();
                            //$total = 0;
                            //foreach ($orderDetails as $key => $orderDetail) {
                                //if($orderDetail->order != null && $orderDetail->order != null && $orderDetail->order->payment_status == 'paid'){
                                    //$total += $orderDetail->price;
                                //}
                            //}
                        @endphp
                        <small class="d-block fs-12 mb-2">{{ translate('Your sold amount (current month)')}}</small>
                        <span class="btn btn-primary fw-600 fs-18">{{ single_price($orderTotal) }}</span>
                    </div>
                    <table class="table table-borderless">
                        <tr>
                            @php
                                $orderTotal = \App\Order::where('seller_id', Auth::user()->id)->where("payment_status", 'paid')->sum('grand_total');
                            @endphp
                            <td class="p-1" width="60%">
                                {{ translate('Total Sold')}}:
                            </td>
                            <td class="p-1 fw-600" width="40%">
                                {{ single_price($orderTotal) }}
                            </td>
                        </tr>
                        <tr>
                            @php
                                $orderTotal = \App\Order::where('seller_id', Auth::user()->id)->where("payment_status", 'paid')->where('created_at', '>=', $days_ago_60)->where('created_at', '<=', $days_ago_30)->sum('grand_total');
                            @endphp
                            <td class="p-1" width="60%">
                                {{ translate('Last Month Sold')}}:
                            </td>
                            <td class="p-1 fw-600" width="40%">
                                {{ single_price($orderTotal) }}
                            </td>
                        </tr>
                    </table>
                </div>
                <table>

                </table>
            </div>
        @endif

    </div>
    <a href="{{route('card.register_new_card')}}" class="btn btn-outline-primary btn-block fs-14 md-fs-16 fw-400 py-1 lh-1 py-10px border-width-2 border-primary">{{ toUpper(translate('Register New Card')) }}</a>

    <div class="d-lg-block mt-20px">
        <h3 class="mb-15px fs-14 xl-fs-16 fw-700">{{translate('Help & Info')}}</h3>
        <div class="fs-12 xl-fs-14 text-primary-50">
            <p class="mb-2">
                <a class="hov-text-primary account-side-link" href="tel:{{ get_setting('contact_phone') }}">
                    <svg class="w-25px align-middle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 25">
                        <use xlink:href="{{static_asset('assets/img/icons/account-tel.svg')}}#content"></use>
                    </svg>
                    {{ get_setting('contact_phone') }}
                </a>
            </p>
            <p class="mb-2 account-side-link">
                <svg class="w-25px align-middle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 25">
                    <use xlink:href="{{static_asset('assets/img/icons/account-mail.svg')}}#content"></use>
                </svg>
                <img class="text-email" src="{{static_asset('assets/img/icons/email-text-dashboard.svg')}}" alt="">
            </p>
            <p class="mt-20px">
                <a class="border-bottom border-inherit hov-text-primary" href="{{ route('logout') }}">{{translate('Logout')}}</a>
            </p>
        </div>
    </div>
</div>
