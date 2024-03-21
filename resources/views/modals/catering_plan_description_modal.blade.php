<div class="modal-body p-0">
    <div class="p-10px">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
            <svg class="h-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14.74 14.74">
                <use xlink:href="{{static_asset('assets/img/icons/popup-close.svg')}}#content"></use>
            </svg>
        </button>
    </div>
    <div class="px-15px px-lg-35px pb-20px">
        <h3 class="text-center fs-18 lg-fs-25 fw-700 mb-30px mb-sm-35px">{{toUpper(translate('Meal Package Description'))}}</h3>

        <div class="w-55px border-top border-secondary border-width-2 mb-30px mb-sm-35px mx-auto"></div>

        <div class="text-primary-50 fs-14 text-primary-50">{!! $catering_plan->description !!}</div>
    </div>

</div>
