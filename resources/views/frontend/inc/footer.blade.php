<div class="fixed-footer">
    @if(in_array(Route::currentRouteName(), isPageWithPrice()))
        <div class="mb-5px fs-13 md-fs-16 text-primary-50">
            <div class="container">
                <p>{{includeVatText()}}</p>
            </div>
        </div>
    @endif
    <footer class="ff-Roboto">
        <div class="py-10px py-md-20px">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-auto fs-14 d-none d-md-block">
                        {{ toUpper(translate('By'))}}: <a href="https://www.skwebline.net/" target="_blank">{{ toUpper(translate('SK Webline Ltd'))}}</a>
                    </div>
                    <div class="col">
                        <div class="row gutters-5 md-gutters-15 xl-gutters-30 justify-content-end">
                            <div class="col col-md-auto order-md-2 footer-item">{{ get_setting('contact_phone') }}</div>
                            <div class="col-auto col-md-auto order-md-1 footer-item"><img class="text-email" src="{{static_asset('assets/img/icons/email-text.svg')}}" alt=""></div>
                            <div class="text-right col-md-auto order-md-3 footer-item mt-5px mt-md-0"><a href="{{route('termspolicies')}}">{{translate('Terms & Policies')}}</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="py-10px bg-white d-md-none fs-12">
            <div class="container">
                {{ toUpper(translate('By'))}}: <a href="https://www.skwebline.net/" target="_blank">{{ toUpper(translate('SK Webline Ltd'))}}</a>
            </div>
        </div>
    </footer>
</div>


