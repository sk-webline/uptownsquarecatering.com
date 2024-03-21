@extends('frontend.layouts.app')

@section('content')
    <section class="my-100px my-md-175px my-xl-250px">
        <div class="container">
            <h1 class="fs-24 md-fs-35 xl-fs-42 xxl-fs-53 fw-600 text-primary mb-50px mb-md-100px text-center">{{translate('Terms & Policies')}}</h1>
            <div class="page-content">
                <div class="terms-res-item">
                    <a href="{{route('terms')}}">{{translate('Terms & Conditions')}}</a>
                </div>
            </div>
        </div>
    </section>
@endsection
