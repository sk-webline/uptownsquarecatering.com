<div class="modal-body p-0">
    <div class="p-10px">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
            <svg class="h-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14.74 14.74">
                <use xlink:href="{{static_asset('assets/img/icons/popup-close.svg')}}#content"></use>
            </svg>
        </button>
    </div>
    <div class="px-15px px-lg-35px">
        <div class="row gutters-10">
            <div class="col col-grow-50px">
                <h3 class="fs-17 lg-fs-22 fw-600 lh-1 mb-5px">
                    @if($data['type_id'] == 'custom')
                        {{toUpper(translate('Custom Subscription'))}}
                    @else
                        {{toUpper(translate($data['name']))}}
                    @endif
                </h3>
                <p class="text-primary-50">{{translate('is added to your Cart')}}</p>
            </div>
            <div class="col col-50px">
                <div class="border border-primary border-width-2 size-30px d-flex align-items-center justify-content-center">
                    <svg class="h-17px" fill="var(--primary)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
                        <use xlink:href="{{static_asset('assets/img/icons/tick.svg')}}#tick"></use>
                    </svg>
                </div>
            </div>
        </div>

        <div class="border-top border-primary-300 mt-15px mb-5px"></div>

        <h3 class="fs-14 fw-500 text-primary-50 lh-1 mb-0">{{translate('Total Price')}}</h3>
        <p class="fs-26 fw-700">{{format_price($data['total'])}}</p>

        <a class="btn btn-outline-primary btn-block fw-500 mt-15px fs-18" href="{{route('cart')}}">{{ toUpper(translate('Proceed to checkout')) }}</a>

        <div class="text-center text-primary-50 py-15px fs-14">
            <a class="hov-text-primary border-bottom border-inherit" data-dismiss="modal" >{{ translate('Back to Meals')}}</a>
        </div>
    </div>
</div>
