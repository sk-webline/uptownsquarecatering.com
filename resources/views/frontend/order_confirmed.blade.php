@extends('frontend.layouts.app')

@section('content')
    {{--@php
        $status = $order->orderDetails->first()->delivery_status;
    @endphp--}}
    <section class="my-70px my-lg-150px my-xxl-125px" data-aos="fade-up">
        <div class="container">
            <div class="mw-1350px mx-auto fs-15 md-fs-22">
                <div class="border-bottom-primary border-default-200 pb-15px mb-25px mb-md-70px">
                    <h1 class="fs-18 md-fs-30 fw-700 m-0 lh-1">{{ toUpper(translate('Success')) }}</h1>
                </div>
                <div class="text-primary-50 bg-primary-10 py-15px py-md-35px px-20px px-md-50px mb-25px mb-md-70px">
                    <p class="text-primary mb-25px lh-1-5"><span class="border-bottom border-inherit"><img class="h-10px" src="{{static_asset('assets/img/icons/check-order.svg')}}" alt=""> {{ translate('Your payment has been completed successfully.') }}</span></p>
                    <p>{{ translate('An email will be sent to') }} <span class="fw-700">{{ json_decode($order->shipping_address)->email }}</span> {{translate('to further inform you about your meal package.') }}</p>
                </div>
                <div class="border-left border-width-5 border-primary pl-15px pl-md-30px lh-1">
                    <p class="mb-20px mb-md-45px">{{translate("You'll be redirected to your dashboard in just")}} <span class="count-down">10</span> {{translate('seconds.')}}</p>
                    <p><img class="h-20px h-md-35px mr-1" src="{{static_asset('assets/img/icons/order-user.svg')}}" alt=""> {{translate("If you can't wait, click")}} <a class="d-inline-block fw-700 border-bottom border-width-2 border-inherit hov-text-secondary" href="{{ route('dashboard') }}">{{mb_strtolower(translate("here"))}}</a> {{translate("to go now.")}}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
<script>
    var count = 10;
    var countdown = setInterval(function() {
        $(".count-down").html(count);
        if (count == 0) {
            clearInterval(countdown);
            window.open('{{ route('dashboard') }}', "_self");

        }
        count--;
    }, 1000);
</script>
@endsection
