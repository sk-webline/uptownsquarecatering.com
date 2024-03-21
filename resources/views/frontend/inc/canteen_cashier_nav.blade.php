<?php

    use Illuminate\Support\Facades\Session;
    $user = \Illuminate\Support\Facades\Auth::user();
    $organisation = \App\Models\Organisation::findorfail(Session::get('organisation_id'));
    $location = \App\Models\OrganisationLocation::findorfail(Session::get('location_id'));

?>
<div class="cashier-header py-10px py-md-30px py-xxl-50px">
    <div class="cashier-grid">
        <div class="row gutters-10 sm-gutters-15 align-items-center">
            <div class="col-auto">

                <a href="{{ route('canteen_cashier.dashboard') }}">
                    <svg class="h-40px h-md-75px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.47 76.24">
                        <use xlink:href="{{static_asset('assets/img/icons/logo-25.svg')}}#uptown-logo"></use>
                    </svg>

                </a>


            </div>

            <div class="fs-10 sm-fs-12 md-fs-15 lg-fs-20 col text-right">
                @if($route!=null && $route_text!=null)
                    <a href="{{$route}}"><span class="text-underline">{{$route_text}}</span></a>
                @else
                    <div class="d-md-none">
                        <div class="text-truncate">
                            <svg class="h-1em" xmlns="http://www.w3.org/2000/svg" fill="var(--primary)"
                                 viewBox="0 0 15.61 16.1">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/my-account-icon.svg')}}#my_account"></use>
                            </svg> {{ $user->name }}
                        </div>
                        <div class="text-truncate">
                            <svg class="h-1em" fill="var(--primary)" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 10.7 11.9">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/organisation_icon.svg')}}#organisation_svg"></use>
                            </svg>
                            {{ $organisation->name }}
                        </div>
                        <div class="text-truncate">
                            <svg class="h-1em" xmlns="http://www.w3.org/2000/svg" fill="var(--primary)"
                                 viewBox="0 0 12.47 9.5">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/location_icon.svg')}}#location_svg"></use>
                            </svg>
                            {{ $location->name }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

