
@extends('backend.layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6"> <a href="{{route('organisations.index')}}" class="text-black" >{{translate('Organisations')}} </a> >
                        {{$organisation->name}} >
                        <a href="{{route('canteen.index', $organisation->id)}}" class="text-black" >{{translate('Canteen Locations')}} </a> > {{translate('Add New Location')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('canteen_locations.store', ['organisation_id'=>$organisation->id] ) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Name')}}</label>
                            <div class="col-md-9">
                                <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <a href="{{route('canteen.index', $organisation->id)}}">
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

    </script>
@endsection
