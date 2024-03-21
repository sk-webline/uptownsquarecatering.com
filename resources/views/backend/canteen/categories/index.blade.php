@extends('backend.layouts.app')

@section('content')

    @if ($errors->has('delete'))
        <div class="alert alert-danger">
            {{ $errors->first('delete') }}:
            <span class="d-block">
                @foreach($errors->all() as $key => $error)
                    @if($key>0)
                       <span class="d-block">{{$error}}</span>
                    @endif
                @endforeach
            </span>
        </div>
    @endif

    <div class="sk-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{translate('All categories')}}</h1>
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{ route('canteen_product_categories.create') }}" class="btn btn-primary">
                    <span>{{translate('Add New category')}}</span>
                </a>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-block d-md-flex">
            <h5 class="mb-0 h6">{{ translate('Categories') }}</h5>
            <form class="" id="sort_categories" action="" method="GET">
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
                    <th width="10%" class="text-right">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($categories as $key => $category)
                    <tr>
                        <td>{{ ($key+1) + ($categories->currentPage() - 1)*$categories->perPage() }}</td>
                        <td>{{ $category->name }}</td>

{{--                        <td>--}}
{{--                            @if($category->icon != null)--}}
{{--                                <span class="avatar avatar-square avatar-xs">--}}
{{--                                    <img src="{{ uploaded_asset($category->icon) }}" alt="{{translate('icon')}}">--}}
{{--                                </span>--}}
{{--                            @else--}}
{{--                                —--}}
{{--                            @endif--}}
{{--                        </td>--}}

                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('canteen_product_categories.edit', ['id'=>$category->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('canteen_product_categories.destroy', $category->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="sk-pagination">
                {{ $categories->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
@endsection


@section('modal')
    @include('modals.delete_modal')
@endsection


@section('script')
    <script type="text/javascript">

    </script>
@endsection
