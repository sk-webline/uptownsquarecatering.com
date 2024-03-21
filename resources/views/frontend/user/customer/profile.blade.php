@extends('frontend.layouts.user_panel')

@section('panel_content')
    @php
        $full_name = getAccountName(Auth::user()->name);
    @endphp
    <h1 class="fs-16 mb-15px text-primary-50 fw-700 lh-1">{{ toUpper(translate('Manage Profile')) }}</h1>
    <!-- Basic Info-->
    <div class="background-brand-grey px-10px pt-10px px-md-30px pt-md-30px pb-md-15px px-xl-40px pt-xl-40px pb-xl-10px fs-13 md-fs-16 mb-10px profile-info-content">
        <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row gutters-5 md-gutters-15 xxl-gutters-25">
                <div class="col-sm-6">
                    <div class="form-group mb-10px mb-md-15px mb-xl-30px">
                        <div class="form-control-with-label small-focus @if($full_name['name']) focused @endif ">
                            <label>{{ translate('First Name') }}</label>
                            <input type="text" class="form-control form-no-space" name="name" value="{{ $full_name['name'] }}">
                        </div>
                        @if ($errors->has('name'))
                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group mb-10px mb-md-15px mb-xl-30px">
                        <div class="form-control-with-label small-focus @if($full_name['surname']) focused @endif ">
                            <label>{{ translate('Last Name') }}</label>
                            <input type="text" class="form-control" name="surname" value="{{ $full_name['surname'] }}">
                        </div>
                        @if ($errors->has('surname'))
                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                {{ $errors->first('surname') }}
                            </div>
                        @endif
                    </div>
                </div>
                <?php /*
                <div class="col-sm-6">
                    <div class="form-group mb-10px mb-md-15px mb-xl-30px">
                        <div class="form-control-with-label small-focus always-focused">
                            <label>{{ translate('Country') }}</label>
                            <select id="country-user"  class="form-control sk-selectpicker" data-live-search="true" data-placeholder="{{translate('Select your country')}}" name="country">
                                @foreach (\App\Country::getActiveCountriesForShipping() as $key => $country)
                                    <option value="{{ $country->id }}" @if(Auth::user()->country == $country->id) selected @endif>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group mb-10px mb-md-15px mb-xl-30px" data-rel="country-user">
                        <div class="form-control-with-label small-focus always-focused">
                            <label>{{ translate('City') }}</label>
                            <select class="form-control sk-selectpicker" data-live-search="true" name="city">
                                <option value="">{{ translate('Select City') }}</option>
                            </select>
                            <input type="text" name="city_name" class="form-control fs-13 md-fs-16 d-none" disabled>
                        </div>
                        @if ($errors->has('city_name'))
                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                {{ $errors->first('city_name') }}
                            </div>
                        @endif
                    </div>
                </div>*/ ?>
                <div class="col-sm-6">
                    <div class="form-group mb-10px mb-md-15px mb-xl-30px">
                        <div class="form-control-with-label small-focus always-focused">
                            <label>{{ translate('Email') }}</label>
                            <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : ''}}" name="email" value="{{ Auth::user()->email }}" >
                        </div>
                        @if ($errors->has('email'))
                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                {{ $errors->first('email') }}
                            </div>
                        @endif
                    </div>
                </div>
                <?php /*
                <div class="col-sm-6">
                    <div class="form-group mb-10px mb-md-15px mb-xl-30px">
                        <div class="form-control-with-label small-focus always-focused">
                            <label>{{ translate('Phone') }}</label>
                            <input type="text" class="form-control form-control-phone" name="phone" value="{{ Auth::user()->phone }}">
                            <div class="form-control-phone-code" data-rel="country-user">+ {{ Auth::user()->phone_code }}</div>
                            <input type="hidden" name="phone_code" value="" data-rel="country-user">
                        </div>
                        @if ($errors->has('phone'))
                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                {{ $errors->first('phone') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group mb-10px mb-md-15px mb-xl-30px">
                        <div class="form-control-with-label small-focus @if(Auth::user()->address) focused @endif ">
                            <label>{{ translate('Address') }}</label>
                            <input type="text" class="form-control" name="address" value="{{ Auth::user()->address }}">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group mb-10px mb-md-15px mb-xl-30px">
                        <div class="form-control-with-label small-focus @if(Auth::user()->postal_code) focused @endif ">
                            <label>{{ translate('Post Code') }}</label>
                            <input type="text" class="form-control" name="postal_code" value="{{ Auth::user()->postal_code }}">
                        </div>
                    </div>
                </div>*/ ?>
                <div class="col-sm-6">
                    <div class="form-group mb-10px mb-md-15px mb-xl-30px">
                        <div class="form-control-with-label small-focus">
                            <label>{{ translate('Password') }}</label>
                            <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : ''}}" name="password">
                        </div>
                        @if ($errors->has('password'))
                            <div class="invalid-feedback fs-10 d-block" role="alert">
                                {{ $errors->first('password') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group mb-10px mb-md-15px mb-xl-30px">
                        <div class="form-control-with-label small-focus">
                            <label>{{ translate('Repeat Password')}}</label>
                            <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : ''}}"  name="password_confirmation">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 ml-auto">
                    <button type="submit" class="btn btn-outline-primary btn-block py-10px py-sm-13px py-md-15px fs-14">{{toUpper(translate('Save Changes'))}}</button>
                </div>
            </div>
        </form>
    </div>
    <?php /*
    <div class="bg-account p-10px p-md-30px px-xl-50px py-xl-40px fs-13 md-fs-16 lg-fs-12 xxl-fs-16 mb-20px">
        <h3 class="fs-16 fw-600 mb-20px">{{translate('Addresses')}}</h3>
        <div class="row gutters-5 md-gutters-15 xxl-gutters-30">
            @foreach (Auth::user()->addresses as $key => $address)
                <div class="col-lg-6 mb-10px mb-md-15px mb-xl-25px">
                    <div class="border border-black-100 bg-white py-xxl-25px p-20px @if ($address->set_default) pr-70px @endif position-relative h-100">
                        <div>
                            <span class="fw-600">{{ translate('Address') }}:</span> <span class="text-default-50">{{ $address->address }}</span>
                        </div>
                        <div>
                            <span class="fw-600">{{ translate('City') }}:</span> <span class="text-default-50">{{ getCityName($address->city) }}</span>
                        </div>
                        <div>
                            <span class="fw-600">{{ translate('Country') }}:</span> <span class="text-default-50">{{ \App\Country::find($address->country)->name }}</span>
                        </div>
                        <div>
                            <span class="fw-600">{{ translate('Postal Code') }}:</span> <span class="text-default-50">{{ $address->postal_code }}</span>
                        </div>
                        <div>
                            <span class="fw-600">{{ translate('Phone') }}:</span> <span class="text-default-50">+{{ $address->phone_code }} {{ $address->phone }}</span>
                        </div>
                        @if ($address->set_default)
                            <div class="position-absolute right-0 bottom-0 pr-10px pb-10px">
                                <span class="badge badge-inline badge-primary rounded-0">{{ translate('Default') }}</span>
                            </div>
                        @endif
                        <div class="dropdown position-absolute right-0 top-0">
                            <button class="btn bg-gray p-1" type="button" data-toggle="dropdown">
                                <i class="la la-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right dropdown-address p-0" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="javascript:void(0)" onclick="edit_address('{{$address->id}}')">
                                    {{ translate('Edit') }}
                                </a>
                                @if (!$address->set_default)
                                    <a class="dropdown-item" href="{{ route('addresses.set_default', $address->id) }}">{{ translate('Make This Default') }}</a>
                                @endif
                                <a class="dropdown-item" href="{{ route('addresses.destroy', $address->id) }}">{{ translate('Delete') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="col-lg-6 mb-10px mb-md-15px mb-xl-25px">
                <div class="border p-10px c-pointer text-center bg-default-10 fw-600 h-100 min-h-120px xxl-min-h-140px d-flex align-items-center justify-content-center side-popup-toggle" data-rel="new-address-modal">
                    + {{ translate('Add New Address') }}
                </div>
            </div>
        </div>
    </div>*/?>
    <div class="text-right fs-12 text-primary-60">
        <p class="mb-1">{{translate('You can close your account from here')}}</p>
        <p>
            <a href="javascript:void(0)" class="d-inline-block text-primary-50 fw-700 border-bottom border-inherit hov-text-primary close-account-link">
                {{toUpper(translate('Terminate Account'))}}
            </a>
        </p>
    </div>
    <?php /*
    <!-- Email Change -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Change your email')}}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('user.change.email') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-2">
                        <label>{{ translate('Your Email') }}</label>
                    </div>
                    <div class="col-md-10">
                        <div class="input-group mb-3">
                          <input type="email" class="form-control" placeholder="{{ translate('Your Email')}}" name="email" value="{{ Auth::user()->email }}" />
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary new-email-verification">
                                    <span class="d-none loading">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        {{ translate('Sending Email...') }}
                                    </span>
                                    <span class="default">{{ translate('Verify') }}</span>
                                </button>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary">{{translate('Update Email')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    */ ?>
@endsection

@section('modal')
    <div class="modal fade" id="close-account-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom modal-dialog-centered mw-275px sm-mw-400px mx-auto text-center" role="document">
            <div class="modal-content border-0 modal-shadow">
                <div class="modal-body text-tertiary p-0" id="close_account_body">
                    <div class="modal-header px-20px py-5px">
                        <button type="button" class="close fs-30 ml-auto" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="p-20px">
                        <h5 class="modal-title fs-16 sm-fs-25 fw-700 mb-3 text-primary">{{ toUpper(translate('Account Closure')) }}</h5>
                        <p class="mb-2 mb-sm-4 fs-12 sm-fs-14 lh-1-2 text-default px-sm-40px text-quaternary">{{ translate('An email will be sent to the administrator to handle your request') }}</p>
                        <form id="closure-form" class="form-default" role="form" action="{{ route('user.close_account') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <button id="closure-form-btn" type="submit" class="btn btn-primary fs-12 sm-fs-16 px-4 py-2">{{ toUpper(translate('Okay')) }}</button>
                            </div>
                            <a class="close-account-cancel border-bottom border-inherit hov-text-primary c-pointer fs-11 sm-fs-16">{{ translate('Cancel') }}</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('frontend.user.address.addresses_modal_layout')
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    @if(App::getLocale() == "gr")
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/localization/messages_el.min.js"></script>
    @endif
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script type="text/javascript">
        /*Validations*/
        $('#add-address-form').validate({
            errorClass: 'is-invalid',
            rules: {
                address: {
                    required: true,
                },
                country: {
                    required: true,
                },
                city: {
                    required: true,
                },
                city_name: {
                    required: true,
                },
                postal_code: {
                    required: true,
                },
                phone: {
                    required: true,
                    digits: true,
                    minlength: 8,
                },
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") === "address" ) {
                    $("#add_address_message").html(error);
                }
                else if (element.attr("name") === "country" ) {
                    $("#add_country_message").html(error);
                }
                else if (element.attr("name") === "city" ) {
                    $("#add_city_message").html(error);
                }
                else if (element.attr("name") === "city_name" ) {
                    $("#add_city_name_message").html(error);
                }
                else if (element.attr("name") === "postal_code" ) {
                    $("#add_postal_message").html(error);
                }
                else if (element.attr("name") === "phone" ) {
                    $("#add_phone_message").html(error);
                }
            }
        });

        function editValidate() {
            $('#edit-address-form').validate({
                errorClass: 'is-invalid',
                rules: {
                    address: {
                        required: true,
                    },
                    country: {
                        required: true,
                    },
                    city: {
                        required: true,
                    },
                    city_name: {
                        required: true,
                    },
                    postal_code: {
                        required: true,
                    },
                    phone: {
                        required: true,
                        digits: true,
                        minlength: 8,
                    },
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") === "address" ) {
                        $("#edit_address_message").html(error);
                    }
                    else if (element.attr("name") === "country" ) {
                        $("#edit_country_message").html(error);
                    }
                    else if (element.attr("name") === "city" ) {
                        $("#edit_city_message").html(error);
                    }
                    else if (element.attr("name") === "city_name" ) {
                        $("#edit_city_name_message").html(error);
                    }
                    else if (element.attr("name") === "postal_code" ) {
                        $("#edit_postal_message").html(error);
                    }
                    else if (element.attr("name") === "phone" ) {
                        $("#edit_phone_message").html(error);
                    }
                }
            });
        }

      function add_new_address(){
        $('#new-address-modal').modal('show');
      }

      $('.new-email-verification').on('click', function() {
        $(this).find('.loading').removeClass('d-none');
        $(this).find('.default').addClass('d-none');
        var email = $("input[name=email]").val();

        $.post('{{ route('user.new.verify') }}', {_token:'{{ csrf_token() }}', email: email}, function(data){
          data = JSON.parse(data);
          $('.default').removeClass('d-none');
          $('.loading').addClass('d-none');
          if(data.status == 2)
            SK.plugins.notify('warning', data.message);
          else if(data.status == 1)
            SK.plugins.notify('success', data.message);
          else
            SK.plugins.notify('danger', data.message);
        });
      });

      function edit_address(address) {
        var url = '{{ route("addresses.edit", ":id") }}';
        url = url.replace(':id', address);

        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: url,
          type: 'GET',
          success: function (response) {
            $('#edit-address-modal-content').html(response);
            $('body').addClass('side-popup-opened');
            $('#edit-address-modal').addClass('active');
            SK.plugins.bootstrapSelect('refresh');
            var country = $("#country-edit").val();
            var country_rel = 'country-edit';
            if (country==='54') {
              $('[data-rel="'+ country_rel +'"] select[name="city"]').prop('disabled', false).prop('required', true).removeClass('d-none').parent('div').removeClass('d-none');
              $('[data-rel="'+ country_rel +'"] input[name="city_name"]').prop('disabled', true).prop('required', false).addClass('d-none');
              get_selected_city(country, $('[name="selected_city"]').val(), country_rel);
              SK.plugins.bootstrapSelect('refresh');
            } else {
              $('[data-rel="'+ country_rel +'"] select[name="city"]').prop('disabled', true).prop('required', false).addClass('d-none');
              $('[data-rel="'+ country_rel +'"] input[name="city_name"]').prop('disabled', false).prop('required', true).removeClass('d-none').attr('value', $('[name="selected_city"]').val());
              SK.plugins.bootstrapSelect('refresh');
            }
            get_phone_code(country, country_rel);
              editValidate();
          }
        });
      }

      $(document).on('change', '[name=country]', function() {
        var country = $(this).val();
        var country_rel = $(this).attr('id');
        if (country==='54') {
          $('[data-rel="'+ country_rel +'"] select[name="city"]').prop('disabled', false).prop('required', true).removeClass('d-none').parent('div').removeClass('d-none');
          $('[data-rel="'+ country_rel +'"] input[name="city_name"]').prop('disabled', true).prop('required', false).addClass('d-none');
          get_city(country, country_rel);
          SK.plugins.bootstrapSelect('refresh');
        } else {
          $('[data-rel="'+ country_rel +'"] select[name="city"]').prop('disabled', true).prop('required', false).addClass('d-none');
          $('[data-rel="'+ country_rel +'"] input[name="city_name"]').prop('disabled', false).prop('required', true).removeClass('d-none');
          SK.plugins.bootstrapSelect('refresh');
        }
        get_phone_code(country, country_rel);
      });

      function get_city(country, related) {
        $('[data-rel="' + related + '"] [name="city"]').html("");
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
              $('[data-rel="' + related + '"] [name="city"]').html(obj);
            }
            SK.plugins.bootstrapSelect('refresh');
          }
        });
      }

      $(document).ready(function() {
        $('[name="country"]').each(function () {
          var country = $(this).val();
          var country_rel = $(this).attr('id');

          if (country==='54') {
            $('[data-rel="'+ country_rel +'"] select[name="city"]').prop('disabled', false).prop('required', true).removeClass('d-none').parent('div').removeClass('d-none');
            $('[data-rel="'+ country_rel +'"] input[name="city_name"]').prop('disabled', true).prop('required', false).addClass('d-none');
            get_selected_city(country, '{{Auth::user()->city}}', country_rel);
            SK.plugins.bootstrapSelect('refresh');
          } else {
            $('[data-rel="'+ country_rel +'"] select[name="city"]').prop('disabled', true).prop('required', false).addClass('d-none');
            $('[data-rel="'+ country_rel +'"] input[name="city_name"]').prop('disabled', false).prop('required', true).removeClass('d-none').attr('value', '{{Auth::user()->city}}');
            SK.plugins.bootstrapSelect('refresh');
          }
          get_phone_code(country, country_rel);
        });
      });

      function get_selected_city(country, city, related) {
        $('[data-rel="'+ related +'"] [name="city"]').html("");
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
              $('[data-rel="'+ related +'"] [name="city"]').html(obj);
              SK.plugins.bootstrapSelect('refresh');
              $('.profile-info-content').removeClass('loader');
            } else {
              SK.plugins.bootstrapSelect('refresh');
            }
          }
        });
      }

      function get_phone_code(country, related) {
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
              $('[name="phone_code"][data-rel="'+ related +'"]').attr('value', obj);
              $('.form-control-phone-code[data-rel="'+ related +'"]').text('+' + obj);
            }
          }
        });
      }

      $("#closure-form").on("submit", function() {
        $('#closure-form-btn').addClass('loader');
        $("#closure-form").submit();
      });

      $('.close-account-link').on('click', function() {
        $('#close-account-modal').modal('show');
      });

      $('.close-account-cancel').on('click', function() {
        $('#close-account-modal').modal('hide');
      });
    </script>
@endsection
