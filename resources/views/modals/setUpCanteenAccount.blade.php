<div id="set-up-canteen-account" class="modal fade">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="p-10px">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <svg class="h-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14.74 14.74">
                            <use xlink:href="{{static_asset('assets/img/icons/popup-close.svg')}}#content"></use>
                        </svg>
                    </button>
                </div>
                <div class="px-15px px-lg-35px pb-20px">
                    <h3 class="text-center fs-18 lg-fs-30 fw-700 mb-30px mb-sm-35px">{{toUpper(translate("Set up your child's account"))}}</h3>
                    <div class="text-center pb-10px px-30px">
                        {{translate("This card will be assigned on the RFID Card with name")}}
                        <span class="set-account-name">
                        ''
                        </span>
                    </div>
                    <div class="w-55px border-top border-secondary border-width-2 mb-10px mx-auto"></div>

                    <div class="pb-15px fs-16">
                        <form name="set_canteen_account_form" class="form-horizontal" action="" method="POST" enctype="multipart/form-data">
                            @csrf

                            <input name="card_id" type="hidden" value="">

                            <label class="opacity-50">{{ translate('Username')}}</label>
                            <div class="form-group small-field m-0 input-info">
                                 <input value="test canteen user" type="text" class="form-control text-primary" name="username" placeholder="{{translate('Login Username')}}" autocomplete="off">
                            </div>

                            <div class="invalid-feedback fs-12 d-block username_error" role="alert">
                                {{ $errors->first('username') }}
                            </div>

                            <div class="pb-1"></div>

                            <lnabel class="opacity-50">{{ translate('Password') }}</lnabel>
                            <div class="form-group small-field m-0">
                                <input type="password" class="form-control text-primary" name="password" placeholder="{{translate('Set Password')}}" >

                            </div>

                            <div class="invalid-feedback fs-12 d-block password_error" role="alert">
                            </div>

                            <div class="pb-1"></div>


                            <label class="opacity-50">{{ translate('Confirm Password') }}</label>
                            <div class="form-group small-field m-0">
                                <input type="password" class="form-control text-primary" name="password_confirmation" placeholder="{{translate('Confirm Password')}}">
                            </div>
                            <div class="invalid-feedback fs-12 d-block confirm_password_error" role="alert">
                            </div>

                            <div class="pb-1"></div>


                            <label class="opacity-50">{{ translate('Daily Limit') }}</label>
                            <div class="form-group small-field input-info-w-50 m-0">
                                <input value="100" type="number" min="0.01" step="0.01" min="0.5" class="form-control text-primary w-50" name="daily_limit" placeholder="{{translate('Set Allowed Limit')}}" >
                            </div>

                            <div class="invalid-feedback fs-12 d-block daily_limit_error" role="alert">
                            </div>

                            <div class="pb-1"></div>


                            <div class="text-left position-relative mb-20px " >

                                <label class="sk-checkbox fs-14 text-primary-50 mb-0">
                                    <input type="checkbox" name="agree_policies_set_account">
                                    <span class="sk-square-check"></span>
                                    {{ translate('I consent to allowing my kids to use my credit card
                                       for school canteen purchases without having to
                                       re-enter the card details each time')}}.
                                </label>
                                <div id="error-agree-set-account" class="invalid-feedback fs-10 md-fs-12 d-block mt-0" role="alert">

                                </div>

                            </div>

                            <div class="form-group small-field">
                                <button type="submit" class="btn btn-block btn-outline-primary set-account-save fw-600 border-width-2 border-primary fs-18">
                                    {{toUpper(translate('Save info'))}}
                                </button>
                            </div>
                        </form>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>






