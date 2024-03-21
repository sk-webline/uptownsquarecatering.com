@extends('backend.layouts.app')

@section('content')

    @php
        use Carbon\Carbon;
        use App\Models\CanteenPurchase;
        use Carbon\CarbonInterval;

        $lan = App::getLocale();

        $working_week_days = json_decode($canteen_setting->working_week_days);
        $holidays = json_decode($canteen_setting->holidays);

//        $purchases_exist = CanteenPurchase::where('canteen_setting_id', $canteen_setting->id)->first();
        $carbon = Carbon::now();
        $active_purchases = CanteenPurchase::where('canteen_setting_id', $canteen_setting->id)->get();
        $active_purchases_dates = [];
        $last_purchase_date = null;

        if(count($active_purchases) > 0){
            $active_purchases = true;

            $active_purchases_dates = CanteenPurchase::where('canteen_setting_id', $canteen_setting->id)->where('date', '>=', $carbon->format('Y-m-d'))->orderBy('date')->pluck('date')->toArray();
            $active_purchases_dates = array_unique($active_purchases_dates);
            $last_purchase_date = end($active_purchases_dates);

        }else{
            $active_purchases = false;
        }

//        dd($active_purchases, $active_purchases_dates, $last_pucrhase_date);

    @endphp


    <div class="row">
        <div class="col-lg-8 mx-auto">

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6"> <a href="{{route('organisations.index')}}" class="text-black" >{{translate('Organisations')}} </a> > {{$organisation->name}} >
                            <a href="{{route('canteen.index', $organisation->id)}}" class="text-black" >{{translate('Canteen Periods')}} </a> >
                            {{translate('Edit Canteen Period')}}</h5>
                    </div>

                    <div class="card-header row">
                        <a class="col text-center c-pointer lh-2 hov-text-primary text-primary" id="setting-btn"
                           onclick="show_settings()"><h5 class="mb-0 h6">{{translate('Settings')}}</h5></a>
                        <a class="col text-center c-pointer lh-2 hov-text-primary" id="extra-day-btn"
                           onclick="show_extra_days()"><h5 class="mb-0 h6">{{translate('Extra Days')}}</h5></a>
                    </div>

                    <div id="canteen_settings">

                        <form class="form-horizontal"
                              action="{{ route('canteen_settings.update', $canteen_setting->id ) }}"
                              method="POST" enctype="multipart/form-data">
                            <div class="card-body">



                                @csrf

                                @if($active_purchases == false)
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
                                                            $organisation_settings = \App\Models\OrganisationSetting::where('organisation_id', $organisation->id)->where('date_to', '>', \Carbon\Carbon::today()->format('Y-m-d'))->get(); // $organisation->settings; // catering periods
                                                        @endphp
                                                        <option hidden value="">{{translate('Select Catering Period')}}</option>
                                                        @foreach ($organisation_settings as $period)
                                                            <option value="{{$period->id}}">{{ \Carbon\Carbon::create($period->date_from)->format('d/m/Y')}} -  {{ \Carbon\Carbon::create($period->date_to)->format('d/m/Y')}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                @endif

                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">{{translate('Start Date')}}</label>
                                    <div class="col-md-9">
                                        <input type="date" id="start_date" name="start_date"
                                               value="{{Carbon::create($canteen_setting->date_from)->format('Y-m-d')}}"
                                               data-date="{{\Carbon\Carbon::create($canteen_setting->date_from)->format('d/m/Y')}}" data-date-format="DD/MM/YYYY"
                                               class="form-control dd_mm_formatted mb-1 {{ $errors->has('start_date') ? ' is-invalid' : '' }}" onchange="reCreateCalendar()"
                                               @if($active_purchases) readonly @endif >
                                        @if ($errors->has('start_date'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('start_date') }}</strong>
                                            </span>
                                        @endif
                                        @if($active_purchases)
                                            <span class="text-error">*{{translate('Start Date cannot be changed as purchases have already been made for this period')}}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">{{translate('End Date')}}</label>
                                    <div class="col-md-9">
                                        <input type="date" id="end_date" name="end_date" class="form-control dd_mm_formatted mb-1 {{ $errors->has('end_date') ? ' is-invalid' : '' }}"
                                               value="{{Carbon::create($canteen_setting->date_to)->format('Y-m-d')}}" data-date-format="DD/MM/YYYY"
                                               data-date="{{\Carbon\Carbon::create($canteen_setting->date_to)->format('d/m/Y')}}" onchange="reCreateCalendar()"
                                               @if($active_purchases) min="{{$last_purchase_date}}" @endif >
                                        @if ($errors->has('end_date'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('end_date') }}</strong>
                                            </span>
                                        @endif
                                        @if($active_purchases)
                                            <span class="py-2px">*{{translate('End Date should be greater than the last date of an upcoming purchase')}} ({{Carbon::create($last_purchase_date)->format('d/m/Y')}})</span>
                                        @endif
                                        <span class="text-danger mt-2" id="date-warning" style="display: none">End Date should be greater than Start Date.</span>
                                    </div>
                                </div>

                                <div class="form-group text-right">
                                    <button type="button" class="btn btn-soft-secondary"
                                            onclick="reCreateCalendar()"> {{translate('Set Dates')}} </button>
                                </div>

                                @if($organisation->canteen==1)

                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label">{{translate('Working Days')}}</label>
                                        <div class="col-md-9">
                                            <div class="form-group row">
                                                <label class="w-90px col-form-label">{{translate('Monday')}}</label>
                                                <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                                    <input type="checkbox" name="monday" id="monday"   @if(in_array('Mon', json_decode($canteen_setting->working_week_days))) checked @endif
                                                           onchange="checkMonday()" @if($active_purchases) disabled @endif>
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="form-group row">
                                                <label class="w-90px col-form-label">{{translate('Tuesday')}}</label>
                                                <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                                    <input type="checkbox" name="tuesday" onchange="checkTuesday()" @if(in_array('Tue', json_decode($canteen_setting->working_week_days))) checked @endif
                                                    @if($active_purchases) disabled @endif>
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="form-group row">
                                                <label class="w-90px col-form-label">{{translate('Wednesday')}}</label>
                                                <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                                    <input type="checkbox" name="wednesday" onchange="checkWednesday()" @if(in_array('Wed', json_decode($canteen_setting->working_week_days))) checked @endif
                                                    @if($active_purchases) disabled @endif>
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="form-group row">
                                                <label class="w-90px col-form-label">{{translate('Thursday')}}</label>
                                                <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                                    <input type="checkbox" name="thursday" onchange="checkThursday()" @if(in_array('Thu', json_decode($canteen_setting->working_week_days))) checked @endif
                                                    @if($active_purchases) disabled @endif>
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="form-group row">
                                                <label class="w-90px col-form-label">{{translate('Friday')}}</label>
                                                <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                                    <input type="checkbox" name="friday" onchange="checkFriday()" @if(in_array('Fri', json_decode($canteen_setting->working_week_days))) checked @endif
                                                    @if($active_purchases) disabled @endif>
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="form-group row">
                                                <label class="w-90px col-form-label">{{translate('Saturday')}}</label>
                                                <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                                    <input type="checkbox" name="saturday" onchange="checkSaturday()" @if(in_array('Sat', json_decode($canteen_setting->working_week_days))) checked @endif
                                                    @if($active_purchases) disabled @endif>
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="form-group row">
                                                <label class="w-90px col-form-label">{{translate('Sunday')}}</label>
                                                <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                                    <input type="checkbox" name="sunday" onchange="checkSunday()" @if(in_array('Sun', json_decode($canteen_setting->working_week_days))) checked @endif
                                                    @if($active_purchases) disabled @endif>
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

                                    <div class="card p-20px">
                                        <div class="form-group row mt-10px">
                                            <label class="col-md-4 col-form-label">{{translate('Minutes Window for Pre-order')}}</label>
                                             <div class="col-md-2">
                                                <input type="number" id="minimum_preorder_minutes" name="minimum_preorder_minutes" value="{{$canteen_setting->minimum_preorder_minutes}}"
                                                       class="form-control {{ $errors->has('minimum_preorder_minutes') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('minimum_preorder_minutes'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('minimum_preorder_minutes') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row mt-10px">
                                            <label class="col-md-4 col-form-label">{{translate('Minutes Window for Cancellation & Refund')}}</label>
                                            <div class="col-md-2">
                                                <input type="number" id="minimum_cancellation_minutes" name="minimum_cancellation_minutes" value="{{$canteen_setting->minimum_cancellation_minutes}}"
                                                       class="form-control {{ $errors->has('minimum_cancellation_minutes') ? ' is-invalid' : '' }}" >
                                                @if ($errors->has('minimum_cancellation_minutes'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('minimum_cancellation_minutes') }}</strong>
                                                    </span>
                                                @endif
                                            </div>


                                            <div class="col">
                                                <input type="text" name="days_calc" readonly class="form-control" >
                                            </div>


                                            <div class="col">
                                                <input type="text" name="hours_calc" readonly class="form-control" >
                                            </div>

                                            <div class="col">
                                                <input type="text" name="min_calc" readonly class="form-control" >
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label">{{translate('Minutes to access break Before & After')}}</label>
                                            <div class="col-md-2">
                                                <input type="number" placeholder="{{translate('Minutes')}}" id="access_minutes" name="access_minutes" value="{{$canteen_setting->access_minutes}}" class="form-control mb-1 {{ $errors->has('access_minutes') ? ' is-invalid' : '' }}" required>
                                                <span class="">*{{translate('Cashier Screen')}}</span>
                                            @if ($errors->has('access_minutes'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('access_minutes') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row mt-10px">
                                            <label class="col-md-4 col-form-label">{{translate('Minutes Window to Undo Delivery')}}</label>
                                            <div class="col-md-2">
                                                <input type="number" id="max_undo_delivery_minutes" name="max_undo_delivery_minutes" value="{{$canteen_setting->max_undo_delivery_minutes}}"
                                                       class="form-control mb-1 {{ $errors->has('max_undo_delivery_minutes') ? ' is-invalid' : '' }}" >
                                                <span class="">*{{translate('Cashier Screen')}}</span>
                                                @if ($errors->has('max_undo_delivery_minutes'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('max_undo_delivery_minutes') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"> {{translate('Breaks')}}</h6>
                                        </div>

                                        <div class="card-body">

                                            <string class="d-block fw-600 pb-5px">*{{translate('Insert Breaks Sorted')}}</string>

                                            <table class="table text-center create-break">
                                                <thead>
                                                <th>{{translate('Break')}}</th>
                                                <th>{{translate('From Hour')}}</th>
                                                <th>{{translate('To Hour')}}</th>
                                                <th></th>
                                                </thead>
                                                <tbody>

                                                @php
                                                    $breaks = \App\Models\OrganisationBreak::where('canteen_setting_id', $canteen_setting->id)->orderBy('hour_from', 'ASC')->get();

                                                    $counter_break = count($breaks);

                                                    if(count($breaks)==0){
                                                        $counter_break=1;
                                                    }

                                                @endphp

                                                @if(count($breaks)>0)

                                                    @foreach($breaks as $key => $break)

                                                        @if($key == 0)
                                                            <tr>
                                                                <td> {{ $key+1 }}</td>
                                                                <td class="border-none" >
                                                                    <input type="time" value="{{$break->hour_from}}" class="form-control w-200px margin-auto from_hour"  name="from_hour[]">
                                                                </td>
                                                                <td class="border-none">
                                                                    <input type="time" value="{{$break->hour_to}}" class="form-control w-200px margin-auto to_hour" name="to_hour[]">
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-soft-primary btn-icon btn-circle btn-sm fs-15 p-1 add-extra-break"
                                                                            title="{{ translate('Add') }}">
                                                                        +
                                                                    </button>
                                                                </td>
                                                            </tr>

                                                        @else
                                                            <tr>
                                                                <td> {{ $key+1 }}</td>
                                                                <td class="border-none" >
                                                                    <input type="time" value="{{$break->hour_from}}" class="form-control w-200px margin-auto from_hour"  name="from_hour[]">
                                                                </td>
                                                                <td class="border-none">
                                                                    <input type="time" value="{{$break->hour_to}}" class="form-control w-200px margin-auto to_hour" name="to_hour[]">
                                                                </td>
                                                                <td>
                                                                    @if(!$active_purchases)
                                                                    <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm fs-15 p-1 remove-break"
                                                                            title="{{ translate('Remove') }}">
                                                                        -
                                                                    </button>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endif

                                                    @endforeach
                                                @else

                                                    <tr>
                                                        <td>1</td>
                                                        <td class="border-none" >
                                                            <input type="time" class="form-control w-200px margin-auto from_hour"  name="from_hour[]">
                                                        </td>
                                                        <td class="border-none">
                                                            <input type="time" class="form-control w-200px margin-auto to_hour" name="to_hour[]">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-soft-primary btn-icon btn-circle btn-sm fs-15 p-1 add-extra-break"
                                                                    title="{{ translate('Add') }}">
                                                                +
                                                            </button>
                                                        </td>
                                                    </tr>

                                                @endif
                                                <tr>

                                                </tr>
                                                </tbody>
                                            </table>

                                            <span class="d-block text-error d-none fill-all-inputs-error pt-5px">*{{translate('Please fill all hour inputs')}}</span>
                                            <span class="d-block text-error d-none hour-inputs-error">*{{translate('Make sure that From hour is less than To Hour')}}</span>

                                        </div>
                                    </div>

                                @endif

                            </div>


                            <div class="form-group mb-2 mr-4 text-right">
                                <a href="{{route('canteen.index', $organisation->id)}}">
                                    <button type="button" class="btn btn-soft-danger">{{translate('Cancel')}}</button>
                                </a>
                                <button type="submit" class="btn btn-primary save_holidays">{{translate('Save')}}</button>
                            </div>
                        </form>
                    </div>

                    <div id="extra_days" class="d-none">

                        @php

                            $existing_extra_days = \App\Models\CanteenExtraDay::where('canteen_setting_id', $canteen_setting->id)->get();

                        @endphp

                        <form class="form-horizontal"
                              action="{{ route('canteen_settings.extra_days.update', $canteen_setting->id ) }}"
                              method="POST" enctype="multipart/form-data">
                            <div class="card-body">

                                @csrf

                                    <div class=" mr-2 text-right">
                                        <button class="btn btn-soft-primary btn-icon btn-circle btn-sm fs-15 p-1 add-extra-day"
                                                title="<?php echo e(translate('Add Extra Day')); ?>">
                                            +
                                        </button>

                                        <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm fs-15 p-1 remove-extra-day"
                                                title="<?php echo e(translate('Remove Extra Day')); ?>"> -
                                        </button>

                                    </div>

                                    <div class="form-group row pl-2" id="extra-days-div">

                                        <?php if(sizeof($existing_extra_days)<1): ?>
                                        <div class="col-md-4 my-4">
                                            <div class="row d-flex gutters-5">
                                                <input type="date"
                                                       class="form-control px-10px new-date col-lg-10 dd_mm_formatted"
                                                       name="extra_day[]"
                                                       data-date=""
                                                       data-date-format="DD/MM/YYYY"
                                                       required>

                                                <button
                                                    class="btn btn-soft-danger btn-icon btn-circle btn-sm ml-2 delete_this_extra_day">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php else: ?>

                                            <?php $__currentLoopData = $existing_extra_days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $existing_extra_day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                            <?php
                                                   $exist=0;
                                            ?>
                                        <div class="col-md-4 my-4">

                                            <div class="row d-flex gutters-5">
                                                <input type="date"
                                                       class="form-control px-10px new-date dd_mm_formatted col-lg-10"
                                                       data-date-format="DD/MM/YYYY"
                                                       data-date="<?php echo e(Carbon::create($existing_extra_day->date)->format('d/m/Y')); ?>"
                                                       name="extra_day[]" value="<?php echo e($existing_extra_day->date); ?>" required>


                                                <button type="button" data="<?php echo e($key+1); ?>" <?php if($exist==1): ?> disabled <?php endif; ?>
                                                class="btn btn-soft-danger btn-icon btn-circle btn-sm  ml-2 delete_this_extra_day">
                                                    <i class="las la-trash"></i>
                                                </button>


                                            </div>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>




                                    </div>




                            </div>


                            <div class="form-group mb-2 row no-gutters justify-content-between align-items-end mx-4">
                                <div class="col-auto">
                                    <span class="text-red">*{{translate('Extra Date of canteen purchase cannot be deleted')}}</span>
                                </div>
                                <div class="col text-right">
                                    <a href="{{route('canteen.index', $organisation->id)}}">
                                        <button type="button" class="btn btn-soft-danger">{{translate('Cancel')}}</button>
                                    </a>
                                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                                </div>


                            </div>
                        </form>


                    </div>


                    <div class="col-md-4 my-4 d-none" id="add-extra">
                        <div class="row d-flex gutters-5">
                            <input type="date"
                                   class="form-control px-10px new-date col-lg-10 dd_mm_formatted"
                                   name="extra_day[]"
                                   data-date=""
                                   data-date-format="DD/MM/YYYY"
                                   required>

                            <button
                                class="btn btn-soft-danger btn-icon btn-circle btn-sm ml-2 delete_this_extra_day">
                                <i class="las la-trash"></i>
                            </button>
                        </div>
                    </div>

                </div>


        </div>
    </div>

@endsection

@section('script')

    <script type="text/javascript">

        var calStartDate, calEndDate, my_start,my_end ;

        var holidays = [];

        const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

        var businessDays = [];

        let pr_cards = 1, breaks_counter='{{$counter_break}}';

        let purchase_dates = [];
{{--        {!! json_encode($active_purchases_dates) !!};--}}

        let lan = '{{App::getLocale()}}';
        let events = [], old_start_date = null, old_end_date=null;
        let child_count = '{{count($existing_extra_days)}}';

        $(".save_holidays").click(function (e) {

            $('#holidays').val([]);

            $('#holidays').val(holidays);

        });

        document.addEventListener('DOMContentLoaded', function () {

            // console.log(purchase_dates);

            if(lan=='gr'){
                lan = 'el';
            }

            my_start = '{{$canteen_setting->date_from}}';
            old_start_date = '{{$canteen_setting->date_from}}';
            my_end = '{{$canteen_setting->date_to}}';
            old_end_date = '{{$canteen_setting->date_to}}';

            old_holidays = {!! json_encode($holidays) !!};

            @if(in_array('Mon', $working_week_days))
            businessDays.push(1);
            @endif
            @if(in_array('Tue', $working_week_days))
            businessDays.push(2);
            @endif
            @if(in_array('Wed', $working_week_days))
            businessDays.push(3);
            @endif
            @if(in_array('Thu', $working_week_days))
            businessDays.push(4);
            @endif
            @if(in_array('Fri', $working_week_days))
            businessDays.push(5);
            @endif
            @if(in_array('Sat', $working_week_days))
            businessDays.push(5);
            @endif
            @if(in_array('Sun', $working_week_days))
            businessDays.push(0);
            @endif

            console.log('business_days:', businessDays);

            for (var i = 0; i < old_holidays.length; i++) {
                holidays.push(old_holidays[i]);
            }

            @foreach($active_purchases_dates as $date)
            purchase_dates.push('{{$date}}');
            @endforeach

            {{--var temp_arr = '{{$active_purchases_dates}}';--}}
            // console.log('tt: ',temp_arr );
            // for (var i = 0; i < temp_arr.length; i++) {
            //     purchase_dates.push(temp_arr[i]);
            // }

            var durationInMinutes = $('input[name=minimum_cancellation_minutes]').val();

            // Calculate days, hours, and remaining minutes
            var days = Math.floor(durationInMinutes / (24 * 60));
            var hours = Math.floor((durationInMinutes % (24 * 60)) / 60);
            var minutes = durationInMinutes % 60;

            if(days == 1){
                $('input[name=days_calc]').val(days + ' Day');
            }else{
                $('input[name=days_calc]').val(days + ' Days');
            }

            if(hours == 1){
                $('input[name=hours_calc]').val(hours + ' Hour');
            }else{
                $('input[name=hours_calc]').val(hours + ' Hours');
            }

            if(minutes == 1){
                $('input[name=min_calc]').val(minutes + ' Min');
            }else{
                $('input[name=min_calc]').val(minutes + ' Mins');
            }

            // import interactionPlugin from '@fullcalendar/interaction';
            calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                locale: lan,
                headerToolbar: {
                    left: "customPrev",
                    center: "title",
                    right: "customNext",
                },
                initialView: "multiMonth", // Προβολή αρχικά σε Multiview με διάρκεια 2 μηνών
                initialDate: moment().format("YYYY-MM-DD"),
                multiMonthMaxColumns: 2,
                validRange: {
                    start: moment(my_start).format("YYYY-MM-DD"),
                    end: moment(my_end).add(1, "day").format("YYYY-MM-DD"),
                },
                businessHours: {
                    // days of week. an array of zero-based day of week integers (0=Sunday)
                    daysOfWeek: businessDays, // Monday - Thursday

                },
                duration: {months: 2},
                dateIncrement: {months: 1},
                selectable: false,
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
                dateClick: function (info) {

                    var day = (new Date(info.dateStr).getDay());

                    // console.log('dd: ',info.dateStr, $.inArray(info.dateStr, purchase_dates), purchase_dates);

                    if($.inArray(info.dateStr, purchase_dates) == -1){
                        if (calendar.getEventById(info.dateStr) != null) {
                            // console.log('this is event');
                            calendar.getEventById(info.dateStr).remove();

                            for (var i = 0; i < holidays.length; i++) {
                                if (holidays[i] === info.dateStr) {
                                    holidays.splice(i, 1);
                                }
                            }

                        }
                        else {
                            for (var j = 0; j < businessDays.length; j++) {
                                if (day === businessDays[j]) {

                                    if ($('input[name="start_date"]').val() !== '' && $('input[name="end_date"]').val() !== '') {

                                        if (info.dayEl.style.backgroundColor === 'red') {

                                            for (var i = 0; i < holidays.length; i++) {
                                                if (holidays[i] === info.dateStr) {
                                                    holidays.splice(i, 1);
                                                }
                                            }

                                            info.dayEl.style.backgroundColor = 'white';

                                        } else {
                                            // change the day's background color just for fun
                                            info.dayEl.style.backgroundColor = 'red';
                                            holidays.push(info.dateStr);
                                        }

                                    } else {
                                        alert('Specify start and end date.')
                                    }

                                }
                            }
                        }
                    }

                    // console.log(holidays);

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
                datesSet: function (dateInfo) {
                    var prevButton = calendarEl.querySelector(".fc-customPrev-button");
                    var nextButton = calendarEl.querySelector(".fc-customNext-button");
                    nextButton.disabled = false;
                    prevButton.disabled = false;
                    if (calendar.view.currentEnd >= new Date(my_end)) {
                        console.log('Disable Next button: ' + calendar.view.currentEnd + ' - ' + new Date(moment(my_end).add(1, "day")));
                        if (nextButton) {
                            nextButton.disabled = true;
                        }
                    } else if (calendar.view.currentStart <= new Date(my_start)) {
                        console.log('Disable prev button: ' + calendar.view.currentStart + ' - ' + new Date(my_start));
                        if (prevButton) {
                            prevButton.disabled = true;
                        }
                    }
                },

                events: [
                        @foreach($active_purchases_dates as $date )
                        {
                            id: '{{$date}}',
                            start: '{{$date}}',
                            display: 'background',
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        @endforeach
                        @foreach($holidays as $holiday )
                        {
                            id: '{{$holiday}}',
                            start: '{{$holiday}}', //new Date({{$holiday}}),
                            display: 'background',
                            color: 'red'
                        },
                        @endforeach
                ]

            });

            if(moment(my_start).format("M")==moment(my_end).format("M")){
                calendar.changeView('dayGridMonth');
            }

            calendar.render();

        });

        function reCreateCalendar() {

            my_start = $('#start_date').val();
            my_end = $('#end_date').val();

            var hol_arr = [];
            var temp_array = [];

            for (var i = 0; i < holidays.length; i++) {

                if(businessDays.includes(moment(holidays[i]).day())){
                    var h = {
                        id: holidays[i],
                        start: holidays[i],
                        display: 'background',
                        color: 'red'
                    };
                    hol_arr.push(h);
                    temp_array.push(holidays[i]);
                }else{
                    console.log(holidays[i]);
                }

            }

            @foreach($active_purchases_dates as $date )
                var h ={
                    id: '{{$date}}',
                        start: '{{$date}}',
                    display: 'background',
                    color: 'rgba(0, 0, 0, 0.1)'
                };
            hol_arr.push(h);
            @endforeach


            holidays = Array.from(temp_array);

            $('#holidays').val([]);
            $('#holidays').val(holidays);

            if (my_start >= my_end) {
                $('#date-warning').show();
            } else {
                $('#date-warning').hide();

                calendarEl = document.getElementById("calendar");
                $('#calendar').children('div').remove();
                calendar = new FullCalendar.Calendar(calendarEl, {
                    locale: lan,
                    headerToolbar: {
                        left: "customPrev",
                        center: "title",
                        right: "customNext",
                    },
                    initialView: "multiMonth", // Προβολή αρχικά σε Multiview με διάρκεια 2 μηνών
                    initialDate: moment().format("YYYY-MM-DD"),
                    multiMonthMaxColumns: 2,
                    validRange: {
                        start: moment(my_start).format("YYYY-MM-DD"),
                        end: moment(my_end).add(1, "day").format("YYYY-MM-DD"),
                    },
                    businessHours: {
                        // days of week. an array of zero-based day of week integers (0=Sunday)
                        daysOfWeek: businessDays, // Monday - Thursday

                    },
                    duration: {months: 2},
                    dateIncrement: {months: 1},
                    selectable: false,
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
                    selectable: true,
                    dateClick: function (info) {

                        var day = (new Date(info.dateStr).getDay());

                        if($.inArray(info.dateStr, purchase_dates) == -1) {
                            if (calendar.getEventById(info.dateStr) != null) {
                                console.log('this is event');
                                calendar.getEventById(info.dateStr).remove();

                                for (var i = 0; i < holidays.length; i++) {
                                    if (holidays[i] === info.dateStr) {
                                        holidays.splice(i, 1);
                                    }
                                }

                            } else {

                                for (var j = 0; j < businessDays.length; j++) {
                                    if (day === businessDays[j]) {

                                        if ($('input[name="start_date"]').val() !== '' && $('input[name="end_date"]').val() !== '') {

                                            if (info.dayEl.style.backgroundColor === 'red') {

                                                for (var i = 0; i < holidays.length; i++) {
                                                    if (holidays[i] === info.dateStr) {
                                                        holidays.splice(i, 1);
                                                    }
                                                }

                                                info.dayEl.style.backgroundColor = 'white';
                                                (calendar.getEventById(info.dateStr)).remove();

                                            } else {
                                                // change the day's background color just for fun
                                                info.dayEl.style.backgroundColor = 'red';
                                                calendar.addEvent({
                                                    id: info.dateStr,
                                                    start: info.dateStr,
                                                    display: 'background',
                                                    color: 'red'
                                                });
                                                holidays.push(info.dateStr);
                                            }

                                        } else {
                                            alert('Specify start and end date.')
                                        }

                                    }
                                }
                            }
                        }

                        console.log(holidays);

                    },
                    datesSet: function (dateInfo) {
                        var prevButton = calendarEl.querySelector(".fc-customPrev-button");
                        var nextButton = calendarEl.querySelector(".fc-customNext-button");
                        nextButton.disabled = false;
                        prevButton.disabled = false;
                        if (calendar.view.currentEnd >= new Date(my_end)) {
                            console.log('Disable Next button: ' + calendar.view.currentEnd + ' - ' + new Date(moment(my_end).add(1, "day")));
                            if (nextButton) {
                                nextButton.disabled = true;
                            }
                        } else if (calendar.view.currentStart <= new Date(my_start)) {
                            console.log('Disable prev button: ' + calendar.view.currentStart + ' - ' + new Date(my_start));
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
                    events:
                            hol_arr
                    {{--    [--}}
                    {{--    @foreach($holidays as $holiday )--}}
                    {{--    {--}}
                    {{--        id: '{{$holiday}}',--}}
                    {{--        start: '{{$holiday}}',--}}
                    {{--        display: 'background',--}}
                    {{--        color: 'red'--}}
                    {{--    },--}}
                    {{--    @endforeach--}}
                    {{--]--}}
                    // events:  holiday

                });
                if(moment(my_start).format("M")==moment(my_end).format("M")){
                    calendar.changeView('dayGridMonth');
                }
                calendar.render();

                // console.log(holidays);
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

            // console.log($('input[name="absence"]').val());
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

        //     New Script

        $(document).on('click', 'input.from_hour', function(){
            $(this).removeClass('border-red')
        });

        $(document).on('click', 'input.to_hour', function(){
            $(this).removeClass('border-red')
        });

        $(document).on('click', 'button.add-extra-break', function(){

            var last_input = null;
            var breaks_full=true;
            var break_counter

            $('tbody tr input').each(function(index, element) {


                if($(element).val() == ''){
                    breaks_full=false;
                    $('.fill-all-inputs-error').removeClass('d-none');
                    $(element).addClass('border-red');
                }else{
                    $(element).removeClass('border-red');
                }

                if(last_input!=null){
                    console.log($(element).val(),last_input.val() );
                    if($(element).val() <= last_input.val()){
                        breaks_full=false;
                        $('.hour-inputs-error').removeClass('d-none');
                        $(element).addClass('border-red');
                        last_input.addClass('border-red');
                    }else{
                        $(element).removeClass('border-red');
                        last_input.removeClass('border-red');
                    }
                }


                last_input = $(element);


            });

            if(!breaks_full){
                return;
            }


            // if(from_hour_input.val()=='' || to_hour_input.val()==''){
            //
            //     if(from_hour_input.val()==''){
            //         from_hour_input.addClass('border-red');
            //     }
            //     if(to_hour_input.val()==''){
            //         to_hour_input.addClass('border-red');
            //     }
            //
            //     return;
            // }

            var tbody = $(this).parents('tbody');
            var firstRow = $('.create-break tbody tr:first');
            // Clone the selected row
            var clonedRow = firstRow.clone();
            breaks_counter++;

            clonedRow.find('input.from_hour').val('');
            clonedRow.find('input.to_hour').val('');

            var firstCell = clonedRow.find('td:first');

            firstCell.html(breaks_counter);

            var lastCell = clonedRow.find('td:last');

            lastCell.html('<button class="btn btn-soft-danger btn-icon btn-circle btn-sm fs-15 p-1 remove-break" title="{{ translate('Remove Extra Day') }}"> - </button>');

            tbody.append(clonedRow);

            // console.log('done');
        });

        $(document).on('click', 'button.remove-break', function(){

            breaks_counter--;

            $(this).parents('tr').remove();

            $('.create-break tbody tr ').each(function(index, element) {
                var firstCell =  $(element).find('td:first');
                firstCell.html(index+1);
            });


        });

        $(document).on('click', 'input[name=sync_periods]', function(){

            if($(this).prop('checked')==true){
                $('div.sync-period').removeClass('d-none');

            }else{
                $('div.sync-period').addClass('d-none');
                $('select[name=periods_sync_select]').val('');
                $('select[name=periods_sync_select]').selectpicker('refresh');
                // recreate calendar from start
                createCalendarFromStart();
            }
        });

        function createCalendarFromStart(){

            // console.log('createCalendarFromStart');
            businessDays = [];
            events = [];
            holidays = [];

            document.getElementById('holidays').value = [];
            $('input[name=start_date]').val(old_start_date);
            $('input[name=end_date]').val(old_end_date);

            $('input[name=start_date]').attr("data-date",  moment(old_start_date, "YYYY-MM-DD").format($('input[name=start_date]').attr("data-date-format")));
            $('input[name=end_date]').attr("data-date",  moment(old_end_date, "YYYY-MM-DD").format($('input[name=end_date]').attr("data-date-format")));

            $('input[name=monday]').prop('checked', false);
            $('input[name=tuesday]').prop('checked', false);
            $('input[name=wednesday]').prop('checked', false);
            $('input[name=thursday]').prop('checked', false);
            $('input[name=friday]').prop('checked', false);
            $('input[name=saturday]').prop('checked', false);
            $('input[name=sunday]').prop('checked', false);

            my_start = '{{$canteen_setting->date_from}}';
            my_end = '{{$canteen_setting->date_to}}';

            old_holidays = {!! json_encode($holidays) !!};

            @if(in_array('Mon', $working_week_days))
            $('input[name=monday]').prop('checked', true);
            businessDays.push(1);
            @endif
            @if(in_array('Tue', $working_week_days))
            $('input[name=tuesday]').prop('checked', true);
            businessDays.push(2);
            @endif
            @if(in_array('Wed', $working_week_days))
            $('input[name=wednesday]').prop('checked', true);
            businessDays.push(3);
            @endif
            @if(in_array('Thu', $working_week_days))
            $('input[name=thursday]').prop('checked', true);
            businessDays.push(4);
            @endif
            @if(in_array('Fri', $working_week_days))
            $('input[name=friday]').prop('checked', true);
            businessDays.push(5);
            @endif
            @if(in_array('Sat', $working_week_days))
            $('input[name=saturday]').prop('checked', true);
            businessDays.push(5);
            @endif
            @if(in_array('Sun', $working_week_days))
            $('input[name=sunday]').prop('checked', true);
            businessDays.push(0);
            @endif

            // console.log('business_days:', businessDays);

            for (var i = 0; i < old_holidays.length; i++) {
                holidays.push(old_holidays[i]);
            }

            // console.log('holidays: ',  holidays);

            // import interactionPlugin from '@fullcalendar/interaction';
            calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                locale: lan,
                headerToolbar: {
                    left: "customPrev",
                    center: "title",
                    right: "customNext",
                },
                initialView: "multiMonth", // Προβολή αρχικά σε Multiview με διάρκεια 2 μηνών
                initialDate: moment().format("YYYY-MM-DD"),
                multiMonthMaxColumns: 2,
                validRange: {
                    start: moment(my_start).format("YYYY-MM-DD"),
                    end: moment(my_end).add(1, "day").format("YYYY-MM-DD"),
                },
                businessHours: {
                    // days of week. an array of zero-based day of week integers (0=Sunday)
                    daysOfWeek: businessDays, // Monday - Thursday

                },
                duration: {months: 2},
                dateIncrement: {months: 1},
                selectable: false,
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
                dateClick: function (info) {

                    var day = (new Date(info.dateStr).getDay());

                    if($.inArray(info.dateStr, purchase_dates) == -1) {
                        if (calendar.getEventById(info.dateStr) != null) {
                            console.log('this is event');
                            calendar.getEventById(info.dateStr).remove();

                            for (var i = 0; i < holidays.length; i++) {
                                if (holidays[i] === info.dateStr) {
                                    holidays.splice(i, 1);
                                }
                            }

                        } else {

                            for (var j = 0; j < businessDays.length; j++) {
                                if (day === businessDays[j]) {

                                    if ($('input[name="start_date"]').val() !== '' && $('input[name="end_date"]').val() !== '') {

                                        if (info.dayEl.style.backgroundColor === 'red') {

                                            for (var i = 0; i < holidays.length; i++) {
                                                if (holidays[i] === info.dateStr) {
                                                    holidays.splice(i, 1);
                                                }
                                            }

                                            info.dayEl.style.backgroundColor = 'white';

                                        } else {
                                            // change the day's background color just for fun
                                            info.dayEl.style.backgroundColor = 'red';
                                            holidays.push(info.dateStr);
                                        }

                                    } else {
                                        alert('Specify start and end date.')
                                    }

                                }
                            }
                        }
                    }

                    // console.log(holidays);

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
                datesSet: function (dateInfo) {
                    var prevButton = calendarEl.querySelector(".fc-customPrev-button");
                    var nextButton = calendarEl.querySelector(".fc-customNext-button");
                    nextButton.disabled = false;
                    prevButton.disabled = false;
                    if (calendar.view.currentEnd >= new Date(my_end)) {
                        console.log('Disable Next button: ' + calendar.view.currentEnd + ' - ' + new Date(moment(my_end).add(1, "day")));
                        if (nextButton) {
                            nextButton.disabled = true;
                        }
                    } else if (calendar.view.currentStart <= new Date(my_start)) {
                        console.log('Disable prev button: ' + calendar.view.currentStart + ' - ' + new Date(my_start));
                        if (prevButton) {
                            prevButton.disabled = true;
                        }
                    }
                },

                events: [
                        @foreach($active_purchases_dates as $date )
                    {
                        id: '{{$date}}',
                        start: '{{$date}}',
                        display: 'background',
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                        @endforeach
                        @foreach($holidays as $holiday )
                    {
                        id: '{{$holiday}}',
                        start: '{{$holiday}}', //new Date({{$holiday}}),
                        display: 'background',
                        color: 'red'
                    },
                    @endforeach
                ]

            });

            if(moment(my_start).format("M")==moment(my_end).format("M")){
                calendar.changeView('dayGridMonth');
            }

            calendar.render();
        }



        $(document).on('change click', 'select[name=periods_sync_select]', function(){

            //create calendar based on the selected period
            //send ajax to get all details of this catering period

            if($('select[name=periods_sync_select]').val()==''){
                return;
            }

            console.log('select val: ', $('select[name=periods_sync_select]').val());

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{route('organisation_settings.get_settings_details')}}',
                data: {
                    organisation_id: '{{$organisation->id}}',
                    organisation_setting_id: $('select[name=periods_sync_select]').val(),
                },
                dataType: "JSON",
                success: function (data) {
                    // console.log('period data: ', data);

                    if(data.status == 1){
                        //create calendar based on this data

                        $('input[name=start_date]').val(data.start_date);
                        $('input[name=end_date]').val(data.end_date);

                        $('input[name=start_date]').attr("data-date",  moment(data.start_date, "YYYY-MM-DD").format($('input[name=start_date]').attr("data-date-format")));
                        $('input[name=end_date]').attr("data-date",  moment(data.end_date, "YYYY-MM-DD").format($('input[name=end_date]').attr("data-date-format")));

                        businessDays = [];

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

                       holidays = data.holidays;

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


        $(document).on('change', 'input[type=time]', function(){
            $('.hour-inputs-error').addClass('d-none');
            $('.fill-all-inputs-error').addClass('d-none');
        });

        function show_extra_days(){

            $('#extra_days').removeClass('d-none');
            $('#canteen_settings').addClass('d-none');

            $('#extra-day-btn').addClass('text-primary');
            $('#setting-btn').removeClass('text-primary');


        }

        function show_settings(){

            $('#extra_days').addClass('d-none');
            $('#canteen_settings').removeClass('d-none');
            $('#extra-day-btn').removeClass('text-primary');
            $('#setting-btn').addClass('text-primary');

        }

        $(".add-extra-day").click(function (e) {
            e.preventDefault();

            console.log('add extra day');

            if($('#extra-days-div .col-md-4:last').length>0){
                var clone = $('#extra-days-div .col-md-4:last').clone();
            }else{
                var clone = $('#add-extra').clone();
                clone.attr('id', '');
                clone.removeClass('d-none');
            }

            clone.find('input.new-date').attr("data-date", '');
            clone.find('input.new-date').val('');
            clone.find('.delete_this_extra_day').attr("data", (child_count + 1));
            clone.find('.delete_this_extra_day').prop('disabled', false);

            $('#extra-days-div').append(clone);

            child_count = child_count + 1;

        });

        $(document).on('change', 'input.new-date', function(){

            $(this).attr(
                "data-date",
                moment(this.value, "YYYY-MM-DD").format(($(this)).attr("data-date-format"))
            );

        })

        $(".remove-extra-day").click(function (e) {

            e.preventDefault();

            $('#extra-days-div .col-md-4:last').remove();

        });

        $(document).on('click', '.delete_this_extra_day', function () {
            $(this).parents('div.col-md-4').remove();
        });

        $(document).on('click', '.is-invalid', function(){
            $(this).parent().find('.invalid-feedback').first().html('');
            $(this).removeClass('is-invalid');
        });

        $(document).on('keyup keydown change', 'input[name=minimum_cancellation_minutes]', function (){

            var durationInMinutes = $(this).val();

            // Calculate days, hours, and remaining minutes
            var days = Math.floor(durationInMinutes / (24 * 60));
            var hours = Math.floor((durationInMinutes % (24 * 60)) / 60);
            var minutes = durationInMinutes % 60;

            if(days == 1){
                $('input[name=days_calc]').val(days + ' Day');
            }else{
                $('input[name=days_calc]').val(days + ' Days');
            }

            if(hours == 1){
                $('input[name=hours_calc]').val(hours + ' Hour');
            }else{
                $('input[name=hours_calc]').val(hours + ' Hours');
            }

            if(minutes == 1){
                $('input[name=min_calc]').val(minutes + ' Min');
            }else{
                $('input[name=min_calc]').val(minutes + ' Mins');
            }



        });



    </script>
@endsection

