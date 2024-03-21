<div class="fixed-footer">
    <footer>
        <div class="py-10px py-md-20px">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-auto fs-14 d-none d-md-block">
                        {{ toUpper(translate('By'))}}: <a href="https://www.skwebline.net/" target="_blank">{{ toUpper(translate('SK Webline Ltd'))}}</a>
                    </div>
                    <div class="col">
                        <div class="row gutters-5 md-gutters-15 xl-gutters-30 justify-content-end">
                            <div class="col col-md-auto order-md-2 footer-item">{{ get_setting('contact_phone') }}</div>
                            <div class="col-auto col-md-auto order-md-1 footer-item"><img class="application-email" src="{{static_asset('assets/img/icons/email-text.svg')}}" alt=""></div>
                            <div class="text-right col-md-auto order-md-3 footer-item mt-5px mt-md-0"><a href="{{route('termspolicies')}}">{{translate('Terms & Policies')}}</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('application.partials.by_skwebline')
    </footer>
</div>
