@extends('backend.layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6"><a href="{{route('organisations.index')}}" class="text-black" >{{translate('Organisations')}} </a> >
                        {{$organisation->name}} >
                        <a href="{{route('canteen.index', $organisation->id)}}" class="text-black" >{{translate('Canteen Locations')}} </a> > {{$location->name}}
                        > {{translate('Edit Location')}}</h5>
                </div>

                <div class="card-body" id="settings-form">
                    <form class="form-horizontal"
                          action="{{ route('canteen_locations.update', $location->id) }}" method="post"
                          enctype="multipart/form-data">

                        @csrf

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Name')}}</label>
                            <div class="col-md-9">
                                <input type="text" id="name" name="name"
                                       value="{{$location->name}}" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                       onchange="setStartDate()" required>
                                @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <a href="{{route('canteen.index', $organisation->id)}}">
                                <button type="button" class="btn btn-soft-danger">{{translate('Cancel')}}</button>
                            </a>
                            <button type="submit"
                                    class="btn btn-primary save_holidays">{{translate('Save')}}</button>
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
    </script>
@endsection

