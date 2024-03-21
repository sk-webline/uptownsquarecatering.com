<div class="modal-body p-0">
    <div class="p-10px">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
            <svg class="h-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14.74 14.74">
                <use xlink:href="{{static_asset('assets/img/icons/popup-close.svg')}}#content"></use>
            </svg>
        </button>
    </div>
    <div class="px-15px px-lg-35px pb-20px pb-sm-30px">
        <h3 class="text-center fs-17 lg-fs-25 fw-700 mb-30px mb-sm-35px">{{toUpper(translate('You can only have one option'))}}</h3>

        <div class="w-55px border-top border-secondary border-width-2 mb-30px mb-sm-35px mx-auto"></div>

        <div class="text-center text-primary-50 fs-16 mb-35px">
            <p>{{ translate('You have already selected your meal plan. If you want to change your option, your previous one must be removed.')}}</p>
            <p class="fw-700">{{ translate('Are you ok with that?')}}</p>
        </div>

        <div class="text-center">
            <a class="addCateringPlanToCart"> </a>

            <input type="hidden" id="this_catering_plan" value="{{$catering_plan_id}}">

            @if($catering_plan_id=='custom')

                <input type="hidden" id="new_plan_from" value="{{$data['from_date']}}">
                <input type="hidden" id="new_plan_to" value="{{$data['to_date']}}">
                <input type="hidden" id="new_plan_snack" value="{{$data['snack_num']}}">
                <input type="hidden" id="new_plan_meal" value="{{$data['meal_num']}}">
                <input type="hidden" id="new_plan_price" value="{{$data['total']}}">
            @endif

            <button id="one_option" class="btn btn-outline-primary btn-block fw-700 fs-14 sm-fs-18 py-13px px-5px lh-1 addCateringPlanToCart" data-button="remove_prev" onclick="go_to_cart()">
                <svg class="h-1em mr-2 align-top opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14.41 18.8">
                    <use xlink:href="{{static_asset('assets/img/icons/cart-trash-icon.svg')}}#content"></use>
                </svg>
                {{toUpper(translate('Remove my Previous Option'))}}
            </button>
        </div>

        <div class="text-center text-primary-60 fs-14 sm-fs-18 mt-15px mt-sm-20px">
            <a href="javascript:void(0);" class="border-bottom border-inherit hov-text-primary" data-dismiss="modal">
                {{ translate('Cancel')}}
            </a>
        </div>
    </div>
</div>
