
@extends('backend.layouts.app')

@section('content')
    <div class="sk-titlebar text-left mt-2 mb-3">
        <div class=" align-items-center">
            <h1 class="h3">{{translate('Catering Report')}}</h1>
        </div>
    </div>
    <?php

    $total_snack = 0;
    $total_lunch = 0;

    $all_organisations= array();

    foreach (\App\Models\Organisation::all() as $key => $organisation) {
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
        $fileName = $start_date_string . " - " . $end_date_string;
    }


    ?>

    <div class="row calc-height catering_report">
        <div class="col mx-auto">
            <div class="card">
                <form action="{{route('catering_reports.show')}}">
                    <div class="card-body row align-items-end">
                        <div class="col-9 row align-items-end">

                            <div class="col-auto">
                                <div class="form-group mb-0">
                                    <label class="h6"> {{translate('Organisation')}} </label>
                                    <select id="demo-ease" class="sk-selectpicker w-300px d-block" name="organisation[]"
                                            required multiple onchange="all_selected()">
                                        <option value="all">{{ translate('All') }}</option>
                                        @foreach (\App\Models\Organisation::all() as $key => $organisation)
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

                            {{--                            </div>--}}
                        </div>

                        @if(isset($response))
                            <div class="col-3 text-right">
                                <button class="ml-3 btn btn-soft-primary w-200px" id="downloadexcel"
                                        onclick="excelExport()">
                                    {{translate('Export to Excel')}}
                                </button>

                            </div>

                        @endif

                    </div>
                </form>


                @if(isset($response))

                    <div id="table-scroll" class="table-scroll mb-3">
                        <div class="table-wrap">
                            <table id="table_data" class=" main-table text-center  py-3">
                                <thead>
                                <tr class="h-30px">
                                    <th class="fixed-side min-w-200px border-table "></th>
                                    @foreach($dates as $date)
                                        <th class="min-w-200px border-table" colspan="2">{{$date}}</th>
                                    @endforeach

                                </tr>
                                <tr class="h-30px">
                                    <th class=" fixed-side min-w-200px border-table">Organisation Name</th>
                                    @for($i=0; $i<$day_count; $i++)
                                        <th class="text border-table">Snack</th>
                                        <th class=" border-table">Lunch</th>
                                    @endfor


                                </tr>
                                </thead>
                                <tbody>

                                    <?php

                                    $c = 0;

                                    ?>

                                @foreach($organisations as $organisation)
                                    <tr class="h-30px text">
                                        <th class="fixed-side min-w-200px border-table text-red" ><span>{{$organisation->name}}</span>
                                            <span style="color: #eee">--</span>
                                        </th>

                                        @for($i=$c; $i<$day_count+$c; $i++)

                                            <td class=" border-table">{{$response[$i]['snack']}}</td>
                                            <td class=" border-table">{{$response[$i]['meal']}}</td>

                                        @endfor

                                            <?php

                                            $c = $c + $day_count;

                                            ?>
                                    </tr>
                                @endforeach

                                <tr class="h-30px">
                                    <th class=" fixed-side min-w-200px border-table">{{translate('Totals')}}</th>

                                    @foreach($totals as $total)

                                        <td class=" border-table">{{$total['snack']}}</td>
                                        <td class=" border-table">{{$total['meal']}}</td>


                                            <?php
                                            $total_snack = $total_snack + $total['snack'];
                                            $total_lunch = $total_lunch + $total['meal'];
                                            ?>
                                    @endforeach

                                </tr>

                                <tr class="h-30px" style="display: none">
                                    <th class=" fixed-side min-w-200px border-table"></th>


                                    <td class=" border-table"></td>
                                    <td class=" border-table"></td>

                                </tr>

                                <tr class="h-30px" style="display: none">
                                    <th class=" fixed-side min-w-200px border-table">{{translate('Snack Total')}}</th>

                                    <td class=" border-table">{{$total_snack}}</td>

                                </tr>

                                <tr class="h-30px" style="display: none">
                                    <th class=" fixed-side min-w-200px border-table">{{translate('Lunch Total')}}</th>

                                    <td class=" border-table">{{$total_lunch}}</td>

                                </tr>


                                </tbody>
                            </table>

                        </div>
                    </div>

                @endif

            </div>
        </div>



        <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment-with-locales.min.js"></script>
        <script type="text/javascript"
                src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

        <script type="text/javascript">

            let old_select = [];

            $(function () {

                @if(isset($start_date_string))
                    console.log('$start_date_string: ', '{{$start_date_string}}');
                @endif

                var start = moment().startOf('year');
                var end = moment().endOf('year');

                start = moment(start, 'X')
                end = moment(end, 'X')

                function cb(start, end) {
                    // $('input[name="datefilter"] span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    // $('input[name="datefilter"] span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                }

                moment.locale('es');
                $('input[name="datefilter"]').daterangepicker({
                    minDate: moment().endOf('day'),

                    @if(isset($start_date_string))
                    startDate: '{{$start_date_string}}'
                    @else
                    startDate: start
                    @endif,

                    @if(isset($end_date_string))
                    endDate: '{{$end_date_string}}'
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

                console.log($('input[name="datefilter"]').val());

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


                $('#demo-ease').selectpicker('refresh');

            });

            function excelExport() {

                var fileName = $('#datefilter').val();

                var table2excel = new Table2Excel({
                    defaultFileName: 'Catering Report ' + '{{$fileName}}',
                    align: 'center',
                    preserveColors: true,
                    fileext: ".xls",
                    preserveFont:true,
                });

                table2excel.export(document.querySelectorAll("#table_data"));

            }

            jQuery(document).ready(function () {
                jQuery(".main-table").clone(true).appendTo('#table-scroll').addClass('clone');
            });

            @endif

        </script>

@endsection


