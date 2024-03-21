@extends('backend.layouts.app')

@section('content')
    @php
        $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();


//    dd($refunds);

    @endphp


    <div class="card">

        <div class="card-header d-block">

            <h5 class="p-15px mb-md-2 h5">{{ translate('Canteen Refunds') }}</h5>
            <div class="col-auto">
                <span class="h6">{{translate('Total Refunds')}}: </span>
                <span class="h6 fw-400 pl-5px pr-15px">{{$total_refunds}}</span>
                {{--                <span class="h6">{{translate('Total Plans')}}: </span>--}}
                {{--                <span class="h6 fw-400 pl-5px">{{$total_plans}}</span>--}}
            </div>

        </div>

        <div class="card-body">
            <table class="table sk-table mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('Order Code') }}</th>
                    <th data-breakpoints="md">{{ translate('Num. of Products') }}</th>
                    <th data-breakpoints="md">{{ translate('Canteen Customer') }}</th>
                    <th data-breakpoints="md">{{ translate('RFID No') }}</th>
                    <th data-breakpoints="md">{{ translate('Amount') }}</th>
                    <th data-breakpoints="md">{{ translate('Refund Date') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($refunds as $key => $refund)

                    @php
                        $order = \App\Models\AppOrder::select('app_orders.grand_total', 'canteen_app_users.username', 'canteen_app_users.card_id', 'cards.rfid_no', 'cards.organisation_id')
                            ->join('canteen_app_users', 'canteen_app_users.id', '=', 'app_orders.user_id')
                            ->join('cards', 'cards.id', '=', 'canteen_app_users.card_id')
                            ->where('app_orders.id', $refund->app_order_id)->first();

//                        dd($order);

                    @endphp

                    <tr>
                        <td>
                            {{ ($key+1) + ($refunds->currentPage() - 1)*$refunds->perPage() }}
                        </td>
                        <td>
                            <a class=""
                              @if($order!=null) href="{{route('canteen_orders.show', encrypt($refund->app_order_id))}}" @endif  title="{{ translate('View') }}">
                                {{ $refund->app_order_code }}
                            </a>
                        </td>
                        <td>
                            {{ $refund->items_refunded_quantity }}
                        </td>

                        <td>
                            @if($order!=null)
                                {{$order->username}}
                            @endif
                        </td>

                        <td>
                            @if($order!=null)
                                {{$order->rfid_no}}
                            @endif
                        </td>

                        <td>
                            @if($order!=null)
                            {{ single_price($order->grand_total) }}
                            @endif
                        </td>
                        <td>
                            {{\Carbon\Carbon::create($refund->created_at)->format('d/m/Y H:i:s')}}
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="sk-pagination">
                {{ $refunds->appends(request()->input())->links() }}

            </div>

        </div>
    </div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">

        function filter_form() {

            $('#form_type').val('filter');
            $("#search_form").attr('target', '');
            $('#search_form').submit();

        }

        function excel_export() {

            $('#form_type').val('export');
            $("#search_form").attr('target', '_blank');

            $('#search_form').submit();

        }


    </script>
@endsection
