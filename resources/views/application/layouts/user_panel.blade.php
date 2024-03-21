@extends('application.layouts.app')
@section('content')
    <div id="account-panel" class="mt-20px">
        <div class="container">
            <a href="{{ route('application.account') }}" class="fs-14 fw-300 text-black-50 border-bottom border-black-100 pb-5px mb-25px d-block hov-text-secondary hov-border-secondary">
                <span class="arrow left"></span> {{toUpper(translate('My Account'))}}
            </a>
        </div>
        <div class="account-panel-content">
            @yield('panel_content')
        </div>
    </div>
@endsection
