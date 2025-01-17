@extends('backend.layouts.app')

@section('content')

<div class="col-lg-7 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Role Information')}}</h5>
        </div>
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="name">{{translate('Name')}}</label>
                    <div class="col-md-9">
                        <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Permissions') }}</h5>
                </div>
                <br>
                <div class="form-group row">
                    <label class="col-md-2 col-from-label"></label>
                    <div class="col-md-8">
                    @if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions)))
                        @if (\App\Addon::where('unique_identifier', 'pos_system')->first() != null && \App\Addon::where('unique_identifier', 'pos_system')->first()->activated)
                          <div class="row">
                              <div class="col-md-10">
                                  <label class="col-from-label">{{ translate('POS System') }}</label>
                              </div>
                              <div class="col-md-2">
                                  <label class="sk-switch sk-switch-success mb-0">
                                      <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="1">
                                      <span class="slider round"></span>
                                  </label>
                              </div>
                          </div>
                        @endif
                    @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('2', json_decode(Auth::user()->staff->role->permissions)))
                        <div class="row">
                            <div class="col-md-10">
                                <label class="col-from-label">{{ translate('Products') }}</label>
                            </div>
                            <div class="col-md-2">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('3', json_decode(Auth::user()->staff->role->permissions)))
                        <div class="row">
                            <div class="col-md-10">
                                <label class="col-from-label">{{ translate('All Orders') }}</label>
                            </div>
                            <div class="col-md-2">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="3">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('4', json_decode(Auth::user()->staff->role->permissions)))
                        <div class="row">
                            <div class="col-md-10">
                                <label class="col-from-label">{{ translate('Inhouse orders') }}</label>
                            </div>
                            <div class="col-md-2">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="4">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('5', json_decode(Auth::user()->staff->role->permissions)))
                        <div class="row">
                            <div class="col-md-10">
                                <label class="col-from-label">{{ translate('Seller Orders') }}</label>
                            </div>
                            <div class="col-md-2">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="5">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('6', json_decode(Auth::user()->staff->role->permissions)))
                        <div class="row">
                            <div class="col-md-10">
                                <label class="col-from-label">{{ translate('Pick-up Point Order') }}</label>
                            </div>
                            <div class="col-md-2">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="6">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('7', json_decode(Auth::user()->staff->role->permissions)))
                            @if (\App\Addon::where('unique_identifier', 'refund_request')->first() != null && \App\Addon::where('unique_identifier', 'refund_request')->first()->activated)
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Refunds') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="7">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('8', json_decode(Auth::user()->staff->role->permissions)))
                        <div class="row">
                            <div class="col-md-10">
                                <label class="col-from-label">{{ translate('Customers') }}</label>
                            </div>
                            <div class="col-md-2">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="8">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('9', json_decode(Auth::user()->staff->role->permissions)))
                        <div class="row">
                            <div class="col-md-10">
                                <label class="col-from-label">{{ translate('Sellers') }}</label>
                            </div>
                            <div class="col-md-2">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="9">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('10', json_decode(Auth::user()->staff->role->permissions)))
                        <div class="row">
                            <div class="col-md-10">
                                <label class="col-from-label">{{ translate('Reports') }}</label>
                            </div>
                            <div class="col-md-2">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="10">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('11', json_decode(Auth::user()->staff->role->permissions)))
                        <div class="row">
                            <div class="col-md-10">
                                <label class="col-from-label">{{ translate('Marketing') }}</label>
                            </div>
                            <div class="col-md-2">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="11">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions)))
                        <div class="row">
                            <div class="col-md-10">
                                <label class="col-from-label">{{ translate('Support') }}</label>
                            </div>
                            <div class="col-md-2">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="12">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('13', json_decode(Auth::user()->staff->role->permissions)))
                        <div class="row">
                            <div class="col-md-10">
                                <label class="col-from-label">{{ translate('Website Setup') }}</label>
                            </div>
                            <div class="col-md-2">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="13">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('14', json_decode(Auth::user()->staff->role->permissions)))
                        <div class="row">
                            <div class="col-md-10">
                                <label class="col-from-label">{{ translate('Setup & Configurations') }}</label>
                            </div>
                            <div class="col-md-2">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('15', json_decode(Auth::user()->staff->role->permissions)))
                            @if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated)
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Affiliate System') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="15">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('16', json_decode(Auth::user()->staff->role->permissions)))
                            @if (\App\Addon::where('unique_identifier', 'offline_payment')->first() != null && \App\Addon::where('unique_identifier', 'offline_payment')->first()->activated)
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Offline Payment System') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="16">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('17', json_decode(Auth::user()->staff->role->permissions)))
                            @if (\App\Addon::where('unique_identifier', 'paytm')->first() != null && \App\Addon::where('unique_identifier', 'paytm')->first()->activated)
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Paytm Payment Gateway') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="17">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('18', json_decode(Auth::user()->staff->role->permissions)))
                            @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Club Point System') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="18">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('19', json_decode(Auth::user()->staff->role->permissions)))
                            @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('OTP System') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="19">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('20', json_decode(Auth::user()->staff->role->permissions)))
                        <div class="row">
                            <div class="col-md-10">
                                <label class="col-from-label">{{ translate('Staff') }}</label>
                            </div>
                            <div class="col-md-2">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="20">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('21', json_decode(Auth::user()->staff->role->permissions)))
                        <div class="row">
                            <div class="col-md-10">
                                <label class="col-from-label">{{ translate('Addon Manager') }}</label>
                            </div>
                            <div class="col-md-2">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="21">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                </div>
            </div>
        </from>
    </div>
</div>

@endsection
