@extends('backend.layouts.app')

@section('content')

    @php

        //            echo json_encode($card);
        //            echo json_encode($valid_subscriptions);
    @endphp


    <div class="card">
        <div class="card-header d-block">
            <h5 class="mb-0 ">{{ translate('Organisation') }}</h5>

            @if($card!=null)

                <div class="d-md-flex fw-600 mt-1">
                    <h6 class="mb-0">{{ translate($organisation->name) }}</h6>
                </div>
                <div class="d-md-flex mt-1">
                    <h6 class="mb-0 ">{{ translate('Customer') }}:
                        @if($user==null)
                            -
                        @else
                            <a class="text-black hov-text-hov-primary"
                               href="{{route('customers.view_catering_plans', encrypt($user->id))}}"> {{ translate($user->name) }}

                                <i class="pl-1 las la-eye"></i> </a>
                        @endif
                    </h6>
                </div>
                <div class=" d-md-flex mt-1">
                    <h6 class="mb-0"> {{translate('RFID No')}}: {{$card->rfid_no }}</h6>
                </div>
                <div class=" d-md-flex mt-1">
                    <h6 class="mb-0">{{ translate($subscription_status) }}</h6>
                </div>

            @endif

        </div>


        <div class="card-body">
            <table class="table sk-table mb-0">
                <thead>
                <tr>
                    <th width="20px" data-breakpoints="lg">#</th>
                    {{--                    <th>{{translate('Name')}}</th>--}}
                    {{--                    <th>{{translate('Cards')}}</th>--}}
                    {{--                    <th  class="text-right">{{translate('Options')}}</th>--}}

                    <th>{{toUpper(translate('Purchase Date'))}}</th>
                    <th>{{toUpper(translate('Start Date'))}}</th>
                    <th>{{toUpper(translate('End Date'))}}</th>
                    <th>{{toUpper(translate('Snack'))}}</th>
                    <th>{{toUpper(translate('Lunch'))}}</th>
                    <th>{{toUpper(translate('Price'))}}</th>
                    <th class="text-right">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($valid_subscriptions as $key => $subscription)
                    <tr>

                        <td>{{ ($key+1) + ($valid_subscriptions->currentPage() - 1)*$valid_subscriptions->perPage() }}</td>

                            <?php
                            $order_detail = \App\Models\OrderDetail::where('type', 'catering_plan')->where('type_id', $subscription->id)->first();

//                            echo $order_detail;

                            if ($order_detail != null) {
                                $order = \App\Models\Order::findOrFail($order_detail->order_id);
                            }

                            ?>
                        {{--                        <td>{{$key+1}}</td>--}}
                        <td>{{\Carbon\Carbon::create($subscription->created_at)->format('d/m/y')}}</td>
                        <td>{{\Carbon\Carbon::create($subscription->from_date)->format('d/m/y')}}</td>
                        <td>{{\Carbon\Carbon::create($subscription->to_date)->format('d/m/y')}}</td>

                        <td>{{$subscription->snack_quantity}}</td>


                        <td>{{$subscription->meal_quantity}}</td>

                        <td>
                            @if($order_detail!=null)
                                {{format_price($order->grand_total)}}
                            @endif
                        </td>

                        <td class="text-right">

                            @if(\Carbon\Carbon::create($subscription->to_date)->gte(\Carbon\Carbon::today()))
                                <a href="#"
                                   class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                   data-href="{{route('catering_plan_purchases.destroy', encrypt($subscription->purchase_id))}}"
                                   title="{{ translate('Cancel Plan') }}">
                                    <i class="las la-trash"></i>
                                </a>

                            @endif
                        </td>

                    </tr>
                @endforeach
                </tbody>

            </table>
            <div class="sk-pagination">

                @if($card!=null)
                    {{$valid_subscriptions->links()}}
                @endif
            </div>
        </div>
    </div>

@endsection

