@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Country Information')}}</h5>
            </div>
            <div class="card-body">
                <form id="add_form" class="form-horizontal" action="{{ route('countries.update', $country->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Name')}}</label>
                        <div class="col-md-9">
                            <div class="form-control">{{ $country->name }}</div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="btms_vat">{{translate('BTMS VAT')}}</label>
                        <div class="col-md-9 input-group">
                            <select class="form-control sk-selectpicker" name="btms_vat" id="btms_vat" required>
                                <option value="" selected disabled>Select VAT</option>
                                @foreach($btms_vat_codes as $btms_vat_code)
                                    <option value="{{ $btms_vat_code->{'VAT Code'} }}" {{ selected($btms_vat_code->{'VAT Code'}, $country->btms_vat_code) }}>{{ $btms_vat_code->{'Display Name'} }} - {{ number_format($btms_vat_code->{'Percentage'}, 2) }}%</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @php
                    /*
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('VAT Percentage')}}</label>
                        <div class="col-md-9 input-group">
                            <input type="number" lang="en" min="0" step="0.01" id="vat_percentage" name="vat_percentage" value="{{ $country->vat_percentage }}" class="form-control">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    */
                    @endphp

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('VAT Included')}}</label>
                        <div class="col-md-9">
                            <label class="sk-switch sk-switch-success mb-0">
                                <input type="checkbox" name="vat_included" value="1" @if($country->vat_included == 1) checked @endif>
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Show')}}</label>
                        <div class="col-md-9">
                            <label class="sk-switch sk-switch-success mb-0">
                                <input type="checkbox" name="status" value="1" @if($country->status == 1) checked @endif>
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">
                            {{translate('Save')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
