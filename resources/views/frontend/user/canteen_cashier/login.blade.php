
@extends('frontend.layouts.app_cashier')

@section('content')
    <section class="my-55px my-lg-90px my-xxl-125px">
        <div class="container">
            <div class="mb-40px mb-lg-70px mb-md-100px text-center">
                <svg class="h-90px h-md-120px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.47 76.24">
                    <use xlink:href="{{static_asset('assets/img/icons/logo-25.svg')}}#uptown-logo"></use>
                </svg>
            </div>
            <div class="profile">
                <div class="mw-600px mx-auto background-brand-grey px-20px px-md-50px pt-20px pt-md-30px pb-40px pb-md-70px fs-16 sm-fs-20 md-fs-25">
                    <h1 class="fs-25 sm-fs-30 md-fs-35 fw-700 text-center mb-25px mb-sm-30px mb-md-35px">{{toUpper(translate('Login'))}}</h1>

                    <form class="form-default" role="form" action="{{ route('login') }}" method="POST">
                        @csrf

                        <div class="form-group mb-20px mb-md-25px">
                            <input type="text" class="form-control {{ $errors->has('username') ? ' is-invalid' : '' }}" placeholder="{{translate('Username')}}" value="{{ old('username') }}" name="username">
                        </div>

                        <div class="form-group mb-20px mb-md-30px">
                            <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{translate('Password')}}" name="password" id="password">
                        </div>

                        <div class="form-group mb-20px mb-md-30px">
                            <select class="form-control {{ $errors->has('location') ? ' is-invalid' : '' }}" placeholder="{{translate('Location')}}" name="location" >
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block fw-500">{{toUpper(translate('Login'))}}</button>
                    </form>


                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script type="text/javascript">

    </script>
@endsection
