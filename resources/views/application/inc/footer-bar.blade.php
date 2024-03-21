<div class="footer-bar">
    <div class="row row-cols-5 no-gutters">
        <div class="col">
            <a href="{{ route('application.home') }}" class="footer-bar-link {{(Route::currentRouteName() == 'application.home') ? 'active' : ''}}">
                <span class="icon">
                    <svg class="h-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18.02 18.52">
                        <use xlink:href="{{static_asset('assets/img/icons/footer-home.svg')}}#content"></use>
                    </svg>
                </span>
                <span class="text">{{toUpper(translate('Home'))}}</span>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('application.history') }}" class="footer-bar-link {{(Route::currentRouteName() == 'application.history') ? 'active' : ''}}">
                <span class="icon">
                    <svg class="h-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 17.6 16.53">
                        <use xlink:href="{{static_asset('assets/img/icons/footer-history.svg')}}#content"></use>
                    </svg>
                </span>
                <span class="text">{{toUpper(translate('History'))}}</span>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('application.upcoming_meals') }}" class="footer-bar-link {{(Route::currentRouteName() == 'application.upcoming_meals') ? 'active' : ''}}">
                <span class="icon upcoming-icon">
                    <svg class="h-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 17.07 17.06">
                        <use xlink:href="{{static_asset('assets/img/icons/footer-upcoming.svg')}}#content"></use>
                    </svg>

                    @php

                    use App\Models\CanteenPurchase;
                    use Carbon\Carbon;

                        $user = auth()->guard('application')->user(); // canteen user
                        $today = \Carbon\Carbon::today();
                        $time = Carbon::now()->format('H:i:s');
                        $upcoming_purchases_count = CanteenPurchase::where('canteen_app_user_id', $user->id )
                                    ->where('date', '>', $today->format('Y-m-d'))
                                    ->orWhere(function ($query) use ($today, $time) {
                                         $query->where('date', '=', $today->format('Y-m-d'))
                                                  ->where('break_hour_from', '>', $time);
                                    })
                                    ->orderBy('date')->count();
                    @endphp
                    <span class="footer-number">{{$upcoming_purchases_count}}</span>
                </span>
                <span class="text">{{toUpper(translate('Upcoming'))}}</span>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('application.cart') }}" class="footer-bar-link {{(Route::currentRouteName() == 'application.cart') ? 'active' : ''}}">
                <span class="icon">
                    <svg class="h-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 17.52 15.8">
                        <use xlink:href="{{static_asset('assets/img/icons/footer-cart.svg')}}#content"></use>
                    </svg>
                    <span class="footer-number cart-number">
                        @if(\Illuminate\Support\Facades\Session::has('total_items') && \Illuminate\Support\Facades\Session::get('total_items')>0)
                            {{\Illuminate\Support\Facades\Session::get('total_items')}}
                        @else
                            0
                        @endif
                    </span>
                </span>
                <span class="text">{{toUpper(translate('Cart'))}}</span>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('application.account') }}" class="footer-bar-link {{(in_array(Route::currentRouteName(), isApplicationAccountPage())) ? 'active' : ''}}">
                <span class="icon">
                    <svg class="h-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 17.44 17.45">
                        <use xlink:href="{{static_asset('assets/img/icons/footer-account.svg')}}#content"></use>
                    </svg>
                </span>
                <span class="text">{{toUpper(translate('Account'))}}</span>
            </a>
        </div>
    </div>
</div>
