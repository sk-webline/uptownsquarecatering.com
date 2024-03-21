@extends('application.layouts.user_panel')

@section('meta_title'){{ translate('Manage Profile') }}@endsection

@section('panel_content')
    <div id="manage-prfile" class="mb-50px">
        <div class="container">
            <h1 class="fs-19 fw-700 mb-15px">{{translate('Manage Profile')}}</h1>
            <div class="form-group mb-30px">
                <div class="form-control-with-label small-focus always-focused">
                    <div class="row no-gutters">
                        <div class="col">
                            <label>{{ translate('User Name')}}</label>
                            <div class="form-control">{{auth()->guard('application')->user()->username}}</div>
                        </div>
                        <div class="col-auto">
                            <div class="form-control-check">
                                <svg class="h-13px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15.21 11.32">
                                    <use xlink:href="{{static_asset('assets/img/icons/check-order.svg')}}#content"></use>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <h2 class="fs-19 fw-700 mb-5px">{{translate('Change Password')}}</h2>
            <form name="change_password" action="{{route('application.update_password')}}" method="POST">
                @csrf
                <div class="form-group mb-10px">
                    <div class="form-control-with-label small-focus">
                        <label>{{ translate('Old Password')}}</label>
                        <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password">
                    </div>
                    <div id="password_error" class="invalid-feedback fs-12 d-block" role="alert">
                        @if ($errors->has('password'))
                            {{ $errors->first('password') }}
                        @endif
                    </div>
                </div>
                <div class="form-group mb-10px">
                    <div class="form-control-with-label small-focus">
                        <label>{{ translate('New Password')}}</label>
                        <input  type="password" id="new_password" class="form-control{{ $errors->has('new_password') ? ' is-invalid' : '' }}" name="new_password">
                    </div>

                    <div id="new_password_error" class="invalid-feedback fs-12 d-block" role="alert">
                        @if ($errors->has('new_password'))
                            {{ $errors->first('new_password') }}
                         @endif
                    </div>


                </div>
                <div class="form-group mb-10px">
                    <div class="form-control-with-label small-focus">
                        <label>{{ translate('Confirm Password')}}</label>
                        <input type="password" class="form-control {{ $errors->has('confirm_password') ? ' is-invalid' : '' }}" name="confirm_password">
                    </div>
                    <div id="confirm_password_error" class="invalid-feedback fs-12 d-block" role="alert">
                        @if ($errors->has('confirm_password'))
                            {{ $errors->first('confirm_password') }}
                        @endif
                    </div>
                </div>
                <button type="submit" class="btn btn-block btn-black rounded-10px py-5px fs-15 fw-700">{{toUpper(translate('Save Password'))}}</button>
            </form>
        </div>
    </div>
@endsection

@section('script')

    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

    <script type="text/javascript">

        $(document).ready(function () {
            $('form[name=change_password]').validate({
                errorClass: 'is-invalid',
                rules: {
                    password: {
                        required: true
                    },
                    new_password: {
                        required: true,
                        minlength: 6
                    },
                    confirm_password: {
                        required: true,
                        minlength: 6,
                        equalTo: '#new_password'
                    },

                },
                messages: {
                    confirm_password: {
                        required: 'Please confirm your password.',
                        equalTo: 'Passwords do not match.'
                    }
                },
                errorPlacement: function (error, element) {
                    // if (element.attr("name") === "password") {
                    $("#" + element.attr("name") + "_error").html(error);
                    // }
                }
            });
        });




    </script>

@endsection


