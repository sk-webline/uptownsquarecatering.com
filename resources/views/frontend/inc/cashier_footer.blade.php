<footer class="fs-13 md-fs-15 lh-1 cashier-footer">
    <div class="py-5px">

        <?php

        use Illuminate\Support\Facades\Session;
        $user = \Illuminate\Support\Facades\Auth::user();
        $organisation = \App\Models\Organisation::findorfail(Session::get('organisation_id'));
        $location = \App\Models\OrganisationLocation::findorfail(Session::get('location_id'));
        ?>
        <div class="cashier-grid">
            <div class="row align-items-center align-text-bottom">
                <div class="col-auto d-none d-md-block border-right-white ">
                    <div class="row">
                        <div class="col sk-side-nav-icon d-inline-block ">
                            <svg class="h-10px" xmlns="http://www.w3.org/2000/svg" fill="white"
                                 viewBox="0 0 15.61 16.1">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/my-account-icon.svg')}}#my_account"></use>
                            </svg>
                        </div>
                        <div class="col-auto text-left pl-0">{{ $user->name }}</div>
                    </div>
                </div>
                <div class="col-auto d-none d-md-block border-right-white ">
                    <div class="row align-items-end">
                        <div class="col sk-side-nav-icon d-inline-block ">
                            <svg class="h-10px" fill="white" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 10.7 11.9">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/organisation_icon.svg')}}#organisation_svg"></use>
                            </svg>
                        </div>
                        <div class="col-auto text-left pl-0">{{ $organisation->name }}</div>
                    </div>
                </div>
                <div class="col-auto d-none d-md-block ">
                    <div class="row align-items-end">
                        <div class="col sk-side-nav-icon d-inline-block ">
                            <svg class="h-10px" xmlns="http://www.w3.org/2000/svg" fill="white"
                                 viewBox="0 0 12.47 9.5">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/location_icon.svg')}}#location_svg"></use>
                            </svg>
                        </div>
                        <div class="col-auto text-left pl-0">{{ $location->name }}</div>
                    </div>
                </div>
                <div class="col text-right">
                    <a href="{{route('logout')}}">{{translate('Logout')}}
                        <svg class="h-10px" xmlns="http://www.w3.org/2000/svg" fill="white"
                             viewBox="0 0 16.19 11.86">
                            <use xlink:href="{{static_asset('assets/img/icons/logout_icon.svg')}}#logout_svg"></use>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
