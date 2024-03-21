<a href="{{ route('user.login') }}" class="header-icon">
    @if(Auth::check())
        <span class="header-dot"></span>
    @endif
    <svg class="h-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15.61 16.1">
        <use xlink:href="{{static_asset('assets/img/icons/my-account-icon.svg')}}#my_account"></use>
    </svg>
</a>
