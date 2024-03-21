@extends('application.layouts.app')

@section('meta_title'){{ translate('Order Pending') }}@endsection

@section('content')

    @php

    $cart = json_decode($app_viva_log->cart_items);

    $titles = [];
    $dates = [];

    foreach (json_decode($app_viva_log->cart_items, true) as $key => $cartItem){
//        dd($cartItem['price'], $cartItem);
        $carbon_date = \Carbon\Carbon::create($cartItem['date']);
        $dates[] = $carbon_date->format('Y-m-d');
        $titles [] = ucfirst($carbon_date->format('l')) . ' ' . $carbon_date->format('d/m') . ' - ' . ordinal($cartItem['break_sort']) . ' ' . translate('Break');

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
                <span class="order-pending-icon">
                    <svg class="h-10px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14.81 15.02">
                        <use xlink:href="{{static_asset('assets/img/icons/pending-order.svg')}}#content"></use>
                    </svg>
                </span>
                {{toUpper(translate('Order Pending!'))}}
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
                    </span> is under processing and in the next few minutes it will appear in your upcoming meals.</p>
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

                            foreach (json_decode($app_viva_log->cart_items, true) as $key => $cartItem){

                                if($date == $cartItem['date']){
                                    $sum += $cartItem['quantity'] * $cartItem['price'];
                                }
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

//    $app_viva_log->confirm_page_seen = 1;
//    $app_viva_log->save();
@endphp
