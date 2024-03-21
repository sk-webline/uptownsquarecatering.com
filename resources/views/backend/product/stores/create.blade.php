@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Store Information')}}</h5>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('stores.store') }}" method="POST" enctype="multipart/form-data">
                	@csrf
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Name')}}</label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Image')}} <small>({{ translate('180 x 126') }})</small></label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="skuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="thumbnail_img" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('City')}}</label>
                        <div class="col-md-9">
                            <select class="select2 form-control sk-selectpicker" name="city_id" data-toggle="select2" data-placeholder="Choose ..." data-live-search="true">
                                <option value="0">{{ translate('Choose City') }}</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->getTranslation('name') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Ordering Number')}}</label>
                        <div class="col-md-9">
                            <input type="number" name="order_level" class="form-control" id="order_level" placeholder="{{translate('Order Level')}}">
                            <small>Higher number has high priority</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Telephone')}}</label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Telephone')}}" id="phone" name="phone" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Fax')}}</label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Fax')}}" id="fax" name="fax" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Address')}}</label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Address')}}" id="address" name="address" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Google Maps Url')}}</label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Google Maps Url')}}" id="google_map_url" name="google_map_url" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Working Hours')}} <small>({{ translate('Row 1') }})</small></label>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" placeholder="{{translate('Days')}}" id="working_days_1" name="working_days_1" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" placeholder="{{translate('Hours')}}" id="working_hours_1" name="working_hours_1" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Working Hours')}} <small>({{ translate('Row 2') }})</small></label>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" placeholder="{{translate('Days')}}" id="working_days_2" name="working_days_2" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" placeholder="{{translate('Hours')}}" id="working_hours_2" name="working_hours_2" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Working Hours')}} <small>({{ translate('Row 3') }})</small></label>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" placeholder="{{translate('Days')}}" id="working_days_3" name="working_days_3" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" placeholder="{{translate('Hours')}}" id="working_hours_3" name="working_hours_3" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="position-relative store-img mb-3 overflow-hidden">
                        <img class="w-100" src="{{static_asset('assets/img/icons/cyprus-map.svg')}}" alt="">
                    </div>

                    <div class="form-group row dot-info-row" data-bind=".store-img" data-key="0">
                        <label class="col-md-3 col-form-label">{{translate('Store Map')}}</label>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="number" min="0" max="100" placeholder="{{translate('X Position')}}" name="x_pos" class="form-control dot-info-x" value="50">
                                </div>
                                <div class="col-md-6">
                                    <input type="number" min="0" max="100" placeholder="{{translate('Y Position')}}" name="y_pos" class="form-control dot-info-y" value="50">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="position-relative store-img-phone mb-3 overflow-hidden">
                        <img class="w-100" src="{{static_asset('assets/img/icons/cyprus-map-mobile.svg')}}" alt="">
                    </div>

                    <div class="form-group row dot-info-row" data-bind=".store-img-phone" data-key="0">
                        <label class="col-md-3 col-form-label">{{translate('Store Map Mobile')}}</label>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="number" min="0" max="100" placeholder="{{translate('X Position')}}" name="x_pos_phone" class="form-control dot-info-x" value="50">
                                </div>
                                <div class="col-md-6">
                                    <input type="number" min="0" max="100" placeholder="{{translate('Y Position')}}" name="y_pos_phone" class="form-control dot-info-y" value="50">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
