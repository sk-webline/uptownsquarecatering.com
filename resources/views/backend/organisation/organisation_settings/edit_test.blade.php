@extends('backend.layouts.app')

@section('content')

    <?php

    use Carbon\Carbon;

    $lan = App::getLocale();

    $working_week_days = json_decode($organisation_setting->working_week_days);

    $show_prices = 0;

    if (isset($go_to_prices) && $go_to_prices == 1) {
        $show_prices = 1;
    }

    ?>
    <div class="row">

        <div class="col-lg-8 mx-auto">

            <div class="m-2 mb-3">
                <h5 class="mb-0 h6"><a href="{{route('organisations.index')}}"
                                       class="text-black">{{translate('Organisations')}} </a> > {{$organisation->name}}
                    >
                    <a href="{{route('organisation_settings.index', $organisation->id)}}"
                       class="text-black">{{translate('Periods')}} </a> >
                    {{date("d/m/Y", strtotime($organisation_setting->date_from))}}
                    - {{date("d/m/Y", strtotime($organisation_setting->date_to))}} > {{translate('Edit Period')}}</h5>

            </div>

        </div>
        <div class="col-lg-8 mx-auto my-10px">
            <div class="card">
                <div class="card-header row">
                    <a class="col-md-2 text-center c-pointer lh-2 hov-text-primary text-primary" id="setting-btn"
                       onclick="show_settings()"><h5 class="mb-0 h6">{{translate('Settings')}}</h5></a>
                    <a class="col-md-2 text-center c-pointer lh-2 hov-text-primary " id="price-btn"
                       onclick="show_prices()"><h5 class="mb-0 h6">{{translate('Prices')}}</h5></a>
                    <a class="col-md-2 text-center c-pointer lh-2 hov-text-primary" id="extra-day-btn"
                       onclick="show_extra_days()"><h5 class="mb-0 h6">{{translate('Extra Days')}}</h5></a>
                </div>

                <div class="card-body" id="settings-form">
                    <form class="form-horizontal"
                          action="{{ route('organisation_settings.update', $organisation_setting->id) }}" method="post"
                          enctype="multipart/form-data">

                        @csrf

                        <?php

                        $holidays = json_decode($organisation_setting->holidays);
                        ?>


                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Start Date')}}</label>
                            <div class="col-md-9">
                                <input type="date" id="start_date" name="start_date"
                                       value="{{$organisation_setting->date_from}}" class="form-control dd_mm_formatted"
                                       data-date="{{\Carbon\Carbon::create($organisation_setting->date_from)->format('d/m/Y')}}"
                                       data-date-format="DD/MM/YYYY"
                                       onchange="reCreateCalendar()" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('End Date')}}</label>
                            <div class="col-md-9">
                                <input type="date" id="end_date" name="end_date"
                                       value="{{$organisation_setting->date_to}}" class="form-control dd_mm_formatted"
                                       data-date="{{\Carbon\Carbon::create($organisation_setting->date_to)->format('d/m/Y')}}"
                                       data-date-format="DD/MM/YYYY"
                                       onchange="reCreateCalendar()" required>
                                <span class="text-danger mt-2" id="date-warning" style="display: none">End Date should be greater than Start Date.</span>
                            </div>

                        </div>


                        <div class="form-group mr-2 text-right">
                            <button type="button" class="btn btn-soft-secondary"
                                    onclick="reCreateCalendar()"> {{translate('Set Dates')}} </button>
                        </div>


                        @if($organisation->catering==1)
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{translate('Max Snack Quantity')}}</label>
                                <div class="col-md-9">
                                    <input type="number" placeholder="{{translate('Max Snack Quantity')}}"
                                           value="{{$organisation_setting->max_snack_quantity}}"
                                           id="max_snack_quantity"
                                           name="max_snack_quantity" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{translate('Max Lunch Quantity')}}</label>
                                <div class="col-md-9">
                                    <input type="number" placeholder="{{translate('Max Lunch Quantity')}}"
                                           value="{{$organisation_setting->max_meal_quantity}}" id="max_meal_quantity"
                                           name="max_meal_quantity" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{translate('Absence')}}</label>
                                <div class="col-md-9">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        @if($organisation->absence==1)
                                            <input type="checkbox" name="absence" onchange="showAbsenceDays()" checked>
                                        @else
                                            <input type="checkbox" name="absence" onchange="showAbsenceDays()">
                                        @endif
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            @if($organisation->absence==1)
                                <div class="form-group row" id="absence_div">
                                    <label
                                        class="col-md-3 col-form-label">{{translate('Absence Min Days Warning')}}</label>
                                    <div class="col-md-9">
                                        <input type="number" placeholder="{{translate('Absence Min Days Warning')}}"
                                               value="{{$organisation_setting->absence_days_num}}" id="absence_days"
                                               name="absence_days" class="form-control">
                                    </div>
                                </div>
                            @else
                                <div class="form-group row" id="absence_div" style="display: none">
                                    <label
                                        class="col-md-3 col-form-label">{{translate('Absence Min Days Warning')}}</label>
                                    <div class="col-md-9">
                                        <input type="number" placeholder="{{translate('Absence Min Days Warning')}}"
                                               value="{{$organisation_setting->absence_days_num}}" id="absence_days"
                                               name="absence_days" class="form-control">
                                    </div>
                                </div>

                            @endif

                            <div class="form-group row">
                                <label
                                    class="col-md-3 col-form-label">{{translate('Min Days before to Place Order')}}</label>
                                <div class="col-md-9">
                                    <input type="number" placeholder="{{translate('Min Days before to Place Order')}}"
                                           value="{{$organisation_setting->preorder_days_num}}" id="preorder_days_num"
                                           name="preorder_days_num" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{translate('Working Days')}}</label>
                                <div class="col-md-9">

                                    <div class="form-group row">
                                        <label class="w-90px col-form-label">{{translate('Monday')}}</label>
                                        <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                            @if(in_array('Mon', json_decode($organisation_setting->working_week_days)))
                                                <input type="checkbox" name="monday" id="monday" value=0
                                                       onchange="checkMonday()" checked>
                                            @else
                                                <input type="checkbox" name="monday" id="monday" value=0
                                                       onchange="checkMonday()">
                                            @endif
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="w-90px col-form-label">{{translate('Tuesday')}}</label>

                                        <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                            @if(in_array('Tue', json_decode($organisation_setting->working_week_days)))
                                                <input type="checkbox" name="tuesday" onchange="checkTuesday()" checked>
                                            @else
                                                <input type="checkbox" name="tuesday" onchange="checkTuesday()">
                                            @endif

                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="w-90px col-form-label">{{translate('Wednesday')}}</label>

                                        <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                            @if(in_array('Wed', json_decode($organisation_setting->working_week_days)))
                                                <input type="checkbox" name="wednesday" onchange="checkWednesday()"
                                                       checked>
                                            @else
                                                <input type="checkbox" name="wednesday" onchange="checkWednesday()">
                                            @endif

                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="w-90px col-form-label">{{translate('Thursday')}}</label>
                                        <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                            @if(in_array('Thu', json_decode($organisation_setting->working_week_days)))
                                                <input type="checkbox" name="thursday" onchange="checkThursday()"
                                                       checked>
                                            @else
                                                <input type="checkbox" name="thursday" onchange="checkThursday()">
                                            @endif

                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="w-90px col-form-label">{{translate('Friday')}}</label>
                                        <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                            @if(in_array('Fri', json_decode($organisation_setting->working_week_days)))
                                                <input type="checkbox" name="friday" onchange="checkFriday()" checked>
                                            @else
                                                <input type="checkbox" name="friday" onchange="checkFriday()">
                                            @endif

                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="w-90px col-form-label">{{translate('Saturday')}}</label>

                                        <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                            @if(in_array('Sat', json_decode($organisation_setting->working_week_days)))
                                                <input type="checkbox" name="saturday" onchange="checkSaturday()"
                                                       checked>
                                            @else
                                                <input type="checkbox" name="saturday" onchange="checkSaturday()">
                                            @endif

                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="w-90px col-form-label">{{translate('Sunday')}}</label>

                                        <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                            @if(in_array('Sun', json_decode($organisation_setting->working_week_days)))
                                                <input type="checkbox" name="sunday" onchange="checkSunday()" checked>
                                            @else
                                                <input type="checkbox" name="sunday" onchange="checkSunday()">
                                            @endif
                                            <span></span>
                                        </label>
                                    </div>

                                </div>
                            </div>

                            <div class="py-10px">
                                <h5>{{translate('Holidays')}}</h5>
                            </div>

                            <div id='calendar' class="mt-2 mb-2 mx-auto " style="height: 500px!important;">
                                <input type="hidden" name="holidays[]" id="holidays"
                                       value="{{$organisation_setting->holidays}}">
                            </div>

                            <div class="form-group mb-0 text-right">
                                <a href="{{route('organisation_settings.index', $organisation->id)}}">
                                    <button type="button" class="btn btn-soft-danger">{{translate('Cancel')}}</button>
                                </a>
                                <button type="submit"
                                        class="btn btn-primary save_holidays">{{translate('Save')}}</button>
                            </div>

                        @endif

                    </form>
                </div>

                <?php

                $organisation_price_ranges = $organisation_setting->organisation_price_ranges()->get();

                $max_snack = $organisation_setting->max_snack_quantity;
                $max_meal = $organisation_setting->max_meal_quantity;

//                echo $max_snack;
//                echo $max_meal;

                $temp_snack = 0;
                $temp_meal = 0;

                $temp = 0;

                $counter = 1;
                ?>


                <div class="card-body" id="prices-form" style="display: none">

                    <form class="form-horizontal" id="formId"
                          action="{{ route('organisation_prices.store', ['organisation_setting_id'=>$organisation_setting->id]) }}"
                          method="POST" enctype="multipart/form-data">


                        @csrf
                        <div id="prices-div">


                            <div class=" mr-2 text-right">
                                <button type="button" id="duplicate_card_btn"
                                        class="btn btn-soft-primary btn-icon btn-circle btn-sm fs-15 p-1"
                                        title="{{ translate('Duplicate card') }}">
                                    +
                                </button>

                                <button type="button" id="delete_div_btn"
                                        class="btn btn-soft-danger btn-icon btn-circle btn-sm fs-15 p-1"
                                        title="{{ translate('Delete card') }}"> -
                                </button>

                            </div>

                            @if(sizeof($organisation_price_ranges)==0)

                                <div class="col-auto pb-4" id="price_range_card">

                                    <div class="form-group row">
                                        <label
                                            class="col-md-2 col-form-label fs-15 day-range font-weight-bold ">{{translate('1. Day Range')}}</label>
                                    </div>

                                    <div class="form-group row">

                                        <div class="col-md-1">
                                            <input type="number"
                                                   class="form-control px-12px start-day text-center my-px-0-4em"
                                                   name="start_range[]" value="1" required>
                                        </div>

                                        <label
                                            class="px-3 my-auto col-form-label text-center font-weight-bold fs-15">{{translate('-')}}</label>

                                        <div class="col-md-1 ">
                                            <input type="number"
                                                   class="form-control px-12px end-day text-center my-px-0-4em"
                                                   name="end_range[]"
                                                   required>
                                        </div>

                                    </div>
                                    @if($max_snack>0)
                                        <div class="form-group row">
                                            <label
                                                class="col-md-2 col-form-label fs-15">{{translate('Snack Prices')}}</label>
                                        </div>


                                        <div class="form-group row row-cols-6">">

                                            @while($temp_snack<$max_snack)
                                                    <?php
                                                    $temp_snack = $temp_snack + 1;

                                                    ?>

                                                <div class="col">
                                                    <div class="form-group row gutters-10">
                                                        <label class="col-md-2 fs-15">{{$temp_snack}}</label>
                                                        <div class="col-md-10">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <div
                                                                        class="input-group-text bg-soft-secondary font-weight-medium px-2">
                                                                        €
                                                                    </div>
                                                                </div>
                                                                <input type="number"
                                                                       class="form-control px-10px snack-price"
                                                                       step="0.1"
                                                                       min="1" name="snack_prices[{{$temp_snack-1}}][]"
                                                                       required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endwhile

                                        </div>

                                    @endif

                                    @if($max_meal>0)

                                        <div class="form-group row">
                                            <label
                                                class="col-md-2 col-form-label fs-15">{{translate('Lunch Prices' )}}</label>
                                        </div>

                                        <div class="form-group row row-cols-6">

                                            @while($temp_meal<$max_meal)
                                                    <?php
                                                    $temp_meal = $temp_meal + 1;

                                                    ?>
                                                {{--                                            <label class="col-form-label fs-15 pl-3">{{$temp_meal}}</label>--}}
                                                {{--                                            <div class="w-55px mx-3">--}}
                                                {{--                                                <input type="number" class="form-control px-10px meal-price" step="0.1"--}}
                                                {{--                                                       min="1" name="meal_prices[{{$temp_meal-1}}][]" required>--}}
                                                {{--                                            </div>--}}

                                                <div class="col">
                                                    <div class="form-group row gutters-10">
                                                        <label class="col-md-2 fs-15">{{$temp_meal}}</label>
                                                        <div class="col-md-10">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <div
                                                                        class="input-group-text bg-soft-secondary font-weight-medium px-2">
                                                                        €
                                                                    </div>
                                                                </div>
                                                                <input type="number"
                                                                       class="form-control px-10px meal-price"
                                                                       step="0.1"
                                                                       min="1" name="meal_prices[{{$temp_meal-1}}][]"
                                                                       required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endwhile

                                        </div>

                                    @endif

                                </div>

                            @else

                                @foreach($organisation_price_ranges as $key => $price_range)

                                    <div class="col-auto pb-4" id="price_range_card_{{$temp}}">

                                            <?php
                                            $temp = $temp + 1;
                                            ?>

                                        <div class="form-group row">
                                            <label
                                                class="col-md-2 col-form-label fs-15 day-range font-weight-bold">{{$key+1}}{{translate('. Day Range')}}</label>
                                        </div>

                                        <div class="form-group row">

                                            <div class="col-md-1">
                                                <input type="number"
                                                       class="form-control px-12px text-center start-day my-px-0-4em"
                                                       value="{{$price_range->start_range}}" name="start_range[]"
                                                       required>
                                            </div>

                                            <label
                                                class="px-3 my-auto col-form-label text-center font-weight-bold fs-15">{{translate('-')}}</label>

                                            <div class="col-md-1">
                                                <input type="number"
                                                       class="form-control px-12px end-day text-center my-px-0-4em"
                                                       value="{{$price_range->end_range}}" name="end_range[]" required>
                                            </div>

                                        </div>
                                            <?php
                                            $organisation_prices_snack = $price_range->organisation_prices()->where('type', '=', 'snack')->get();

                                            $organisation_prices_meal = $price_range->organisation_prices()->where('type', '=', 'meal')->get();
                                            ?>


                                        @if($max_snack>0)

                                            <div class="form-group row">
                                                <label
                                                    class="col-md-2 col-form-label fs-15">{{translate('Snack Prices')}}</label>
                                            </div>




                                            <div class="form-group row row-cols-6">

                                                @php
                                                    $count_snack = 0;
                                                @endphp
                                                @foreach($organisation_prices_snack as $snack_price)
                                                    {{--                                                <label--}}
                                                    {{--                                                    class="col-form-label fs-15 pl-3">{{$snack_price->quantity}}</label>--}}
                                                    {{--                                                <div class="w-60px mx-3">--}}
                                                    {{--                                                    <input type="number" value="{{$snack_price->price}}"--}}
                                                    {{--                                                           class="form-control px-10px snack-price" step="0.1" min="1"--}}
                                                    {{--                                                           name="snack_prices[{{$snack_price->quantity-1}}][]" required>--}}
                                                    {{--                                                </div>--}}

                                                    <div class="col">
                                                        <div class="form-group row gutters-10">
                                                            <label
                                                                class="col-md-2 fs-15">{{$snack_price->quantity}}</label>
                                                            <div class="col-md-10">
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <div
                                                                            class="input-group-text bg-soft-secondary font-weight-medium px-2">
                                                                            €
                                                                        </div>
                                                                    </div>
                                                                    <input type="number" value="{{$snack_price->price}}"
                                                                           class="form-control px-10px snack-price"
                                                                           step="0.1" min="1"
                                                                           name="snack_prices[{{$snack_price->quantity-1}}][]"
                                                                           required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @php
                                                        $count_snack++;
                                                    @endphp
                                                @endforeach
                                                @if($count_snack < $max_snack)
                                                    @for($i = $count_snack+1; $i <= $max_snack; $i++)
                                                        <div class="col">
                                                            <div class="form-group row gutters-10">
                                                                <label class="col-md-2 fs-15">{{$i}}</label>
                                                                <div class="col-md-10">
                                                                    <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <div
                                                                                class="input-group-text bg-soft-secondary font-weight-medium px-2">
                                                                                €
                                                                            </div>
                                                                        </div>
                                                                        <input type="number" value=""
                                                                               class="form-control px-10px snack-price"
                                                                               step="0.1" min="1"
                                                                               name="snack_prices[{{$i-1}}][]" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endfor
                                                @endif
                                            </div>

                                        @endif

                                        @if($max_meal>0)
                                            <div class="form-group row">
                                                <label
                                                    class="col-md-2 col-form-label fs-15">{{translate('Lunch Prices')}}</label>
                                            </div>

                                            <div class="form-group row row-cols-6">
                                                @php
                                                    $count_lunch = 0;
                                                @endphp
                                                @foreach($organisation_prices_meal as $meal_price)

                                                    <div class="col">
                                                        <div class="form-group row gutters-10">
                                                            <label
                                                                class="col-md-2 fs-15">{{$meal_price->quantity}}</label>
                                                            <div class="col-md-10">
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <div
                                                                            class="input-group-text bg-soft-secondary font-weight-medium px-2">
                                                                            €
                                                                        </div>
                                                                    </div>
                                                                    <input type="number" value="{{$meal_price->price}}"
                                                                           class="form-control px-10px meal-price"
                                                                           step="0.1" min="1"
                                                                           name="meal_prices[{{$meal_price->quantity-1}}][]"
                                                                           required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @php
                                                        $count_lunch++;
                                                    @endphp
                                                @endforeach
                                                @if($count_lunch < $max_meal)
                                                    @for($i = $count_lunch+1; $i <= $max_meal; $i++)
                                                        <div class="col">
                                                            <div class="form-group row gutters-10">
                                                                <label class="col-md-2 fs-15">{{$i}}</label>
                                                                <div class="col-md-10">
                                                                    <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <div
                                                                                class="input-group-text bg-soft-secondary font-weight-medium px-2">
                                                                                €
                                                                            </div>
                                                                        </div>
                                                                        <input type="number" value=""
                                                                               class="form-control px-10px meal-price"
                                                                               step="0.1" min="1"
                                                                               name="meal_prices[{{$i-1}}][]" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endfor
                                                @endif

                                            </div>


                                    </div>

                                    @endif
                                @endforeach

                            @endif


                        </div>
                        <div class="form-group mb-0 text-right">
                            <a href="{{route('organisation_settings.index', $organisation->id)}}">
                                <button type="button" class="btn btn-soft-danger">{{translate('Cancel')}}</button>
                            </a>
                            <button type="submit" onclick="set_holidays()"
                                    class="btn btn-primary">{{translate('Save')}}</button>
                        </div>

                    </form>

                </div>

                <div class="card-body" id="extra-days-form" style="display: none">
                    <form class="form-horizontal" id="formId"
                          action="{{ route('organisation_extra_days.store', ['organisation_setting_id'=>$organisation_setting->id]) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf

                        <?php
                        $existing_extra_days = \App\Models\OrganisationExtraDay::where('organisation_setting_id', $organisation_setting->id)->get();

                        ?>


                        <div class=" mr-2 text-right">
                            <button class="btn btn-soft-primary btn-icon btn-circle btn-sm fs-15 p-1 add-extra-day"
                                    title="{{ translate('Add Extra Day') }}">
                                +
                            </button>

                            <button class="btn btn-soft-danger btn-icon btn-circle btn-sm fs-15 p-1 remove-extra-day"
                                    title="{{ translate('Remove Extra Day') }}"> -
                            </button>

                        </div>

                        <div class="form-group row pl-2" id="extra-days-div">

                            @if(sizeof($existing_extra_days)<1)
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
                            @else

                                @foreach($existing_extra_days as $key => $existing_extra_day)

                                    @php
                                        $catering_plan_purchase_controller = new \App\Http\Controllers\CateringPlanPurchaseController();

                                        $exist = $catering_plan_purchase_controller->date_exists_in_purchases($organisation->id, Carbon::create($existing_extra_day->date)->format('Y-m-d'));
                                    @endphp
                                    <div class="col-md-4 my-4">

                                        <div class="row d-flex gutters-5">
                                            <input type="date"
                                                   class="form-control px-10px new-date dd_mm_formatted col-lg-10"
                                                   data-date-format="DD/MM/YYYY"
                                                   data-date="{{Carbon::create($existing_extra_day->date)->format('d/m/Y')}}"
                                                   name="extra_day[]" value="{{$existing_extra_day->date}}" required>


                                            <button type="button" data="{{$key+1}}" @if($exist==1) disabled @endif
                                            class="btn btn-soft-danger btn-icon btn-circle btn-sm  ml-2 delete_this_extra_day">
                                                <i class="las la-trash"></i>
                                            </button>


                                        </div>
                                    </div>
                                @endforeach
                            @endif


                        </div>

                        <div class="form-group mb-0 text-right">
                            <a href="{{route('organisation_settings.index', $organisation->id)}}">
                                <button type="button" class="btn btn-soft-danger">{{translate('Cancel')}}</button>
                            </a>
                            <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-4 my-4" id="add-extra" style="display: none">
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

@endsection

@section('script')

    <script type="text/javascript">

        let lan = '{{$lan}}';
        var calStartDate, calEndDate, monthRange;

        let my_start, my_end;

        var holidays = [];

        const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

        let businessDays = [];

        let pr_cards = 1, s, e, yearDifference;

        let old_holidays, calendar, calendarEl;

        var child_count = '{{count($existing_extra_days)}}';

        document.addEventListener('DOMContentLoaded', function () {

            if(lan=='gr'){
                lan = 'el';
            }

            my_start = '{{$organisation_setting->date_from}}';
            my_end = '{{$organisation_setting->date_to}}';

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

                    console.log(holidays);

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

            @if($show_prices==1)
            show_prices();
            @endif
        });


        function show_settings() {

            my_start = '{{$organisation_setting->date_from}}';
            my_end = '{{$organisation_setting->date_to}}';

            businessDays = [];

            @if(in_array('Mon', $working_week_days))
            businessDays.push(1);
            $('input[name="monday"]').prop('checked')
            @endif
            @if(in_array('Tue', $working_week_days))
            businessDays.push(2);
            $('input[name="tuesday"]').prop('checked', true);
            @endif
            @if(in_array('Wed', $working_week_days))
            businessDays.push(3);
            $('input[name="wednesday"]').prop('checked', true);
            @endif
            @if(in_array('Thu', $working_week_days))
            businessDays.push(4);
            $('input[name="thursday"]').prop('checked', true);
            @endif
            @if(in_array('Fri', $working_week_days))
            businessDays.push(5);
            $('input[name="friday"]').prop('checked', true);
            @endif
            @if(in_array('Sat', $working_week_days))
            businessDays.push(5);
            $('input[name="saturday"]').prop('checked', true);
            @endif
            @if(in_array('Sun', $working_week_days))
            businessDays.push(0);
            $('input[name="sunday"]').prop('checked', true);
            @endif

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

                    if (calendar.getEventById(info.dateStr) != null) {
                        console.log('this is event');
                        calendar.getEventById(info.dateStr).remove();

                        for (var i = 0; i < holidays.length; i++) {
                            if (holidays[i] === info.dateStr) {
                                holidays.splice(i, 1);
                            }
                        }

                        (calendar.getEventById(info.dateStr)).remove();

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
                            nextButton.disabled = truef;
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
                events: [
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


            for (var i = 0; i < old_holidays.length; i++) {
                holidays.push(old_holidays[i]);
            }


            $('#prices-form').hide();
            $('#extra-days-form').hide();

            $('#price-btn').removeClass('text-primary');
            $('#extra-day-btn').removeClass('text-primary');
            $('#setting-btn').addClass('text-primary');

            if(moment(my_start).format("M")==moment(my_end).format("M")){
                calendar.changeView('dayGridMonth');
            }

            calendar.render();

            $('#settings-form').show();

            calendar.render();


        }

        function show_prices() {
            $('#prices-form').show();
            $('#price-btn').addClass('text-primary');
            $('#extra-days-form').hide();
            $('#extra-day-btn').removeClass('text-primary');
            $('#settings-form').hide();
            $('#setting-btn').removeClass('text-primary');
        }

        function show_extra_days() {
            $('#extra-days-form').show();
            $('#prices-form').hide();
            $('#settings-form').hide();

            $('#price-btn').removeClass('text-primary');
            $('#extra-day-btn').addClass('text-primary');
            $('#setting-btn').removeClass('text-primary');
        }

        function reCreateCalendar() {

            my_start = $('#start_date').val();
            my_end = $('#end_date').val();
            var hol_arr = [];

            for (var i = 0; i < holidays.length; i++) {
                var h = {
                    id: holidays[i],
                    start: holidays[i],
                    display: 'background',
                    color: 'red'
                };
                hol_arr.push(h);
            }

            // console.log('hol_array:', hol_arr);

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

                        if (calendar.getEventById(info.dateStr) != null) {
                            // console.log('this is event');
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

                        console.log(holidays);

                    },
                    datesSet: function (dateInfo) {
                        var prevButton = calendarEl.querySelector(".fc-customPrev-button");
                        var nextButton = calendarEl.querySelector(".fc-customNext-button");
                        nextButton.disabled = false;
                        prevButton.disabled = false;
                        if (calendar.view.currentEnd >= new Date(my_end)) {
                            // console.log('Disable Next button: ' + calendar.view.currentEnd + ' - ' + new Date(moment(my_end).add(1, "day")));
                            if (nextButton) {
                                nextButton.disabled = true;
                            }
                        } else if (calendar.view.currentStart <= new Date(my_start)) {
                            // console.log('Disable prev button: ' + calendar.view.currentStart + ' - ' + new Date(my_start));
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
                    {{--events: [--}}
                    {{--        @foreach($holidays as $holiday )--}}
                    {{--    {--}}
                    {{--        id: '{{$holiday}}',--}}
                    {{--        start: '{{$holiday}}', --}}
                    {{--        display: 'background',--}}
                    {{--        color: 'red'--}}
                    {{--    },--}}
                    {{--    @endforeach--}}
                    {{--]--}}

                   

                });

                if(moment(my_start).format("M")==moment(my_end).format("M")){
                    calendar.changeView('dayGridMonth');
                }
                calendar.render();

                // console.log(holidays);
            }
        }

        $(".save_holidays").click(function (e) {

            $('#holidays').val([]);

            // console.log($('#holidays').val());

            $('#holidays').val(holidays);

            // console.log("holidays input: ", $('#holidays').val());


        });


        function showAbsenceDays() {
            if (!$('input[name="absence"]').prop('checked')) {
                $('#absence_div').hide();
                $('input[name="absence"]').val(0);
            } else {
                $('#absence_div').show();
                $('input[name="absence"]').val(1);
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
            // //checkHolidays();
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

            // //checkHolidays();
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
            //checkHolidays();
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
            //checkHolidays();
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
            //checkHolidays();
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
            //checkHolidays();
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
            //checkHolidays();
            reCreateCalendar();
        }

        let counter = document.getElementById("prices-div").childElementCount;

        $("#duplicate_card_btn").click(function (e) {
            e.preventDefault();

            var clone = document.getElementById("prices-div").lastElementChild.cloneNode(true);

            clone.getElementsByClassName("day-range")[0].innerHTML = counter + '. Day Range';
            clone.getElementsByClassName("start-day")[0].value = '';
            clone.getElementsByClassName("start-day")[0].removeAttribute("readonly");
            clone.getElementsByClassName("end-day")[0].value = '';

            for (var i = 0; i < clone.getElementsByClassName("snack-price").length; i++) {
                clone.getElementsByClassName("snack-price")[i].value = '';
            }

            for (var i = 0; i < clone.getElementsByClassName("meal-price").length; i++) {
                clone.getElementsByClassName("meal-price")[i].value = '';
            }

            counter++;

            // console.log(clone);
            document.getElementById("prices-div").appendChild(clone);

        });

        $("#delete_div_btn").click(function (e) {
            e.preventDefault();

            var price_range_div = document.getElementById("prices-div");

            if (price_range_div.childElementCount > 2) {
                counter--;
                price_range_div.removeChild(price_range_div.lastElementChild);
            }

        });

        $(".add-extra-day").click(function (e) {
            e.preventDefault();

            console.log(document.getElementById("extra-days-div").lastElementChild);

            var clone;

            if (document.getElementById("extra-days-div").lastElementChild == null) {
                clone = document.getElementById("add-extra").cloneNode(true);
                clone.style.display = '';
            } else {
                clone = document.getElementById("extra-days-div").lastElementChild.cloneNode(true);



            }
            (clone.getElementsByClassName("new-date")[0]).setAttribute("data-date", '');
            (clone.getElementsByClassName("new-date")[0]).value = '';

            (clone.getElementsByClassName("new-date")[0]).onchange = function () {
                if ((clone.getElementsByClassName("new-date")[0]).value.length > 0) {
                    this.setAttribute(
                        "data-date",
                        moment(this.value, "YYYY-MM-DD").format(((clone.getElementsByClassName("new-date")[0])).getAttribute("data-date-format"))
                    );
                }
            }

            clone.getElementsByClassName("delete_this_extra_day")[0].setAttribute("data", (child_count + 1));
            clone.getElementsByClassName("delete_this_extra_day")[0].disabled = false;


            document.getElementById("extra-days-div").appendChild(clone);
            child_count = child_count + 1;

        });

        $(".remove-extra-day").click(function (e) {
            e.preventDefault();

            var extra_day_div = document.getElementById("extra-days-div");

            // extra_day_div.removeChild(extra_day_div.lastElementChild);

            var last_child = extra_day_div.lastElementChild;

            var el = last_child.getElementsByClassName("btn-soft-danger")[0];

            while(el.disabled==true){

                last_child = last_child.previousElementSibling ;


                if(last_child!=null){
                    // console.log('previus_sibling: ', last_child);

                    el = last_child.getElementsByClassName("btn-soft-danger")[0];

                    if(el.disabled==false){
                        extra_day_div.removeChild(last_child);
                    }
                }


            }


        });

        $(document).on('click', '.delete_this_extra_day', function () {
            $(this).parents('div.col-md-4').remove();
        });



    </script>

@endsection
