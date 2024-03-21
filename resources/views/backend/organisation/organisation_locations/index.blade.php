@extends('backend.layouts.app')

@section('content')

    <div class="sk-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3 text-capitalize">
                    <a href="{{route('organisations.index')}}" class="text-black" >{{translate('Organisations')}} </a> >
                    {{$organisation->name}} > {{translate('Locations')}}</h1>
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{ route('organisation_locations.create', ['organisation_id'=>$organisation->id]  )}}" class="btn btn-primary">
                    <span>{{translate('Add New Location')}}</span>
                </a>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-block d-md-flex">
            <h5 class="mb-0 h6">{{ translate('Organisation Locations') }}</h5>
            <form class="" id="sort_categories" action="" method="GET">
                <span class="fs-17 fw-600">{{translate('Search')}}:</span>
                <div class="d-inline-block box-inline pad-rgt pull-left ml-3">
                    <div class="" style="min-width: 200px;">
                        <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name & Enter') }}">
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table sk-table mb-0">
                <thead>
                <tr>
                    <th width="20px" data-breakpoints="lg">#</th>
                    <th>{{translate('Name')}}</th>
                    <th  class="text-right">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($organisation_locations as $key => $location)
                    <tr>
                        <td>{{ ($key+1) + ($organisation_locations->currentPage() - 1)*$organisation_locations->perPage() }}</td>
                        <td> {{$location->name}}</td>
                        <td class="text-right">
                            <a class="btn btn-soft-secondary" href="{{route('organisation_locations.edit', $location->id)}}" title="{{ translate('Edit') }}">
                                {{ translate('Edit') }}
                            </a>
                            <a href="#" class="btn btn-soft-danger confirm-delete" data-href="{{route('organisation_locations.destroy', $location->id)}}" title="{{ translate('Delete') }}">
                                {{ translate('Delete') }}
                            </a>

                        </td>

                    </tr>
                @endforeach
                </tbody>

            </table>
            <div class="sk-pagination">
                {{$organisation_locations->links()}}
            </div>
        </div>
    </div>
@endsection


@section('modal')
    @include('modals.delete_modal')
@endsection


@section('script')
    <script type="text/javascript">

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('organisation-link').classList.add('active');
        });

        function update_featured(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('categories.featured') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    SK.plugins.notify('success', '{{ translate('Featured categories updated successfully') }}');
                }
                else{
                    SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
    </script>
@endsection
