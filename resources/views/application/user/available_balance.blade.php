@extends('application.layouts.user_panel')

@section('meta_title'){{ translate('My Available Balance') }}@endsection

@section('panel_content')

    @php
        use Illuminate\Support\Facades\Session;
        use Carbon\Carbon;

        $user = auth()->guard('application')->user(); // canteen user
        $daily_limit = $user->daily_limit;

        $today = Carbon::today();
        $upcoming_purchases = \App\Models\CanteenPurchase::where('canteen_app_user_id', $user->id)->where('date', '>=', $today->format('Y-m-d'))->get();

        $spending = [];
        $date_str = [];



        foreach ($upcoming_purchases as $key => $purchase){
            if(isset($spending[$purchase->date])){
                $spending[$purchase->date] += $purchase->price * $purchase->quantity;
            }else{
                $spending[$purchase->date] = $purchase->price * $purchase->quantity;
            }

            if(!isset($date_str[$purchase->date])){
                $carbon = Carbon::create($purchase->date);
                $date_str[$purchase->date] = substr($carbon->format('l'), 0, 3) . '. ' . $carbon->format('d/m');
            }

        }

        $dates = array_keys($spending);




    @endphp
    <div id="available-balance">
        <div class="container">
            <h1 class="fs-19 fw-700 mb-15px">{{translate('My Available Balance')}}</h1>
            <div class="border border-width-2 border-login-box px-10px py-15px">
                <div class="row align-items-center">
                    <div class="col fs-12 text-black-50">{{translate('Daily Limit')}}</div>
                    <div class="col-auto fw-700">{{single_price($daily_limit)}}</div>
                </div>
            </div>
        </div>
        <div class="mt-15px">
            <div class="container">
                <h2 class="fs-19 fw-700 mb-10px">{{translate('Remaining Balance')}}</h2>
            </div>
            <div class="remaining-balance-table">
                <div class="header">
                    <div class="remaining-balance-row row no-gutters">
                        <div class="col-4 remaining-balance-col">{{translate('Date')}}</div>
                        <div class="col-4 remaining-balance-col">{{translate('Spend')}}</div>
                        <div class="col-4 remaining-balance-col">{{translate('Remaining')}}</div>
                    </div>
                </div>
                <div class="body">
                    @foreach($spending as $date => $spent)
                        <div class="remaining-balance-row row no-gutters">
                            <div class="col-4 remaining-balance-col">{{$date_str[$date]}}</div>
                            <div class="col-4 remaining-balance-col">{{single_price($spent)}}</div>
                            <div class="col-4 text-black remaining-balance-col">{{single_price($daily_limit - $spent)}}</div>
                        </div>
                    @endforeach
                </div>
                <div class="remaining-balance-sticky"></div>
            </div>
        </div>
    </div>
@endsection
