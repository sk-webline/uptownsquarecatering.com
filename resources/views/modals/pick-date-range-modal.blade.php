<!-- Date Range Modal -->
<div id="date-range-modal" class="modal fade">
    <div class="modal-dialog modal-lg modal-dialog-centered my-modal-lg-2 ">
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
                    <h3 class="text-center fs-18 lg-fs-27 fw-700 mb-30px mb-sm-35px">{{toUpper(translate('Choose yor subscription Dates'))}}</h3>
                    <div class="w-55px border-top border-secondary border-width-2 mb-35px mb-sm-55px mx-auto"></div>
                    {{--                <button id="custom-prev-btn" class="prev-btn-style"><</button>--}}
                    {{--                <button class="next-btn-style"><</button>--}}

                    <div class="data-range-calendar-style">
                        <div id="calendar-date-range" class="mb-20px w-100">
                        </div>
                    </div>

                    <button id="select-dates" class="btn btn-outline-primary py-10px fs-18 fw-700 btn-block">{{toUpper(translate('Select Dates'))}}</button>

                    {{--                <input type="text" name="datefilter" value="" />--}}

                    {{--                <div id="range-picker" class="mt-4 w-100 h-320px mb-2 " >--}}

                    {{--                </div>--}}
                </div>
            </div>
        </div>
    </div>
</div><!-- /.modal -->


