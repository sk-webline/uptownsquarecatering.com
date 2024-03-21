@extends('frontend.layouts.user_panel')

@section('meta_title'){{ translate('Subscription History') }}@stop

@section('panel_content')

    <?php
    use App\Models\Order;
    use App\Models\OrderDetail;

    $max_snack = $latest_organisation_setting->max_snack_quantity;
    $max_meal = $latest_organisation_setting->max_meal_quantity;


    ?>

    <?php

//    echo json_encode($subscription_purchases)
    ?>

    <h1 class="fs-14 md-fs-16 mb-10px mb-md-15px text-primary-50 fw-700 lh-1-2 xl-lh-1">
        <a class="hov-text-primary" href="{{route('dashboard')}}">
            {{ toUpper(translate('Dashboard')) }}
        </a> /
        <span class="d-inline-block"><span class="border-bottom border-inherit">{{ toUpper(translate('Subscription History')) }} - {{toUpper($card->name)}}</span></span>
    </h1>

    <div class="background-brand-grey px-lg-25px fs-14">
        @if(count($subscription_purchases) > 0)
            <div class="pb-lg-20px">
                <div class="d-none d-lg-block">
                    <table class="table sk-table mb-0 history-table">
                        <thead>
                        <tr>
                            <th>{{toUpper(translate('S.NO'))}}</th>
                            <th width="150">{{toUpper(translate('Purchase Date'))}}</th>
                            <th>{{toUpper(translate('Start Date'))}}</th>
                            <th width="35%">{{toUpper(translate('End Date'))}}</th>

                            @if($max_snack>0)
                            <th>{{toUpper(translate('Snack'))}}</th>
                            @endif
                            @if($max_meal>0)
                            <th>{{toUpper(translate('Lunch'))}}</th>
                            @endif
                            <th>{{toUpper(translate('Price'))}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($subscription_purchases as $key => $subscription)
                            <tr>
                                <?php
                                    $order_detail = OrderDetail::where('type', 'catering_plan')->where('type_id', $subscription->id)->first();

                                    if($order_detail!=null) {
                                        $order = Order::findOrFail($order_detail->order_id);
                                    }
                                ?>
                                <td>{{$key+1}}</td>
                                <td>{{\Carbon\Carbon::create($subscription->created_at)->format('d/m/y')}}</td>
                                <td>{{\Carbon\Carbon::create($subscription->from_date)->format('d/m/y')}}</td>
                                <td>{{\Carbon\Carbon::create($subscription->to_date)->format('d/m/y')}}</td>
                                @if($max_snack>0)
                                    <td>{{$subscription->snack_quantity}}</td>
                                @endif
                                @if($max_meal>0)
                                    <td>{{$subscription->meal_quantity}}</td>
                                @endif
                                <td>@if($order_detail!=null){{format_price($order->grand_total)}}@endif</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-lg-none">
                    @foreach($subscription_purchases as $key => $subscription)
                        <?php
                            $order_detail = OrderDetail::where('type', 'catering_plan')->where('type_id', $subscription->id)->first();

                            if($order_detail!=null) {
                                $order = Order::findOrFail($order_detail->order_id);
                            }
                        ?>
                        <div class="table-row-results">
                            <div class="table-row-item">
                                <div class="row gutters-5 align-items-center">
                                    <div class="col fw-700">{{toUpper(translate('S.NO'))}}</div>
                                    <div class="col-auto text-primary-50">{{$key+1}}</div>
                                </div>
                            </div>
                            <div class="table-row-item">
                                <div class="row gutters-5 align-items-center">
                                    <div class="col fw-700">{{toUpper(translate('Purchase Date'))}}</div>
                                    <div class="col-auto text-primary-50">{{\Carbon\Carbon::create($subscription->created_at)->format('d/m/y')}}</div>
                                </div>
                            </div>
                            <div class="table-row-item">
                                <div class="row gutters-5 align-items-center">
                                    <div class="col fw-700">{{toUpper(translate('Start Date'))}}</div>
                                    <div class="col-auto text-primary-50">{{\Carbon\Carbon::create($subscription->from_date)->format('d/m/y')}}</div>
                                </div>
                            </div>
                            <div class="table-row-item">
                                <div class="row gutters-5 align-items-center">
                                    <div class="col fw-700">{{toUpper(translate('End Date'))}}</div>
                                    <div class="col-auto text-primary-50">{{\Carbon\Carbon::create($subscription->to_date)->format('d/m/y')}}</div>
                                </div>
                            </div>
                            @if($max_snack>0)
                                <div class="table-row-item">
                                    <div class="row gutters-5 align-items-center">
                                        <div class="col fw-700">{{toUpper(translate('Snack'))}}</div>
                                        <div class="col-auto text-primary-50">{{$subscription->snack_quantity}}</div>
                                    </div>
                                </div>
                            @endif
                            @if($max_meal>0)
                                <div class="table-row-item">
                                    <div class="row gutters-5 align-items-center">
                                        <div class="col fw-700">{{toUpper(translate('Lunch'))}}</div>
                                        <div class="col-auto text-primary-50">{{$subscription->meal_quantity}}</div>
                                    </div>
                                </div>
                            @endif
                            <div class="table-row-item">
                                <div class="row gutters-5 align-items-center">
                                    <div class="col fw-700">{{toUpper(translate('Price'))}}</div>
                                    <div class="col-auto text-primary-50">@if($order_detail!=null){{format_price($order->grand_total)}}@endif</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center fw-700 p-30px">{{translate('No subscription history yet')}}</div>
        @endif
    </div>
@endsection

@section('script')

@endsection
