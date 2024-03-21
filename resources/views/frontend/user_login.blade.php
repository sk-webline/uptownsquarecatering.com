@extends('frontend.layouts.app')

@php

        $req = 1;
        $req_name = null;

        if(old('create_account')){
            $card = \App\Models\Card::where('rfid_no',old('card_to_register'))->first();
            if ($card!=null){
                if($card->required_field_name==null){
                    $req = 0;

                }else{
                    $req_name = $card->required_field_name;
                }
            }
        }
@endphp

@section('content')
    <section class="py-65px" data-aos="fade-up">
        <div class="profile">
            <div class="container">
                <div class="mw-510px mx-auto">
                    <div class="card custom-card-background pt-20px pt-sm-40px fs-14 sm-fs-16 min-h-250px">

                        <div class="px-20px px-sm-40px">
                            <h1 class="fs-18 sm-fs-22 fw-700 mb-10px lh-1 text-center">{{toUpper(translate('Register New Card'))}}</h1>

                            <div class="w-55px border-top border-secondary border-width-2 mx-auto"></div>

                            <div class="text-center my-10px text-primary-50 mw-365px mx-auto fs-14 sm-fs-16">
                                <p>{{ translate('Please provide the RFID number to complete the registration process for your new card.')}}</p>
                            </div>
                            {{--                            @if(old('create_account'))--}}
                            {{--                                @dd(old('create_account'))--}}
                            {{--                            @endif--}}

                            <div class="mt-15px">
                                <div class="form-group mb-20px mb-sm-30px">
                                    <div class="input-group" id="rfid_div">
                                        <div class="form-control-with-label small-focus animate flex-grow-1">
                                            <label>{{ translate('RFID no.')}}</label>
                                            <input type="text" class="form-control border-none remove-all-spaces remove-last-space pt-20px" name="rfid_no"
                                                   id="rfid_no" autocomplete="off">
                                        </div>

                                        <div class="input-group-append line w-40px w-sm-85px">

                                            <div id="submit_button_div" class="flex-grow-1 d-none">
                                                <button id="rfid_no_submit"
                                                        class="btn btn-primary btn-block fs-12 xxl-fs-14 px-2px fw-400 "
                                                        type="button">{{ toUpper(translate('Submit'))}}</button>
                                            </div>

                                            <div class="loader w-100" id="loader-div" style="display: none">
                                            </div>

                                            <div id="correct_rfid" style="display: none">

                                                <div
                                                    class="w-100 h-30px position-absolute custom-div d-flex flex-column justify-content-center">
                                                    <div class="text-center">

                                                        <svg class="h-30px" fill="green"
                                                             xmlns="http://www.w3.org/2000/svg" height="25" width="25"
                                                             viewBox="0 0 30 30">
                                                            <use
                                                                xlink:href="{{static_asset('assets/img/icons/tick.svg')}}#tick"></use>
                                                        </svg>
                                                    </div>
                                                </div>

                                            </div>

                                            <div id="incorrect_rfid" style="display: none">
                                                <div
                                                    class="w-100 h-30px position-absolute custom-div d-flex flex-column justify-content-center">
                                                    <div class="text-center">
                                                        <svg class="h-17px" stroke-width="1" stroke="#990013"
                                                             fill="#990013"
                                                             xmlns="http://www.w3.org/2000/svg" height="20" width="20"
                                                             viewBox="0 0 25.39 25.39">
                                                            <use
                                                                xlink:href="{{static_asset('assets/img/icons/x_icon_error.svg')}}#x-icon-error"></use>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="existing_rfid" style="display: none ">
                                                <div
                                                    class="w-100 h-30px position-absolute custom-div d-flex flex-column justify-content-center">
                                                    <div class="text-center">

                                                        <svg class="h-20px"
                                                             xmlns="http://www.w3.org/2000/svg" height="30" width="30"
                                                             viewBox="0 0 21.18 21.27">
                                                            <use
                                                                xlink:href="{{static_asset('assets/img/icons/warning_icon.svg')}}#warning_svg"></use>
                                                        </svg>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div id="rfid-error-msg" class="invalid-feedback absolute fs-14"
                                         style="display: none">{{ translate('This RFID does not exist')}}</div>
                                </div>


                                {{--                                    <div id="change-no-div" class="fs-12 text-right" style="display: none">--}}
                                {{--                                        <button id="changeRfidNo"--}}
                                {{--                                                class="btn btn-soft-danger ">{{ translate('Change RFID No.')}}</button>--}}
                                {{--                                    </div>--}}

                                <div id="select_account" style="display: none">

                                    <div
                                        class="my-20px my-sm-25px py-15px py-sm-20px text-center custom-group fw-700 text-primary-50 fs-14 sm-fs-16">
                                        <p>{{ translate('Where do you want this card to be registered?')}}</p>
                                    </div>

                                    <div class="gutters-10 sm-no-gutters">
                                        <div class="row fs-10 sm-fs-14 fw-600 text-center mb-25px mb-sm-15px gutters-5">
                                            <div class="col-6">
                                                <span
                                                    class="border-bottom border-inherit">{{toUpper(translate('In an existing account'))}}</span>
                                            </div>
                                            <div class="col-6">
                                                <span
                                                    class="border-bottom border-inherit">{{toUpper(translate('In a new account'))}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="select_account_existing_rfid" style="display: none">
                                    <div
                                        class="my-20px my-sm-30px py-15px py-sm-20px text-center custom-group fw-700 text-primary-50 fs-14 sm-fs-16">
                                        <p>{{ translate('This RFID is already registered. Login to Continue.')}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div id="last_buttons" class="row no-gutters fw-500 fs-14 sm-fs-16" style="display: none">
                            <div class="col-6">
                                <button id="login_and_register_card_btn"
                                        class="btn btn-outline-primary btn-block btn-fade-hover h-100 py-7px py-sm-15px px-5px">{{ toUpper(translate('Login')) }}</button>
                            </div>
                            <div class="col-6">
                                <button id="create_account_btn"
                                        class="btn btn-primary btn-block h-100 py-7px py-sm-15px px-5px">{{ toUpper(translate('Create Account')) }}</button>
                            </div>
                        </div>

                        <div class="px-20px px-sm-40px">

                            <div id="login_form">

                                <div id="login_or_div" class="mb-20px mb-sm-30px text-primary-20 fw-700">
                                    <div class="row gutters-2 align-items-center">
                                        <div class="col">
                                            <div class="border border-inherit border-primary-100"></div>
                                        </div>
                                        <div class="col-auto">{{toUpper(translate('or'))}}</div>
                                        <div class="col">
                                            <div class="border border-inherit border-primary-100"></div>
                                        </div>
                                    </div>
                                </div>

                                <h2 class="fs-18 sm-fs-22 fw-700 mb-10px lh-1 text-center">{{ toUpper(translate('Login'))}}</h2>

                                <div class="w-55px border-top border-secondary border-width-2 mx-auto"></div>

                                <div class="text-center my-10px text-primary-50 mw-365px mx-auto pb-5px fs-14 sm-fs-16">
                                    <p>{{ translate('If you possess an account and wish to register a new card, kindly log in below.')}}</p>
                                </div>

                                <div class="pb-20px pb-sm-40px">

                                    <form id="login-form" class="form-default" role="form" action="{{ route('login') }}"
                                          method="POST">
                                        @csrf

                                        <div class="form-group mb-10px mb-sm-20px">
                                            <div
                                                class="form-control-with-label small-focus @if (old('email')) focused @endif">
                                                <label>{{ translate('Email')}}</label>
                                                <input type="email"
                                                       class="form-control pt-20px"
                                                       name="email"
                                                       value="{{ old('email') }}">
                                            </div>
                                            @if ($errors->has('fail_attempts_login_error'))
                                                <div class="invalid-feedback fs-14 d-block"
                                                     role="alert">{{ $errors->first('fail_attempts_login_error') }}</div>
                                            @endif
                                            <div id="email_login_message" class="invalid-feedback fs-14 d-block"></div>
                                        </div>

                                        <div class="form-group mb-5px">
                                            <div class="form-control-with-label small-focus animate">
                                                <label>{{ translate('Password')}}</label>
                                                <input type="password"
                                                       class="form-control pt-20px"
                                                       name="password"
                                                >
                                            </div>
                                            <div id="password_login_message"
                                                 class="invalid-feedback fs-14 d-block"></div>
                                        </div>

                                        <div class="mb-10px text-right text-black-40 fs-12 sm-fs-14">
                                            <a href="{{ route('password.request') }}"
                                               class="border-bottom border-inherit hov-text-primary">{{ translate('Forgot your password?')}}</a>
                                        </div>

                                        <button type="submit"
                                                class="btn btn-outline-primary btn-block fs-16 sm-fs-18 fw-500 py-5px py-sm-13px">{{ toUpper(translate('Login')) }}</button>


                                    </form>


                                </div>

                            </div>

                            <div id="login_and_card_registration_form" style="display: none">

                                <div class="border-top border-width-2 border-primary-100 mb-20px mb-sm-30px"></div>

                                <h2 class="fs-18 sm-fs-22 fw-700 mb-10px lh-1 text-center">{{ toUpper(translate('Login'))}}</h2>

                                <div class="w-55px border-top border-secondary border-width-2 mx-auto"></div>

                                <div class="text-center my-20px text-primary-50 mw-365px mx-auto fw-700 fs-14 sm-fs-16">
                                    <p>{{ translate('Register your card in an existing account')}}</p>
                                </div>

                                <form id="reg-form" class="form-default" role="form" action="{{ route('login') }}"
                                      method="POST">
                                    @csrf

                                    <input type="hidden" name="card_to_register" id="card_to_register">

                                    <h3 class="mb-5px fw-600 fs-14">{{ toUpper(translate('Card Info'))}}</h3>

                                    <div class="form-group mb-15px">
                                        <div
                                            class="form-control-with-label small-focus @if (old('card_name')) focused @endif">
                                            <label>{{ translate('Name your Card*')}}</label>
                                            <input type="text"
                                                   class="form-control {{ $errors->has('card_name') ? ' is-invalid' : '' }}"
                                                   value="{{ old('card_name') }}"
                                                   name="card_name" id="card_name" >
                                        </div>
                                        <div id="card_name_reg_login_message"
                                             class="invalid-feedback fs-14 d-block"></div>
                                    </div>

                                    <div class="form-group mb-15px">
                                        <div id="required_field_value_div" class="form-control-with-label small-focus">
                                            <label id="required_field_name_tag">{{ translate('Student ID*')}}</label>
                                            <input type="text"
                                                   class="form-control {{ $errors->has('required_field_value') ? ' is-invalid' : '' }}"
                                                   name="required_field_value" id="required_field_value" >
                                        </div>
                                        <div id="required_field_reg_login_message"
                                             class="invalid-feedback fs-14 d-block"></div>
                                    </div>

                                    <div class="border-top border-width-2 border-primary-100 mb-5px"></div>
                                    <h3 class="mb-5px fw-600 fs-14">{{ toUpper(translate('Account Info'))}}</h3>

                                    <div class="form-group mb-15px">
                                        <div
                                            class="form-control-with-label small-focus @if (old('email')) focused @endif">
                                            <label>{{ translate('Email')}}</label>
                                            <input type="email"
                                                   class="form-control "
                                                   name="email"
                                                   value="{{ old('email') }}">
                                        </div>
                                        {{--                                            @if ($errors->has('email'))--}}
                                        {{--                                                <div class="invalid-feedback fs-10 d-block"--}}
                                        {{--                                                     role="alert">{{ $errors->first('email') }}</div>--}}
                                        {{--                                            @endif--}}
                                        <div id="email_reg_login_message"
                                             class="invalid-feedback fs-14 d-block"></div>
                                    </div>

                                    <div class="form-group mb-5px">
                                        <div class="form-control-with-label small-focus">
                                            <label>{{ translate('Password')}}</label>
                                            <input type="password"
                                                   class="form-control"
                                                   name="password" required>
                                        </div>
                                        <div id="password_reg_login_message"
                                             class="invalid-feedback fs-14 d-block"></div>
                                    </div>

                                    <div class="mb-10px text-right text-black-40 fs-12 sm-fs-14">
                                        <a href="{{ route('password.request') }}"
                                           class="border-bottom border-inherit hov-text-primary">{{ translate('Forgot your password?')}}</a>
                                    </div>

                                    <button type="submit"
                                            class="btn btn-primary btn-block fs-16 sm-fs-18 fw-500 py-5px py-sm-13px">{{ toUpper(translate('Login')) }}</button>

                                </form>

                                <div class="my-20px my-sm-30px text-primary-20 fw-700">
                                    <div class="row gutters-2 align-items-center">
                                        <div class="col">
                                            <div class="border border-inherit border-primary-100"></div>
                                        </div>
                                        <div class="col-auto">{{toUpper(translate('or'))}}</div>
                                        <div class="col">
                                            <div class="border border-inherit border-primary-100"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-20px mb-sm-30px text-center fw-700 text-primary-50 fs-14 sm-fs-16">
                                    <p>{{ translate('Register your card in a new account')}}</p>
                                </div>

                                <div class="pb-20px pb-sm-40px">
                                    <button id="login_to_register_btn"
                                            class="btn btn-outline-primary btn-block fw-500 py-5px py-sm-13px">{{ toUpper(translate('Create Account')) }}</button>
                                </div>
                            </div>

                            <div id="registration_form" style="display: none">

                                <div class="border-top border-width-2 border-primary-100 mb-20px mb-sm-30px"></div>

                                <h2 class="fs-18 sm-fs-22 fw-700 mb-10px lh-1 text-center">{{ toUpper(translate('Create Account'))}}</h2>

                                <div class="w-55px border-top border-secondary border-width-2 mx-auto"></div>

                                <div class="text-center my-20px text-primary-50 mw-365px mx-auto fw-700 fs-14 sm-fs-16">
                                    <p>{{ translate('Register your card in a new account')}}</p>
                                </div>

                                <form id="reg_and_create_acc_form" class="form-default" role="form"
                                      action="{{ route('customer.register') }}"
                                      method="POST">
                                    @csrf
                                    <input type="hidden" name="card_to_register" id="card_to_register_reg_form">

                                    <h3 class="mb-5px fw-600 fs-14">{{ toUpper(translate('Card Info'))}}</h3>

                                    <div class="form-group mb-15px">
                                        <div
                                            class="form-control-with-label small-focus @if (old('card_name')) focused @endif">
                                            <label>{{ translate('Name your Card*')}}</label>
                                            <input type="text"
                                                   class="form-control {{ $errors->has('card_name') ? ' is-invalid' : '' }}"
                                                   value="{{ old('card_name') }}"
                                                   name="card_name" id="card_name" >
                                        </div>
                                        @if ($errors->has('card_name'))
                                            <div class="invalid-feedback fs-14 d-block"
                                                 role="alert">{{ $errors->first('card_name') }}</div>
                                        @endif
                                        <div id="card_name_reg_message"
                                             class="invalid-feedback fs-14 d-block"></div>
                                    </div>

                                    <input type="hidden" name="required_field_name" id="required_field_name_reg">

                                    <div class="form-group mb-15px">
                                        <div id="required_field_value_reg_div"
                                            class="form-control-with-label small-focus @if (old('required_field_value')) focused @endif">
                                            <label id="required_field_name_reg_tag"> {{ translate('Required Field Name*')}}</label>
                                            <input type="text"
                                                   class="form-control {{ $errors->has('required_field_value') ? ' is-invalid' : '' }}"
                                                   value="{{ old('required_field_value') }}"
                                                   name="required_field_value" id="required_field_value_reg">
                                        </div>
                                        @if ($errors->has('required_field_value'))
                                            <div class="invalid-feedback fs-14 d-block"
                                                 role="alert">{{ $errors->first('required_field_value') }}</div>
                                        @endif
                                        <div id="required_reg_message"
                                             class="invalid-feedback fs-14 d-block"></div>
                                    </div>

                                    <div class="border-top border-width-2 border-primary-100 mb-5px"></div>
                                    <h3 class="mb-5px fw-600 fs-14">{{ toUpper(translate('Account Info'))}}</h3>

                                    <div class="form-group mb-15px">
                                        <div
                                            class="form-control-with-label small-focus @if (old('name')) focused @endif">
                                            <label>{{ translate('Your Name*')}}</label>
                                            <input type="text"
                                                   class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}"
                                                   value="{{ old('name') }}" name="name" id="name" >
                                        </div>
                                        @if ($errors->has('name'))
                                            <div class="invalid-feedback fs-14 d-block"
                                                 role="alert">{{ $errors->first('name') }}</div>
                                        @endif
                                        <div id="name_reg_message"
                                             class="invalid-feedback fs-14 d-block"></div>
                                    </div>

                                    <div class="form-group mb-15px">
                                        <div
                                            class="form-control-with-label small-focus @if (old('surname')) focused @endif">
                                            <label>{{ translate('Your Surname*')}}</label>
                                            <input type="text"
                                                   class="form-control {{ $errors->has('surname') ? ' is-invalid' : '' }}"
                                                   value="{{ old('surname') }}" name="surname" id="surname" >
                                        </div>
                                        @if ($errors->has('surname'))
                                            <div class="invalid-feedback fs-14 d-block"
                                                 role="alert">{{ $errors->first('surname') }}</div>
                                        @endif
                                        <div id="surname_reg_message"
                                             class="invalid-feedback fs-14 d-block"></div>
                                    </div>

                                    <div class="form-group mb-15px">
                                        <div
                                            class="form-control-with-label small-focus @if (old('email')) focused @endif">
                                            <label>{{ translate('Email*')}}</label>
                                            <input type="text"
                                                   class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                   value="{{ old('email') }}" name="email" >
                                        </div>
                                        @if ($errors->has('email'))
                                            <div class="invalid-feedback fs-14 d-block"
                                                 role="alert">{{ $errors->first('email') }}</div>
                                        @endif
                                        <div id="email_reg_message"
                                             class="invalid-feedback fs-14 d-block"></div>
                                    </div>

                                    <div class="form-group mb-15px">
                                        <div class="form-control-with-label small-focus">
                                            <label>{{ translate('Password*')}}</label>
                                            <input type="password"
                                                   class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}"
                                                   name="password"
                                            >
                                        </div>
                                        @if ($errors->has('password'))
                                            <div class="invalid-feedback fs-14 d-block"
                                                 role="alert">{{ $errors->first('password') }}</div>
                                        @endif
                                        <div id="password_reg_message"
                                             class="invalid-feedback fs-14 d-block"></div>
                                    </div>

                                    <div
                                        class="form-group mb-15px">
                                        <div class="form-control-with-label small-focus">
                                            <label>{{ translate('Confirm Password*')}}</label>
                                            <input type="password"
                                                   class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}"
                                                   name="password_confirmation"
                                                   >
                                        </div>
                                        <div id="password_reg_message"
                                             class="invalid-feedback fs-14 d-block"></div>
                                    </div>

                                    <div class="text-left mb-20px text-primary-60 position-relative">
                                        <label class="sk-checkbox m-0">
                                            <input type="checkbox" id="agree_checkbox" name="agree_policies"
                                                   onchange="hide_checkout_error()">
                                            <span class="sk-square-check custom-square-box bg-white"></span>
                                            {{ translate('I agree with the')}}
                                            <a class="hov-text-primary"
                                               href="{{ route('termspolicies') }}"
                                               target="_blank">{{ translate('Terms&Policies')}}</a>
                                        </label>
                                        <div id="register-form-error-agree"
                                             class="invalid-feedback absolute fs-12 d-block mt-0" role="alert"></div>
                                    </div>

                                    <button type="submit"
                                            class="btn btn-primary btn-block fs-16 sm-fs-18 fw-500 py-5px py-sm-13px">{{  toUpper(translate('Register your card & create account')) }}</button>
                                </form>

                                <div class="my-20px my-sm-30px text-primary-20 fw-700">
                                    <div class="row gutters-2 align-items-center">
                                        <div class="col">
                                            <div class="border border-inherit border-primary-100"></div>
                                        </div>
                                        <div class="col-auto">{{toUpper(translate('or'))}}</div>
                                        <div class="col">
                                            <div class="border border-inherit border-primary-100"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-20px mb-sm-30px text-center fw-700 text-primary-50 fs-14 sm-fs-16">
                                    <p>{{ translate('Register your card in an existing account')}}</p>
                                </div>

                                <div class="pb-20px pb-sm-40px">
                                    <button id="register_to_login_btn"
                                            class="btn btn-outline-primary btn-block fw-500 py-5px py-sm-13px">{{toUpper(translate('Login'))}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    @if(App::getLocale() == "gr")
        <script
            src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/localization/messages_el.min.js"></script>
    @endif
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

    <script type="text/javascript">

        let required_field_name = null;

        $(document).ready(function () {

            @if(old('create_account'))

            hideLoginForm();
            $('#registration_form').show();
            $('#rfid_no').val('{{ old('card_to_register') }}');
            $('#rfid_no').parent('.form-control-with-label').addClass('focused');
            $('#card_to_register_reg_form').val('{{ old('card_to_register') }}');
            $('#rfid_no').prop("focused", true);
            @if($req==0)
                $('#required_field_value').prop("required", false);
                $('#required_field_value_div').hide();
                $('#required_field_value_reg').prop("required", false);
                $('#required_field_value_reg_div').hide();

            @else

            $('#required_field_value').prev('label').text('{{$req_name}}' + '*');
            $('#required_field_name').val('{{$req_name}}');
            $('#required_field_value').prop("required", true);
            $('#required_field_value_div').show();

            $('#required_field_value_reg').prev('label').text('{{$req_name}}' + '*');
            $('#required_field_name_reg').val('{{$req_name}}');
            // $('#required_field_value_reg').prop("required", true);
            $('#required_field_value_reg_div').show();

            @endif

            // $('#rfid_no').prop("readonly", true);
            $('#submit_button_div').hide();
            $('#correct_rfid').show();
            $('#change-no-div').show();
            doFooter();


            {{--                @dd(old('create_account'))--}}
            @endif

            @if(old('login_and_register'))

            hideLoginForm();
            $('#login_and_card_registration_form').show();
            $('#rfid_no').val('{{ old('card_to_register') }}');
            $('#rfid_no').parent('.form-control-with-label').addClass('focused');
            $('#card_to_register_reg_form').val('{{ old('card_to_register') }}');
            $('#rfid_no').prop("focused", true);
            // $('#rfid_no').prop("readonly", true);
            $('#submit_button_div').hide();
            $('#correct_rfid').show();
            $('#change-no-div').show();
            doFooter();

            {{--                @dd(old('create_account'))--}}
            @endif

            $(document).on('keypress keyup', '#rfid_no', function (e) {
                let $this = $(this);

                if (e.keyCode === 13) {
                    hideLoginForm();
                    originalDisplay();
                    rfid_no_submit();
                    return false;
                }

                if (($(this).val()).length >= 6) {
                    // $('#rfid_no_submit').click();
                    $('#submit_button_div').removeClass('d-none');
                    hideLoginForm();
                    originalDisplay();
                    // rfid_no_submit();
                }else{
                    $('#submit_button_div').addClass('d-none');
                    originalDisplay();
                        showLoginForm();
                }
                // if (e.keyCode === 13) {
                //     rfid_no_submit();
                // } else if (($this.val()).length >= 6) {
                //     originalDisplay();
                //     hideLoginForm();
                // } else {
                //     originalDisplay();
                //     showLoginForm();
                // }
            })

            $(document).on('click', '#rfid_no_submit', function (e) {
                $('form#reg-form')[0].reset();
                $('form#reg-form .form-control-with-label').removeClass('focused');
                $('form#reg_and_create_acc_form')[0].reset();
                $('form#reg_and_create_acc_form .form-control-with-label').removeClass('focused');
                $('form#login-form')[0].reset();
                $('form#login-form .form-control-with-label').removeClass('focused');
                rfid_no_submit();
            });


            $("#reg_and_create_acc_form").on("submit", function () {
                if ($('input[name="agree_policies"]').prop('checked') == false) {
                    $('#register-form-error-agree').addClass('d-block').text('{{translate("You need to agree with our policies")}}');
                    return false;
                } else {
                    $('#register-form-btn').addClass('loader');
                }
            });

        });

        // Returns a function, that, as long as it continues to be invoked, will not
        // be triggered. The function will be called after it stops being called for
        // N milliseconds. If `immediate` is passed, trigger the function on the
        // leading edge, instead of the trailing.
        function debounce(func, wait, immediate) {
            var timeout;
            return function () {
                var context = this, args = arguments;
                var later = function () {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        };

        function showLoginForm() {
            $('#login_form').show();
            $('#submit_button_div').show();
            $('#correct_rfid').hide();
            $('#select_account').hide();
            $('#last_buttons').hide();
            $('#login_and_card_registration_form').hide();
            $('#change-no-div').hide();
            $('#registration_form').hide();
            $('#card_to_register').val('');
            $('#card_to_register_reg_form').val('');
            doFooter();
        }

        function hideLoginForm() {
            $('#login_form').hide();
            doFooter();
        }

        function submit_rfid_no(rfid_no) {

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('rfid-card-available')}}",
                type: 'post',
                data: {
                    rfid_no: rfid_no
                },
                success: function (response) {


                    var card = JSON.parse(response);


                    $('#loader-div').hide();

                    $("#rfid_no").attr('readonly', false);

                    if (card.status == '1') {

                        //display other parts

                        enableLastButtons();

                        if (card.required_field_name == null) {
                            // required_field_value
                            $('#required_field_value').prop("required", false);
                            $('#required_field_value_div').hide();
                            $('#required_field_value_reg').prop("required", false);
                            $('#required_field_value_reg_div').hide();

                        } else {
                            $('#required_field_value').prev('label').text(card.required_field_name + '*');
                            $('#required_field_name').val(card.required_field_name);
                            $('#required_field_value').prop("required", true);
                            $('#required_field_value_div').show();

                            $('#required_field_value_reg').prev('label').text(card.required_field_name + '*');
                            $('#required_field_name_reg').val(card.required_field_name);
                            // $('#required_field_value_reg').prop("required", true);
                            $('#required_field_value_reg_div').show();
                        }

                        $('#rfid-error-msg').hide();
                        $('#change-no-div').show();

                        $('#card_to_register').val($('#rfid_no').val());
                        $('#card_to_register_reg_form').val($('#rfid_no').val());


                        $('#login_form').hide();
                        $("#rfid_no").attr('readonly', false);
                        $('#loader-div').hide();
                        $('#correct_rfid').show();
                        $('#select_account').show();
                        $('#last_buttons').show();

                        doFooter();

                    } else if (card.status == '2') {

                        // existing_rfid


                        warningDisplay();
                    } else {

                        errorDisplay();
                    }
                }
            });
        }

        // $('#rfid_no_submit').on("click", function () {

        function rfid_no_submit() {

            $('#loader-div').show();
            disableLastButtons();

            $("#rfid_no").attr('readonly', true);
            $('#rfid-error-msg').hide();
            $('#submit_button_div').hide();
            submit_rfid_no($('#rfid_no').val());
            doFooter();
        }

        $('#changeRfidNo').on("click", function () {

            showLoginForm();
            $('#rfid_no').prop("readonly", false);
            $('#rfid_no').val('');
            $('#rfid_no').parent('.form-control-with-label').removeClass('focused');
        });

        function errorDisplay() {
            $('#rfid_div').addClass('border-bright-red');
            $('#existing_rfid').hide();
            $('#rfid-error-msg').show();
            $('#incorrect_rfid').show();

            disableLastButtons();


        }

        function warningDisplay() {
            $('#rfid_div').removeClass('border-bright-red');
            $('#rfid-error-msg').hide();
            $('#incorrect_rfid').hide();
            $('#existing_rfid').show();
            $('#select_account_existing_rfid').show();
            $('#login_or_div').addClass('d-none');

            $('#login_form').show();


            disableLastButtons();
            doFooter();

        }

        function originalDisplay() {
            $('#rfid_div').removeClass('border-bright-red');
            $('#rfid-error-msg').hide();
            $('#incorrect_rfid').hide();
            $('#existing_rfid').hide();
            $('#correct_rfid').hide();

            document.getElementById("reg-form").reset();
            document.getElementById("login-form").reset();
            document.getElementById("reg_and_create_acc_form").reset();


            $('#select_account_existing_rfid').hide();

            $('#login_or_div').removeClass('d-none');

            $('#login_and_card_registration_form').hide();
            $('#registration_form').hide();
            $('#submit_button_div').show();

            doFooter();
        }

        function disableLastButtons() {
            $('#select_account').addClass('opacity-40');
            $('#login_and_register_card_btn').prop("disabled", true);
            $('#create_account_btn').prop("disabled", true);
            doFooter();
        }

        function enableLastButtons() {
            $('#select_account').removeClass('opacity-40');
            $('#login_and_register_card_btn').prop("disabled", false);
            $('#create_account_btn').prop("disabled", false);
            doFooter();
        }


        $('#login_and_register_card_btn').on("click", function () {

            $('#login_form').hide();
            $('#submit_button_div').hide();
            $('#select_account').hide();
            $('#last_buttons').hide();
            $('#login_and_card_registration_form').show();
            $('#card_to_register').val($('#rfid_no').val());

            doFooter();
        });

        $('#create_account_btn').on("click", function () {
            $('#login_form').hide();
            $('#submit_button_div').hide();
            $('#select_account').hide();
            $('#last_buttons').hide();
            $('#login_and_card_registration_form').hide();
            $('#card_to_register').val('');
            $('#registration_form').show();
            $('#card_to_register_reg_form').val($('#rfid_no').val());
            doFooter();
        });

        $('#login_to_register_btn').on("click", function () {
            $('#registration_form').show();
            $('#login_and_card_registration_form').hide();
            doFooter();
        });


        $('#register_to_login_btn').on("click", function () {
            $('#registration_form').hide();
            $('#login_and_card_registration_form').show();
            doFooter();
        });

        function hide_checkout_error() {


            if ($('#agree_checkbox').is(":checked")) {

                $('#register-form-error-agree').removeClass('d-block');
            } else {

                $('#register-form-error-agree').addClass('d-block');
            }
            doFooter();
        }

        /*Validations*/
        $('#login-form').validate({
            errorClass: 'is-invalid',
            rules: {
                email: {
                    required: true,
                },
                password: {
                    required: true,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") === "email") {
                    $("#email_login_message").html(error);
                } else if (element.attr("name") === "password") {
                    $("#password_login_message").html(error);
                }
            }
        });

        $('#reg-form').validate({
            errorClass: 'is-invalid',
            rules: {
                card_name: {
                    required: true,
                },
                required_field_value: {
                    required: true,
                },
                email: {
                    required: true,
                },
                password: {
                    required: true,
                },
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") === "card_name" ) {
                    $("#card_name_reg_login_message").html(error);
                }
                else if (element.attr("name") === "required_field_value" ) {
                    $("#required_field_reg_login_message").html(error);
                }
                else if (element.attr("name") === "email" ) {
                    $("#email_reg_login_message").html(error);
                }
                else if (element.attr("name") === "password" ) {
                    $("#password_reg_login_message").html(error);
                }
            }
        });

        $('#reg_and_create_acc_form').validate({
            errorClass: 'is-invalid',
            rules: {
                card_name: {
                    required: true,
                },
                required_field_value: {
                    required: true,
                },
                name: {
                    required: true,
                },
                surname: {
                    required: true,
                },
                email: {
                    required: true,
                },
                password: {
                    required: true,
                },
                password_confirmation: {
                    required: true,
                },
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") === "card_name" ) {
                    $("#card_name_reg_message").html(error);
                }
                else if (element.attr("name") === "required_field_value" ) {
                    $("#required_reg_message").html(error);
                }
                else if (element.attr("name") === "name" ) {
                    $("#name_reg_message").html(error);
                }
                else if (element.attr("name") === "surname" ) {
                    $("#surname_reg_message").html(error);
                }
                else if (element.attr("name") === "email" ) {
                    $("#email_reg_message").html(error);
                }
                else if (element.attr("name") === "password" ) {
                    $("#password_reg_message").html(error);
                }
                else if (element.attr("name") === "password_confirmation" ) {
                    $("#password_confirmation_reg_message").html(error);
                }
            }
        });

    </script>
@endsection
