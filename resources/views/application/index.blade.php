@extends('application.layouts.app')

@section('meta_title'){{ translate('Home') }}@endsection

@section('content')

    @php

        use Carbon\Carbon;
        use App\Models\CanteenPurchase;

        $breaks = $organisation->breaks;

        $canteen_setting = $organisation->current_canteen_settings();
        $business_days = json_decode($canteen_setting->working_week_days);
        $holidays = json_decode($canteen_setting->holidays);
        $extra_days = $canteen_setting->extra_days->pluck('date')->toArray();
        $minimum_preorder_minutes = $canteen_setting->minimum_preorder_minutes;

        $this_week_dates = [];  // $this_week_dates[11/12] = 'Mo'
        $this_week_full_dates = []; // $this_week_full_dates[11/12] = '2023-12-11'
        $dashboard_calendar = [];
        $next_week_dates = [];

        $today = Carbon::today();

        $start_of_week = Carbon::today()->startOfWeek();
        $end_of_week = Carbon::today()->endOfWeek();
        $flag = false;

         // Loop through each day of the week
        for ($date = $start_of_week; $date->lte($end_of_week); $date->addDay()) {

            $day = $date->format('D');

            if((in_array($day, $business_days) ) || in_array($date->format('Y-m-d'), $extra_days)){
                $this_week_dates[$date->format('d/m')] = substr($date->format('D'), 0,2);
                $this_week_full_dates[$date->format('d/m')] = $date->format('Y-m-d');
            }

            if(((in_array($date->format('D'), $business_days) && !in_array($date->format('D'), $holidays)) || in_array($date->format('Y-m-d'), $extra_days)) && $date->gte(Carbon::today())){

                if(!$flag){
                    // prepi na elegxw an ine meta to teleuteo dialimma
                    if(preorder_availability($date->format('Y-m-d'), $breaks[count($breaks)-1], $minimum_preorder_minutes)){
                        $flag = true;
                    }
                }

            }

        }


        if($flag){

            $start_of_next_week = Carbon::today()->addDays(7)->startOfWeek();
            $end_of_next_week = Carbon::today()->addDays(7)->endOfWeek();

            $start_of_last_week = Carbon::today()->subDays(7)->startOfWeek();
            $end_of_last_week = Carbon::today()->subDays(7)->endOfWeek();

        }else{
            $start_of_week = Carbon::today()->addDays(7)->startOfWeek();
            $end_of_week = Carbon::today()->addDays(7)->endOfWeek();

            $this_week_dates = [];
            $this_week_full_dates = [];

             // Loop through each day of the week
            for ($date = $start_of_week; $date->lte($end_of_week); $date->addDay()) {

                $day = $date->format('D');

                if(in_array($day, $business_days) || in_array($date->format('Y-m-d'), $extra_days)){
                    $this_week_dates[$date->format('d/m')] = substr($date->format('D'), 0,2);
                    $this_week_full_dates[$date->format('d/m')] = $date->format('Y-m-d');
                }

            }

            $start_of_next_week = Carbon::today()->addDays(2*7)->startOfWeek();
            $end_of_next_week = Carbon::today()->addDays(2*7)->endOfWeek();

            $start_of_last_week = Carbon::today()->startOfWeek();
            $end_of_last_week = Carbon::today()->endOfWeek();

        }


        // for all 3 weeks
        $canteen_purchases = CanteenPurchase::where('canteen_app_user_id', $user->id)->where('date', '>=', $start_of_last_week->format('Y-m-d'))
                            ->where('date', '<=', $end_of_next_week->format('Y-m-d'))->orderBy('date')->get();


        foreach ($canteen_purchases as $purchase){

           if($purchase->date < $today->format('Y-m-d')){
               $dashboard_calendar[Carbon::create($purchase->date)->format('d/m')][$purchase->break_num] = 'old';
           }elseif ($purchase->date == $today->format('Y-m-d')){

              $temp = $purchase->date. ' '. $purchase->break_hour_from;

              if($temp<= Carbon::now()->format('Y-m-d H:i:s')){
                  $dashboard_calendar[$today->format('d/m')][$purchase->break_num] = 'old';
              }else{
                  $dashboard_calendar[$today->format('d/m')][$purchase->break_num] = 'preordered';
              }

           }else{
               $dashboard_calendar[Carbon::create($purchase->date)->format('d/m')][$purchase->break_num] = 'preordered';
           }

        }

//        // Loop through each day of the week
//        for ($date = $start_of_week; $date->lte($end_of_week); $date->addDay()) {
//
//            $day = $date->format('D');
//
//            if(in_array($day, $business_days) || in_array($date->format('Y-m-d'), $extra_days)){
//                $this_week_dates[$date->format('d/m')] = substr($date->format('D'), 0,2);
//                $this_week_full_dates[$date->format('d/m')] = $date->format('Y-m-d');
//            }
//
//        }


        $next_week_dates = [];  // $this_week_dates[11/12] = 'Mo'
        $next_week_full_dates = []; // $this_week_full_dates[11/12] = '2023-12-11'

         // Loop through each day of the week
        for ($date = $start_of_next_week; $date->lte($end_of_next_week); $date->addDay()) {

            $day = $date->format('D');

            if(in_array($day, $business_days) || in_array($date->format('Y-m-d'), $extra_days)){
                $next_week_dates[$date->format('d/m')] = substr($date->format('D'), 0,2);
                $next_week_full_dates[$date->format('d/m')] = $date->format('Y-m-d');
            }

        }

        $last_week_dates = [];  // $last_week_dates[11/12] = 'Mo'
        $last_week_full_dates = []; // $last_week_full_dates[11/12] = '2023-12-11'

        // Loop through each day of the week
        for ($date = $start_of_last_week; $date->lte($end_of_last_week); $date->addDay()) {

            $day = $date->format('D');

            if(in_array($day, $business_days) || in_array($date->format('Y-m-d'), $extra_days)){
                $last_week_dates[$date->format('d/m')] = substr($date->format('D'), 0,2);
                $last_week_full_dates[$date->format('d/m')] = $date->format('Y-m-d');
            }

        }



        // next order opportunity calculate

        $flag = false;

        $next_available_date = null;
        $next_available_day = null;
        $next_available_break = 1;
        $remaining_hours = null;
        $remaining_minutes = null;

        $temp_date = Carbon::today();

        $purchases = CanteenPurchase::where('canteen_app_user_id', $user->id)->where('date', '>=', $temp_date->format('Y-m-d'))->orderBy('date')->get();

        if($purchases == null || count($purchases)==0){

                foreach($breaks as $key => $break){

                    if(preorder_availability($temp_date->format('Y-m-d'), $break, $minimum_preorder_minutes)){
                        $next_available_date = $temp_date->format('d/m');
                        $next_available_day = $temp_date->format('l');
                        $next_available_break = $key+1;

                        $remaining_hours = Carbon::now()->diffInHours(Carbon::create($temp_date->format('Y-m-d') .  ' ' . $break->hour_from)->subMinutes($canteen_setting->minimum_preorder_minutes));
                        if($remaining_hours<1){
                           $remaining_minutes = Carbon::now()->diffInMinutes(Carbon::create($temp_date->format('Y-m-d') .  ' ' . $break->hour_from)->subMinutes($canteen_setting->minimum_preorder_minutes));
                        }
                        $flag=true;
                        break;
                    }

                }


                if(!$flag){

                    $carbon_date = Carbon::tomorrow();

                    while (!$flag){

                         foreach($breaks as $key => $break){

                            if(preorder_availability($carbon_date->format('Y-m-d'), $break, $minimum_preorder_minutes)){
                                $next_available_date = $carbon_date->format('d/m');
                                $next_available_day = $carbon_date->format('l');
                                $next_available_break = $key+1;

                                $remaining_hours = Carbon::now()->diffInHours(Carbon::create($carbon_date->format('Y-m-d') .  ' ' . $break->hour_from)->subMinutes($canteen_setting->minimum_preorder_minutes));
                                if($remaining_hours<1){
                                   $remaining_minutes = Carbon::now()->diffInMinutes(Carbon::create($carbon_date->format('Y-m-d') .  ' ' . $break->hour_from)->subMinutes($canteen_setting->minimum_preorder_minutes));
                                 }

                                $flag=true;
                                break;
                            }

                        }
//

                        $carbon_date = $carbon_date->addDay();
                    }

                }

        }else{

            $dates= [];
            $comp = [];

            foreach ($purchases as $purchase){
                $comp[$purchase->date][$purchase->break_num] = 1;
                $dates[] = $purchase->date;
            }

            $dates = array_unique($dates);

            $start_carbon = Carbon::create($dates[0]);
            $end_carbon =  Carbon::create(end($dates));

            for ($date = $start_carbon; $date->lte($end_carbon); $date->addDay()) {

                if( in_array($date->format('D'), $business_days)){

                    foreach($breaks as $key => $break){
                        if(preorder_availability($date->format('Y-m-d'), $break, $minimum_preorder_minutes) && !isset($comp[$date->format('Y-m-d')][$break->break_num])){
                            $next_available_date = $date->format('d/m');
                            $next_available_day = $date->format('l');
                            $next_available_break = $key+1;

                            $remaining_hours = Carbon::now()->diffInHours(Carbon::create($date->format('Y-m-d') .  ' ' . $break->hour_from)->subMinutes($canteen_setting->minimum_preorder_minutes));

                            if($remaining_hours<1){
                               $remaining_minutes = Carbon::now()->diffInMinutes(Carbon::create($date->format('Y-m-d') .  ' ' . $break->hour_from)->subMinutes($canteen_setting->minimum_preorder_minutes));
                            }


                            $flag=true;
                            break;
                        }
                    }

                    if($flag){
                        break;
                    }
                }

            }

            if(!$flag){
//                $d = Carbon::create(end($dates))->addDay();
//                $remaining = Carbon::now()->diff(Carbon::create(end($dates) .  ' ' . $breaks[0]->hour_from)->subMinutes($canteen_setting->minimum_preorder_minutes));
//                $next_available_date = $d->format('d/m');
//                $next_available_day = $d->format('l');
//                $next_available_break = 1;
//                $flag=true;

                $carbon_date = Carbon::create(end($dates))->addDay();

                    while (!$flag){

                         foreach($breaks as $key => $break){

                            if(preorder_availability($carbon_date->format('Y-m-d'), $break, $minimum_preorder_minutes)){
                                $next_available_date = $carbon_date->format('d/m');
                                $next_available_day = $carbon_date->format('l');
                                $next_available_break = $key+1;

                                $remaining_hours = Carbon::now()->diffInHours(Carbon::create($carbon_date->format('Y-m-d') .  ' ' . $break->hour_from)->subMinutes($canteen_setting->minimum_preorder_minutes));

                                if($remaining_hours<1){
                                    $remaining_minutes = Carbon::now()->diffInMinutes(Carbon::create($carbon_date->format('Y-m-d') .  ' ' . $break->hour_from)->subMinutes($canteen_setting->minimum_preorder_minutes));
                                }

                                $flag=true;
                                break;
                            }

                        }
//

                        $carbon_date = $carbon_date->addDay();
                    }
            }

        }



    @endphp


    <div id="home" class="my-20px">
        <div class="container">
            <h1 class="fs-18 fw-700 fw-700 mb-10px">{{toUpper(translate('Hey There!'))}}</h1>
            <p>Time to pick your <span class="fw-800">break</span> and decide what yummy <span class="fw-800">food</span> or <span class="fw-800">drink</span> you’d like for each break!</p>
        </div>
        <div class="mt-20px">
            <div class="bg-green py-5px fs-11 container py-5px text-white">
                {{translate('Next order opportunity')}}: <span class="fs-13 fw-700">{{$next_available_day}} {{$next_available_date}} - {{ordinal($next_available_break)}} Break</span>
            </div>

            @if($remaining_hours<=12)
                <div class="my-5px container text-black-50 fs-12">
                    <img class="h-13px mr-2" src="{{static_asset('assets/img/icons/clock-icon.svg')}}" alt="">
                    @if($remaining_hours<1)
                        {{$remaining_minutes}} {{translate('minutes remaining to order')}}
                    @else
                    {{$remaining_hours}} {{translate('hours remaining to order')}}
                    @endif
                </div>
            @endif


            <div id="prev_week" class="break-table prev_week d-none">
                <div class="row no-gutters">
                    <div class="col-80px break-table-left">
                        <div class="break-table-row header" data-row="header">
                            <div class="break-table-col">
                 <span>
                      {{toUpper(translate('Breaks'))}} <span class="arrow"></span>
                 </span>
                            </div>
                        </div>
                        <div class="break-table-body">

                            @php
                                $counter = 1
                            @endphp

                            @foreach($last_week_dates as $key => $day)
                                <div class="break-table-row" data-row="{{$counter}}">
                                    <div class="break-table-col">{{$day}}. {{$key}}</div>
                                </div>

                                @php   $counter++; @endphp
                            @endforeach

                        </div>
                    </div>
                    <div class="col-grow-80px break-table-right">
                        <div class="break-table-row header" data-row="header">
                            <div class="row no-gutters">

                                @foreach($breaks as $key => $break)
                                    <div class="col">
                                        <div class="break-table-col">{{ordinal($key+1)}}</div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                        <div class="break-table-body">
                            @php

                                $counter = 1;
                                $next_date_available_found = false;

                            @endphp
                            @foreach($last_week_dates as $date => $day)
                                <div class="break-table-row" data-row="{{$counter}}">
                                    <div class="row no-gutters">
                                        @if(in_array($last_week_full_dates[$date], $holidays))
                                            <div class="col" >
                                                <div class="break-table-col">
                                                    <div class="public-holiday">
                                                            <span>
                                                               <img class="h-13px mr-2" src="{{static_asset('assets/img/icons/happy-face.svg')}}" alt=""> {{toUpper(translate('Public Holiday'))}}
                                                             </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            @foreach($breaks as $key => $break)
                                                <div class="col">
                                                    <div class="break-table-col">
                                                        @if(isset($dashboard_calendar[$date][$break->break_num]))
                                                            @if($dashboard_calendar[$date][$break->break_num] == 'old')
                                                                <div class="checked">
                                                                    <img class="h-13px" src="{{static_asset('assets/img/icons/check-break.svg')}}" alt="">
                                                                </div>
                                                            @endif

                                                        @else
                                                           <div class="no-orders">
                                                               {{toUpper(translate('No Orders'))}}
                                                           </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif

                                    </div>
                                </div>

                                @php
                                    $counter++;
                                @endphp
                            @endforeach

                        </div>
                    </div>
                </div>

            </div>

            <div id="current_week" class="break-table current_week active">
                <div class="row no-gutters">
                    <div class="col-80px break-table-left">
                        <div class="break-table-row header" data-row="header">
                            <div class="break-table-col">
                                 <span>
                                      {{toUpper(translate('Breaks'))}} <span class="arrow"></span>
                                 </span>
                            </div>
                        </div>
                        <div class="break-table-body">

                            @php
                                $counter = 1
                            @endphp

                            @foreach($this_week_dates as $key => $day)
                                <div class="break-table-row" data-row="{{$counter}}">
                                    <div class="break-table-col">{{$day}}. {{$key}}</div>
                                </div>

                                @php   $counter++; @endphp
                            @endforeach

                        </div>
                    </div>
                    <div class="col-grow-80px break-table-right">
                        <div class="break-table-row header" data-row="header">
                            <div class="row no-gutters">

                                @foreach($breaks as $key => $break)
                                    <div class="col">
                                        <div class="break-table-col">{{ordinal($key+1)}}</div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                        <div class="break-table-body">
                            @php

                                $counter = 1;
                                $next_date_available_found = false;

                            @endphp
                            @foreach($this_week_dates as $date => $day)
                                <div class="break-table-row" data-row="{{$counter}}">
                                    <div class="row no-gutters">
                                        @if(in_array($this_week_full_dates[$date], $holidays))

                                            <div class="col" >
                                                <div class="break-table-col">
                                                    <div class="public-holiday">
                                                            <span>
                                                               <img class="h-13px mr-2" src="{{static_asset('assets/img/icons/happy-face.svg')}}" alt=""> {{toUpper(translate('Public Holiday'))}}
                                                             </span>
                                                    </div>
                                                </div>
                                            </div>

                                        @else

                                            @foreach($breaks as $key => $break)
                                                <div class="col">
                                                    <div class="break-table-col">

                                                        @if(isset($dashboard_calendar[$date][$break->break_num]))
                                                            @if($dashboard_calendar[$date][$break->break_num] == 'old')
                                                                <div class="checked">
                                                                    <img class="h-13px" src="{{static_asset('assets/img/icons/check-break.svg')}}" alt="">
                                                                </div>
                                                            @elseif($dashboard_calendar[$date][$break->break_num] == 'preordered')
                                                                <a @if(preorder_availability($this_week_full_dates[$date], $break, $minimum_preorder_minutes)) href="{{ route('application.choose_snack', ['date' => encrypt($this_week_full_dates[$date]), 'break_id' => encrypt($break->id) ]) }}" @endif class="pre-ordered" data-date="{{$this_week_full_dates[$date]}}"
                                                                   >
                                                                    <svg class="h-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15.62 14.55">
                                                                        <use xlink:href="{{static_asset('assets/img/icons/preorder-icon.svg')}}#content"></use>
                                                                    </svg>
                                                                    {{toUpper(translate('Preordered'))}}
                                                                </a>
                                                            @endif

                                                        @else

{{--                                                            @dd($date, $this_week_full_dates)--}}
                                                            @if($this_week_full_dates[$date]<$today->format('Y-m-d'))

                                                                <div class="no-orders">
                                                                    {{toUpper(translate('No Orders'))}}
                                                                </div>

                                                            @elseif($this_week_full_dates[$date]>=$today->format('Y-m-d'))

                                                                @if(preorder_availability($this_week_full_dates[$date], $break, $minimum_preorder_minutes))

                                                                    @if(!$next_date_available_found)

                                                                        <a href="{{ route('application.choose_snack', ['date' => encrypt($this_week_full_dates[$date]), 'break_id' => encrypt($break->id) ]) }}" class="order-now" data-date="{{$this_week_full_dates[$date]}}"
                                                                        >
                                                                            <svg class="h-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18.65 18.64">
                                                                                <use xlink:href="{{static_asset('assets/img/icons/order-now.svg')}}#content"></use>
                                                                            </svg>
                                                                            {{toUpper(translate('Order Now'))}}
                                                                        </a>

                                                                        @php $next_date_available_found = true; @endphp
                                                                    @else

                                                                        <a href="{{ route('application.choose_snack', ['date' => encrypt($this_week_full_dates[$date]), 'break_id' => encrypt($break->id) ]) }}"
                                                                           class="order-now later" data-date="{{$this_week_full_dates[$date]}}">
                                                                            <svg class="h-20px"
                                                                                 xmlns="http://www.w3.org/2000/svg"
                                                                                 viewBox="0 0 18.65 18.64">
                                                                                <use
                                                                                    xlink:href="{{static_asset('assets/img/icons/order-now.svg')}}#content"></use>
                                                                            </svg>
                                                                            {{toUpper(translate('Order Now'))}}
                                                                        </a>

                                                                    @endif

                                                                @else

                                                                    <div class="no-orders">
                                                                        {{toUpper(translate('No Orders'))}}
                                                                    </div>

                                                                @endif


                                                            @endif

                                                        @endif

                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif

                                    </div>
                                </div>

                                @php
                                    $counter++;
                                @endphp
                            @endforeach

                        </div>
                    </div>
                </div>

            </div>

            <div id="next_week" class="break-table next_week d-none">

                <div class="row no-gutters">
                    <div class="col-80px break-table-left">
                        <div class="break-table-row header" data-row="header">
                            <div class="break-table-col">
                                 <span>
                                      {{toUpper(translate('Breaks'))}} <span class="arrow"></span>
                                 </span>
                            </div>
                        </div>
                        <div class="break-table-body">

                            @php
                                $counter = 1
                            @endphp

                            @foreach($next_week_dates as $key => $day)
                                <div class="break-table-row" data-row="{{$counter}}">
                                    <div class="break-table-col">{{$day}}. {{$key}}</div>
                                </div>

                                @php   $counter++; @endphp
                            @endforeach

                        </div>
                    </div>
                    <div class="col-grow-80px break-table-right">
                        <div class="break-table-row header" data-row="header">
                            <div class="row no-gutters">

                                @foreach($breaks as $key => $break)
                                    <div class="col">
                                        <div class="break-table-col">{{ordinal($key+1)}}</div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                        <div class="break-table-body">
                            @php

                                $counter = 1;

                            @endphp
                            @foreach($next_week_dates as $date => $day)
                                <div class="break-table-row" data-row="{{$counter}}">
                                    <div class="row no-gutters">
                                        @if(in_array($next_week_full_dates[$date], $holidays))

                                            <div class="col" >
                                                <div class="break-table-col">
                                                    <div class="public-holiday">
                                                            <span>
                                                               <img class="h-13px mr-2" src="{{static_asset('assets/img/icons/happy-face.svg')}}" alt=""> {{toUpper(translate('Public Holiday'))}}
                                                             </span>
                                                    </div>
                                                </div>
                                            </div>

                                        @else

                                            @foreach($breaks as $key => $break)
                                                <div class="col">
                                                    <div class="break-table-col">

                                                        @if(isset($dashboard_calendar[$date][$break->break_num]))
                                                            @if($dashboard_calendar[$date][$break->break_num] == 'old')
                                                                <div class="checked">
                                                                    <img class="h-13px" src="{{static_asset('assets/img/icons/check-break.svg')}}" alt="">
                                                                </div>
                                                            @elseif($dashboard_calendar[$date][$break->break_num] == 'preordered')
                                                                <a @if(preorder_availability($next_week_full_dates[$date], $break, $minimum_preorder_minutes)) href="{{ route('application.choose_snack', ['date' => encrypt($next_week_full_dates[$date]), 'break_id' => encrypt($break->id) ]) }}" @endif class="pre-ordered"
                                                                   data-date="{{$next_week_full_dates[$date]}}"
                                                                >
                                                                    <svg class="h-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15.62 14.55">
                                                                        <use xlink:href="{{static_asset('assets/img/icons/preorder-icon.svg')}}#content"></use>
                                                                    </svg>
                                                                    {{toUpper(translate('Preordered'))}}
                                                                </a>
                                                            @endif

                                                        @else

                                                            @if(preorder_availability($next_week_full_dates[$date], $break, $minimum_preorder_minutes))

                                                                @if(!$next_date_available_found)

                                                                    <a href="{{ route('application.choose_snack', ['date' => encrypt($next_week_full_dates[$date]), 'break_id' => encrypt($break->id)]) }}" class="order-now" data-date="{{$next_week_full_dates[$date]}}"
                                                                      >
                                                                        <svg class="h-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18.65 18.64">
                                                                            <use xlink:href="{{static_asset('assets/img/icons/order-now.svg')}}#content"></use>
                                                                        </svg>
                                                                        {{toUpper(translate('Order Now'))}}
                                                                    </a>

                                                                    @php $next_date_available_found = true; @endphp
                                                                @else

                                                                    <a href="{{ route('application.choose_snack', ['date' => encrypt($next_week_full_dates[$date]), 'break_id' => encrypt($break->id) ]) }}"
                                                                       class="order-now later"  data-breakID="{{$break->id}}" data-breakKey="{{$key+1}}" data-date="{{$next_week_full_dates[$date]}}"
                                                                      >
                                                                        <svg class="h-20px"
                                                                             xmlns="http://www.w3.org/2000/svg"
                                                                             viewBox="0 0 18.65 18.64">
                                                                            <use
                                                                                xlink:href="{{static_asset('assets/img/icons/order-now.svg')}}#content"></use>
                                                                        </svg>
                                                                        {{toUpper(translate('Order Now'))}}
                                                                    </a>

                                                                @endif

                                                            @else

                                                                <div class="no-orders">
                                                                    {{toUpper(translate('No Orders'))}}
                                                                </div>

                                                            @endif

                                                        @endif

                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif

                                    </div>
                                </div>

                                @php
                                    $counter++;
                                @endphp
                            @endforeach

                        </div>
                    </div>
                </div>

            </div>

            <div class="break-table-controls my-5px py-2px fw-500 fs-12 bg-login-box">
                <div class="container">
                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <a href="javascript:void(0);" class="break-table-arrow prev"><span class="icon"></span> {{toUpper(translate('Previous Week'))}}</a>
                        </div>
                        <div class="col-auto">
                            <a href="javascript:void(0);" class="break-table-arrow next">{{toUpper(translate('View Next Week'))}} <span class="icon"></span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script type="text/javascript">

        let calendar_showing = 'current';
        const start_of_current_week = '{{Carbon::today()->startOfWeek()->format('Y-m-d')}}';
        const start_of_next_week = '{{Carbon::today()->addDays(7)->startOfWeek()->format('Y-m-d')}}';
        const start_of_last_week = '{{Carbon::today()->subDays(7)->startOfWeek()->format('Y-m-d')}}';

        let next_week_views = []; // next_week_views [2] => calendar view for after 2 weeks if caclucated
        let prev_week_views = []; // prev_week_views [2] => calendar view for befor 2 weeks if caclucated

        let prev_clicks = 0;
        let next_clicks = 0;

        const next_date_available_found = '{{$next_date_available_found}}';

        $(document).ready(function () {
            // console.log(start_of_last_week, start_of_current_week, start_of_next_week);
        });

        $(document).on('click', '.next', function () {

            prev_clicks = 0;

            var active_div = $('div.active');

            if(active_div.hasClass('current_week')){
                calendar_showing = 'current';
            }else if(active_div.hasClass('next_week') || active_div.hasClass('added-next-week')){
                calendar_showing = 'next';
            }else if(active_div.hasClass('prev_week') || active_div.hasClass('added-prev-week')){
                calendar_showing = 'prev';
            }

            console.log('active div: ', $('div.active'));
            console.log('active div next : ', active_div.nextAll(".break-table:first"));

            if(active_div.nextAll(".break-table:first").length > 0) {
                active_div.addClass('d-none');
                active_div.nextAll(".break-table:first").addClass('active');
                active_div.nextAll(".break-table:first").removeClass('d-none');
                active_div.removeClass('active');
            }

            if(calendar_showing == 'current'){

                next_clicks=1;

                // ajax to get the next week

                if(!next_week_views.includes(2)){

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type:"POST",
                        url: '{{ route('application.get_week_calendar_view') }}',
                        data: {
                            type: 'next',
                            weeks: 2, // from today
                            next_date_available_found: next_date_available_found
                        },
                        success: function(data){

                            console.log('data: ', data);

                            if(data.status == 1){

                                var lastDiv = $('div.next_week');
                                // Add a new div after the last div
                                lastDiv.after('<div class="break-table added-next-week d-none">' + data.view + '</div>');

                                next_week_views.push(2);

                            }else{

                                console.log('Something went wrong: ', data);

                            }

                        }
                    });

                }

                console.log('next_week_views: ', next_week_views);

                calendar_showing = 'next';

            }else if(calendar_showing == 'prev'){

                calendar_showing = 'current';

            }else if(calendar_showing == 'next'){

                next_clicks++;

                // else if na ine apo to pio next

                console.log('is in next with clicks = ', next_clicks);

                if(!next_week_views.includes(next_clicks+1)){

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type:"POST",
                        url: '{{ route('application.get_week_calendar_view') }}',
                        data: {
                            type: 'next',
                            weeks: next_clicks+1, // from today
                            next_date_available_found: next_date_available_found
                        },
                        success: function(data){

                            console.log('data: ', data);

                            if(data.status == 1){

                                var lastDiv = $('div.added-next-week:last');
                                // Add a new div after the last div
                                lastDiv.after('<div class="break-table added-next-week d-none">' + data.view + '</div>');

                                next_week_views.push(next_clicks+1);

                            }else{

                                console.log('Something went wrong: ', data);

                            }

                        }
                    });

                }

                console.log('next_week_views: ', next_week_views);

            }

        });

        $(document).on('click', '.prev', function () {

            next_clicks=0;

            var active_div = $('div.active');

            if(active_div.hasClass('current_week')){
                calendar_showing = 'current';
            }else if(active_div.hasClass('next_week') || active_div.hasClass('added-next-week')){
                calendar_showing = 'next';
            }else if(active_div.hasClass('prev_week') || active_div.hasClass('added-prev-week')){
                calendar_showing = 'prev';
            }

            console.log('active div: ', $('div.active'));
            console.log('active div prev : ', active_div.prevAll(".break-table:first"));

            if(active_div.prevAll(".break-table:first").length > 0){
                active_div.addClass('d-none');
                active_div.prevAll(".break-table:first").addClass('active');
                active_div.prevAll(".break-table:first").removeClass('d-none');
                active_div.removeClass('active');
            }

            if(calendar_showing == 'current') {
                prev_clicks=1;

                if(!prev_week_views.includes(2)){

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type:"POST",
                        url: '{{ route('application.get_week_calendar_view') }}',
                        data: {
                            type: 'prev',
                            weeks: 2, // from today
                            next_date_available_found: next_date_available_found
                        },
                        success: function(data){

                            console.log('data: ', data);

                            if(data.status == 1){

                                var lastDiv = $('div.prev_week');
                                // Add a new div after the last div
                                lastDiv.before('<div class="break-table added-prev-week d-none">' + data.view + '</div>');

                                prev_week_views.push(2);

                            }else{

                                console.log('Something went wrong: ', data);

                            }
                        }
                    });

                }

                calendar_showing = 'prev';

            }else if(calendar_showing == 'next'){

                calendar_showing = 'current';

            }else if(calendar_showing == 'prev'){

                prev_clicks++;

                console.log('is in prev with clicks = ', prev_clicks);

                if(!prev_week_views.includes(prev_clicks+1)){

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type:"POST",
                        url: '{{ route('application.get_week_calendar_view') }}',
                        data: {
                            type: 'prev',
                            weeks: prev_clicks+1, // from today
                            next_date_available_found: next_date_available_found
                        },
                        success: function(data){

                            console.log('data: ', data);

                            if(data.status == 1){

                                var lastDiv = $('div.added-prev-week:first');
                                // Add a new div after the last div
                                lastDiv.before('<div class="break-table added-prev-week d-none">' + data.view + '</div>');

                                prev_week_views.push(prev_clicks+1);

                            }else{

                                console.log('Something went wrong: ', data);

                            }
                        }
                    });
                }
            }
        });


    </script>

@endsection


