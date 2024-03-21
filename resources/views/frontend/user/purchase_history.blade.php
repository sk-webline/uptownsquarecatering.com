@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="sk-titlebar mt-20px mb-15px mb-lg-40px">
        <h1 class="fs-16 sm-fs-20 fw-500 m-0">{{ translate('Orders') }}</h1>
    </div>
    <div class="bg-account py-5px px-15px fs-13 md-fs-16 lg-fs-12 xxl-fs-16 overflow-hidden">
        @if (count($orders) > 0)
            <div class="d-none d-xl-block">
                <table class="table sk-table mb-0">
                <thead>
                <tr>
                    <th>{{ translate('Order Number')}}</th>
                    <th data-breakpoints="md">{{ translate('Date')}}</th>
                    <th>{{ translate('Amount')}}</th>
                    <th data-breakpoints="md">{{ translate('Delivery Status')}}</th>
                    <th data-breakpoints="md">{{ translate('Tracking Number')}}</th>
                    <th class="text-right">{{ translate('Options')}}</th>
                </tr>
                </thead>
                <tbody class="text-black-50">
                    @foreach ($orders as $key => $order)
                        @if (count($order->orderDetails) > 0)
                            <tr>
                                <td>
                                    <a class="text-secondary" href="#{{ $order->code }}" onclick="show_purchase_history_details({{ $order->id }})">
                                        {{ $order->code }}
                                        @if($order->delivery_viewed == 0)
                                            <span class="mr-1 bg-secondary text-white px-5px py-2px lh-1 fs-10">{{translate('New')}}</span>
                                        @endif
                                    </a>
                                </td>
                                <td>{{ date('d-m-Y', $order->date) }}</td>
                                <td>
                                    {{ single_price($order->grand_total) }}
                                </td>
                                <td>
                                    {{ translate(ucfirst(str_replace('_', ' ', $order->orderDetails->first()->delivery_status))) }}
                                </td>
                                <td>
                                    {{ $order->tracking_number }}
                                </td>
                                <td class="text-right">
                                    <?php /*
                                    @if ($order->orderDetails->first()->delivery_status == 'pending' && $order->payment_status == 'unpaid')
                                        <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('orders.destroy', $order->id)}}" title="{{ translate('Cancel') }}">
                                            <i class="las la-trash"></i>
                                        </a>
                                    @endif
     */ ?>
                                    <a class="btn btn-outline-black btn-icon btn-circle opacity-50 hov-opacity-100" href="{{ route('invoice.download', $order->id) }}" title="{{ translate('Download Invoice') }}">
                                        <i class="las la-download"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-outline-black btn-icon btn-circle opacity-50 hov-opacity-100" onclick="show_purchase_history_details({{ $order->id }})" title="{{ translate('Order Details') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            </div>
            <div class="d-xl-none">
                @foreach ($orders as $key => $order)
                    @if (count($order->orderDetails) > 0)
                        <div class="order-table-res-item py-10px c-pointer @if($order->payment_status_viewed == 0) not-viewed @endif" onclick="show_purchase_history_details({{ $order->id }})">
                            <div class="row gutters-5 py-5px">
                                <div class="col-6 fw-600 fs-11 md-fs-14">{{ translate('Order Number')}}</div>
                                <div class="col-6 text-right text-secondary">
                                    {{ $order->code }}
                                </div>
                            </div>
                            <div class="row gutters-5 py-5px">
                                <div class="col-6 fw-600 fs-11 md-fs-14">{{ translate('Date')}}</div>
                                <div class="col-6 text-right text-black-50">{{ date('d-m-Y', $order->date) }}</div>
                            </div>
                            <div class="row gutters-5 py-5px">
                                <div class="col-6 fw-600 fs-11 md-fs-14">{{ translate('Amount')}}</div>
                                <div class="col-6 text-right text-black-50">{{ single_price($order->grand_total) }}</div>
                            </div>
                            <div class="row gutters-5 py-5px">
                                <div class="col-6 fw-600 fs-11 md-fs-14">{{ translate('Delivery Status')}}</div>
                                <div class="col-6 text-right text-black-50">{{ translate(ucfirst(str_replace('_', ' ', $order->orderDetails->first()->delivery_status))) }}</div>
                            </div>
                            <div class="row gutters-5 py-5px">
                                <div class="col-6 fw-600 fs-11 md-fs-14">{{ translate('Tracking Number')}}</div>
                                <div class="col-6 text-right text-black-50"></div>
                            </div>
                            <div class="row gutters-5 py-5px">
                                <div class="col-6 fw-600 fs-11 md-fs-14">{{ translate('Options')}}</div>
                                <div class="col-6 text-right text-black-50">
                                    <a class="btn btn-outline-black btn-icon btn-circle opacity-50 hov-opacity-100" href="{{ route('invoice.download', $order->id) }}" title="{{ translate('Download Invoice') }}">
                                        <i class="las la-download"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-outline-black btn-icon btn-circle opacity-50 hov-opacity-100" onclick="show_purchase_history_details({{ $order->id }})" title="{{ translate('Order Details') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="sk-pagination mt-5px">
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center fs-12 md-fs-14 py-md-10px fw-600">{{translate('There are no orders')}}</div>
        @endif
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
    <div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div id="order-details-modal-body">

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div id="payment_modal_body">

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $('#order_details').on('hidden.bs.modal', function () {
            location.reload();
        })
    </script>
@endsection
