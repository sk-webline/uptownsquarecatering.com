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

                use Carbon\Carbon;

                $today = Carbon::today();
                $counter = 1

            @endphp

            @foreach($this_week_dates as $key => $day)
                <div class="break-table-row" data-row="{{$counter}}">
                    <div class="break-table-col">{{$day}}. {{$key}}</div>
                </div>

                @php
                    $counter++;
                @endphp

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
//                    $next_date_available_found = false;


            @endphp
            @foreach($this_week_dates as $date => $day)
                <div class="break-table-row" data-row="{{$counter}}">
                    <div class="row no-gutters">
                        @if(in_array($this_week_full_dates[$date], $holidays))

                            <div class="col" >
                                <div class="break-table-col">
                                    <div class="public-holiday">
                                        <span><img class="h-13px mr-2" src="{{static_asset('assets/img/icons/happy-face.svg')}}" alt=""> {{toUpper(translate('Public Holiday'))}}</span>
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
                                                <a @if(preorder_availability($this_week_full_dates[$date], $break, $minimum_preorder_minutes )) href="{{ route('application.choose_snack', ['date' => encrypt($this_week_full_dates[$date]), 'break_id' => encrypt($break->id) ]) }}" @endif class="pre-ordered">
                                                    <svg class="h-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15.62 14.55">
                                                        <use xlink:href="{{static_asset('assets/img/icons/preorder-icon.svg')}}#content"></use>
                                                    </svg>
                                                    {{toUpper(translate('Preordered'))}}
                                                </a>
                                            @endif

                                        @else

                                            @if($type == 'prev')

                                                <div class="no-orders">
                                                    {{toUpper(translate('No Orders'))}}
                                                </div>

                                            @elseif($type == 'next')

                                                @if(preorder_availability($this_week_full_dates[$date], $break, $minimum_preorder_minutes ))

                                                    @if(!$next_date_available_found)

                                                        <a href="{{ route('application.choose_snack', ['date' => encrypt($this_week_full_dates[$date]), 'break_id' => encrypt($break->id) ]) }}" class="order-now">
                                                            <svg class="h-20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18.65 18.64">
                                                                <use xlink:href="{{static_asset('assets/img/icons/order-now.svg')}}#content"></use>
                                                            </svg>
                                                            {{toUpper(translate('Order Now'))}}
                                                        </a>

                                                        @php $next_date_available_found = true; @endphp
                                                    @else

                                                        <a href="{{ route('application.choose_snack', ['date' => encrypt($this_week_full_dates[$date]), 'break_id' => encrypt($break->id) ]) }}"
                                                           class="order-now later">
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
