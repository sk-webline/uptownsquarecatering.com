<div id="refund-popup" class="bottom-popup">
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
                    <input type="hidden" name="break_num">
                    <input type="hidden" name="date">
                    <input type="hidden" name="quantity">

                    <h2 class="fs-14 fw-700 mb-10px">{{toUpper(translate('Delete Confirmation'))}}</h2>
                    <div class="text-black-50">

                        <div class="one-item d-none">
                            <p>{{translate('Are you sure you want to remove this item?')}}</p>

                            <a onclick="refundItem()" class="btn btn-block btn-outline-black-50 rounded-10px fs-15 fw-700 py-5px mb-15px mt-30px delete-item">
                                <svg class="h-13px mr-2 align-baseline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 7.73 10.92">
                                    <use xlink:href="{{static_asset('assets/img/icons/cart-delete.svg')}}#content"></use>
                                </svg>
                                {{toUpper(translate('Remove Item'))}}
                            </a>

                        </div>

                        <div class="multiple-items d-none">

                            <p>{{translate('Choose how many items you want to remove')}}</p>

                            <div class="quantity d-flex justify-content-center">
                                <div class="row gutters-2">
                                    <div class="col bg-black-15 px-5px">
                                        <div class="control quantity-minus c-pointer">-</div>
                                    </div>
                                    <div class="col px-5px bg-black-07">
                                        <div class="quantity-total">
                                            1
                                            {{--                                        @if(in_array($product->id, $items_in_cart)) {{$quantity_for_each_product[$product->id]}} @else 0 @endif--}}
                                        </div>
                                    </div>
                                    <div class="col px-5px bg-black-15">
                                        <div class="control quantity-plus c-pointer">+</div>
                                    </div>
                                </div>
                            </div>

                            <a onclick="refundItem()" class="btn btn-block btn-outline-black-50 rounded-10px fs-15 fw-700 py-5px mb-15px mt-30px delete-item">
                                <svg class="h-13px mr-2 align-baseline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 7.73 10.92">
                                    <use xlink:href="{{static_asset('assets/img/icons/cart-delete.svg')}}#content"></use>
                                </svg>
                                {{toUpper(translate('Remove Items'))}}
                            </a>

                        </div>
{{--                       --}}



                        <a href="javascript:void(0);" class="popup-close-link border-bottom border-inherit hov-text-secondary">{{toUpper(translate('Cancel'))}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
