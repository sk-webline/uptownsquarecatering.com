@extends('backend.layouts.app')

@section('content')
    <div class="sk-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">{{translate('Role Information')}}</h5>
    </div>


    <div class="col-lg-7 mx-auto">
        <div class="card">
            <div class="card-body p-0">
                <ul class="nav nav-tabs nav-fill border-light">
                    @foreach (\App\Language::all() as $key => $language)
                        <li class="nav-item">
                            <a class="nav-link text-reset @if ($language->code == $lang) active @else bg-soft-dark border-light border-left-0 @endif py-3" href="{{ route('roles.edit', ['id'=>$role->id, 'lang'=> $language->code] ) }}">
                                <img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" height="11" class="mr-1">
                                <span>{{$language->name}}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <form class="p-4" action="{{ route('roles.update', $role->id) }}" method="POST">
                    <input name="_method" type="hidden" value="PATCH">
                    <input type="hidden" name="lang" value="{{ $lang }}">
                    @csrf
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="name">{{translate('Name')}} <i class="las la-language text-danger" title="{{translate('Translatable')}}"></i></label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" value="{{ $role->getTranslation('name', $lang) }}" required>
                        </div>
                    </div>
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{ translate('Permissions') }}</h5>
                    </div>
                    <br>
                    @php
                        $permissions = json_decode($role->permissions);
                    @endphp
                    <div class="form-group row">
                        <label class="col-md-2 col-from-label" for="banner"></label>
                        <div class="col-md-8">
                            @if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions)))
                            @if (\App\Addon::where('unique_identifier', 'pos_system')->first() != null && \App\Addon::where('unique_identifier', 'pos_system')->first()->activated)
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('POS System') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="1" @php if(in_array(1, $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('2', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Products') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2" @php if(in_array(2, $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            <div class="pl-2">
                                @if(Auth::user()->user_type == 'admin' || in_array('2_1', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Add New Product') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2_1"
                                                {{ (in_array("2_1", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('2_2', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('All Products') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2_2" {{ (in_array("2_2", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('2_3', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('In House Products') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2_3" {{ (in_array("2_3", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                 @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('2_4', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Seller Products') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2_4" {{ (in_array("2_4", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('2_5', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Digital Products') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2_5" {{ (in_array("2_5", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('2_6', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Bulk Import') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2_6" {{ (in_array("2_6", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('2_7', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Bulk Export') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2_7" {{ (in_array("2_7", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('2_8', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Category') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2_8" {{ (in_array("2_8", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('2_8_1', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover pl-2">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Add New Category') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2_8_1" {{ (in_array("2_8_1", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('2_8_2', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover pl-2">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Delete Category') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2_8_2" {{ (in_array("2_8_2", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('2_9', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Brand') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2_9" {{ (in_array("2_9", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('2_9_1', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover pl-2">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Add New Brand') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2_9_1" {{ (in_array("2_9_1", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('2_9_2', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover pl-2">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Delete Brand') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2_9_2" {{ (in_array("2_9_2", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('2_0_1', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Attribute') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2_0_1" {{ (in_array("2_0_1", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('2_0_2', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Product Reviews') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2_0_2" {{ (in_array("2_0_2", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('2_0_3', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Colors') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="2_0_3" {{ (in_array("2_0_3", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @if(Auth::user()->user_type == 'admin' || in_array('03', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Sales') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="03" @php if(in_array(03, $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            <div class="pl-2">
                                @if(Auth::user()->user_type == 'admin' || in_array('3', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('All Orders') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="3" @php if(in_array(3, $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('4', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Inhouse orders') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="4" @php if(in_array(4, $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('5', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Seller Orders') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="5" @php if(in_array(5, $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('6', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Pick-up Point Order') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="6" @php if(in_array(6, $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @if (\App\Addon::where('unique_identifier', 'refund_request')->first() != null && \App\Addon::where('unique_identifier', 'refund_request')->first()->activated)
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Refunds') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="7" @php if(in_array(7, $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('8', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Customers') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="8" @php if(in_array(8, $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('partnership_request', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Partnership Request') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="partnership_request" @php if(in_array('partnership_request', $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('cashiers', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Cashiers') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="cashiers" @php if(in_array('cashiers', $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('9', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Sellers') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="9" @php if(in_array(9, $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            <div class="pl-2">
                                @if(Auth::user()->user_type == 'admin' || in_array('9_0_1', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Seller Verification Form') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="9_0_1" {{ (in_array("9_0_1", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('9_0_2', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Seller Commission') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="9_0_2" {{ (in_array("9_0_2", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @if(Auth::user()->user_type == 'admin' || in_array('10', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Reports') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="10" {{ (in_array("10", $permissions) ? 'checked' : '' ) }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            <div class="pl-2">
                                @if(Auth::user()->user_type == 'admin' || in_array('10_0_1', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('In House Product Sale') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="10_0_1" {{ (in_array("10_0_1", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('10_0_2', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Seller Products Sale') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="10_0_2" {{ (in_array("10_0_2", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('10_0_3', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Products Stock') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="10_0_3" {{ (in_array("10_0_3", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('10_0_4', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Products wishlist') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="10_0_4" {{ (in_array("10_0_4", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('10_0_5', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('User Searches') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="10_0_5" {{ (in_array("10_0_5", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('10_0_6', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Commission History') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="10_0_6" {{ (in_array("10_0_6", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif @if(Auth::user()->user_type == 'admin' || in_array('10_0_7', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Wallet Recharge History') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="10_0_7" {{ (in_array("10_0_7", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @if(Auth::user()->user_type == 'admin' || in_array('23', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Blog System') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="23" {{ (in_array("23", $permissions) ? 'checked' : '' ) }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            <div class="pl-2">
                                @if(Auth::user()->user_type == 'admin' || in_array('23_0_1', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('All Posts') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="23_0_1" {{ (in_array("23_0_1", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('23_0_2', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Categories') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="23_0_2" {{ (in_array("23_0_2", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @if(Auth::user()->user_type == 'admin' || in_array('11', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Marketing') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="11" @php if(in_array(11, $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Support') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="12" @php if(in_array(12, $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            <div class="pl-2">
                                @if(Auth::user()->user_type == 'admin' || in_array('12_0_1', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Ticket') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="12_0_1" {{ (in_array("12_0_1", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('12_0_2', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Product Queries') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="12_0_2" {{ (in_array("12_0_2", $permissions) ? 'checked' : '' ) }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @if(Auth::user()->user_type == 'admin' || in_array('13', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Website Setup') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="13" @php if(in_array(13, $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            <div class="pl-2">
                                @if(Auth::user()->user_type == 'admin' || in_array('13_1', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Header') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="13_1" @php if(in_array("13_1", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('13_2', json_decode(Auth::user()->staff->role->permissions)))

                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Footer') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="13_2" @php if(in_array("13_2", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('13_3', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Pages') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="13_3" @php if(in_array("13_3", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('13_3_1', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10 pl-4">
                                        <label class="col-from-label">{{ translate('Add New Page') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="13_3_1" @php if(in_array("13_3_1", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('13_3_2', json_decode(Auth::user()->staff->role->permissions)))

                                <div class="row hover">
                                    <div class="col-md-10 pl-4">
                                        <label class="col-from-label">{{ translate('Delete Pages') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="13_3_2" @php if(in_array("13_3_2", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('13_4', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Appearance') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="13_4" @php if(in_array("13_4", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @if(Auth::user()->user_type == 'admin' || in_array('14', json_decode(Auth::user()->staff->role->permissions)))

                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Setup & Configurations') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14" @php if(in_array('14', $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            <div class="pl-2">
                                @if(Auth::user()->user_type == 'admin' || in_array('14_0_1', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('General Settings') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14_0_1" @php if(in_array("14_0_1", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('14_0_1', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Features activation') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14_0_2" @php if(in_array("14_0_2", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('14_0_3', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Languages') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14_0_3" @php if(in_array("14_0_3", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('14_0_4', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Currency') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14_0_4" @php if(in_array("14_0_4", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('14_0_5', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Vat & TAX') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14_0_5" @php if(in_array("14_0_5", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('14_0_6', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Pickup point') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14_0_6" @php if(in_array("14_0_6", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('14_0_7', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('SMTP Settings') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14_0_7" @php if(in_array("14_0_7", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('14_0_8', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Payment Methods') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14_0_8" @php if(in_array("14_0_8", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('14_0_9', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('File System Configuration') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14_0_9" @php if(in_array("14_0_9", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('14_1_0', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Social media Logins') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14_1_0" @php if(in_array("14_1_0", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('14_1_1', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Analytics Tools') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14_1_1" @php if(in_array("14_1_1", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('14_1_2', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Facebook') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14_1_2" @php if(in_array("14_1_2", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('14_1_3', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Google reCAPTCHA') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14_1_3" @php if(in_array("14_1_3", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('14_1_4', json_decode(Auth::user()->staff->role->permissions)))
                                <div class="row hover">
                                    <div class="col-md-10">
                                        <label class="col-from-label">{{ translate('Shipping') }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14_1_4" @php if(in_array("14_1_4", $permissions)) echo "checked"; @endphp>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                <div class="pl-2">
                                    @if(Auth::user()->user_type == 'admin' || in_array('14_1_4_0', json_decode(Auth::user()->staff->role->permissions)))
                                    <div class="row hover">
                                        <div class="col-md-10">
                                            <label class="col-from-label">{{ translate('Shipping Configuration') }}</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="sk-switch sk-switch-success mb-0">
                                                <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14_1_4_0" @php if(in_array("14_1_4_0", $permissions)) echo "checked"; @endphp>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                    @endif
                                    @if(Auth::user()->user_type == 'admin' || in_array('14_1_4_1', json_decode(Auth::user()->staff->role->permissions)))
                                    <div class="row hover">
                                        <div class="col-md-10">
                                            <label class="col-from-label">{{ translate('Shipping Countries') }}</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="sk-switch sk-switch-success mb-0">
                                                <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14_1_4_1" @php if(in_array("14_1_4_1", $permissions)) echo "checked"; @endphp>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                    @endif
                                    @if(Auth::user()->user_type == 'admin' || in_array('14_1_4_2', json_decode(Auth::user()->staff->role->permissions)))
                                    <div class="row hover">
                                        <div class="col-md-10">
                                            <label class="col-from-label">{{ translate('Shipping Cities') }}</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="sk-switch sk-switch-success mb-0">
                                                <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="14_1_4_2" @php if(in_array("14_1_4_2", $permissions)) echo "checked"; @endphp>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @if(Auth::user()->user_type == 'admin' || in_array('15', json_decode(Auth::user()->staff->role->permissions)))
                                @if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated)
                                    <div class="row hover">
                                        <div class="col-md-10">
                                            <label class="col-from-label">{{ translate('Affiliate System') }}</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="sk-switch sk-switch-success mb-0">
                                                <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="15" @php if(in_array(15, $permissions)) echo "checked"; @endphp>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('16', json_decode(Auth::user()->staff->role->permissions)))
                                @if (\App\Addon::where('unique_identifier', 'offline_payment')->first() != null && \App\Addon::where('unique_identifier', 'offline_payment')->first()->activated)
                                    <div class="row hover">
                                        <div class="col-md-10">
                                            <label class="col-from-label">{{ translate('Offline Payment System') }}</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="sk-switch sk-switch-success mb-0">
                                                <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="16" @php if(in_array(16, $permissions)) echo "checked"; @endphp>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('17', json_decode(Auth::user()->staff->role->permissions)))
                                @if (\App\Addon::where('unique_identifier', 'paytm')->first() != null && \App\Addon::where('unique_identifier', 'paytm')->first()->activated)
                                    <div class="row hover">
                                        <div class="col-md-10">
                                            <label class="col-from-label">{{ translate('Paytm Payment Gateway') }}</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="sk-switch sk-switch-success mb-0">
                                                <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="17" @php if(in_array(17, $permissions)) echo "checked"; @endphp>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('18', json_decode(Auth::user()->staff->role->permissions)))
                                @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
                                    <div class="row hover">
                                        <div class="col-md-10">
                                            <label class="col-from-label">{{ translate('Club Point System') }}</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="sk-switch sk-switch-success mb-0">
                                                <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="18" @php if(in_array(18, $permissions)) echo "checked"; @endphp>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('19', json_decode(Auth::user()->staff->role->permissions)))
                                @if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                    <div class="row hover">
                                        <div class="col-md-10">
                                            <label class="col-from-label">{{ translate('OTP System') }}</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="sk-switch sk-switch-success mb-0">
                                                <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="19" @php if(in_array(19, $permissions)) echo "checked"; @endphp>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('20', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Staff') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="20" @php if(in_array(20, $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('22', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('System') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="22" @php if(in_array(22, $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('21', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Addon Manager') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="21" @php if(in_array(21, $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('24', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Uploaded Files') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="24" @php if(in_array(24, $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('services', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Services') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="services" @php if(in_array('services', $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('faq', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('FAQ') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="faq" @php if(in_array('faq', $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('catering_reports', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Catering Reports') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="catering_reports" @php if(in_array('catering_reports', $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('meal_reports', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Meal Reports') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="meal_reports" @php if(in_array('meal_reports', $permissions)) echo "checked"; @endphp>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('organisations', json_decode(Auth::user()->staff->role->permissions)))
                            <div class="row hover">
                                <div class="col-md-10">
                                    <label class="col-from-label">{{ translate('Organisations') }}</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="organisations" @php if(in_array('organisations', $permissions)) echo "checked"; @endphp>
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
                </form>
            </div>
        </div>
    </div>
@endsection
