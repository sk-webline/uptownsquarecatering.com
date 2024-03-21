<div class="modal-body p-0">
    <div class="p-10px">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
            <svg class="h-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14.74 14.74">
                <use xlink:href="{{static_asset('assets/img/icons/popup-close.svg')}}#content"></use>
            </svg>
        </button>
    </div>
    <div class="px-15px px-lg-35px pb-20px">
        <h3 class="text-center fs-18 lg-fs-27 fw-700 mb-30px mb-sm-35px">{{toUpper($card->name)}}
            - {{toUpper(translate('Upcoming Meals'))}}</h3>

        <div class="w-55px border-top border-secondary border-width-2 mb-35px mb-sm-55px mx-auto"></div>

        <?php


        ?>


        <div class="upcoming-meals-calendar-style">
            <div id="calendar" class="mb-20px w-100">
            </div>
        </div>

        <div class="fw-500 fs-13 sm-fs-15 row gutters-5 mb-5px">

            @if($has_blue==1)
                <div class="col-6 col-md-4">
                    <div class="d-inline-block h-10px w-10px" style="background-color: rgba(0,30,91,0.6);"></div>
                    <span style="color: rgba(0,30,91,0.6);"> {{toUpper(translate('Only Snack'))}}</span>
                </div>
            @endif
            @if($has_green==1)
                <div class="col-6 col-md-4">
                    <div class="d-inline-block h-10px w-10px" style="background-color: #09934A;"></div>
                    <span style="color: #09934A;"> {{toUpper(translate('Snack & Lunch'))}}</span>
                </div>
            @endif
            {{--        </div>--}}

            {{--        <div class="fw-500 fs-13 sm-fs-15 row gutters-5">--}}
            @if($has_orange==1)
                <div class="col-6 col-md-4">
                    <div class="d-inline-block h-10px w-10px" style="background-color: #E09105;"></div>
                    <span style="color: #E09105;"> {{toUpper(translate('Only Lunch'))}}</span>
                </div>
            @endif
            <div class="col-6 col-md-4">
                <div class="d-inline-block h-10px w-10px" style="background-color: #DBDADA;"></div>
                <span style="color: #DBDADA;"> {{toUpper(translate('Not Available'))}}</span>
            </div>
        </div>
    </div>
</div>

<script>

    var calendarEl = document.getElementById('calendar');

    var date_start = '{{$start_day}}';
    var date_end = '{{\Carbon\Carbon::create($last_day)->addDay()->format('Y-m-d')}}';
    let lan = '{{App::getLocale()}}';

    if(lan=='gr'){
        lan = 'el';
    }


    // console.log(calendarEl);
    calendar = new FullCalendar.Calendar(calendarEl, {
        locale: lan,
        headerToolbar: {
            left: "prev",
            center: "",
            right: "next",
        },
        firstDay: 1,
        initialView: 'multiMonthFourMonth',
        // businessHours: {
        //     dow: [1],
        // },
        validRange: {
            start: date_start,
            end: date_end,
        },
        views: {
            multiMonthFourMonth: {
                type: 'multiMonthYear',
                @if($monthsDifference>0)
                duration: {months: 2}
                @else
                duration: {months: 1}
                @endif
            }
        },
        dateIncrement: {months: 1},
        datesSet: function (dateInfo) {
            var prevButton = calendarEl.querySelector(".fc-prev-button");
            var nextButton = calendarEl.querySelector(".fc-next-button");
            nextButton.disabled = false;
            prevButton.disabled = false;
            console.log('calendar.view.currentEnd: ', calendar.view.currentEnd);
            console.log('my end: ', (new Date(date_end)).setHours(0, 0, 0));
            if (calendar.view.currentEnd >= (new Date(date_end)).setHours(0, 0, 0)) {
                // // console.log('Disable Next button: ' + calendar.view.currentEnd + ' - ' + new Date(moment(date_end).add(1, "day")));
                if (nextButton) {
                    nextButton.disabled = true;
                }
            }

            if (calendar.view.currentStart <= new Date(date_start)) {
                // // console.log('Disable prev button: ' + calendar.view.currentStart + ' - ' + new Date(date_start));
                if (prevButton) {
                    prevButton.disabled = true;
                }
            }
        },
        events: [

                @foreach($events as $event)
                @php
                    if($event[ 'color'] == 1) {
                        $color = '#09934A';
                    } elseif($event[ 'color'] == 2) {
                        $color = 'rgba(0,30,91,0.6)';
                    } elseif($event[ 'color'] == 3) {
                        $color = '#E09105';
                    }
                @endphp
            {
                id: '{{$event[ 'date']}}',
                start: '{{$event[ 'date']}}', // '2023-07-31' ,
                display: 'background',
                color: '{{$color}}',

            },


            @endforeach
        ],


    });

    @foreach($events as $event)


    {{--console.log($(calendar.getEventById({{$event['date']}})).prev("div"));--}}
    $(calendar.getEventById({{$event['date']}})).prev("div").css('color', 'red');

    @endforeach

    if(moment(date_start).format("M")==moment(date_end).format("M")){
        calendar.changeView('dayGridMonth');
    }

    calendar.render();


</script>
