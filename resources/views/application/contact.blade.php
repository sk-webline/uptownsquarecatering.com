@extends('application.layouts.app')

@section('meta_title'){{ translate('Get in Touch') }}@endsection

@section('content')
    <div id="contact" class="my-20px">
        <div class="container">
            <h1 class="fs-14 fw-300 text-black-50 border-bottom border-black-100 pb-5px mb-30px">{{toUpper(translate('Get in Touch'))}}</h1>
            <a href="tel:+35799887744" class="mb-25px contact-link-box">
                <span class="row align-items-center">
                    <span class="d-block col-auto">
                        <svg class="size-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15.76 20.88">
                            <use xlink:href="{{static_asset('assets/img/icons/contact-telephone.svg')}}#content"></use>
                        </svg>
                    </span>
                    <span class="d-block col">+357 99 88 77 44</span>
                </span>
            </a>
            <div class="contact-link-box mb-30px">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <svg class="size-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.57 13.75">
                            <use xlink:href="{{static_asset('assets/img/icons/contact-email.svg')}}#content"></use>
                        </svg>
                    </div>
                    <div class="col">email@here.com</div>
                </div>
            </div>
            <h2 class="fs-14 fw-300 text-black-50 border-bottom border-black-100 pb-5px mb-10px">{{toUpper(translate('Social Media'))}}</h2>
            <div class="row gutters-5">
                <div class="col-auto">
                    <a href="#" target="_blank">
                        <img class="h-25px" src="{{static_asset('assets/img/icons/app-facebook.svg')}}" alt="">
                    </a>
                </div>
                <div class="col-auto">
                    <a href="#" target="_blank">
                        <img class="h-25px" src="{{static_asset('assets/img/icons/app-instagram.svg')}}" alt="">
                    </a>
                </div>
                <div class="col-auto">
                    <a href="#" target="_blank">
                        <img class="h-25px" src="{{static_asset('assets/img/icons/app-youtube.svg')}}" alt="">
                    </a>
                </div>
                <div class="col-auto">
                    <a href="#" target="_blank">
                        <img class="h-25px" src="{{static_asset('assets/img/icons/app-tripadvisor.svg')}}" alt="">
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
