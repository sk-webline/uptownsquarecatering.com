<form id="edit-address-form" class="form-default" role="form" action="{{ route('addresses.update', $address_data->id) }}" method="POST">
    @csrf
    <div class="l-space-1-2 mb-20px text-default-50 fs-11 sm-fs-16">
        <h2 class="fs-32 sm-fs-45 font-play mb-20px mb-sm-50px fw-700 lh-1 text-secondary">
            {{ translate('Edit Address')}}
        </h2>
        <p>{{translate('You can change your address information here')}}</p>
    </div>
    <div class="form-group mb-15px">
        <div class="form-control-with-label small-focus @if($address_data->address) focused @endif ">
            <label>{{ translate('Address')}}</label>
            <input class="form-control fs-14 md-fs-16" name="address" value="{{ $address_data->address }}" required>
        </div>
        <div id="edit_address_message" class="invalid-feedback fs-11 d-block"></div>
    </div>

    <div class="form-group mb-15px">
        <div class="form-control-with-label small-focus always-focused">
            <label>{{ translate('Country')}}</label>
            <select class="form-control fs-14 md-fs-16 sk-selectpicker" data-live-search="true" data-placeholder="{{ translate('Select your country')}}" name="country" id="country-edit" required>
                @foreach (\App\Country::getActiveCountriesForShipping() as $key => $country)
                    <option value="{{ $country->id }}" @if($address_data->country == $country->id) selected @endif>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div id="edit_country_message" class="invalid-feedback fs-11 d-block"></div>
    </div>
    <input type="hidden" name="selected_city" value="{{$address_data->city}}">
    <div class="row gutters-10">
        <div class="col-sm-6">
            <div class="form-group mb-15px" data-rel="country-edit">
                <div class="form-control-with-label small-focus always-focused">
                    <label>{{ translate('City')}}</label>
                    <select class="form-control fs-14 md-fs-16 sk-selectpicker" data-live-search="true" name="city" required></select>
                    <input type="text" name="city_name" class="form-control fs-13 md-fs-16 d-none" disabled>
                </div>
                <div id="edit_city_message" class="invalid-feedback fs-11 d-block"></div>
                <div id="edit_city_name_message" class="invalid-feedback fs-11 d-block"></div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group mb-15px">
                <div class="form-control-with-label small-focus @if($address_data->postal_code) focused @endif ">
                    <label>{{ translate('Post code')}}</label>
                    <input type="text" class="form-control fs-14 md-fs-16" value="{{ $address_data->postal_code }}" name="postal_code" required>
                </div>
                <div id="edit_postal_message" class="invalid-feedback fs-11 d-block"></div>
            </div>
        </div>
    </div>

    <div class="form-group mb-15px">
        <div class="form-control-with-label small-focus always-focused">
            <label>{{ translate('Phone') }}</label>
            <input type="text" class="form-control form-control-phone" name="phone" value="{{ Auth::user()->phone }}" required>
            <div class="form-control-phone-code" data-rel="country-edit">+ {{ Auth::user()->phone_code }}</div>
            <input type="hidden" name="phone_code" value="" data-rel="country-edit">
        </div>
        <div id="edit_phone_message" class="invalid-feedback fs-11 d-block"></div>
    </div>

    <button type="submit" class="btn btn-block btn-outline-primary py-10px fs-16 fw-500">{{toUpper(translate('Save'))}}</button>
</form>
