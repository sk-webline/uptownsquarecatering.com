<div id="delete-popup" class="bottom-popup">
    <div class="bottom-popup-scroll c-scrollbar">
        <div class="bottom-popup-container pt-40px pb-20px">
            <div class="bottom-popup-close">
                <svg class="h-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
                    <use xlink:href="{{static_asset('assets/img/icons/close-icon.svg')}}#content"></use>
                </svg>
            </div>
            <div class="container">
                <div class="text-center">

                    <input type="hidden" name="product_id">
                    <input type="hidden" name="break_id">
                    <input type="hidden" name="date">

                    <h2 class="fs-14 fw-700 mb-10px">{{toUpper(translate('Delete Confirmation'))}}</h2>
                    <div class="text-black-50">
                        <p>{{translate('Are you sure you want to remove this item?')}}</p>
                        <a href="javascript:void(0);" class="btn btn-block btn-outline-black-50 rounded-10px fs-15 fw-700 py-5px mb-15px mt-30px delete-item">
                            <svg class="h-13px mr-2 align-baseline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 7.73 10.92">
                                <use xlink:href="{{static_asset('assets/img/icons/cart-delete.svg')}}#content"></use>
                            </svg>
                            {{toUpper(translate('Remove Item'))}}
                        </a>
                        <a href="javascript:void(0);" class="popup-close-link border-bottom border-inherit hov-text-secondary">{{toUpper(translate('Cancel'))}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
