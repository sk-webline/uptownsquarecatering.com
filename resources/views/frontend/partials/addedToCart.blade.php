<div class="modal-body px-15px px-sm-30px pt-40px pb-15px added-to-cart">
    <div class="added-cart-image">
        <img src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($product->thumbnail_img) }}" class="lazyload img-contain absolute-full h-100" alt="">
    </div>
    <h3 class="text-default-30 fs-20 sm-fs-30 fw-700 font-play mb-10px mt-5px">
        <img class="size-15px size-sm-20px align-baseline" src="{{static_asset('assets/img/icons/added-icon.svg')}}" alt="">
        {{ translate('Item added to your Cart')}}
    </h3>
    <h6 class="fs-13 sm-fs-16 fw-700 l-space-1-2">{{$product->getTranslation('name')}}</h6>
    <div class="border-top border-default-200 pt-2px mb-20px mb-sm-30px">
        <h3 class="fs-12 fw-400 text-default-50 lh-1 mb-0">{{ translate('Total Price')}}</h3>
        <div class="fs-20 fw-700 font-play">{{ single_price(($data['price']+$data['tax'])*$data['quantity']) }}</div>
    </div>
    <div class="text-center">
        <a href="{{ route('cart') }}" class="btn btn-outline-primary btn-block mb-15px fs-16 sm-fs-18 l-space-1-2 btn-b-width-2 px-10px">{{ toUpper(translate('Proceed to Checkout'))}}</a>
        <a href="javascript:void(0);" class="border-bottom border-inherit text-default-50 fs-12 hov-text-primary" data-dismiss="modal">{{translate('Continue Shopping')}}</a>
    </div>
</div>




