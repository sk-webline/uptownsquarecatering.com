@extends('backend.layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Organisation Information')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('organisations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Name')}}</label>
                            <div class="col-md-9">
                                <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Catering')}}</label>
                            <div class="col-md-9">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="catering" value="0" onchange="cateringValue()">
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Canteen')}}</label>
                            <div class="col-md-9">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="canteen">
                                    <span></span>
                                </label>
                            </div>
                        </div>


                        <div class="form-group row" id="custom_packets_div">
                            <label class="col-md-3 col-form-label">{{translate('Custom Plans for Catering')}}</label>
                            <div class="col-md-9">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="custom_packets" value="0" checked >
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row" style="display: none">
                            <label class="col-md-3 col-form-label">{{translate('Top Up')}}</label>
                            <div class="col-md-9">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="hidden" name="top_up" value="0" onchange="topUpValue()">
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Required Field')}}</label>
                            <div class="col-md-9">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="required_field" value="1" onchange="showLayout()" checked>
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row" id="required_field_name_div">
                            <label class="col-md-3 col-form-label">{{translate('Required Field Name')}}</label>
                            <div class="col-md-9">
                                <input type="text" placeholder="{{translate('Required Field Name')}}" id="required_field_name" name="required_field_name" class="form-control" required>
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

            // if(!$('input[name="catering"]').prop('checked')) {
            //     $('input[name="catering"]').val(0);
            //     $('#custom_packets_div').css("display", "none");
            //     $('input[name="custom_packets"]').prop( "checked", true );
            //
            // } else {
            //     $('input[name="catering"]').val(1);
            //     $('#custom_packets_div').css("display", "");
            // }

        });


        function cateringValue(){
            if(!$('input[name="catering"]').prop('checked')) {
                $('input[name="catering"]').val(0);

                $('#custom_packets_div').css("display", "none");


                $('input[name="custom_packets"]').prop( "checked", true );

            } else {
                $('input[name="catering"]').val(1);
                $('#custom_packets_div').css("display", "");
            }

            console.log($('input[name="catering"]').val());
        }

        function topUpValue(){
            if(!$('input[name="topup"]').prop('checked')) {
                $('input[name="topup"]').val(0);
            } else {
                $('input[name="topup"]').val(1);
            }

            console.log($('input[name="topup"]').val());
        }

        function showLayout(){
            if(!$('input[name="required_field"]').prop('checked')) {
                $('#required_field_name_div').hide();
                $('#required_field_name').prop( "disabled", true );
            } else {
                $('#required_field_name_div').show();
                $('#required_field_name').prop( "disabled", false );
            }

            console.log($('input[name="required_field"]').val());
        }

    </script>
@endsection

