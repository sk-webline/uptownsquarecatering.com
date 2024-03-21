@php
    use \App\Models\CanteenMenu;
    use Carbon\Carbon;

    $business_days = json_decode($canteen_setting->working_week_days);

        $start_of_week = Carbon::today()->startOfWeek();
        $end_of_week = Carbon::today()->endOfWeek();
        $days = [];

        // Loop through each day of the week
        for ($date = $start_of_week; $date->lte($end_of_week); $date->addDay()) {
           $days[] = $date->format('D');
        }

    $products_with_custom_prices = CanteenMenu::where('canteen_setting_id', $canteen_setting->id)->where('custom_price_status', 1)->pluck('canteen_product_id');

    $products_with_custom_prices = array_unique($products_with_custom_prices->toArray());

    $checked_inputs = [];
    $temp = [];
    $selected_products = [];

    foreach ($menus as $key => $menu){
        $input_key = 'product_' . $menu->canteen_product_id . '_day_' . ucfirst(substr($menu->day, 0, 3)) . '_break_' .$menu->organisation_break_num;  // product_4_day_Tue_break_48
        $checked_inputs[] = $input_key;

        if(!in_array($menu->canteen_product_id, $selected_products)){
             $selected_products[] = $menu->canteen_product_id;
        }

    }

    $products_of_page = [];

@endphp


<input type="hidden" id="all_products" name="products">

<table class="table">
    <thead>
    <th width="20">#</th>
    <th >{{translate('Product')}}</th>
    <th class="text-center">{{translate('Custom Price')}}</th>
    @foreach($days as $day)
        <th width="120" class="text-center @if(!in_array($day, $business_days)) bg-black-05 @endif">{{translate(day_name($day))}}  @if(!in_array($day, $business_days))<span class="text-danger">*</span> @endif</th>
    @endforeach
    <th width="90" class="text-center">{{translate('Select All')}}</th>
    </thead>
    <tbody>
    @foreach($products as $key => $product)

        @php
            $products_of_page[] = $product->id;
        @endphp

        <tr>
            <td>{{ ($key+1) + ($products->currentPage() - 1)*$products->perPage() }}</td>
            <td>
                <div class="row gutters-5">
                    <div class="col-auto">
                        <img src="{{ uploaded_asset($product->thumbnail_img) }}?width=400&qty=50"
                             alt="Image" class="size-50px img-fit">
                    </div>
                    <div class="col">
                        <span class="fs-13 fw-500">{{$product->name}} </span>
                        <span class="d-block"> {{single_price($product->price)}}</span>
                    </div>
                </div>

            </td>
            <td>
                <div class="text-center">
                    <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                        <input type="checkbox" name="custom_price_status_{{$product->id}}"
                               class="custom_price_status" data-product="{{$product->id}}"
                               @if(in_array($product->id, $products_with_custom_prices)) checked @endif>
                        <span></span>
                    </label>
                </div>
                <div class="d-flex justify-content-center">
                    <div
                        class="input-group w-110px @if(!in_array($product->id, $products_with_custom_prices)) d-none @endif">
                        <div class="input-group-prepend">
                            <div class="input-group-text bg-soft-secondary font-weight-medium px-2">€
                            </div>
                        </div>
                        <input type="number" name="custom_price_{{$product->id}}" min="0.01" step="0.01"
                               max="100" data-generalPrice="{{$product->price}}"  data-product="{{$product->id}}"
                               @if(in_array($product->id, $products_with_custom_prices))
                                   value="{{CanteenMenu::where('canteen_setting_id', $canteen_setting->id)->where('canteen_product_id', $product->id)->first()->custom_price}}"
                               @else
                                   value="{{$product->price}}"
                               @endif class="form-control custom-price-input">
                    </div>
                </div>
            </td>

            @php
                $select_all_checked = true;
            @endphp

            @foreach($days as $day)
                <td class="text-left @if(!in_array($day, $business_days)) bg-black-05 @endif">
                    @foreach($breaks as $key => $break)
                        <div>
                            <input type="checkbox"
                                   name="product_{{$product->id}}_day_{{$day}}_break_{{$break->break_num}}"
                                   class="break_checkbox"
                                   data-product="{{$product->id}}" data-day="{{$day}}"
                                   data-break="{{$break->break_num}}"
                                   @if(in_array('product_' .$product->id . '_day_' .$day. '_break_' .$break->break_num, $checked_inputs)) checked @endif
                            >
                            <label> {{ordinal($key+1)}} {{translate('Break')}} </label>
                        </div>

                        @php

                            if(!in_array('product_' .$product->id . '_day_' .$day. '_break_' .$break->break_num, $checked_inputs)){
                                $select_all_checked = false;
                            }

                        @endphp

                    @endforeach
                </td>
            @endforeach

            <td class="text-center">
                <div>
                    <input type="checkbox"
                           name="select_all_{{$product->id}}"
                           class="select_all"
                           data-product="{{$product->id}}" @if($select_all_checked) checked @endif
                    >
                    <label></label>
                </div>
            </td>
        </tr>
    @endforeach


    </tbody>
</table>
<div class="sk-pagination">
    {{$products->links()}}
</div>

@if((isset($search) && $search!=null) || (isset($category_filter) && $category_filter!=null))

    <input type="hidden" name="products_of_page" value="{{json_encode($products_of_page)}}">

@endif

<script type="text/javascript">

    var all_products = [];
    const canteen_setting_id = '{{$canteen_setting->id}}';

    $(document).ready(function () {
        var selected_products = {!! json_encode($selected_products) !!};

        for (var i = 0; i < selected_products.length; i++) {
            all_products.push(selected_products[i])
        }

        $('#all_products').val(all_products);

    });

    $(document).on('click', 'input[type=checkbox].break_checkbox', function () {

        var product_id = $(this).attr('data-product');
        var day = $(this).attr('data-day');
        var break_num = $(this).attr('data-break');
        var product_checked = true;
        var change = 'insert'; //'delete'

        var custom_price_status = $('input[type=checkbox][name=custom_price_status_' + product_id + ']').prop('checked');
        var custom_price = 0;

        if (custom_price_status == true) {
            custom_price = $('input[name=custom_price_' + product_id + ']').val();
        }

        if (custom_price < 0) {
            custom_price = 0;
        }

        if (custom_price_status == true) {
            custom_price_status = 1;
        } else {
            custom_price_status = 0;
        }

        if (!$(this).prop('checked')) { // find all inputs in this tr
            change = 'delete';
        }

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
                break_num: break_num,
                custom_price_status: custom_price_status,
                custom_price: custom_price,
                change: change
            },
            success: function (data) {
                console.log('data ajax: ', data);

                if (data.status == 1) {
                    SK.plugins.notify('success', data.msg);
                } else {
                    $(this).prop('checked', false);
                    SK.plugins.notify('danger', data.msg);
                }

            },
            error: function () {

            }
        });


        // code for form update
        if (!$(this).prop('checked')) { // find all inputs in this tr
            var tr = $(this).parents('tr');

            var inputs = tr.find(':input[type=checkbox].custom_price_status:checked');

            if (inputs.length > 0) {
                product_checked = true;
            } else {
                product_checked = false;
            }
        }

        if (product_checked && !all_products.includes(product_id)) {
            all_products.push(product_id);
        } else if (!product_checked && all_products.includes(product_id)) {
            var indexToRemove = all_products.indexOf(product_id);
            if (indexToRemove !== -1) {
                all_products.splice(indexToRemove, 1);
            }
        }

        $('#all_products').val(all_products);

    });

    $(document).on('click', 'input[type=checkbox].custom_price_status', function () {

        if ($(this).prop('checked')) {

            $(this).parents('td').find('div.input-group').removeClass('d-none');

        } else {

            $(this).parents('td').find('div.input-group').addClass('d-none');

            var product_id = $(this).attr('data-product');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:'{{ route('canteen_menu.ajax_delete_custom_price') }}',

                type: 'POST',
                data: {
                    canteen_setting_id: canteen_setting_id,
                    product_id: product_id,
                },
                success: function (data) {
                    console.log('data delete custom price ajax: ', data);

                    if (data.status == 1) {

                        SK.plugins.notify('success', data.msg);

                    } else {

                        SK.plugins.notify('danger', data.msg);

                    }

                },
                error: function () {

                }
            });

            var input = $(this).parents('td').find('input.custom-price-input');
            input.val(input.attr('data-generalPrice'))

        }

    });

    $(document).on('click', 'input[type=checkbox].select_all', function () {

        var change = 'select_all';

        var product_id = $(this).attr('data-product');

        var custom_price_status = $('input[type=checkbox][name=custom_price_status_' + product_id + ']').prop('checked');
        var custom_price = 0;

        if (custom_price_status == true) {
            custom_price = $('input[name=custom_price_' + product_id + ']').val();
        }

        if (custom_price < 0) {
            custom_price = 0;
        }

        if (custom_price_status == true) {
            custom_price_status = 1;
        } else {
            custom_price_status = 0;
        }

        if (!$(this).prop('checked')) {
            change = 'delete_all';
        }

        var inputs = $(this).parents('tr').find('.break_checkbox');

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route('canteen_menu.ajax_change_all') }}',
            type: 'POST',
            data: {
                canteen_setting_id: canteen_setting_id,
                product_id: product_id,
                custom_price_status: custom_price_status,
                custom_price: custom_price,
                change: change
            },
            success: function (data) {
                // console.log('data ajax: ', data);

                if (data.status == 1) {

                    if(data.change=='select_all'){

                        inputs.each(function() {
                            $(this).prop('checked', true).change();
                        });

                    }else if(data.change=='delete_all'){

                        inputs.each(function() {
                            $(this).prop('checked', false).change();
                            $(this).removeAttr('checked');
                        });

                    }

                    SK.plugins.notify('success', data.msg);

                } else {

                    SK.plugins.notify('danger', data.msg);

                }

            },
            error: function () {

            }
        });

    });



    $(document).on('change', 'input.custom-price-input', function () {

        var product_id = $(this).attr('data-product');

        var custom_price = $(this).val();
        $(this).val(parseFloat(custom_price).toFixed(2))

        if($('input[type=checkbox][name=custom_price_status_' + product_id + ']').prop('checked') == true){

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:'{{ route('canteen_menu.ajax_change_custom_price') }}',

                type: 'POST',
                data: {
                    canteen_setting_id: canteen_setting_id,
                    product_id: product_id,
                    custom_price_status: '1',
                    custom_price: custom_price
                },
                success: function (data) {

                    if (data.status == 1) {

                        SK.plugins.notify('success', data.msg);

                    } else {

                        SK.plugins.notify('danger', data.msg);

                    }

                },
                error: function () {

                }
            });
        }
    });
</script>
