<div id="change-password-canteen-account" class="modal fade">
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
                    <h3 class="text-center fs-18 lg-fs-30 fw-700 mb-30px mb-sm-35px">{{toUpper(translate("Change Password"))}}</h3>

                    <div class="w-55px border-top border-secondary border-width-2 mb-10px mx-auto"></div>

                    <div class="pb-15px fs-16">
                        <form name="change_password_canteen_account" class="form-horizontal" action="" method="POST" enctype="multipart/form-data">
                            @csrf

                            <input name="canteen_user_id" type="hidden" value="">

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


                            <div class="pb-1"></div>


                            <div class="form-group small-field">
                                <button type="submit" class="btn btn-block btn-outline-primary fw-600 border-width-2 border-primary fs-18">
                                    {{toUpper(translate('Change Password'))}}
                                </button>
                            </div>
                        </form>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>






