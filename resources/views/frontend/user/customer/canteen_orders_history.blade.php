@extends('frontend.layouts.user_panel')

@section('meta_title'){{ translate('Meal History') }}@stop

@section('panel_content')
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\DB;

        $card = $canteen_user->card;

        $orders = \App\Models\AppOrder::select('app_orders.*', DB::raw('SUM(app_order_details.total_quantity) as total_quantity'))
        ->join('app_order_details', 'app_order_details.app_order_id', '=', 'app_orders.id')
        ->where('app_orders.user_id', $canteen_user->id)
        ->groupBy('app_orders.id')
        ->orderBy('app_orders.created_at', 'desc');

        $order_ids = $orders->pluck('id');

        $orders = $orders->paginate(10);

        $refunded_order_ids = \App\Models\AppRefundDetail::whereIn('app_order_id', $order_ids)->pluck('app_order_id')->toArray();

    @endphp
    <h1 class="fs-14 md-fs-16 mb-10px mb-md-15px text-primary-50 fw-700 lh-1-2 xl-lh-1">
        <a class="hov-text-primary" href="{{route('dashboard')}}">
            {{ toUpper(translate('Dashboard')) }}
        </a> /
        <span class="d-inline-block"><span class="border-bottom border-inherit">{{ toUpper(translate('Order History')) }} - {{toUpper($card->name)}}</span></span>
    </h1>

    <div class="background-brand-grey px-lg-25px fs-14">
        @if(count($orders) > 0)
            <div class="pb-lg-20px">
                <div class="d-none d-lg-block">
                    <table class="table sk-table mb-0 history-table">
                        <thead>
                        <tr>
                            <th class="pr-30px">{{toUpper(translate('Order Code'))}}</th>
                            @if(count($refunded_order_ids)>0 ) <th class="pr-30px"></th> @endif
                            <th class="pr-30px">{{toUpper(translate('Order Date & Time'))}}</th>
                            <th class="pr-30px">{{toUpper(translate('Cost'))}}</th>
                            <th class="pr-20px">{{toUpper(translate('Item Qty'))}}</th>
                            <th class="text-right">{{toUpper(translate('Actions'))}}</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($orders as $key => $order)
                            <tr>
                                <td class="col-auto pr-20px">
                                    {{$order->code}}

                                </td>
                                @if(count($refunded_order_ids)>0 )
                                    <td class="col-auto">
                                        @if(in_array($order->id, $refunded_order_ids))
                                           {{translate('Refunded')}}
                                        @endif
                                    </td>
                                @endif
                                <td class="col-auto pl-15px pr-30px ">{{Carbon::create($order->created_at)->format('d/m/y') }}</td>
                                <td class="col-auto pr-30px">{{single_price($order->grand_total)}}</td>
                                <td class="col-auto pr-20px ">{{$order->total_quantity}}</td>
                                <td class="col no-gutters text-right">
                                    <a class="btn btn-outline-primary btn-icon btn-circle btn-sm fw-800 show_order_history" data-orderID="{{$order->id}}"
                                       title="{{ translate('View History')}}">
                                        <i class="las la-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-lg-none">
                    @foreach($orders as $key => $order)
                        <div class="table-row-results">
                            <div class="table-row-item">
                                <div class="row gutters-5 align-items-center">
                                    <div class="col fw-700">{{toUpper(translate('Order Code'))}}</div>
                                    <div class="col-auto text-primary-50">{{$order->code}}  @if(in_array($order->id, $refunded_order_ids))  {{translate('Refunded')}} @endif</div>
                                </div>
                            </div>
                            @if(count($refunded_order_ids)>0 )
                            <div class="table-row-item">
                                <div class="row gutters-5 align-items-center">
                                    <div class="col fw-700"></div>
                                    <div class="col-auto text-primary-50"> @if(in_array($order->id, $refunded_order_ids))
                                            {{translate('Refunded')}}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="table-row-item">
                                <div class="row gutters-5 align-items-center">
                                    <div class="col fw-700">{{toUpper(translate('Order Date & Time'))}}</div>
                                    <div class="col-auto text-primary-50">
                                        {{Carbon::create($order->created_at)->format('d/m/y') }}
                                    </div>
                                </div>
                            </div>
                            <div class="table-row-item">
                                <div class="row gutters-5 align-items-center">
                                    <div class="col fw-700">{{toUpper(translate('Cost'))}}</div>
                                    <div class="col-auto text-primary-50">
                                        {{single_price($order->grand_total)}}
                                    </div>
                                </div>
                            </div>
                            <div class="table-row-item">
                                <div class="row gutters-5 align-items-center">
                                    <div class="col fw-700">{{toUpper(translate('Item Qty'))}}</div>
                                    <div class="col-auto text-primary-50">
                                        {{$order->total_quantity}}
                                    </div>
                                </div>
                            </div>
                            <div class="table-row-item">
                                <div class="row gutters-5 align-items-center">
                                    <div class="col fw-700">{{toUpper(translate('Actions'))}}</div>
                                    <div class="col-auto text-primary-50">
                                        <a class="btn btn-outline-primary btn-icon btn-circle btn-sm fw-800 show_order_history" data-orderID="{{$order->id}}"
                                           title="{{ translate('View History')}}">
                                            <i class="las la-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="custom-pagination" class="sk-pagination">
                    {{$orders->links()}}
                </div>
            </div>
        @else
            <div class="text-center fw-700 p-30px">{{translate('No meal history yet')}}</div>
        @endif
    </div>
@endsection

@section('modal')

    <div id="app-order-modal" class="modal fade">
        <div class="modal-dialog modal-md modal-dialog-centered w-lg-600px lg-mw-600px">
            <div class="modal-content">
                <div class="modal-body loader min-h-100px">

                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')
    <script type="text/javascript">
        {{--$(document).ready(function () {--}}
        {{--    console.log({!! json_encode($card_usages) !!});--}}
        {{--});--}}



        $(document).on('click', '.show_order_history', function (){

            var order_id = $(this).attr('data-orderID');

            $('#app-order-modal .modal-body').html('');
            $('#app-order-modal .modal-body').addClass('loader');
            $('#app-order-modal').modal('show');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('user.app_order_details')}}",
                type: 'post',
                data: {
                    order_id: order_id
                },
                success: function (data) {

                    console.log('response: ', data);

                    // var data = JSON.parse(response);

                    if (data.status == 1) {

                        $('#app-order-modal .modal-body').removeClass('loader');
                        $('#app-order-modal .modal-body').html(data.view);


                    } else if (data.status == 0) {

                    }

                }
            });

            console.log('opa tou ', order_id );
        });


    </script>
@endsection
