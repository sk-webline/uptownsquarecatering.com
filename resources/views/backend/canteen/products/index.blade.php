@extends('backend.layouts.app')

@section('content')

    @php




    @endphp


    @if ($errors->has('delete'))
        <div class="alert alert-danger">
            {{ $errors->first('delete') }}:
            <span class="d-block">
                @foreach($errors->all() as $key => $error)
                    @if($key>0)
                        {{$error}}
                    @endif
                @endforeach
            </span>
        </div>
    @endif

    <div class="sk-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{translate('All Canteen Products')}}</h1>
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{ route('canteen_products.create') }}" class="btn btn-primary">
                    <span>{{translate('Add New Products')}}</span>
                </a>
            </div>
        </div>
    </div>
    <div class="card">
        <form class="" id="sort_products" action="" method="GET">
            <div class="card-header row gutters-5">

                <div class="col-auto text-center text-md-left">
                    <h5 class="mb-md-0 h6">{{ translate('All Products') }}</h5>
                </div>

                <div class="col">
                    <div class="row gutters-5 align-items-end">
                        <div class="col-auto ml-auto">
                            <div class="" style="min-width: 200px;">
                                <select class="form-control form-control-sm sk-selectpicker mb-2 mb-md-0"
                                        name="category_filter">
                                    <option value="">{{ translate('All Categories') }}</option>
                                    @foreach (\App\Models\CanteenProductCategory::all() as $key => $category)
                                        <option value="{{ $category->id }}"
                                                @if ($category->id == $category_filter) selected @endif> {{ $category->name }}</option>

                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="" style="min-width: 200px;">
                                <input type="text" class="form-control" id="search" name="search"
                                       @isset($sort_search) value="{{ $sort_search }}"
                                       @endisset placeholder="{{ translate('Type name & Enter') }}">
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-soft-primary btn-sm">{{translate('Search')}}</button>
                        </div>
                        <div class="col-auto">
                            <input type="hidden" name="reset" value="">
                            <button type="button" class="btn btn-soft-secondary reset-button btn-sm">{{translate('Reset')}}</button>
                        </div>
                    </div>
                </div>



            </div>
        </form>


        <div class="card-body">
            <table class="table sk-table mb-0">
                <thead>
                <tr>
                    <th>#</th>
{{--                    <th width="90">{{translate('IMG')}}</th>--}}
                    <th>{{translate('Name')}}</th>
                    <th>{{translate('Category')}}</th>
                    <th>{{translate('Price')}}</th>
                    <th width="10%" class="text-right">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($products as $key => $product)
                    <tr>
                        <td>{{ ($key+1) + ($products->currentPage() - 1)*$products->perPage() }}</td>
                        <td>
                            <div class="row gutters-5">
                                <div class="col-auto">
                                    <img src="{{ uploaded_asset($product->thumbnail_img) }}?width=400&qty=50"
                                         alt="Image" class="size-50px img-fit">
                                </div>
                                <div class="col">
                                    <span >{{ $product->getTranslation('name') }}</span>
                                </div>
                            </div>
                        </td>
{{--                        <td>{{ $product->getTranslation('name') }}</td>--}}
                        <td>{{ $product->category }}</td>
                        <td>{{ single_price($product->price)}}</td>

                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('canteen_products.edit', ['id'=>$product->id] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('canteen_products.destroy', $product->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="sk-pagination">
                {{ $products->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
@endsection


@section('modal')
    @include('modals.delete_modal')
@endsection


@section('script')
    <script type="text/javascript">

        $(document).on('click', 'button.reset-button', function(){
            $('input[name=reset]').val('true');
            $('#sort_products').submit();
        });

        $(window).bind("pageshow", function () {

        });


    </script>
@endsection
