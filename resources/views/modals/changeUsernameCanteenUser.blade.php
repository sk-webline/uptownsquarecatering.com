<div id="change-canteen-username" class="modal fade">
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
                    <h3 class="text-center fs-18 lg-fs-30 fw-700 mb-30px mb-sm-35px">{{toUpper(translate("Change Username"))}}</h3>

                    <div class="w-55px border-top border-secondary border-width-2 mb-10px mx-auto"></div>

                    <div class="pb-15px fs-16">
                        <form name="change_canteen_username" class="form-horizontal" enctype="multipart/form-data">
                            @csrf

                            <input name="canteen_user_id" type="hidden" value="">

                            <div class="pb-1"></div>

                            <lnabel class="opacity-50">{{ translate('Username') }}</lnabel>
                            <div class="form-group small-field m-0 input-info">
                                <input type="text" class="form-control text-primary" name="username" autocomplete="off">
                            </div>

                            <div class="invalid-feedback fs-12 d-block username_error" role="alert">
                            </div>

                            <div class="form-group small-field pt-10px">
                                <button type="submit" class="btn btn-block btn-outline-primary fw-600 border-width-2 border-primary fs-18">
                                    {{toUpper(translate('Save Changes'))}}
                                </button>
                            </div>
                        </form>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>






