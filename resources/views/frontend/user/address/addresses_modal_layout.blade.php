<div id="new-address-modal" class="side-popup">
    <div class="side-popup-box">
        <div class="side-popup-close">
            <div class="side-popup-close-icon"></div>
            <div class="side-popup-close-text-wrap">
                <div class="side-popup-close-text">{{toUpper(translate('Close'))}}</div>
            </div>
        </div>
        <div class="side-popup-container">
            <div class="side-popup-scroll c-scrollbar">
                <div class="px-20px px-sm-25px py-20px py-sm-40px fs-13 sm-fs-16 fw-500">
                    <form id="add-address-form" class="form-default" role="form" action="{{ route('addresses.store') }}" method="POST">
                        <div class="l-space-1-2 mb-20px text-default-50 fs-11 sm-fs-16">
                            <h2 class="fs-32 sm-fs-45 font-play mb-20px mb-sm-50px fw-700 lh-1 text-secondary">
                                {{ translate('New Address')}}
                            </h2>
                            <p>{{translate('Please fill your new address to continue')}}</p>
                        </div>
                        @csrf
                        <div class="form-group mb-15px">
                            <div class="form-control-with-label small-focus">
                                <label>{{ translate('Address')}}</label>
                                <input class="form-control" name="address" required>
                            </div>
                            <div id="add_address_message" class="invalid-feedback fs-11 d-block"></div>
                        </div>
                        <div class="form-group mb-15px">
                            <div class="form-control-with-label small-focus always-focused">
                                <label>{{ translate('Country')}}</label>
                                <select id="country-add" class="form-control sk-selectpicker" data-live-search="true" name="country" required>
                                    @foreach (\App\Country::getActiveCountriesForShipping() as $key => $country)
                                        <option value="{{ $country->id }}" @if(Auth::check() && Auth::user()->country == $country->id) selected @endif>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="add_country_message" class="invalid-feedback fs-11 d-block"></div>
                        </div>
                        <div class="row gutters-10">
                            <div class="col-sm-6">
                                <div class="form-group mb-15px" data-rel="country-add">
                                    <div class="form-control-with-label small-focus always-focused">
                                        <label>{{ translate('City')}}</label>
                                        <select class="form-control sk-selectpicker" data-live-search="true" name="city" required>

                                        </select>
                                        <input type="text" name="city_name" class="form-control fs-13 md-fs-16 {{ $errors->has('city_name') ? ' is-invalid' : '' }} d-none" disabled>
                                    </div>
                                    <div id="add_city_message" class="invalid-feedback fs-11 d-block"></div>
                                    <div id="add_city_name_message" class="invalid-feedback fs-11 d-block"></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-15px">
                                    <div class="form-control-with-label small-focus">
                                        <label>{{ translate('Post Code')}}</label>
                                        <input type="text" class="form-control" name="postal_code" value="" required>
                                    </div>
                                    <div id="add_postal_message" class="invalid-feedback fs-11 d-block"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-15px">
                            <div class="form-control-with-label small-focus always-focused">
                                <label>{{ translate('Phone') }}</label>
                                <input type="text" class="form-control form-control-phone" name="phone" value="{{ Auth::user()->phone ?? '' }}" required>
                                <div class="form-control-phone-code" data-rel="country-add">+ {{ Auth::user()->phone_code ?? '' }}</div>
                                <input type="hidden" name="phone_code" value="" data-rel="country-add">
                            </div>
                            <div id="add_phone_message" class="invalid-feedback fs-11 d-block"></div>
                        </div>
                        <button type="submit" class="btn btn-block btn-outline-primary py-10px fs-16 fw-500">{{toUpper(translate('Save'))}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="edit-address-modal" class="side-popup">
    <div class="side-popup-box">
        <div class="side-popup-close">
            <div class="side-popup-close-icon"></div>
            <div class="side-popup-close-text-wrap">
                <div class="side-popup-close-text">{{toUpper(translate('Close'))}}</div>
            </div>
        </div>
        <div class="side-popup-container">
            <div class="side-popup-scroll c-scrollbar">
                <div class="px-20px px-sm-25px py-20px py-sm-40px fs-13 sm-fs-16 fw-500" id="edit-address-modal-content"></div>
            </div>
        </div>
    </div>
</div>
