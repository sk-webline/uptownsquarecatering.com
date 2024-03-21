@extends('application.layouts.app')

@section('meta_title'){{ translate('Order Completed') }}@endsection

@section('content')

    <div id="order-success" class="my-20px">
        <div class="container">
            <h1 class="fs-18 fw-700 mb-10px">
                <span class="order-error-icon">
                    <svg class="h-10px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.39 25.39">
                        <use xlink:href="{{static_asset('assets/img/icons/x_icon_error.svg')}}#x-icon-error"></use>
                    </svg>
                </span>
                {{toUpper(translate('Something went wrong'))}}
            </h1>
        </div>
        <div class="bg-login-box py-20px lh-1-5 mb-30px">
            <div class="container">
                @if(isset($error_message) && $error_message!=null)
                <p>
                    {{$error_message}}
                </p>

                @endif

                </div>
        </div>

        </div>
    </div>
@endsection


