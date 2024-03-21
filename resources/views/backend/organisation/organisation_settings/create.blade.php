@extends('backend.layouts.app')

@section('content')


    <div class="row">
        <div class="col-lg-8 mx-auto">
            <form class="form-horizontal"
                  action="{{ route('organisation_settings.store', ['organisation_id'=>$organisation->id] ) }}"
                  method="POST" enctype="multipart/form-data">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6"> <a href="{{route('organisations.index')}}" class="text-black" >{{translate('Organisations')}} </a> > {{$organisation->name}} >
                            <a href="{{route('catering.index', $organisation->id)}}" class="text-black" >{{translate('Periods')}} </a> >
                            {{translate('Add New Catering Period')}}</h5>
                    </div>

                    <div class="card-body">

                        @csrf

                        <div class="card p-15px">

                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">{{translate('Load Setting from Catering Periods')}}</label>
                                <div class="col-md-2">
                                    <div class="form-group row">
                                        <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                            <input type="checkbox" name="sync_periods">
                                            <input type="hidden" name="sync_periods_complete" value="0">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-5 sync-period d-none">
                                    <div class="form-group row">

                                        <select class="select2 form-control sk-selectpicker" name="periods_sync_select" data-toggle="select2" data-placeholder="Choose ...">
                                            @php
                                                $canteen_settings = \App\Models\CanteenSetting::where('organisation_id', $organisation->id)->where('date_to', '>', \Carbon\Carbon::today()->format('Y-m-d'))->get();  // catering periods
                                            @endphp
                                            <option hidden value="">{{translate('Select Catering Period')}}</option>
                                            @foreach ($canteen_settings as $period)
                                                <option value="{{$period->id}}">{{ \Carbon\Carbon::create($period->date_from)->format('d/m/Y')}} -  {{ \Carbon\Carbon::create($period->date_to)->format('d/m/Y')}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Start Date')}}</label>
                            <div class="col-md-9">
                                <input type="date" id="start_date" name="start_date" data-date-format="DD/MM/YYYY"
                                       class="form-control dd_mm_formatted" onchange="reCreateCalendar()" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('End Date')}}</label>
                            <div class="col-md-9">
                                <input type="date" id="end_date" name="end_date" class="form-control dd_mm_formatted" data-date-format="DD/MM/YYYY" onchange="reCreateCalendar()" required>

                                <span class="text-danger mt-2" id="date-warning" style="display: none">End Date should be greater than Start Date.</span>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <button type="button" class="btn btn-soft-secondary"
                                    onclick="reCreateCalendar()"> {{translate('Set Dates')}} </button>
                        </div>


                        @if($organisation->catering==1)


                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{translate('Working Days')}}</label>
                                <div class="col-md-9">

                                    <div class="form-group row">
                                        <label class="w-90px col-form-label">{{translate('Monday')}}</label>

                                        <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                            <input type="checkbox" name="monday" id="monday" value=0
                                                   onchange="checkMonday()">
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="w-90px col-form-label">{{translate('Tuesday')}}</label>

                                        <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                            <input type="checkbox" name="tuesday" onchange="checkTuesday()">
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="w-90px col-form-label">{{translate('Wednesday')}}</label>

                                        <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                            <input type="checkbox" name="wednesday" onchange="checkWednesday()">
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="w-90px col-form-label">{{translate('Thursday')}}</label>

                                        <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                            <input type="checkbox" name="thursday" onchange="checkThursday()">
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="w-90px col-form-label">{{translate('Friday')}}</label>
                                        <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                            <input type="checkbox" name="friday" onchange="checkFriday()">
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="w-90px col-form-label">{{translate('Saturday')}}</label>

                                        <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                            <input type="checkbox" name="saturday" onchange="checkSaturday()">
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="w-90px col-form-label">{{translate('Sunday')}}</label>

                                        <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                            <input type="checkbox" name="sunday" onchange="checkSunday()">
                                            <span></span>
                                        </label>
                                    </div>

                                </div>
                            </div>

                            <div class="py-10px">
                                <h5>{{translate('Holidays')}}</h5>
                            </div>

                            <div id='calendar' class="mt-2 mb-3 mx-auto h-500px" style="height: 500px">
                                <input type="hidden" name="holidays[]" id="holidays" multiple>
                            </div>

                            <div class="extra-days-loaded py-20px d-none">

                                <h5>{{translate('Extra Days')}} <span class="fs-14 fw-400">({{translate('Extra Days can only be modified on Canteen Period Editing')}})</span></h5>
                                <div class="extra-days-list fs-14">
                                    <ul>
                                    </ul>
                                </div>

                                <input type="hidden" name="extra_days"  id="extra_days" multiple>
                            </div>

                            <div class="form-group row ">
                                <label class="col-md-3 col-form-label">{{translate('Max Snack Quantity')}}</label>
                                <div class="col-md-9">
                                    <input type="number" placeholder="{{translate('Max Snack Quantity')}}"
                                           onchange="setMaxQuantitySnack()" id="max_snack_quantity"
                                           name="max_snack_quantity" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{translate('Max Lunch Quantity')}}</label>
                                <div class="col-md-9">
                                    <input type="number" placeholder="{{translate('Max Lunch Quantity')}}"
                                           onchange="setMaxQuantityMeal()" id="max_meal_quantity"
                                           name="max_meal_quantity" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{translate('Absence')}}</label>
                                <div class="col-md-9">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="absence" onchange="showAbsenceDays()">
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row" id="absence_div" style="display: none;">
                                <label class="col-md-3 col-form-label">{{translate('Absence Min Days Warning')}}</label>
                                <div class="col-md-9">
                                    <input type="number" placeholder="{{translate('Absence Min Days Warning')}}"
                                           required id="absence_days" name="absence_days" class="form-control" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-md-3 col-form-label">{{translate('Min Days before to Place Order')}}</label>
                                <div class="col-md-9">
                                    <input type="number" placeholder="{{translate('Min Days before to Place Order')}}"
                                           id="preorder_days_num" name="preorder_days_num"
                                           class="form-control">
                                </div>
                            </div>

                        @endif

                    </div>

                    <div>

                    </div>

                    <div class="form-group mb-2 mr-4 text-right">
                        <a href="{{route('catering.index', $organisation->id)}}">
                            <button type="button" class="btn btn-soft-danger">{{translate('Cancel')}}</button>
                        </a>
                        <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                    </div>

                </div>

            </form>
        </div>
    </div>

@endsection

@section('script')

    <script type="text/javascript">

        var calStartDate, calEndDate, my_start,my_end ;

        var holidays = [];

        const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

        var businessDays = [];

        let pr_cards = 1;

        let lan = '{{App::getLocale()}}';

        let events = [];

        document.addEventListener('DOMContentLoaded', function () {

            if(lan=='gr'){
                lan = 'el';
            }

            // import interactionPlugin from '@fullcalendar/interaction';
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: lan,
                headerToolbar: {
                    left: "customPrev",
                    center: "title",
                    right: "customNext",
                },
                initialView: "multiMonth", // Προβολή αρχικά σε Multiview με διάρκεια 2 μηνών
                initialDate: moment().format("YYYY-MM-DD"),
                multiMonthMaxColumns: 2,
                duration: { months: 2 },
                dateIncrement: { months: 1 },
                selectable: false,
                businessHours: {
                    // days of week. an array of zero-based day of week integers (0=Sunday)
                    daysOfWeek: businessDays, // Monday - Thursday

                },
                customButtons: {
                    customPrev: {
                        text: '{{translate("Prev Month")}}' ,
                        click: function() {
                            calendar.prev();
                        }
                    },
                    customNext: {
                        text:  '{{translate("Next Month")}}' ,
                        click: function() {
                            calendar.next();
                        }
                    },
                },


            });
            calendar.render();
        });

        function reCreateCalendar() {

            my_start = $('#start_date').val();
            my_end = $('#end_date').val();

            console.log('my_start: ',my_start, my_start.length);
            console.log('my_end: ',my_end, my_end.length);

            if((my_start=='' || my_start.length<=0 ) && (my_end.length<=0 || my_end=='')){
                console.log('not in: ');
                return;
            }

            if ((my_start=='' || my_end=='') || my_start >= my_end) {
                $('#date-warning').show();
            } else {

                $('#date-warning').hide();
                console.log('goes in: ');

                var calendarEl = document.getElementById("calendar");
                $('#calendar').children('div').remove();
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    locale: lan,
                    headerToolbar: {
                        left: "customPrev",
                        center: "title",
                        right: "customNext",
                    },
                    initialView: "multiMonth", // Προβολή αρχικά σε Multiview με διάρκεια 2 μηνών
                    initialDate: moment(my_start).format("YYYY-MM-DD"),
                    multiMonthMaxColumns: 2,
                    duration: { months: 2 },
                    dateIncrement: { months: 1 },
                    validRange: {
                        start: moment(my_start).format("YYYY-MM-DD"),
                        end: moment(my_end).add(1, "day").format("YYYY-MM-DD"),
                    },
                    businessHours: {
                        // days of week. an array of zero-based day of week integers (0=Sunday)
                        daysOfWeek: businessDays, // Monday - Thursday

                    },
                    selectable: true,
                    dateClick: function (info) {

                        var day = (new Date(info.dateStr).getDay());

                        for (var j = 0; j < businessDays.length; j++) {
                            if (day === businessDays[j]) {

                                if (info.dateStr >= $('input[name="start_date"]').val() && info.dateStr <= $('input[name="end_date"]').val()) {

                                    if (info.dayEl.style.backgroundColor === 'red') {
                                        console.log('gia');

                                        for (var i = 0; i < holidays.length; i++) {
                                            if (holidays[i] === info.dateStr) {
                                                holidays.splice(i, 1);


                                            }
                                        }

                                        (calendar.getEventById(info.dateStr)).remove();

                                        // calendar.removeEvent(  {
                                        //     id: info.dateStr,
                                        //     start: info.dateStr,
                                        //     display: 'background',
                                        //     color: 'red'
                                        // });

                                        info.dayEl.style.backgroundColor = 'white';

                                    } else {
                                        // change the day's background color just for fun
                                        info.dayEl.style.backgroundColor = 'red';

                                        calendar.addEvent(  {
                                            id: info.dateStr,
                                            start: info.dateStr,
                                            display: 'background',
                                            color: 'red'
                                        });

                                        holidays.push(info.dateStr);
                                    }

                                    document.getElementById('holidays').value = holidays;


                                }

                            }
                        }

                        // console.log(holidays);
                    },
                    datesSet: function (dateInfo) {
                        var prevButton = calendarEl.querySelector(".fc-customPrev-button");
                        var nextButton = calendarEl.querySelector(".fc-customNext-button");
                        nextButton.disabled = false;
                        prevButton.disabled = false;
                        if  (calendar.view.currentEnd >= new Date(my_end)) {
                            console.log('Disable Next button: '+calendar.view.currentEnd+' - '+new Date(moment(my_end).add(1, "day")));
                            if (nextButton) {
                                nextButton.disabled = true;
                            }
                        }
                        else if (calendar.view.currentStart <= new Date(my_start)) {
                            console.log('Disable prev button: '+calendar.view.currentStart+' - '+new Date(my_start));
                            if (prevButton) {
                                prevButton.disabled = true;
                            }
                        }
                    },
                    customButtons: {
                        customPrev: {
                            text: '{{translate("Prev Month")}}' ,
                            click: function() {
                                calendar.prev();
                            }
                        },
                        customNext: {
                            text:  '{{translate("Next Month")}}' ,
                            click: function() {
                                calendar.next();
                            }
                        },
                    },
                    events: events
                });


                if(moment(my_start).format("M")==moment(my_end).format("M")){
                    calendar.changeView('dayGridMonth');
                }



                calendar.render();

            }

        }


        function showAbsenceDays() {
            if (!$('input[name="absence"]').prop('checked')) {
                $('#absence_div').hide();
                $('input[name="absence_days"]').prop("disabled", true);
            } else {
                $('#absence_div').show();
                $('input[name="absence_days"]').prop("disabled", false);
            }

            console.log($('input[name="absence"]').val());
        }

        function checkSunday() {
            if (!$('input[name="sunday"]').prop('checked')) {
                for (var i = 0; i < businessDays.length; i++) {
                    if (businessDays[i] === 0) {
                        businessDays.splice(i, 1);
                    }
                }
            } else {
                businessDays.push(0);
            }

            reCreateCalendar();
        }

        function checkMonday() {
            if (!$('input[name="monday"]').prop('checked')) {
                for (var i = 0; i < businessDays.length; i++) {
                    if (businessDays[i] === 1) {
                        businessDays.splice(i, 1);
                    }
                }
            } else {
                businessDays.push(1);
            }

            reCreateCalendar();
        }

        function checkTuesday() {
            if (!$('input[name="tuesday"]').prop('checked')) {
                for (var i = 0; i < businessDays.length; i++) {
                    if (businessDays[i] === 2) {
                        businessDays.splice(i, 1);
                    }
                }
            } else {
                businessDays.push(2);
            }

            reCreateCalendar();
        }

        function checkWednesday() {
            if (!$('input[name="wednesday"]').prop('checked')) {
                for (var i = 0; i < businessDays.length; i++) {
                    if (businessDays[i] === 3) {
                        businessDays.splice(i, 1);
                    }
                }
            } else {
                businessDays.push(3);
            }

            reCreateCalendar();
        }

        function checkThursday() {
            if (!$('input[name="thursday"]').prop('checked')) {
                for (var i = 0; i < businessDays.length; i++) {
                    if (businessDays[i] === 4) {
                        businessDays.splice(i, 1);
                    }
                }
            } else {
                businessDays.push(4);
            }

            reCreateCalendar();
        }

        function checkFriday() {
            if (!$('input[name="friday"]').prop('checked')) {
                for (var i = 0; i < businessDays.length; i++) {
                    if (businessDays[i] === 5) {
                        businessDays.splice(i, 1);
                    }
                }
            } else {
                businessDays.push(5);
            }

            reCreateCalendar();
        }

        function checkSaturday() {
            if (!$('input[name="saturday"]').prop('checked')) {
                for (var i = 0; i < businessDays.length; i++) {
                    if (businessDays[i] === 6) {
                        businessDays.splice(i, 1);
                    }
                }
            } else {
                businessDays.push(6);
            }

            reCreateCalendar();
        }


        $(document).on('click', 'input[name=sync_periods]', function(){

            if($(this).prop('checked')==true){
                $('div.sync-period').removeClass('d-none');

            }else{
                $('div.sync-period').addClass('d-none');
                $('select[name=periods_sync_select]').val('');
                $('select[name=periods_sync_select]').selectpicker('refresh');

                // SK.plugins.bootstrapSelect();
                // recreate calendar from start
                createCalendarFromStart();
            }
        });

        function createCalendarFromStart(){
            businessDays = [];
            events = [];
            holidays = [];
            $('#extra_days').val(null);
            document.getElementById('holidays').value = [];
            $('input[name=start_date]').val('');
            $('input[name=end_date]').val('');

            $('input[name=start_date]').attr("data-date", '');
            $('input[name=end_date]').attr("data-date", '');

            $('input[name=monday]').prop('checked', false);
            $('input[name=tuesday]').prop('checked', false);
            $('input[name=wednesday]').prop('checked', false);
            $('input[name=thursday]').prop('checked', false);
            $('input[name=friday]').prop('checked', false);
            $('input[name=saturday]').prop('checked', false);
            $('input[name=sunday]').prop('checked', false);

            $('.days-list ul').empty();
            $('div.extra-days-loaded').addClass('d-none');

            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: lan,
                headerToolbar: {
                    left: "customPrev",
                    center: "title",
                    right: "customNext",
                },
                initialView: "multiMonth", // Προβολή αρχικά σε Multiview με διάρκεια 2 μηνών
                initialDate: moment().format("YYYY-MM-DD"),
                multiMonthMaxColumns: 2,
                duration: { months: 2 },
                dateIncrement: { months: 1 },
                selectable: false,
                businessHours: {
                    // days of week. an array of zero-based day of week integers (0=Sunday)
                    daysOfWeek: businessDays, // Monday - Thursday

                },
                customButtons: {
                    customPrev: {
                        text: '{{translate("Prev Month")}}' ,
                        click: function() {
                            calendar.prev();
                        }
                    },
                    customNext: {
                        text:  '{{translate("Next Month")}}' ,
                        click: function() {
                            calendar.next();
                        }
                    },
                },


            });
            calendar.render();
        }

        $(document).on('change', 'select[name=periods_sync_select]', function(){

            //create calendar based on the selected period
            //send ajax to get all details of this catering period

            console.log('select val: ', $('select[name=periods_sync_select]').val());

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{route('canteen_settings.get_settings_details')}}',
                data: {
                    organisation_id: '{{$organisation->id}}',
                    canteen_setting_id: $('select[name=periods_sync_select]').val(),
                },
                dataType: "JSON",
                success: function (data) {
                    console.log('period data: ', data);

                    if(data.status == 1){
                        //create calendar based on this data

                        $('input[name=start_date]').val(data.start_date);
                        $('input[name=end_date]').val(data.end_date);

                        $('input[name=start_date]').attr("data-date",  moment(data.start_date, "YYYY-MM-DD").format($('input[name=start_date]').attr("data-date-format")));
                        $('input[name=end_date]').attr("data-date",  moment(data.end_date, "YYYY-MM-DD").format($('input[name=end_date]').attr("data-date-format")));

                        businessDays = [];
                        events = [];

                        $('input[name=monday]').prop('checked', false);
                        $('input[name=tuesday]').prop('checked', false);
                        $('input[name=wednesday]').prop('checked', false);
                        $('input[name=thursday]').prop('checked', false);
                        $('input[name=friday]').prop('checked', false);
                        $('input[name=saturday]').prop('checked', false);
                        $('input[name=sunday]').prop('checked', false);

                        for(var i=0; i<(data.working_week_days).length; i++){
                            if((data.working_week_days)[i] == "Mon"){
                                $('input[name=monday]').prop('checked', true);
                                businessDays.push(1);
                            }else if((data.working_week_days)[i] == "Tue"){
                                $('input[name=tuesday]').prop('checked', true);
                                businessDays.push(2);
                            } else if((data.working_week_days)[i] == "Wed"){
                                $('input[name=wednesday]').prop('checked', true);
                                businessDays.push(3);
                            }
                            else if((data.working_week_days)[i] == "Thu"){
                                $('input[name=thursday]').prop('checked', true);
                                businessDays.push(4);
                            }
                            else if((data.working_week_days)[i] == "Fri"){
                                $('input[name=friday]').prop('checked', true);
                                businessDays.push(5);
                            }
                            else if((data.working_week_days)[i] == "Sat"){
                                $('input[name=saturday]').prop('checked', true);
                                businessDays.push(6);
                            }else if((data.working_week_days)[i] == "Sun"){
                                $('input[name=sunday]').prop('checked', true);
                                businessDays.push(7);
                            }
                        }

                        document.getElementById('holidays').value = data.holidays;

                        for(var i=0; i<(data.holidays).length; i++){

                            var object =  {
                                id: data.holidays[i],
                                start: data.holidays[i],
                                display: 'background',
                                color: 'red'
                            };

                            events.push(object);

                        }

                        console.log(businessDays, events)
                        var extra_days_arr = [];

                        if((data.extra_days).length > 0){
                            console.log(data.extra_days);
                            for(var i=0; i<(data.extra_days).length; i++){
                                $('.extra-days-list ul').append('<li>'+ moment(data.extra_days[i]).format('D/M/Y') +' </li>');
                                extra_days_arr.push(data.extra_days[i]);
                            }
                            $('div.extra-days-loaded').removeClass('d-none');
                        }

                        $('#extra_days').val(extra_days_arr);

                        reCreateCalendar();
                        $('input[name=sync_periods_complete]').val('1');

                    }else{

                        createCalendarFromStart();
                        $('input[name=sync_periods_complete]').val('0');

                    }
                },
                error: function () {
                }
            });
        });




    </script>
@endsection

