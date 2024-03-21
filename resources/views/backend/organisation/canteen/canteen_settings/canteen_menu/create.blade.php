@extends('backend.layouts.app')

@section('content')
{{--    <div class="sk-titlebar text-left mt-2 mb-3">--}}
{{--        <div class="row align-items-center">--}}
{{--            <div class="col-md-6">--}}
{{--                <h1 class="h3">{{translate('All categories')}}</h1>--}}
{{--            </div>--}}
{{--            <div class="col-md-6 text-md-right">--}}
{{--                <a href="{{ route('canteen_product_categories.create') }}" class="btn btn-primary">--}}
{{--                    <span>{{translate('Add New category')}}</span>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

<form name="create_menu" class="form-horizontal" action="{{route('canteen_menu.store',$canteen_setting->id ) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card">
        <div class="card-header d-block d-md-flex">
            <h5 class="mb-0 h6">{{ translate('Canteen Menu') }}</h5>

            <div class="box-inline pad-rgt pull-left">
                <div class="" >
                  <button class="btn btn-primary" type="submit">{{translate('Save')}}</button>
                </div>
            </div>

        </div>
        <div class="card-body">

            @php

                $products = \App\Models\CanteenProduct::where('status', 1)->paginate(20);

                $business_days = json_decode($canteen_setting->working_week_days);

            @endphp

            <input type="hidden" id="all_products" name="products" >

            <table class="table">
                <thead>
                    <th width="20">#</th>
                    <th width="10%">{{translate('Product')}}</th>
                    <th width="15%" class="text-center">{{translate('Custom Price')}}</th>
                    @foreach($business_days as $day)
                        <th class="text-center">{{translate($day)}}</th>
                    @endforeach
                </thead>
                <tbody>
                @foreach($products as $key => $product)
                    <tr>
                        <td>{{ ($key+1) + ($products->currentPage() - 1)*$products->perPage() }}</td>
                        <td> {{$product->name}}</td>
                        <td>
                            <div class="row">
                                <div class="col-auto">
                                    <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                        <input type="checkbox" name="custom_price_status_{{$product->id}}" class="custom_price_status">
                                        <span></span>
                                    </label>
                                </div>
                                <div class="col">
{{--                                    <div class="input-group">--}}
{{--                                        <div class="input-group-prepend">--}}
{{--                                            <div class="input-group-text bg-soft-secondary font-weight-medium px-2">€</div>--}}
{{--                                        </div>--}}
                                        <input type="number" name="custom_price_{{$product->id}}" min="0.01" step="0.01" max="100" value="{{$product->price}}" class="form-control"   data-product="{{$product->id}}" data-day="{{$day}}" data-break="{{$break->id}}" >
{{--                                    </div>--}}
                                </div>
                            </div>
                        </td>

                        @foreach($business_days as $day)
                            <td class="text-left">
                                @foreach($breaks as $key => $break)
                                    <div>
                                        <input type="checkbox" name="product_{{$product->id}}_day_{{$day}}_break_{{$break->id}}" class="break_checkbox" data-product="{{$product->id}}">
                                        <label> {{ordinal($key+1)}} {{translate('Break')}} </label>
                                    </div>
                                @endforeach
                            </td>
                        @endforeach
                    </tr>
                @endforeach

                </tbody>
            </table>

        </div>
    </div>
</form>
@endsection


@section('modal')
    @include('modals.delete_modal')
@endsection


@section('script')
    <script type="text/javascript">

        let all_products = [];
        const canteen_setting_id = '{{$canteen_setting->id}}'

        $(document).on('click', 'input[type=checkbox].break_checkbox', function(){

            // var all_products = $('#all_products').val();
            var product_id = $(this).attr('data-product');
            var day = $(this).attr('data-day');
            var break_id = $(this).attr('data-break');
            var product_checked = true;
            var change = 'insert'; //'delete'

            var custom_price_status = $('input[type=checkbox][name=custom_price_status_' + product_id + ']').prop('checked');
            var custom_price = 0;

            if(custom_price_status==true){
                custom_price = $('input[type=checkbox][name=custom_price_' + product_id + ']').val();
            }

            if(custom_price < 0){
                custom_price = 0;
            }

            if(custom_price_status==true){
                custom_price_status = 1;
            }else{
                custom_price_status = 0;
            }

            if(!$(this).prop('checked')){ // find all inputs in this tr
                change = 'delete';
            }

            console.log('data before: ',  {
                canteen_setting_id: canteen_setting_id,
                product_id: product_id,
                day: day,
                break_id: break_id,
                custom_price_status: custom_price_status,
                custom_price: custom_price,
                change: change
            });

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('canteen_menu.ajax_change') }}',
                type: 'POST',
                data: {
                    canteen_setting_id: canteen_setting_id,
                    product_id: product_id,
                    day: day,
                    break_id: break_id,
                    custom_price_status: custom_price_status,
                    custom_price: custom_price,
                    change: change
                },
                success: function (data) {
                    console.log('data ajax: ', data);

                    if(data.status == 1){
                        SK.plugins.notify('success', data.msg);
                    }else{
                        $(this).prop('checked', false);
                        SK.plugins.notify('danger', data.msg);
                    }

                },
                error: function () {

                }
            });


            // code for form update
            if(!$(this).prop('checked')){ // find all inputs in this tr
                var tr = $(this).parents('tr');

                var inputs = tr.find(':input[type=checkbox].custom_price_status:checked');

                if(inputs.length > 0){
                    product_checked = true;
                }else{
                    product_checked = false;
                }
            }

            if(product_checked && !all_products.includes(product_id)){
                all_products.push(product_id);
            }else if(!product_checked && all_products.includes(product_id)){
                var indexToRemove = all_products.indexOf(product_id);
                if (indexToRemove !== -1) {
                    all_products.splice(indexToRemove, 1);
                }
            }

            $('#all_products').val(all_products);

        });

    </script>
@endsection
