<div class="megamenu">
    <div class="megamenu-scroll c-scrollbar">
        <div class="megamenu-header p-5px position-relative">
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
        <div class="megamenu-body d-flex flex-column justify-content-center fs-18">
            <div class="container">
                <div class="py-40px">
                    <ul>
                        <li>
                            <a href="{{route('application.contact')}}">{{ucwords(translate('Get in Touch'))}}</a>
                        </li>
                        <li>
                            <a href="#">{{translate('Terms & Policies')}}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="megamenu-footer">
            @include('application.inc.footer-bar')
            @include('application.partials.by_skwebline')
        </div>
    </div>
</div>
