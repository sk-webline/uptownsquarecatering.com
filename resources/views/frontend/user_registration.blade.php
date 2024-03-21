@extends('frontend.layouts.app')

@section('content')
    @php
        $val_city = old('city');
    @endphp
    <section class="my-50px my-lg-90px my-xxl-125px">
        <div class="bg-login py-20px py-sm-40px text-center l-space-1-2 text-default-60 fs-11 sm-fs-18">
            <div class="container">
                <div class="mw-475px mx-auto">
                    <h1 class="text-secondary fw-700 font-play fs-20 sm-fs-40 mb-10px mb-sm-20px">{{ translate('Create your account')}}</h1>
                    <p>{{translate('Creating an account has many benefits: check out faster, keep your address saved, track orders and more.')}}</p>
                </div>
            </div>
        </div>
        <div class="profile mt-20px mt-sm-30px">
            <div class="container">
                <div class="mw-365px mx-auto fs-13 sm-fs-16 fw-500">
                    <form id="reg-form" class="form-default" role="form" action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="form-group mb-10px mb-sm-15px">
                            <div class="form-control-with-label small-focus small-field @if(old('name')) focused @endif ">
                                <label>{{ translate('Name') }}</label>
                                <input type="text" class="form-control form-no-space{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" name="name">
                            </div>
                            @if ($errors->has('name'))
                                <div class="invalid-feedback fs-10 d-block" role="alert">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                        <div class="form-group mb-10px mb-sm-15px">
                            <div class="form-control-with-label small-focus small-field @if(old('surname')) focused @endif ">
                                <label>{{ translate('Surname') }}</label>
                                <input type="text" class="form-control{{ $errors->has('surname') ? ' is-invalid' : '' }}" value="{{ old('surname') }}" name="surname">
                            </div>
                            @if ($errors->has('surname'))
                                <div class="invalid-feedback fs-10 d-block" role="alert">{{ $errors->first('surname') }}</div>
                            @endif
                        </div>
                        <div class="form-group mb-10px mb-sm-15px">
                            <div class="form-control-with-label small-focus small-field @if(old('email')) focused @endif ">
                                <label>{{ translate('Email') }}</label>
                                <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" name="email">
                            </div>
                            @if ($errors->has('email'))
                                <div class="invalid-feedback fs-10 d-block" role="alert">{{ $errors->first('email') }}</div>
                            @endif
                        </div>

                        <div class="form-group mb-10px mb-sm-15px">
                            <div class="form-control-with-label small-focus small-field always-focused ">
                                <label>{{ translate('Country') }}</label>
                                <select class="form-control sk-selectpicker{{ $errors->has('country') ? ' is-invalid' : '' }}" data-live-search="true" name="country">
                                    @foreach (\App\Country::getActiveCountriesForShipping() as $key => $country)
                                        <option value="{{ $country->id }}" @if(old('country') == $country->id || (!old('country') && $country->id == 54)) selected @endif>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($errors->has('country'))
                                <div class="invalid-feedback fs-10 d-block" role="alert">
                                    {{ $errors->first('country') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group mb-10px mb-sm-15px">
                            <div class="form-control-with-label small-focus small-field always-focused ">
                                <label>{{ translate('City') }}</label>
                                <select class="form-control sk-selectpicker{{ $errors->has('city') ? ' is-invalid' : '' }}" data-live-search="true" name="city">
                                    <option value="">{{translate('City')}}</option>
                                </select>
                            </div>
                            @if ($errors->has('city'))
                                <div class="invalid-feedback fs-10 d-block" role="alert">
                                    {{ $errors->first('city') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group mb-10px mb-sm-15px">
                            <div class="position-relative">
                                <input type="number" lang="en" min="0" class="form-control form-control-phone{{ $errors->has('phone') ? ' is-invalid' : '' }}" value="{{old('phone')}}" name="phone">
                                <div class="form-control-phone-code"></div>
                                <input type="hidden" name="phone_code" value="">
                            </div>
                            @if ($errors->has('phone'))
                                <div class="invalid-feedback fs-10 d-block" role="alert">
                                    {{ $errors->first('phone') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group mb-10px mb-sm-15px">
                            <div class="form-control-with-label small-focus small-field">
                                <label>{{ translate('Password') }}</label>
                                <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password">
                            </div>
                            @if ($errors->has('password'))
                                <div class="invalid-feedback fs-10 d-block" role="alert">{{ $errors->first('password') }}</div>
                            @endif
                        </div>

                        <div class="form-group mb-10px">
                            <div class="form-control-with-label small-focus small-field">
                                <label>{{ translate('Confirm Password') }}</label>
                                <input type="password" class="form-control" name="password_confirmation">
                            </div>
                        </div>

                        <div class=" mb-10px fs-11 sm-fs-14 text-black-50">
                            <label class="sk-checkbox m-0">
                                <input type="checkbox" name="agree_policies">
                                <span class="sk-square-check"></span>
                                {{ translate('I agree with the')}}
                                <a class="text-reset hov-text-primary" href="{{ route('termspolicies') }}" target="_blank">{{ translate('Terms&Policies')}}</a>
                            </label>
                            <div id="register-form-error-agree" class="invalid-feedback fs-10 d-block mt-0 mb-10px" role="alert"></div>
                        </div>

                        <button id="register-form-btn" class="btn btn-outline-primary btn-block fw-500 fs-14 sm-fs-16">{{toUpper(translate('Create Account'))}}</button>

                        @if(\App\BusinessSetting::where('type', 'google_recaptcha')->first()->value == 1)
                            <div id="recaptcha" class="g-recaptcha" data-sitekey="{{ getRecaptchaKeys()->public }}" data-callback="onSubmit" data-size="invisible"></div>
                            <div id="register-form-error-recaptcha" class="invalid-feedback fs-10 text-right" role="alert"></div>
                        @endif
                    </form>
                    @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1 || \App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                        <div class="separator mb-3">
                            <span class="bg-white px-3 opacity-60">{{ translate('Or Join With')}}</span>
                        </div>
                        <ul class="list-inline social colored text-center mb-5">
                            @if (\App\BusinessSetting::where('type', 'facebook_login')->first()->value == 1)
                                <li class="list-inline-item">
                                    <a href="{{ route('social.login', ['provider' => 'facebook']) }}" class="facebook">
                                        <i class="lab la-facebook-f"></i>
                                    </a>
                                </li>
                            @endif
                            @if(\App\BusinessSetting::where('type', 'google_login')->first()->value == 1)
                                <li class="list-inline-item">
                                    <a href="{{ route('social.login', ['provider' => 'google']) }}" class="google">
                                        <i class="lab la-google"></i>
                                    </a>
                                </li>
                            @endif
                            @if (\App\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                                <li class="list-inline-item">
                                    <a href="{{ route('social.login', ['provider' => 'twitter']) }}" class="twitter">
                                        <i class="lab la-twitter"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    @endif
                    <div class="text-center mt-35px mt-sm-50px border-top border-default-200 text-default-50 pt-20px pt-sm-30px">
                        <p class="mb-5px">{{ translate('Do you already have an account?')}}</p>
                        <a href="{{ route('user.login') }}" class="text-secondary border-bottom border-inherit hov-text-primary fs-16">
                            {{toUpper(translate('Login Now'))}}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
              $('#reg-form').submit();
            }
          }
        window.onSubmit = onSubmit;
        $("#register-form-btn").on("click", function(evt){
          evt.preventDefault();
          if($('input[name="agree_policies"]').prop('checked')==false) {
            $('#register-form-error-agree').addClass('d-block').text('{{translate('You need to agree with our policies')}}');
            return false;
          } else {
            $('#register-form-btn').addClass('loader');
            grecaptcha.execute();
            return true;
          }
        });
          @else
          $("#reg-form").on("submit", function(){
            if($('input[name="agree_policies"]').prop('checked')==false){
              $('#register-form-error-agree').addClass('d-block').text('{{translate("You need to agree with our policies")}}');
              return false;
            } else {
              $('#register-form-btn').addClass('loader');
            }
          });
          @endif
      });


      $(document).on('change', '[name=country]', function() {
        var country = $(this).val();
        get_city(country);
        get_phone_code(country);
      });

      $(document).ready(function() {
        get_city($('[name="country"]').val());
        get_phone_code($('[name="country"]').val());
        get_selected_city($('[name="country"]').val(), {{ $val_city }});
      });

      function get_city(country) {
        $('[name="city"]').html("");
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{route('get-city')}}",
          type: 'POST',
          data: {
            country_id: country
          },
          success: function (response) {
            var obj = JSON.parse(response);
            if(obj != '') {
              $('[name="city"]').html(obj);
              SK.plugins.bootstrapSelect('refresh');
            }
          }
        });
      }

      function get_selected_city(country, city) {
        $('[name="city"]').html("");
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{route('get-selected-city')}}",
          type: 'POST',
          data: {
            country_id: country,
            city_id: city
          },
          success: function (response) {
            var obj = JSON.parse(response);
            if(obj != '') {
              $('[name="city"]').html(obj);
              SK.plugins.bootstrapSelect('refresh');
              $('.delivery-info-content').removeClass('loader');
            }
          }
        });
      }

      function get_phone_code(country) {
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{route('get-phone-code')}}",
          type: 'POST',
          data: {
            country_id: country,
          },
          success: function (response) {
            var obj = JSON.parse(response);
            if(obj != '') {
              $('[name="phone_code"]').attr('value', obj);
              $('.form-control-phone-code').text('+' + obj);
            }
          }
        });
      }
    </script>
@endsection
