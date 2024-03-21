@extends('backend.layouts.app')

@section('content')

    @php
        use Carbon\Carbon;

        $start_of_week = Carbon::today()->startOfWeek();
        $end_of_week = Carbon::today()->endOfWeek();
        $days = [];

        // Loop through each day of the week
        for ($date = $start_of_week; $date->lte($end_of_week); $date->addDay()) {
           $days[] = $date->format('D');
        }

    @endphp
    <div class="sk-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{ route('canteen_menu.edit', $canteen_setting->id) }}" class="btn btn-primary">
                    @if(count($menu) > 0)
                        <span>{{translate('Edit Menu')}}</span>
                    @else
                        <span>{{translate('Create Menu')}}</span>
                    @endif
                </a>

            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-block d-md-flex">
            <h5 class="mb-0 h6">
                <a class="text-black hov-opacity-70" href="{{route('canteen.index',$canteen_setting->organisation_id )}}"> {{ $canteen_setting->organisation->name }} </a> >
                 {{date("d/m/Y", strtotime($canteen_setting->date_from))}} - {{date("d/m/Y", strtotime($canteen_setting->date_to))}} > {{ translate('Menu') }}</h5>

        </div>
        <div class="card-body">

            <div class="row py-10px">
                @foreach($days as $key =>$day)
                    <a class="col text-center c-pointer lh-2 hov-text-primary day-header @if($key==0) text-primary @endif"
                       data-contentClass="{{$day}}"><h5 class="mb-0 h6 md-fs-1em ">{{day_name($day)}}</h5></a>
                @endforeach
            </div>


            @foreach($days as $key =>$day)
                <div class="{{$day}} @if($key>0) d-none @endif pt-30px border-top-none">

                    @php

                        $inputs = [];
                        $day_menu = $canteen_setting->canteen_menus->where('day', strtolower(day_name($day)));
                        $product_custom_prices = [];

                        foreach ($day_menu as $menu){
                            $inputs[] = 'product_' .$menu->canteen_product_id . '_day_' . ucfirst(substr($menu->day, 0, 3)). '_break_' .$menu->organisation_break_num;

                            if($menu->custom_price_status == '1'){
                                 $product_custom_prices[$menu->canteen_product_id] = $menu->custom_price;
                            }else{
                                 $product_custom_prices[$menu->canteen_product_id] = 0;
                            }
                        }

                        $product_ids = $day_menu->pluck('canteen_product_id');

                        $products = \App\Models\CanteenProduct::whereIn('id',$product_ids )->get();

                    @endphp

                    @if(count($products) > 0)

                        <table class="table border-top-none">
                            <thead>
                            <tr>
                                <th width="25%">{{translate('Product')}}</th>
                                <th class="text-center">{{translate('Custom Price')}}</th>
                                @foreach($breaks as $key_br => $break)
                                    <th class="text-center">{{ordinal($key_br+1)}} {{translate('Break')}}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        <div class="row gutters-5">
                                            <div class="col-auto">
                                                <img
                                                    src="{{ uploaded_asset($product->thumbnail_img) }}?width=400&qty=50"
                                                    alt="Image" class="size-50px img-fit">
                                            </div>
                                            <div class="col">
                                                <span class="fs-13 fw-500">{{$product->name}} </span>
                                                <span class="d-block">

{{--                                                    @if($product_custom_prices[$product->id] > 0)--}}
{{--                                                        {{single_price($product_custom_prices[$product->id])}}--}}
{{--                                                    @else--}}
                                                        {{single_price($product->price)}}
{{--                                                    @endif--}}

                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($product_custom_prices[$product->id] > 0)
                                            {{single_price($product_custom_prices[$product->id])}}
                                        @else
                                           -
                                        @endif
                                    </td>
                                    @foreach($breaks as $break)
                                        <td class="text-center">
                                            @if(in_array('product_' .$product->id . '_day_' .$day. '_break_' .$break->break_num, $inputs))
                                                <svg class=" h-20px" fill="red"
                                                     xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 30 30">
                                                    <use
                                                        xlink:href="{{static_asset('assets/img/icons/tick.svg')}}#tick"></use>
                                                </svg>
                                            @endif
                                        </td>
                                    @endforeach

                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                    @else

                        <div class="text-center">
                            <i class="las la-frown la-3x opacity-60 mb-3"></i>
                            <h3 class="h6 fw-700">{{translate('Nothing Found')}}</h3>
                        </div>

                    @endif


                </div>
            @endforeach


        </div>
    </div>
@endsection


@section('script')
    <script type="text/javascript">

        $(document).on('click', '.day-header', function () {
            // console.log($(this));
            $(this).parents('.row').find('.day-header').each(function () {
                $(this).removeClass('text-primary');

                var day_div = $(this).attr('data-contentClass');
                $('div.' + day_div).addClass('d-none');
                console.log($(this));
            });

            $(this).addClass('text-primary');
            var day_div = $(this).attr('data-contentClass');
            $('div.' + day_div).removeClass('d-none');

        });

    </script>
@endsection
