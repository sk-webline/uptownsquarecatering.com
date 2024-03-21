@extends('backend.layouts.app')

@section('content')

<div class="sk-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate('All products')}}</h1>
        </div>
        @if($type != 'Seller')
        <div class="col text-right">
            <a href="{{ route('products.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add New Product')}}</span>
            </a>
        </div>
        @endif
    </div>
</div>
<br>

<div class="card">
    <form class="" id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('All Product') }}</h5>
            </div>
            @if($type == 'Seller')
                @if (hasAccessOnContent())
                    <div class="col-md-2 ml-auto">
                        <select class="form-control form-control-sm sk-selectpicker mb-2 mb-md-0" id="user_id" name="user_id" onchange="sort_products()">
                            <option value="">{{ translate('All Sellers') }}</option>
                            @foreach (App\Seller::all() as $key => $seller)
                                @if ($seller->user != null && $seller->user->shop != null)
                                    <option value="{{ $seller->user->id }}" @if ($seller->user->id == $seller_id) selected @endif>{{ $seller->user->shop->name }} ({{ $seller->user->name }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                @endif
            @endif
            @if($type == 'All')
                @if (hasAccessOnContent())
                    <div class="col-md-2 ml-auto">
                        <select class="form-control form-control-sm sk-selectpicker mb-2 mb-md-0" id="user_id" name="user_id" onchange="sort_products()">
                            <option value="">{{ translate('All Sellers') }}</option>
                                @foreach (App\User::where('user_type', '=', 'admin')->orWhere('user_type', '=', 'seller')->get() as $key => $seller)
                                    <option value="{{ $seller->id }}" @if ($seller->id == $seller_id) selected @endif>{{ $seller->name }}</option>
                                @endforeach
                        </select>
                    </div>
                @endif
            @endif
            <div class="col-md-2 ml-auto">
                <select class="form-control form-control-sm sk-selectpicker mb-2 mb-md-0" name="products_to_show" id="products_to_show" onchange="sort_products()">
                    <option value="all" @isset($products_to_show) @if($products_to_show == 'all') selected @endif @endisset>{{translate('For Sale & Not For Sale Products')}}</option>
                    <option value="for_sale" @isset($products_to_show) @if($products_to_show == 'for_sale') selected @endif @endisset>{{translate('Only For Sale Products')}}</option>
                    <option value="not_for_sale" @isset($products_to_show) @if($products_to_show == 'not_for_sale') selected @endif @endisset>{{translate('Only Not For Sale Products')}}</option>
                </select>
            </div>
            <div class="col-md-2 ml-auto">
                <select class="form-control form-control-sm sk-selectpicker mb-2 mb-md-0" name="type" id="type" onchange="sort_products()">
                    <option value="">{{ translate('Sort By') }}</option>
                    @if (hasAccessOnContent())
                    <option value="rating,desc" @isset($col_name , $query) @if($col_name == 'rating' && $query == 'desc') selected @endif @endisset>{{translate('Rating (High > Low)')}}</option>
                    <option value="rating,asc" @isset($col_name , $query) @if($col_name == 'rating' && $query == 'asc') selected @endif @endisset>{{translate('Rating (Low > High)')}}</option>
                    @endif
                    <option value="num_of_sale,desc"@isset($col_name , $query) @if($col_name == 'num_of_sale' && $query == 'desc') selected @endif @endisset>{{translate('Num of Sale (High > Low)')}}</option>
                    <option value="num_of_sale,asc"@isset($col_name , $query) @if($col_name == 'num_of_sale' && $query == 'asc') selected @endif @endisset>{{translate('Num of Sale (Low > High)')}}</option>
                    <option value="unit_price,desc"@isset($col_name , $query) @if($col_name == 'unit_price' && $query == 'desc') selected @endif @endisset>{{translate('Base Price (High > Low)')}}</option>
                    <option value="unit_price,asc"@isset($col_name , $query) @if($col_name == 'unit_price' && $query == 'asc') selected @endif @endisset>{{translate('Base Price (Low > High)')}}</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type & Enter') }}">
                </div>
            </div>
        </div>
    </form>
        <div class="card-body">
            <table class="table sk-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="lg">#</th>
                        <th width="140px">{{translate('Thumbnail Image')}}</th>
                        <th width="30%">{{translate('Name')}}</th>
{{--                        <th width="10%">{{translate('Category')}}</th>--}}
                        <th data-breakpoints="lg">Family Code</th>
                        <th data-breakpoints="lg">Import from BTMS</th>
                        @if(hasAccessOnContent())
                            @if($type == 'Seller' || $type == 'All')
                                <th data-breakpoints="lg">{{translate('Added By')}}</th>
                            @endif
                        @endif
                        <th data-breakpoints="lg">{{translate('Info')}}</th>
                        <th width="140px" data-breakpoints="lg">{{translate('Total Stock')}}</th>
                        @if(hasAccessOnContent())
                            <th data-breakpoints="lg">{{translate('Todays Deal')}}</th>
                        @endif
                        <th data-breakpoints="lg">{{translate('Cyprus Shipping Only')}}</th>
                        <th data-breakpoints="lg">{{translate('Published')}}</th>
                        <th data-breakpoints="lg">{{translate('Featured')}}</th>
                        <th data-breakpoints="lg" class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $key => $product)
                    <tr>
                        <td>{{ ($key+1) + ($products->currentPage() - 1)*$products->perPage() }}</td>
                        <td>
                            @if($product->thumbnail_img)
                                <div class="col-auto">
                                    <img src="{{ uploaded_asset($product->thumbnail_img)}}" alt="Image" class="size-50px img-fit">
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="col">
                                <span class="text-muted text-truncate-2  fw-600">{{ $product->getTranslation('name') }}</span>
                                <span class="text-muted text-truncate-2">{{translate('Category')}}: {{ $product->category != null ? $product->category->getTranslation('name') : '-' }}</span>
                            </div>
                        </td>
{{--                        <td>--}}
{{--                            <div class="col">--}}
{{--                                <span class="text-muted text-truncate-2">{{ $product->category != null ? $product->category->getTranslation('name') : '' }}</span>--}}
{{--                            </div>--}}
{{--                        </td>--}}
                        <td>{{ $product->part_number }}</td>
                        <td><span class="badge badge-inline badge-{{ $product->import_from_btms ? 'primary' : 'danger' }}">{{ $product->import_from_btms ? 'YES' : 'NO' }}</span></td>
                        @if(hasAccessOnContent())
                            @if($type == 'Seller' || $type == 'All')
                                <td>{{ $product->user->name }}</td>
                            @endif
                        @endif
                        <td>
                            <strong>Num of Sale:</strong> {{ $product->num_of_sale }} {{translate('times')}} </br>
                            <strong>Base Price:</strong> {{ single_price($product->unit_price) }} </br>
                            @if(hasAccessOnContent())
                                <strong>Rating:</strong> {{ $product->rating }} </br>
                            @endif
                        </td>
                        <td>
                            @php
                                $qty = 0;
                                if($product->variant_product) {
                                    foreach ($product->stocks as $key => $stock) {
                                        echo getStrFromProductVariant($product, $stock->variant).' - '.$stock->qty.' '.($stock->qty <= $product->low_stock_quantity ? '<span class="badge badge-inline badge-danger">Low</span>' : "").'<br>';
                                        // $qty += $stock->qty;
                                        // echo $stock->variant.' - '.$stock->qty.'<br>';
                                    }
                                }
                                else {
                                    $qty = $product->current_stock;
                                    echo $qty;
                                    if ($qty <= $product->low_stock_quantity) {
                                        echo '<span class="badge badge-inline badge-danger">Low</span>';
                                    }
                                }
                            @endphp

                        </td>
                        @if(hasAccessOnContent())
                        <td>
                            <label class="sk-switch sk-switch-success mb-0">
                                <input onchange="update_todays_deal(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->todays_deal == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        @endif
                        <td>
                            <label class="sk-switch sk-switch-success mb-0">
                                <input onchange="update_cyprus_shipping(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->cyprus_shipping_only == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>
                            <label class="sk-switch sk-switch-success mb-0">
                                <input onchange="update_published(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->published == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>
                            <label class="sk-switch sk-switch-success mb-0">
                                <input onchange="update_featured(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->featured == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td class="text-right">
                            <a class="btn btn-soft-success btn-icon btn-circle btn-sm"  href="{{ route('product', $product->slug) }}" target="_blank" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                            @if ($type == 'Seller')
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('products.seller.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            @else
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('products.admin.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            @endif
                            @if(hasAccessOnContent())
                            <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="{{route('products.duplicate', ['id'=>$product->id, 'type'=>$type]  )}}" title="{{ translate('Duplicate') }}">
                                <i class="las la-copy"></i>
                            </a>
                            @endif
                            @if($product->import_from_btms == 0)
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('products.destroy', $product->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                            @endif
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

        $(document).ready(function(){
            //$('#container').removeClass('mainnav-lg').addClass('mainnav-sm');
        });

        function update_todays_deal(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.todays_deal') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    SK.plugins.notify('success', '{{ translate('Todays Deal updated successfully') }}');
                }
                else{
                    SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_cyprus_shipping(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.update_cyprus_shipping') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    SK.plugins.notify('success', '{{ translate('Cyprus shipping products updated successfully') }}');
                }
                else{
                    SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_published(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.published') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    SK.plugins.notify('success', '{{ translate('Published products updated successfully') }}');
                }
                else{
                    SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_featured(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.featured') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    SK.plugins.notify('success', '{{ translate('Featured products updated successfully') }}');
                }
                else{
                    SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function sort_products(el){
            $('#sort_products').submit();
        }

    </script>
@endsection
