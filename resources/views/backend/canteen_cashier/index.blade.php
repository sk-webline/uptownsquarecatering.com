@extends('backend.layouts.app')

@section('content')

    <div class="sk-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3 text-capitalize">{{translate('All Canteen Cashiers')}}</h1>
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{ route('canteen_cashiers.create')}}" class="btn btn-primary">
                    <span>{{translate('Add New Canteen Cashier')}}</span>
                </a>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header d-block d-lg-flex">
            <h5 class="mb-0 h6">{{translate('Cashiers')}}</h5>
            <div class="">
                <form class="" id="sort_customers" action="" method="GET">
                    <div class="box-inline pad-rgt pull-left">
                        <div class="" style="min-width: 300px;">
                            <input type="text" class="form-control" id="search" name="search"
                                   @isset($sort_search) value="{{ $sort_search }}"
                                   @endisset placeholder="{{ translate('Type username or name & Enter') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            <table class="table sk-table mb-0">
                <thead>
                <tr>
                    <th data-breakpoints="lg">#</th>
                    <th>{{translate('Name')}}</th>
                    <th data-breakpoints="lg">{{translate('Username')}}</th>
                    <th data-breakpoints="lg">{{translate('Phone')}}</th>
                    <th data-breakpoints="lg" class="text-center">{{translate('Active')}}</th>
                    <th class="text-right">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($canteen_cashiers as $key => $cashier)
                    <tr>
                        <td>{{ ($key+1) + ($canteen_cashiers->currentPage() - 1)*$canteen_cashiers->perPage() }}</td>
                        <td>{{$cashier->name}}</td>
                        <td>{{$cashier->username}}</td>
                        <td>{{$cashier->phone}}</td>
                        <td class="text-center">{{$cashier->active}}</td>

                        <td class="text-right">

                            <a href="{{route('canteen_cashiers.edit', $cashier->id)}}"
                               class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                               title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>

                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                               data-href="{{route('canteen_cashiers.destroy', $cashier->id)}}"
                               title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>

                @endforeach
                </tbody>
            </table>

        </div>
    </div>


    <div class="modal fade" id="confirm-ban">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{translate('Do you really want to ban this Customer?')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <a type="button" id="confirmation" class="btn btn-primary">{{translate('Proceed!')}}</a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        function sort_customers(el) {
            $('#sort_customers').submit();
        }


    </script>
@endsection
