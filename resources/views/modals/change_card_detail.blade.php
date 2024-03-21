// used from Admin

<div class="modal fade" id="change_card_details_modal">
    <div class="modal-dialog modal-dialog-centered modal-sm " role="document">
        <div class="modal-content" id="modal-content">

            <div class="modal-header d-block align-items-center">

                <button type="button" class="close p-0 pt-15px" data-dismiss="modal" aria-hidden="true"></button>

                <h3 class="text-center fs-18 lg-fs-25 fw-700 mb-30px mt-2 mb-sm-35px">{{toUpper(translate('Change Card Details'))}}</h3>

            </div>
            <div class="modal-body p-0">
{{--                <div class="p-20px">--}}
{{--                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">--}}
{{--                        <svg class="h-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14.74 14.74">--}}
{{--                            <use xlink:href="{{static_asset('assets/img/icons/popup-close.svg')}}#content"></use>--}}
{{--                        </svg>--}}
{{--                    </button>--}}
{{--                </div>--}}
                <div class="px-15px px-lg-35px pb-30px">


                    <form method="POST" action="{{ route('card.change_card_details') }}">
                        @csrf

                        <div class="input-group w-100 mt-4" >

                            <div class="form-control-with-label small-focus animate flex-grow-1 always-focused">
                                <label>{{ translate('RFID no.')}}</label>
                                <input type="text" class="form-control" id="old_card"
                                       readonly disabled autocomplete="off">
                            </div>
                        </div>

                        <div class="input-group w-100 mt-3" id="rfid_no_div">

                            <div class="pt-3px form-control-with-label small-focus animate flex-grow-1 @if(old('rfid_no')) focused @endif">
                                <label>{{ translate('New RFID no.')}}</label>
                                <input type="text" class="form-control remove-all-spaces remove-last-space always-focused"
                                       value="{{ old('rfid_no') }}" name="rfid_no" id="rfid_no"
                                       autocomplete="off">

                                <input type="hidden" class="form-control" name="old_card_id" id="old_card_id"
                                       autocomplete="off">

                            </div>
                            <div class="input-group-append line w-60px w-xxl-85px ">
                                <div id="submit_button_div" class="flex-grow-1 d-none">
                                    <button id="rfid_no_submit"
                                            class="btn btn-primary btn-block border-radius-0 fs-12 xxl-fs-14 px-2px fw-400"
                                            type="button">{{ toUpper(translate('Submit'))}}</button>
                                </div>

                                <div class="loader flex-grow-1" id="loader-div" style="display: none">
                                </div>

                                <div id="correct_rfid" class="flex-grow-1" style="display: none">

                                    <div
                                        class="w-100 h-30px position-absolute custom-div d-flex flex-column justify-content-center">
                                        <div class="text-center">

                                            <svg class="h-30px" fill="green"
                                                 xmlns="http://www.w3.org/2000/svg" height="25" width="25"
                                                 viewBox="0 0 30 30">
                                                <use
                                                    xlink:href="{{static_asset('assets/img/icons/tick.svg')}}#tick"></use>
                                            </svg>
                                        </div>
                                    </div>

                                </div>

                                <div id="already_registered_rfid" class="flex-grow-1" style="display: none">

                                    <div
                                        class="w-100 h-30px position-absolute custom-div d-flex flex-column justify-content-center">
                                        <div class="text-center ">

                                            <svg class=" h-25px"
                                                 xmlns="http://www.w3.org/2000/svg" height="30" width="30"
                                                 viewBox="0 0 21.18 21.27">
                                                <use
                                                    xlink:href="{{static_asset('assets/img/icons/warning_icon.svg')}}#warning_svg"></use>
                                            </svg>
                                        </div>
                                    </div>

                                </div>

                                <div id="incorrect_rfid" class="flex-grow-1" style="display: none">

                                    <div class="w-100 h-30px position-absolute custom-div d-flex">
                                        <div class="text-center m-auto" style="color: red;">
                                            <svg class="z-1 h-17px" fill="red"
                                                 xmlns="http://www.w3.org/2000/svg"
                                                 viewBox="0 0 25.39 25.39">
                                                <use
                                                    xlink:href="{{static_asset('assets/img/icons/x_icon_error.svg')}}#x-icon-error"></use>
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <span id="rfid-error-msg" class="text-error text-red fs-12"></span>
                        <div class="mt-3">
                            <button id="edit-rfid-submit" type="submit"
                                    class="btn btn-outline-primary btn-block fw-700 pt-10px"
                                    disabled>{{toUpper(translate('Save RFID No'))}}</button>
                        </div>

                    </form>

                    <form method="POST" action="{{ route('card.change_card_name') }}">
                        @csrf

                        <input type="hidden" class="form-control"
                                name="edit_card_name_id" id="edit_card_name_id"
                               autocomplete="off">

                        <div class=" pt-3px my-3  form-control-with-label small-focus animate flex-grow-1 always-focused">
                            <label>{{ translate('Card Name')}}</label>
                            <input type="text" class="form-control"
                                  name="card_name" id="card_name"
                                   autocomplete="off">

                        </div>

                        <button type="submit"
                                class="btn btn-outline-primary btn-block fw-700 pt-10px"
                                >{{toUpper(translate('Save Card Name'))}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
