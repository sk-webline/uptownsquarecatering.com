@extends('application.layouts.app')

@section('meta_title'){{ translate('Order Completed') }}@endsection

@section('content')

    @php

    $order_details_ids = $order->orderDetails->pluck('id');

    $titles = [];
    $dates = [];
    $upcoming_date = null;
    $upcoming_break = null;
    $canteen_purchases = \App\Models\CanteenPurchase::whereIn('canteen_order_detail_id', $order_details_ids)->get();


    foreach ($canteen_purchases as $key => $canteen_purchase){

        $carbon_date = \Carbon\Carbon::create($canteen_purchase->date);

        if($key == 0){
            $upcoming_date = ucfirst($carbon_date->format('l')) . ' ' . $carbon_date->format('d/m');
            $upcoming_break = substr($canteen_purchase->break_hour_from, 0, 5) . ' - ' . substr($canteen_purchase->break_hour_to, 0, 5);

        }


        $dates[] = $carbon_date->format('Y-m-d');
        $titles [] = ucfirst($carbon_date->format('l')) . ' ' . $carbon_date->format('d/m') . ' - ' . ordinal($canteen_purchase->break_num) . ' ' . translate('Break');
    }

    $titles = array_unique($titles);
    $dates = array_unique($dates);

    $user = auth()->guard('application')->user(); // canteen user
    $rfid_card = $user->card;
    $organisation = $rfid_card->organisation;
    $daily_limit = $user->daily_limit;


    @endphp
    <div id="order-success" class="my-20px">
        <div class="container">
            <h1 class="fs-18 fw-700 mb-10px">
                <span class="order-success-icon">
                    <svg class="h-10px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14.31 10.22">
                        <use xlink:href="{{static_asset('assets/img/icons/check-break.svg')}}#content"></use>
                    </svg>
                </span>
                {{toUpper(translate('Order Submitted!'))}}
            </h1>
        </div>
        <div class="bg-login-box py-20px lh-1-5 mb-30px">
            <div class="container">
                <p>Your order for <span class="fw-800">

                        @foreach($titles as $key => $title)
                            {{$title}}
                            @if($title != last($titles))
                                ,
                            @endif

                        @endforeach
                    </span> has been submitted.</p>
                <p>You can pick up your next upcoming order by scanning your tag at the school canteen on <span class="fw-800">{{$upcoming_date}}</span> between <span class="fw-800">{{$upcoming_break}}.</span></p>
            </div>
        </div>
        <div class="container">
            <div class="my-30px">
                <div class="border border-width-2 border-login-box px-10px">
                    @foreach($dates as $key => $date)

                        @php
                            $canteen_purchases = \App\Models\CanteenPurchase::where('date', $date)->get();
                            $sum = 0;
                            foreach($canteen_purchases as $canteen_purchase){
                                $sum += $canteen_purchase->quantity * $canteen_purchase->price;
                            }
                        @endphp

                        <div class="remaining-row-item">
                            <div class="row align-items-center">
                                <div class="col fs-12 text-black-50">{{\Carbon\Carbon::create($date)->format('d/m')}} - Remaining Amount</div>
                                <div class="col-auto fw-700">{{single_price($daily_limit - $sum)}}</div>
                            </div>
                        </div>

                    @endforeach

                </div>
            </div>
            <a href="{{ route('application.home') }}" class="btn btn-block btn-secondary rounded-3px fw-700 py-5px">{{toUpper(translate('Order for Another Break'))}}</a>
        </div>
    </div>
@endsection


@php

    $order->confirm_page_seen = 1;
    $order->save();

@endphp
