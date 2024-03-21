@extends('frontend.layouts.app')

@section('meta_title'){{ $page->meta_title }}@stop

@section('meta_description'){{ $page->meta_description }}@stop

@section('meta_keywords'){{ $page->tags }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $page->meta_title }}">
    <meta itemprop="description" content="{{ $page->meta_description }}">
    <meta itemprop="image" content="{{ uploaded_asset($page->meta_img) }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ $page->meta_title }}">
    <meta name="twitter:description" content="{{ $page->meta_description }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ uploaded_asset($page->meta_img) }}">
    <meta name="twitter:data1" content="{{ single_price($page->unit_price) }}">
    <meta name="twitter:label1" content="Price">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $page->meta_title }}" />
    <meta property="og:type" content="product" />
    <meta property="og:url" content="{{ route('product', $page->slug) }}" />
    <meta property="og:image" content="{{ uploaded_asset($page->meta_img) }}" />
    <meta property="og:description" content="{{ $page->meta_description }}" />
    <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
    <meta property="og:price:amount" content="{{ single_price($page->unit_price) }}" />
@endsection

@section('content')
    <div class="line-slider-item overflow-hidden">
        <div class="line-slider-image">
            <img
                    class="absolute-full h-100 img-fit"
                    src="{{ uploaded_asset($page->banner) }}"
                    alt=""
                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
            >
            <div class="line-slider-over">
                <div class="row no-gutters">
                    <div class="col-lg-4">
                        <div class="line-slider-over-left">
                            <div class="line-slider-over-box"></div>
                            <div class="line-slider-over-box slogan d-lg-flex justify-content-center">
                                <div class="line-slider-over-box-inner">
                                    <h1 class="font-play fs-25 md-fs-30 xl-fs-45 xxl-fs-60 fw-700 m-0">{{ $page->getTranslation('title') }}</h1>
                                </div>
                            </div>
                            <div class="line-slider-over-box d-none d-lg-block"></div>
                        </div>
                    </div>
                    <div class="col-lg-8 d-none d-lg-block">
                        <div class="line-slider-over-right">
                            <div class="line-slider-over-box"></div>
                            <div class="line-slider-over-box slogan"></div>
                            @if($page->getTranslation('banner_desc'))
                                <div class="line-slider-over-box bg-white text-default-50 fs-16 xl-fs-18 xxl-fs-23 xxxl-fs-28 font-play l-space-1 px-xxl-50px d-flex flex-column justify-content-end lh-1-4">
                                    <p>{{$page->getTranslation('banner_desc')}}</p>
                                </div>
                            @else
                                <div class="line-slider-over-box"></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($page->getTranslation('banner_desc'))
        <div class="my-30px my-md-50px d-lg-none">
            <div class="container">
                <div class="text-default-50 fs-16 font-play l-space-1-2 lh-1-7 mw-850px">
                    <p>{{$page->getTranslation('banner_desc')}}</p>
                </div>
            </div>
        </div>
    @endif
    <div id="contact-form" class="overflow-hidden mb-lg-60px mb-xxl-125px">
        <div class="row no-gutters">
            <div class="col-lg-4 pt-35px pt-lg-100px pt-xxl-175px pb-50px pb-lg-60px bg-black-10 contact-form-right">
                <div class="container-left">
                    <div class="container-right pr-lg-20px">
                        <h2 class="contact-title fs-16 xl-fs-23 xxxl-fs-30 font-play fw-700 pb-10px pb-lg-15px pb-xxl-25px border-bottom border-primary border-width-5 border-lg-width-10">
                            {{ toUpper(translate('Contact Information')) }}
                        </h2>
                    </div>
                    <div class="border-bottom border-width-3 border-black-100 mb-30px mb-lg-50px mb-xxl-70px"></div>
                    <div class="container-right">
                        <div class="contact-information l-space-1-2 text-black-50 fs-11 sm-fs-16 lg-fs-12 xxl-fs-16 fw-500">
                            <h2 class="fs-13 sm-fs-20 lg-fs-14 xxl-fs-20 fw-700 font-play mb-20px mb-lg-30px mb-xxl-45px text-black border-bottom border-inherit d-inline-block">{{toUpper($store->getTranslation('name'))}}</h2>
                            <div class="mb-15px mb-md-25px contact-info-item">
                                <h3 class="fs-13 sm-fs-20 lg-fs-14 xxl-fs-20 fw-700 mb-10px text-black font-play">{{translate('Address')}}</h3>
                                <p class="mw-145px sm-mw-200px lg-mw-145px xxl-mw-200px">{{$store->getTranslation('address')}}</p>
                            </div>
                            <div class="mb-15px mb-md-25px contact-info-item">
                                <h3 class="fs-13 sm-fs-20 lg-fs-14 xxl-fs-20 fw-700 mb-10px text-black font-play">{{translate('Phone Number')}}</h3>
                                <p>{{$store->phone}}</p>
                            </div>
                            @if($store->fax)
                                <div class="mb-15px mb-md-25px contact-info-item">
                                    <h3 class="fs-13 sm-fs-20 lg-fs-14 xxl-fs-20 fw-700 mb-10px text-black font-play">{{translate('Fax')}}</h3>
                                    <p>{{$store->fax}}</p>
                                </div>
                            @endif
                            @if(($store->working_days_1 && $store->working_hours_1) || ($store->working_days_2 && $store->working_hours_2))
                                <div class="mb-15px mb-md-25px contact-info-item">
                                    <h3 class="fs-13 sm-fs-20 lg-fs-14 xxl-fs-20 fw-700 mb-10px text-black font-play">{{translate('Opening Hours')}}</h3>
                                    @if($store->working_days_1 && $store->working_hours_1)
                                        <div class="mb-10px mb-sm-20px">
                                            <div>{{$store->getTranslation('working_days_1')}}:</div>
                                            <div>{{$store->working_hours_1}}</div>
                                        </div>
                                    @endif
                                    @if($store->working_days_2 && $store->working_hours_2)
                                        <div class="mb-10px mb-sm-20px">
                                            <div>{{$store->getTranslation('working_days_2')}}:</div>
                                            <div>{{$store->working_hours_2}}</div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 pt-35px pt-lg-100px pt-xxl-175px pb-50px pb-lg-60px contact-form-left mt-15px mt-lg-0">
                <div class="container-left">
                    <h1 class="contact-title fs-16 xl-fs-23 xxxl-fs-30 font-play fw-700 pb-10px pb-lg-15px pb-xxl-25px border-bottom border-secondary border-width-5 border-lg-width-10">{{toUpper(translate('Contact Form'))}}</h1>
                </div>
                <div class="position-relative">
                    <div class="container-left pl-lg-0 container-lg-right container-xl-right container-xxl-right">
                        <div class="border-bottom border-width-3 border-black-100 mb-30px mb-lg-50px mb-xxl-70px"></div>
                    </div>
                    <div class="container">
                        <div id="contact-form-container" class="md-mw-475px fs-13 sm-fs-16 fw-500">
                            <form id="contact_form" method="POST" action="{{ route('contact.send') }}">
                                @csrf
                                <div class="form-group mb-15px mb-sm-25px">
                                    <div class="form-control-with-label small-focus @if(old('name')) focused @endif ">
                                        <label>{{ translate('Full Name') }}</label>
                                        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}">
                                    </div>
                                    @if ($errors->has('name'))
                                        <div class="invalid-feedback fs-10 d-block" role="alert">
                                            {{ $errors->first('name') }}
                                        </div>
                                    @endif
                                    <div id="nameError" class="invalid-feedback fs-10 d-block" role="alert"></div>
                                </div>
                                <div class="form-group mb-15px mb-sm-25px">
                                    <div class="form-control-with-label small-focus @if(old('email')) focused @endif ">
                                        <label>{{ translate('Email') }}</label>
                                        <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}">
                                    </div>
                                    @if ($errors->has('email'))
                                        <div class="invalid-feedback fs-10 d-block" role="alert">
                                            {{ $errors->first('email') }}
                                        </div>
                                    @endif
                                    <div id="emailError" class="invalid-feedback fs-10 d-block" role="alert"></div>
                                </div>
                                <div class="form-group mb-10px">
                                    <div class="form-control-with-label small-focus textarea-label @if(old('message')) focused @endif ">
                                        <label>{{ translate('Message') }}</label>
                                        <textarea name="message" rows="7" class="form-control resize-off {{ $errors->has('message') ? ' is-invalid' : '' }}">{{ old('message') }}</textarea>
                                    </div>
                                    @if ($errors->has('message'))
                                        <div class="invalid-feedback fs-10 d-block" role="alert">
                                            {{ $errors->first('message') }}
                                        </div>
                                    @endif
                                    <div id="messageError" class="invalid-feedback fs-10 d-block" role="alert"></div>
                                </div>
                                <div class="text-right position-relative mb-20px">
                                    <label class="sk-checkbox fs-12 md-fs-14 text-black-50 mb-5px">
                                        <input type="checkbox" name="agree_policies">
                                        <span class="sk-square-check"></span>
                                        {{ translate('I agree with the')}}
                                        <a class="text-reset hov-text-primary" href="{{ route('termspolicies') }}" target="_blank">{{ translate('Terms&Policies')}}</a>
                                    </label>
                                    <div id="contact-form-error-agree" class="invalid-feedback absolute fs-10 d-block" role="alert"></div>
                                </div>
                                <button id="contact-form-btn" class="btn btn-outline-primary btn-block fs-14 md-fs-18 fw-500 py-10px py-md-20px">{{ toUpper(translate('Submit Form')) }}</button>
                                @if(\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
                                    <div id="recaptcha" class="g-recaptcha" data-sitekey="{{ getRecaptchaKeys()->public }}" data-callback="onSubmit" data-size="invisible"></div>
                                    <div id="contact-form-error-recaptcha" class="invalid-feedback fs-10 text-right" role="alert"></div>
                                @endif
                            </form>
                        </div>
                        <div id="contact-form-success" class="md-mw-700px fw-500 l-space-1-2 fs-18 sm-fs-28 pt-50px pt-sm-100px pt-lg-200px text-default-50" style="display: none;">
                            <p class="fs-20 sm-fs-30 font-play fw-600 mb-30px mb-sm-50px mb-lg-70px text-default">{{translate('Thank you for reaching out!')}}</p>
                            <p>{{translate('We have received your message and we will get to you as soon as possible.')}}</p>
                            <div class="md-mw-475px h-10px bg-secondary mt-30px mt-sm-50px mt-lg-70px"></div>
                        </div>
                    </div>
                    <div class="side-right-bar d-none d-md-block">
                        <a href="{{route('stores')}}" class="side-right-bar-link side-popup-toggle lg-fs-25">
                            <span class="d-block side-right-bar-arrow"></span>
                            <span class="d-block side-right-bar-text-wrap">
                            <span class="side-right-bar-text">{{toUpper(translate('Store Locator'))}}</span>
                        </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @if(\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
    <script type="text/javascript">
      // making the CAPTCHA  a required field for form submission
      $(document).ready(function(){
          @if(\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
          function onSubmit(token) {
            if (token.length > 0) {
                $.ajax({
                    type: "POST",
                    url: '{{ route('contact.send_ajax') }}',
                    data: $('#contact_form').serializeArray(),
                    beforeSend: function() {
                        $('#contact-form-btn').addClass('loader');
                        $('#nameError, #emailError, #messageError').text('');
                    },
                    success: function (data) {
                        if(data.status == 1) {
                            $('#contact-form-container').hide();
                            $('#contact-form-success').show();
                        } else {
                            if(data.status == 3) {
                                if(data.validator) {
                                    if(data.validator.name) {
                                        $('#nameError').text(data.validator.name);
                                    }
                                    if(data.validator.email) {
                                        $('#emailError').text(data.validator.email);
                                    }
                                    if(data.validator.message) {
                                        $('#messageError').text(data.validator.message);
                                    }
                                }
                            } else {
                                SK.plugins.notify('warning', '{{ translate('Something went wrong.') }}');
                            }
                        }
                        $('#contact-form-btn').removeClass('loader');
                        grecaptcha.reset();
                    }
                });
            }
          }
        window.onSubmit = onSubmit;
        $('#contact-form-btn').on("click", function(evt){
          evt.preventDefault();
          if($('input[name="agree_policies"]').prop('checked')==false) {
            $('#contact-form-error-agree').addClass('d-block').text('{{translate('You need to agree with our policies')}}');
            return false;
          } else {
              grecaptcha.execute();
              return false;
          }
        });
          @else
          $("#contact_form").on("submit", function(){
            if($('input[name="agree_policies"]').prop('checked')==false){
              $('#contact-form-error-agree').addClass('d-block').text('{{translate("You need to agree with our policies")}}');
              return false;
            } else {
              $('#contact-form-btn').addClass('loader');
            }
          });
          @endif
      });
      @if(old('error_in_contact') == true)
      $('html, body').animate({
        scrollTop: $('.contact-form-left').offset().top
      }, 500);
      @endif
    </script>
@endsection
