@extends('backend.layouts.app')

@section('content')
    @php
        $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();

        $organisations= \App\Models\Organisation::all();
        $all_organisations= array();

        foreach ($organisations as $key => $organisation) {

            $all_organisations[] = $organisation->id;
        }

        $all_organisations = json_encode($all_organisations);

        $order_ids = [];

        foreach ($orders as $order){
            $order_ids[] = $order->id;
        }

        $possible_break_nums = \App\Models\OrganisationBreak::select('break_num')->groupBy('break_num')->get();

//        dd($possible_break_nums);


    @endphp
    <div class="card">

        <div class="card-header">
            <div class="text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Filters') }}</h5>
            </div>
        </div>

        <div class="card-body">

            <form id="search_form" class="" action="{{route('canteen_orders.index')}}" method="GET">
                <input type="hidden" name="time" value="{{ date('YmdHis') }}">
                <input type="hidden" name="timestamp" value="{{\Carbon\Carbon::now()->format('d/m/y H:i:S')}}">

                <div class="row gutters-5 align-items-baseline ">

                    <div class="col-auto">
                        <div class="form-group mb-0">
                            <label class="h6"> {{translate('Organisation')}} </label>
                            <select id="demo-ease" class="sk-selectpicker w-300px d-block" name="organisation[]"
                                    multiple onchange="all_selected()">
                                <option value="all">{{ translate('All') }}</option>
                                @foreach ($organisations as $key => $organisation)
                                    <option
                                        value="{{ $organisation->id }}">{{ $organisation->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="col-auto">
                        <div class="form-group mb-0 ">
                            <label class="h6"> {{translate('Break')}} </label>
                            <select class="sk-selectpicker w-450px w-lg-450px d-block" id="break_select"
                                    name="break_num[]" multiple>
                                @foreach ($possible_break_nums as $key => $break)
                                    <option
                                        value="{{ $break->break_num }}">{{ ordinal($break->break_num) }} {{translate('Break')}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-auto">
                        <div class="form-group mb-0">
                            <label class="h6"> {{translate('Date')}} </label>
                            <input type="text" class="sk-date-range form-control d-block" value="{{ $date }}"
                                   name="date"
                                   placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y"
                                   data-separator=" to " data-advanced-range="true" autocomplete="off">
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-group mb-0">
                            <label class="h6"> {{translate('Search By Order Code / RFID No / Canteen User')}} </label>
                            <input type="text" class="form-control remove-all-spaces remove-last-space d-block"
                                   id="search"
                                   name="search"
                                   @isset($sort_search) value="{{ $sort_search }}"
                                   @endisset placeholder="{{ translate('Type Order code/RFID no & hit Enter') }}">
                        </div>
                    </div>



                </div>
                <div class="row gutters-5 pt-15px align-items-end justify-content-end">

                    <input type="hidden" value="" name="form_type" id="form_type">

                    <div class="col-auto text-right px-2">

                        <div class="sk-switch-inline d-flex align-items-center pt-10px text-left pl-1">
                            <div class="pr-5px mb-1 d-block h6"> {{ translate('Orders with Refunds only') }} </span>
                            </div>

                            <label class="sk-switch sk-switch-success mb-0">
                                <input type="checkbox" name="show_refunds" @if(isset($show_refunds) && $show_refunds) checked @endif>
                                <span class="d-block mb-1 fs-10"></span>
                            </label>
                        </div>
                    </div>


                    <div class="col-auto">
                        <div class="form-group mb-0">
                            <button type="button" class="btn btn-primary"
                                    onclick="filter_form()">{{ translate('Filter') }}</button>
                        </div>
                    </div>

                    <div class="col-auto">
                        <div class="form-group mb-0">
                            <button type="button" class="btn btn-soft-primary w-200px" onclick="excel_export()"
                                    id="downloadexcel">
                                {{translate('Export to Excel')}}
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <div class="card">

        <div class="card-header d-block">

            <h5 class="p-15px mb-md-2 h5">{{ translate('Canteen Orders') }}</h5>
            <div class="col-auto">
                <span class="h6">{{translate('Total Orders')}}: </span>
                <span class="h6 fw-400 pl-5px pr-15px">{{$total_orders}}</span>
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
                    <th data-breakpoints="md">{{ translate('Payment Status') }}</th>
                    <th data-breakpoints="md">{{ translate('Order Date') }}</th>

                    <th class="text-right" width="15%">{{translate('options')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($orders as $key => $order)
                    <tr>
                        <td>
                            {{ ($key+1) + ($orders->currentPage() - 1)*$orders->perPage() }}
                        </td>
                        <td>
                            {{ $order->code }}
                        </td>
                        <td>

                            @php

                                $total_quantity = \App\Models\AppOrderDetail::where('app_order_id', $order->id)->sum('total_quantity');
                            @endphp

                            {{ $total_quantity }}

                        </td>

                        <td>
                                @if (isset($order->username) && $order->username!=null)

                                    {{ $order->username }}

                                @else
                                    {{ json_decode($order->shipping_address)->app_username }}
                                @endif

                        </td>

                        <td>
                            {{ $order->rfid_no }}
                        </td>

                        <td>
                            {{ single_price($order->grand_total) }}
                        </td>

                        <td>
                            @if ($order->payment_status == 'paid')
                                <span class="badge badge-inline badge-success">{{translate('Paid')}}</span>
                            @else
                                <span class="badge badge-inline badge-danger">{{translate('Unpaid')}}</span>
                            @endif
                        </td>
                        <td>
                            {{\Carbon\Carbon::create($order->created_at)->format('d/m/Y')}}

                        </td>

                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                               href="{{route('canteen_orders.show', encrypt($order->id))}}" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                               href="{{ route('canteen_invoice.download', $order->id) }}"
                               title="{{ translate('Download Invoice') }}">
                                <i class="las la-download"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                               data-href="{{route('canteen_orders.destroy', $order->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="sk-pagination">
                {{ $orders->appends(request()->input())->links() }}

            </div>

        </div>
    </div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">

        let old_select = [];

        $(document).ready(function () {

            @if(!isset($selected_organisations))
            $('.filter-option-inner-inner').text('{{translate('Filter By Organisation')}}');

            $('#demo-ease').selectpicker('refresh');
            @endif


        });


        function all_selected() {

            var new_val = [];

            if (old_select.includes('all')) {

                for (var i = 0; i < ($('#demo-ease').val()).length; i++) {
                    if (i > 0) {
                        new_val.push(($('#demo-ease').val())[i]);
                    }
                }

                $('#demo-ease').val(new_val);
                old_select = $('#demo-ease').val();
                $('#demo-ease').selectpicker('refresh');


            } else if (($('#demo-ease').val()).includes('all')) {
                $('#demo-ease').val(['all']);

                $('#demo-ease').selectpicker('refresh');

                old_select = $('#demo-ease').val();

            }

        }

        $('#demo-ease').on('change', function (e) {

            if (($('#demo-ease').val()).length == 0) {
                $('.filter-option-inner-inner').text('{{translate('Filter By Organisation')}}');
            }

        });


        @if(isset($selected_organisations) && count($selected_organisations)>0)

        $(function () {

            @if(in_array('all', $selected_organisations))
            $('#demo-ease').val(['all']);
            @else

            var values = [];

            @foreach($selected_organisations as $selected_organisation)
            values.push('{{$selected_organisation}}');
            @endforeach
            $('#demo-ease').val(values);
            @endif

            $('#demo-ease').selectpicker('refresh');

        });

        @endif

        @if(isset($break_nums) && count($break_nums)>0)

        $(function () {


            var values_breaks = [];

            @foreach($break_nums as $break_num)
            values_breaks.push('{{$break_num}}');
            @endforeach
            $('#break_select').val(values_breaks);

            $('#break_select').selectpicker('refresh');

        });

        @endif


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
