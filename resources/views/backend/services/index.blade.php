@extends('backend.layouts.app')

@section('content')
<div class="sk-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('All Services')}}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('services.create') }}" class="btn btn-primary">
                <span>{{translate('Add New Service')}}</span>
            </a>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header d-block d-md-flex">
        <h5 class="mb-0 h6">{{ translate('Services') }}</h5>
        <form class="" id="sort_services" action="" method="GET">
            <div class="box-inline pad-rgt pull-left">
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
                    <th data-breakpoints="lg">#</th>
                    <th>{{translate('Name')}}</th>
                    <th data-breakpoints="lg">{{ translate('Order Level') }}</th>
                    <th data-breakpoints="lg">{{translate('Banner')}}</th>
                    <th width="10%" class="text-right">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $key => $service)
                    <tr>
                        <td>{{ ($key+1) + ($services->currentPage() - 1)*$services->perPage() }}</td>
                        <td>{{ $service->getTranslation('name') }}</td>
			            <td>{{ $service->order_level }}</td>
                        <td>
                            @if($service->banner != null)
                                <img src="{{ uploaded_asset($service->banner) }}" alt="{{translate('Banner')}}" class="h-50px">
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('services.edit', ['id'=>$service->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('services.destroy', $service->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="sk-pagination">
            {{ $services->appends(request()->input())->links() }}
        </div>
    </div>
</div>
@endsection


@section('modal')
    @include('modals.delete_modal')
@endsection

