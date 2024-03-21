<header class="p-5px bg-login-box position-relative">
    <a href="{{ route('application.home') }}">
        <img class="h-30px" height="30"
             src="{{static_asset('assets/img/icons/small-logo.svg')}}"
             alt="{{ get_setting('site_name') }}">
    </a>
    <div class="megamenu-toggle">
        <div class="icon">
            <div class="top"></div>
            <div class="bottom"></div>
        </div>
    </div>
</header>
<div class="fixed-header bg-white z-1020">
    <div class="p-5px bg-login-box">
        <a href="{{ route('application.home') }}">
            <img class="h-30px" height="30"
                 src="{{static_asset('assets/img/icons/small-logo.svg')}}"
                 alt="{{ get_setting('site_name') }}">
        </a>
        <div class="megamenu-toggle">
            <div class="icon">
                <div class="top"></div>
                <div class="bottom"></div>
            </div>
        </div>
    </div>
</div>
@include('application.partials.megamenu')
