@extends('backend.layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-5 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Add New Cashier')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" class="form-default" action="{{ route('cashiers.store') }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-10px mb-sm-15px">
                            <div
                                class="form-control-with-label small-focus small-field @if(old('name')) focused @endif ">
                                <label>{{ translate('Name') }}</label>
                                <input type="text"
                                       class="form-control form-no-space{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                       value="{{ old('name') }}" name="name" required>
                            </div>
                            @if ($errors->has('name'))
                                <div class="invalid-feedback fs-10 d-block"
                                     role="alert">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                        <div class="form-group mb-10px mb-sm-15px">
                            <div
                                class="form-control-with-label small-focus small-field @if(old('surname')) focused @endif ">
                                <label>{{ translate('Surname') }}</label>
                                <input type="text"
                                       class="form-control{{ $errors->has('surname') ? ' is-invalid' : '' }}"
                                       value="{{ old('surname') }}" name="surname" required>
                            </div>
                            @if ($errors->has('surname'))
                                <div class="invalid-feedback fs-10 d-block"
                                     role="alert">{{ $errors->first('surname') }}</div>
                            @endif
                        </div>
                        <div class="form-group mb-10px mb-sm-15px">
                            <div class="form-control-with-label small-focus small-field ">
                                <label>{{ translate('Username') }}</label>
                                <input type="text"
                                       class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}"
                                       value="{{ old('username') }}" name="username" required>
                            </div>
                            @if ($errors->has('username'))
                                <div class="invalid-feedback fs-10 d-block"
                                     role="alert">{{ $errors->first('username') }}</div>
                            @endif
                        </div>


                        <div class="form-group mb-10px mb-sm-15px">
                            <div class="position-relative">
                                <label>{{ translate('Phone') }}</label>
                                <input type="number" lang="en" min="0" 
                                       class="form-control form-control-phone{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                                       value="{{old('phone')}}" name="phone">
                                <div class="form-control-phone-code"></div>
                                <input type="hidden" name="phone_code" value="">
                            </div>
                            @if ($errors->has('phone'))
                                <div class="invalid-feedback fs-10 d-block" role="alert">
                                    {{ $errors->first('phone') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group mb-10px mb-sm-15px">
                            <div class="position-relative">
                                <label>{{ translate('City') }}</label>
                                <input type="text" lang="en"
                                       class="form-control form-control-phone{{ $errors->has('city') ? ' is-invalid' : '' }}"
                                       value="{{old('city')}}" name="city">
                            </div>
                            @if ($errors->has('phone'))
                                <div class="invalid-feedback fs-10 d-block" role="alert">
                                    {{ $errors->first('phone') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group mb-10px mb-sm-15px">
                            <div class="form-control-with-label small-focus small-field">
                                <label>{{ translate('Password') }}</label>
                                <input type="password"
                                       class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                       name="password" required>
                            </div>
                            @if ($errors->has('password'))
                                <div class="invalid-feedback fs-10 d-block"
                                     role="alert">{{ $errors->first('password') }}</div>
                            @endif
                        </div>


                        <div class="form-group mb-10px">
                            <div class="form-control-with-label small-focus small-field">
                                <label>{{ translate('Confirm Password') }}</label>
                                <input type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Active')}}</label>
                            <div class="col-md-9">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="active_cashier" checked>
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group mt-3 mb-10px mb-sm-15px">
                            {{--                            <fieldset>--}}
                            <label>{{ translate('Organisations') }}</label>
                            <div class="position-relative row ">
                                @foreach($organisations as $organisation)
                                    <div class="col-md-6 mt-2">
                                        <label class="sk-switch sk-switch-success mb-0 ml-3">
                                            <input class="lh-1-1" type="checkbox" value="{{$organisation->id}}" name="organisations[]" >
                                            <span></span>

                                            <label class="fs-13"  for="organisations[]">{{$organisation->name}}</label>

                                        </label>

                                    </div>
                                @endforeach

                            </div>
                            {{--                            </fieldset>--}}
                        </div>


                        <div class="form-group mb-0 text-right">
                            <a href="{{route('cashiers.index')}}">
                                <button type="button" class="btn btn-soft-danger">{{translate('Cancel')}}</button>
                            </a>
                            <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')

    <script type="text/javascript">




    </script>
@endsection

