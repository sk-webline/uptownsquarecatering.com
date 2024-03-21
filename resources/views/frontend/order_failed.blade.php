@extends('frontend.layouts.app')

@section('content')
    <section class="my-70px my-lg-150px my-xxl-125px" data-aos="fade-up">
        <div class="container">
            <div class="mw-1350px mx-auto">
                <div class="border-bottom-primary border-default-200 pb-15px mb-15px mb-md-30px">
                    <h1 class="fs-18 md-fs-30 fw-700 m-0 lh-1">{{ toUpper(translate('Error on Completing the Order'))}}</h1>
                </div>
                <div class="text-primary-50 fs-16 md-fs-22">
                    <p>{{  translate('There was an error with your order.') }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
