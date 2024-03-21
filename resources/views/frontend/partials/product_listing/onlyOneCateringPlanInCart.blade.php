<div class="modal-header row no-gutters w-100 text-right"
     style="min-height:10px; border-bottom:none; ">
    <div class="col-11 p-0"></div>
    <div class="col-1 p-0">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    </div>
</div>

<div class="modal-body pt-0 pb-0" style="height: 350px">

    <p class="h2 mx-1 text-primary text-center text-uppercase fs-25 fw-600">{{translate('You can only have one option')}}</p>

    <div class="lineWidth-35 border-top-red border-default w-55px mx-auto mt-4 "></div>

    <div class="text-center pt-2 mt-5 fs-16 ff-Manrope fw-600">
        <p class="text-reset px-5 opacity-50 ">
            {{ translate('You have already selected your meal plan. If you want to change your option, your previous one must be removed.')}}
        </p>
    </div>

    <div class="text-center pt-2 mt-2 fs-17 ff-Manrope fw-600">
        <p class="text-reset px-5 opacity-60 ">
            {{ translate('Are you ok with that?')}}
        </p>
    </div>

    <div class="text-center pt-2 mt-2 mx-5 fs-17 ff-Manrope fw-600">

        <button id="{{$catering_plan_id}}"
                class=" mt-4 w-100 btn btn-custom-4 ff-Manrope text-uppercase fw-600  addCateringPlanToCart"
                data-button="remove_prev"
        >  {{ translate('Remove my previous option') }}</button>
    </div>

    <div class="text-center pt-2 mt-2 mb-2 fs-17 ff-Manrope fw-200">
        <a class="text-reset px-5 opacity-80 c-pointer" onclick="close_one_option_modal()">
            <span class="text-underline"> {{ translate('Cancel')}}</span>
        </a>
    </div>


</div>
