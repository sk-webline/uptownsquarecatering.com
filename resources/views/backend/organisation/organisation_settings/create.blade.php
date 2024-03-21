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
                            <a href="{{route('organisation_settings.index', $organisation->id)}}" class="text-black" >{{translate('Periods')}} </a> >
                            {{translate('Add New Period')}}</h5>
                    </div>

                    <div class="card-body">

                        @csrf
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

                            <div id='calendar' class="mt-2 mb-2 mx-auto h-500px" style="height: 500px">
                                <input type="hidden" name="holidays[]" id="holidays" multiple>
                            </div>

                        @endif

                    </div>

                    <div>

                    </div>

                    <div class="form-group mb-2 mr-4 text-right">
                        <a href="{{route('organisation_settings.index', $organisation->id)}}">
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

        let lan = '{{App::getLocale()}}'

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

            var start = moment($('#start_date').val());
            var end = moment($('#end_date').val());

            my_start = $('#start_date').val();
            my_end = $('#end_date').val();

            // console.log('mosnth start: ', moment(my_start).format("M"));

            if (my_start >= my_end) {
                $('#date-warning').show();
            } else {
                $('#date-warning').hide();

            // if (end >= start) {


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





    </script>
@endsection

