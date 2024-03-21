@extends('backend.layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header row">
                    <h5 class="mb-0 h6">{{translate('Information')}}</h5>
                </div>
                <div class="card-body" id="info-form">
                    <form class="form-horizontal" action="{{ route('organisations.update', $organisation->id) }}"
                          method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Name')}}</label>
                            <div class="col-md-9">
                                <input type="text" placeholder="{{translate('Name')}}" id="name" name="name"
                                       value="{{$organisation->name}}" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Catering')}}</label>
                            <div class="col-md-9">
                                <label class="sk-switch sk-switch-success mb-0">
                                    @if($organisation->catering==1)
                                        <input type="checkbox" name="catering" onchange="cateringValue()" checked
                                               disabled>
                                    @else
                                        <input type="checkbox" name="catering" onchange="cateringValue()">
                                    @endif
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Custom Plans for Catering')}}</label>
                            <div class="col-md-9">
                                <label class="sk-switch sk-switch-success mb-0">

                                    <input type="checkbox" name="custom_packets"
                                           @if($organisation->custom_packets==1) checked @endif
                                    >
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        {{--                        <div class="form-group row" >--}}
                        {{--                            <label class="col-md-3 col-form-label">{{translate('Top Up')}}</label>--}}
                        {{--                            <div class="col-md-9">--}}
                        {{--                                <label class="sk-switch sk-switch-success mb-0">--}}
                        {{--                                    @if($organisation->top_up==1)--}}
                        {{--                                    <input type="checkbox" name="top_up" value="0" onchange="topUpValue()" checked>--}}
                        {{--                                    @else--}}
                        {{--                                        <input type="checkbox" name="top_up" value="0" onchange="topUpValue()" >--}}
                        {{--                                    @endif--}}
                        {{--                                    <span></span>--}}
                        {{--                                </label>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Required Field')}}</label>
                            <div class="col-md-9">
                                <label class="sk-switch sk-switch-success mb-0">
                                    @if($organisation->required_field_name!=null)
                                        <input type="checkbox" name="required_field" value="1" onchange="showLayout()"
                                               checked>
                                    @else
                                        <input type="checkbox" name="required_field" value="1" onchange="showLayout()">
                                    @endif
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        @if($organisation->required_field_name==null)
                            <div class="form-group row" id="required_field_name_div" style="display: none">
                                <label class="col-md-3 col-form-label">{{translate('Required Field Name')}}</label>
                                <div class="col-md-9">
                                    <input type="text" placeholder="{{translate('Required Field Name')}}"
                                           id="required_field_name" name="required_field_name" class="form-control"
                                           disabled required>
                                </div>
                            </div>
                        @else
                            <div class="form-group row" id="required_field_name_div">
                                <label class="col-md-3 col-form-label">{{translate('Required Field Name')}}</label>
                                <div class="col-md-9">
                                    <input type="text" value="{{$organisation->required_field_name}}"
                                           id="required_field_name" name="required_field_name" class="form-control"
                                           required>
                                </div>
                            </div>
                        @endif


                        <div class="form-group row" id="required_field_name_div">
                            <label class="col-md-3 col-form-label">{{translate('Email for Orders')}}</label>
                            <div class="col-md-9">

                                @php
                                    $emails = \App\Models\EmailForOrder::all();
                                @endphp
                                <select class="form-control form-control-sm sk-selectpicker" name="email_for_order"
                                        onchange="filter()">
                                    @foreach($emails as $email)
                                        @if($email->id == 1)
                                            <option value="{{$email->id}}"
                                                    @if($organisation->email_for_order_id == null || $organisation->email_for_order_id == $email->id) selected @endif>{{ $email->email}}</option>

                                        @else
                                            <option value="{{$email->id}}"
                                                    @if($organisation->email_for_order_id == $email->id) selected @endif>{{ $email->email}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="form-group mb-0 text-right">
                            <a href="{{route('organisations.index')}}">
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

        document.addEventListener('DOMContentLoaded', function () {


            document.getElementById('organisation-link').classList.add('active');
        });


        function showLayout() {
            if (!$('input[name="required_field"]').prop('checked')) {
                $('#required_field_name_div').hide();
                $('#required_field_name').prop("disabled", true);
            } else {
                $('#required_field_name_div').show();
                $('#required_field_name').prop("disabled", false);
            }

            console.log($('input[name="required_field"]').val());
        }


    </script>
@endsection

