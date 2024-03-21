<div id="add-credit-card-modal" class="modal fade">
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
{{--                    <form class="form-horizontal" action="" method="POST" enctype="multipart/form-data">--}}
{{--                        @csrf--}}
                        <h3 class="text-center fs-18 lg-fs-30 fw-700 mb-30px mb-sm-35px">{{toUpper(translate('Add your card'))}}</h3>
                        <p class="text-primary text-center fs-16">
                            {{translate('This card will have to be assigned on the RFID of your child so they can order from the app without having to enter the card info each time')}}.
                        </p>
                        <p class="text-primary text-center fs-13 px-20px fw-600">
                            {{translate('Viva applies a nominal 1 euro verification charge, which is subsequently refunded, ensuring
                                    a secure and seamless transaction process for our valued customers')}}.
                        </p>


                        <div class="w-55px border-top border-secondary border-width-2 mb-30px mb-sm-35px mx-auto"></div>

                        <label class="opacity-50">{{ translate('Card Nickname') }}</label>
                        <div class="form-group small-field input-info mb-0">
                            <input type="text" class="form-control text-primary" name="nickname" placeholder="{{translate('Give your card a nickname')}}" required>
                        </div>

                        <span class="text-danger fs-12 nickname-error"></span>

                        <div class="text-left position-relative mb-20px mt-3" >

                            <label class="sk-checkbox fs-14 text-primary-50 mb-0">
                                <input type="checkbox" name="agree_policies_add_card">
                                <span class="sk-square-check"></span>
                                {{ translate('I consent to allowing my kids to use my credit card
                                   for school canteen purchases without having to
                                   re-enter the card details each time')}}.
                            </label>
                            <div id="error-agree-add-card-1" class="invalid-feedback fs-10 md-fs-12 d-block mt-0" role="alert">

                            </div>

                        </div>

                        <div class="form-group small-field">
                           <button type="submit" class="btn btn-block btn-outline-primary enter_card_info fw-600 border-width-2 border-primary">
                               {{toUpper(translate('Enter card info'))}}
                           </button>
                        </div>

                        <div class="text-center">

                            <svg class=" h-lg-30px opacity-40"
                                 xmlns="http://www.w3.org/2000/svg" height="50" width="110"
                                 viewBox="0 0 106.72 15.96">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/viva_wallet.svg')}}#viva_wallet_svg"></use>
                            </svg>
                        </div>



                </div>

            </div>
        </div>
    </div>
</div>




