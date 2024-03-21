<div id="undo-canteen-delivery" class="modal fade">
    <div class="modal-dialog modal-md modal-dialog-centered">
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
                    <h3 class="text-center fs-15 lg-fs-25 fw-700 mb-30px mb-sm-20px px-10px">{{toUpper(translate("Are you sure you want to undo this delivery?"))}}</h3>

                    <div class="w-55px border-top border-secondary border-width-2 mb-10px mx-auto"></div>

                    <p class="text-center fs-15 fw-600 opacity-60 pt-10px">
                        {{translate('Are you okay with that?')}}
                    </p>

                    <div class="pb-15px fs-16">
                        <form method="POST" action="{{ route('canteen_cashier.undo_delivery') }}">
                            @csrf

                            <input type="hidden" name="break_id" value="">
                            <input type="hidden" name="canteen_user" value="">
                            <input type="hidden" name="date" value="">

                            <button type="submit" class="w-100 bg-white row no-gutters align-items-end align-content-center fw-700 fs-14 border border-red border-width-2 p-10px text-red ">
                                <div class="col">
                                    <svg class="h-15px mx-1 trash-icon"
                                         xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 10 10">
                                        <use
                                            xlink:href="{{static_asset('assets/img/icons/trash-icon.svg')}}#content"></use>
                                    </svg>
                                    <span>{{toUpper(translate('Undo Delivery'))}}</span>
                                </div>

                            </button>
                        </form>

                    </div>

                    <a class="close text-center fw-400 " data-dismiss="modal" aria-hidden="true">
                        <span class="text-underline">{{translate('Cancel')}}</span>
                    </a>

                </div>

            </div>
        </div>
    </div>
</div>
