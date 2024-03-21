<div id="unassign-credit-card-modal" class="modal fade">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-radious-0">
            <div class="modal-body p-0">
                <div class="p-10px">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <svg class="h-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14.74 14.74">
                            <use xlink:href="{{static_asset('assets/img/icons/popup-close.svg')}}#content"></use>
                        </svg>
                    </button>
                </div>
                <div class="px-15px px-lg-35px pb-20px">
                    <form class="form-horizontal" action="{{route('canteen_app_user.unassigned_credit_card')}}" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <h3 class="text-center fs-18 lg-fs-25 fw-700 mb-30px mb-sm-35px">{{toUpper(translate('Confirm Unassigning Card from RFID'))}}</h3>

                        <input type="hidden" name="canteen_user_id">

                        <div class="w-55px border-top border-secondary border-width-2 mb-30px mb-sm-35px mx-auto"></div>

                        <p class="text-primary text-center fs-16">
                            {{translate('By unassigning this Credit Card from the RFID, the associated kid will no longer be able to make
                                    purchases at the canteen using this RFID')}}.
                        </p>

                        <p class="text-primary text-center fs-16 fw-600 px-5px pb-15px">
                            {{translate('Are you sure you want to proceed with unassigning this card?')}}
                        </p>

                        <div class="form-group small-field px-5px">
                            <button type="submit"
                                    class="btn btn-block btn-outline-primary fw-600 border-width-2 border-primary">
                                <span>
                                    <svg class="h-15px mb-3px" fill="var(--primary)"
                                         xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 10 10">
                                        <use
                                            xlink:href="{{static_asset('assets/img/icons/trash-icon.svg')}}#content"></use>
                                    </svg>
                                {{toUpper(translate('Unassign Credit Card'))}}
                            </button>
                        </div>
                        <div class="mt-15px text-center">
                            <a class="c-pointer" class="close" data-dismiss="modal" aria-hidden="true">
                                <span class="text-underline">
                                    {{toUpper(translate('Cancel'))}}
                                </span>
                            </a>
                        </div>


                    </form>

                </div>

            </div>
        </div>
    </div>
</div>




