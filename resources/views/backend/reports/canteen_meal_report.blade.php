@extends('backend.layouts.app')

@section('content')
    <div class="sk-titlebar text-left mt-2 mb-3">
        <div class=" align-items-center">
            <h1 class="h3">{{translate('Canteen Meal Report')}}</h1>
        </div>
    </div>

    <?php

    use Carbon\Carbon;
    use App\Models\CanteenMenu;
    use App\Models\CanteenPurchase;
    use Illuminate\Support\Facades\DB;

    $count_selected_organisations = 0;

    $all_organisations = array();

    foreach (\App\Models\Organisation::where('canteen', 1)->get() as $key => $organisation) {
        $all_organisations[] = $organisation->id;
    }

    $all_organisations = json_encode($all_organisations);
    $selected_organisations = array();

    if (isset($response)) {
        foreach ($organisations as $organisation) {
            $selected_organisations [] = $organisation->id;
        }
    }

    $count_selected_organisations = count($selected_organisations);
    $selected_organisations = json_encode($selected_organisations);



    if (isset($response)) {
        $fileName = $start_carbon->format('d/m/Y') . " - " . $end_carbon->format('d/m/Y');

//        dd($start_carbon->format('d/m/Y'));
    }


    ?>

    <div class="row calc-height">
        <div class="col mx-auto">
            <div class="card">
                <div class="card-body row align-items-end">
                    <form class="form-horizontal col-9 " action="{{route('canteen_meal_reports.show')}}">
                        <div class="row align-items-end">

                            <div class="col-auto">
                                <div class="form-group mb-0">
                                    <label class="h6"> {{translate('Organisation')}} </label>
                                    <select id="demo-ease" class="sk-selectpicker w-300px d-block" name="organisation[]"
                                            multiple onchange="all_selected()">
                                        <option value="all">{{ translate('All') }}</option>
                                        @foreach (\App\Models\Organisation::where('canteen', 1)->get() as $key => $organisation)
                                            <option
                                                value="{{ $organisation->id }}">{{ $organisation->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-auto">
                                <label class="d-block" for="datefilter"><h6
                                        class="title l-space-05 opacity-80">{{translate('Date Filtering')}}</h6>
                                </label>
                                <div class="form-control-with-label always-focused" id="datefilterID">
                                    <input id="datefilter" name="datefilter" type="text"
                                           class="form-control w-100 fw-400 py-0"
                                           autocomplete="off"/>
                                </div>
                            </div>
                            <div class="col-auto">
                                <button type="submit"
                                        class="btn btn-primary disableButtonAfterFirstClick">{{ toUpper(translate('Submit')) }}</button>
                            </div>
                        </div>
                    </form>

                    @if(isset($response))
                        <div id="export-element" class="col-3 text-right">
                            {{--                                <button class="ml-3 btn btn-soft-primary w-200px" id="downloadexcel"--}}
                            {{--                                        onclick="excelExport()">--}}
                            {{--                                    {{translate('Export to Excel')}}--}}
                            {{--                                </button>--}}

                            <form class="canteen_reports" action="{{route('canteen_meal_reports.export')}}" method="POST" TARGET="_blank">
                                @csrf
                                <input type="hidden" name="timestamp"
                                       value="{{\Carbon\Carbon::now()->format('d/m/y H:i:S')}}">
                                <input type="hidden" name="organisations">
                                <input type="hidden" name="start_date" value="{{$start_date}}">
                                <input type="hidden" name="end_date" value="{{$end_date}}">
                                <button class="ml-3 btn btn-soft-primary w-200px">
                                    {{translate('Export to Excel')}}
                                </button>
                            </form>


                        </div>

                    @endif

                </div>

                @if(isset($response))

                    @foreach($organisations as $organisation)

                        @php

                            $canteen_settings = $organisation->current_canteen_settings();
                            $breaks = $organisation->breaks;
                            $canteen_users = \App\Models\CanteenAppUser::select('canteen_app_users.*', 'cards.rfid_no', 'cards.name as card_name', 'cards.organisation_id')
                                        ->join('cards', 'cards.id', '=', 'canteen_app_users.card_id')
                                        ->where('cards.organisation_id', $organisation->id)
                                        ->get();


                            $purchases = CanteenPurchase::select('canteen_products.name as product_name', 'canteen_purchases.canteen_app_user_id', 'canteen_purchases.date', 'canteen_purchases.break_num', DB::raw('SUM(canteen_purchases.quantity) as total_quantity'))
                                ->join('canteen_products', 'canteen_products.id', '=', 'canteen_purchases.canteen_product_id')
                                ->where('canteen_purchases.date', '>=', $start_date)
                                ->where('canteen_purchases.date', '<=', $end_date)
                                ->where('canteen_purchases.canteen_setting_id', $canteen_settings->id)
                                ->groupBy('canteen_purchases.canteen_app_user_id', 'canteen_purchases.canteen_product_id','canteen_purchases.date', 'canteen_purchases.break_num')
                                ->get();


                            $purchase_totals = CanteenPurchase::select('canteen_products.name as product_name','canteen_purchases.canteen_app_user_id', 'canteen_purchases.date', 'canteen_purchases.break_num', DB::raw('SUM(canteen_purchases.quantity) as total_quantity'))
                                ->join('canteen_products', 'canteen_products.id', '=', 'canteen_purchases.canteen_product_id')
                                ->where('canteen_purchases.date', '>=', $start_date)
                                ->where('canteen_purchases.date', '<=', $end_date)
                                ->where('canteen_purchases.canteen_setting_id', $canteen_settings->id)
                                ->groupBy('canteen_purchases.canteen_app_user_id','canteen_purchases.date', 'canteen_purchases.break_num')
                                ->get();


                            $count_breaks = count($breaks);



//                            dd($purchases, $purchase_totals, $canteen_users);

                        @endphp

                        <h6 class="mx-3">{{toUpper($organisation->name)}}</h6>

                        <div id="table-scroll" class="table-scroll mb-3 margin-auto-none">
                            <div class="table-wrap">
                                <table id="table_data" class=" main-table text-center w-auto ml-20px mr-0 py-3">
                                    <thead>
                                    <tr class="h-30px">
                                        <th class="fixed-side min-w-200px border-table">{{translate('Date')}}</th>
                                        @foreach($dates as $key => $date)
                                            <th class="min-w-200px border-table"
                                                colspan="{{count($breaks)}}">{{ $date_names[$key] }}. {{$date}}</th>
                                        @endforeach

                                    </tr>
                                    <tr class="h-30px">
                                        <th class="fixed-side min-w-200px border-table">{{translate('Break')}}</th>
                                        @foreach($dates as $date)
                                            @if($count_breaks>0)
                                                @foreach($breaks as $break)
                                                    <th class=" border-table" colspan="1">{{$break->break_num}}
                                                    </th>
                                                @endforeach

                                            @else
                                                <th class=" border-table" colspan="1">
                                                </th>
                                            @endif
                                        @endforeach
                                    </tr>

                                    </thead>
                                    <tbody>


                                    @foreach($canteen_users as $user)
                                        <tr class="h-30px text">
                                            <th class="fixed-side min-w-200px border-table fw-400">
                                                @if($user->card_name != null)
                                                    <span>{{$user->card_name}}</span>
                                                @else
                                                    <span>{{$user->username}}</span>
                                                @endif

                                            </th>

                                            @foreach($dates as $key => $date)
                                                @if($count_breaks>0)
                                                    @foreach($breaks as $break)

                                                        @if(count($purchases)>0)
                                                            @php

                                                                $flag = 0;
                                                            @endphp
                                                            <td class=" border-table">
                                                            @foreach($purchases as $purchase)
                                                                @if($purchase->date == $formatted_dates[$key] && $purchase->break_num == $break->break_num && $purchase->canteen_app_user_id == $user->id)
                                                                    @php
                                                                        $flag = 1;
                                                                    @endphp
                                                                   <span class="d-block">({{$purchase->total_quantity}}) {{$purchase->product_name}} </span>
                                                                @endif
                                                            @endforeach
                                                            @if($flag==0)
                                                                    <span class="d-block"> - </span>
                                                                @endif
                                                            </td>

                                                        @else
                                                            <td class="border-table">-</td>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <td class="border-table">-</td>
                                                @endif
                                            @endforeach

                                        </tr>
                                    @endforeach

                                    <tr class="h-30px">
                                        <th class="fixed-side min-w-200px border-table">{{translate('Totals')}}</th>

                                        @foreach($dates as $key => $date)
                                            @if($count_breaks>0)
                                                @foreach($breaks as $break)

                                                    @if(count($purchase_totals)>0)
                                                        @php
                                                            $flag = 0;
                                                        @endphp
                                                        @foreach($purchase_totals as $purchase)
                                                            @if($purchase->date == $formatted_dates[$key] && $purchase->break_num == $break->break_num )
                                                                @php
                                                                    $flag = 1;
                                                                @endphp
                                                                <td class=" border-table">{{$purchase->total_quantity}}</td>
                                                                @break
                                                            @endif
                                                        @endforeach
                                                        @if($flag==0)
                                                            <td class="border-table">0</td>
                                                        @endif

                                                    @else
                                                        <td class="border-table">0</td>
                                                    @endif
                                                @endforeach
                                            @else
                                                <td class="border-table">0</td>
                                            @endif

                                        @endforeach

                                    </tr>

                                    <tr class="h-30px" style="display: none">
                                        <th class=" fixed-side min-w-200px border-table"></th>
                                        <td class=" border-table"></td>
                                        <td class=" border-table"></td>
                                    </tr>

                                    </tbody>
                                </table>

                            </div>
                        </div>

                    @endforeach

                @endif

            </div>
        </div>



        <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment-with-locales.min.js"></script>
        <script type="text/javascript"
                src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

        <script type="text/javascript">

            let old_select = [];

            function show(){
                console.log($('#hourpicker').val());
                alert($('#hourpicker').val());
            }

            $(function () {

                var start = moment().startOf('year');
                var end = moment().endOf('year');

                start = moment(start, 'X')
                end = moment(end, 'X')

                console.log(start, end);
                function cb(start, end) {
                    // $('input[name="datefilter"] span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    // $('input[name="datefilter"] span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                }

                moment.locale('es');


                $('input[name="datefilter"]').daterangepicker({
                    // maxDate: moment().endOf('day'),
                    // minDate: moment().endOf('day'),
                    // timePicker: true,

                    @if(isset($start_carbon))
                    startDate: '{{$start_carbon->format('d/m/Y')}}'
                    @else
                    startDate: start
                    @endif,

                    @if(isset($end_carbon))
                    endDate: '{{$end_carbon->format('d/m/Y')}}'
                    @else
                    endDate: end
                    @endif,
                    {{--locale: {!! config('app.dateRangePicke-en') !!},--}}
                    locale: {
                        format: 'DD/MM/YYYY'
                    },
                    ranges: {
                        'Today': [moment(), moment()],
                        'Current Month': [moment().startOf('month'), moment().endOf('month')],
                        'Current Year': [moment().startOf('year'), moment().endOf('year')],
                        'Next Month': [moment().add(1, 'month').startOf('month'), moment()
                            .add(1, 'month').endOf('month')
                        ]
                    }
                }, cb);


                cb(start, end);

                // console.log($('input[name="datefilter"]').val());

            });

            function all_selected(){

                // console.log('old_select:', old_select);

                var new_val = [];

                if(old_select.includes('all')){

                    for(var i=0; i<($('#demo-ease').val()).length; i++){
                        if(i>0){
                            new_val.push(($('#demo-ease').val())[i]);
                        }
                    }

                    $('#demo-ease').val(new_val);
                    old_select = $('#demo-ease').val();
                    $('#demo-ease').selectpicker('refresh');

                }else if(($('#demo-ease').val()).includes('all')){
                    $('#demo-ease').val(['all']);

                    $('#demo-ease').selectpicker('refresh');

                    old_select = $('#demo-ease').val();
                }

                // console.log('final val: ', $('#demo-ease').val())
            }

            @if(isset($response))

            $(function () {
                @if($count_selected_organisations == count(\App\Models\Organisation::all()))
                $('#demo-ease').val(['all']);
                console.log('$count_selected_organisations');
                @else

                $('#demo-ease').val({{$selected_organisations}});
                @endif
                $('.canteen_reports input[name=organisations]').val({{$selected_organisations}});

                $('#demo-ease').selectpicker('refresh');

            });

            $(document).ready(function () {

                $(".main-table").each(function(index, element) {


                    var parent = $(element).parents('div.table-scroll').first();
                    $(element).clone(true).appendTo(parent).addClass('clone');

                    // console.log($(element), parent);

                });
                // $(".main-table").clone(true).appendTo('#table-scroll').addClass('clone');
            });

            $(document).on('change', '#demo-ease', function (){
                if($('#export-element').length > 0){
                    $('#export-element').remove();
                }
            });

            $(document).on('click', 'div.daterangepicker li, div.daterangepicker .applyBtn', function (){
                if($('#export-element').length > 0){
                    $('#export-element').remove();
                }
            });

            @endif

        </script>

@endsection
