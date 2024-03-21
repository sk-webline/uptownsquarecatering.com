@extends('backend.layouts.app')

@section('content')
    <div class="sk-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">{{translate('Edit Customer Code')}}</h5>
    </div>
    <div class="row">
        <div class="col-lg-5 mx-auto">
            <div class="card">
                <div class="card-body p-0">
                    <form class="p-4" action="{{ route('customers.update', encrypt($customer->id)) }}" method="POST" enctype="multipart/form-data">
                        <input name="_method" type="hidden" value="PATCH">
                        @csrf
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Company Name')}}</label>
                            <div class="col-md-9">
                                <input type="text" placeholder="{{translate('Company Name')}}" value="{{ $customer->user->company }}" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Name')}}</label>
                            <div class="col-md-9">
                                <input type="text" placeholder="{{translate('Name')}}" value="{{ $customer->user->name }}" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Email')}}</label>
                            <div class="col-md-9">
                                <input type="text" placeholder="{{translate('Email')}}" value="{{ $customer->user->email }}" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Phone')}}</label>
                            <div class="col-md-9">
                                <input type="text" placeholder="{{translate('Phone')}}" value="{{ $customer->user->phone }}" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Customer Code')}}</label>
                            <div class="col-md-9">
                                <input type="text" placeholder="{{translate('Customer Code')}}" id="customer_code" name="customer_code" value="{{ $customer->user->btms_customer_code }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <a href="{{ route('customers.index') }}" class="btn btn-danger">{{translate('Cancel')}}</a>
                            <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
