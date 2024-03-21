@extends('backend.layouts.app')

@section('content')
<div class="sk-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('Stores Cities')}}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('store_cities.create') }}" class="btn btn-primary">
                <span>{{translate('Add New City')}}</span>
            </a>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <div class="row flex-grow-1">
            <div class="col">
                <h5 class="mb-0 h6">{{translate('Stores Cities')}}</h5>

            </div>
            <div class="col-md-6 col-xl-4 ml-auto mr-0">
                <form class="" id="sort_by_rating" action="{{ route('store_cities.index') }}" method="GET">
                    <div class="" style="min-width: 200px;">
                        <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name & Enter') }}">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table sk-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="lg">#</th>
                    <th>{{translate('City')}}</th>
                    <th data-breakpoints="lg">{{translate('Order Level')}}</th>
                    <th data-breakpoints="lg">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cities as $key => $city)
                    <tr>
                        <td>{{ ($key+1) + ($cities->currentPage() - 1)*$cities->perPage() }}</td>
                        <td>{{ $city->getTranslation('name') }}</td>
                        <td>{{ $city->order_level }}</td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('store_cities.edit', ['id'=>$city->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('store_cities.destroy', $city->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="sk-pagination">
            {{ $cities->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
