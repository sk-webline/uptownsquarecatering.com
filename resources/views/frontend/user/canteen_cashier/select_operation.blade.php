@extends('frontend.layouts.app_cashier')

@section('content')


    <?php

    use Carbon\Carbon;
    use Illuminate\Support\Facades\Session;
    use App\Models\CanteenLocation;
    use App\Models\Organisation;


    $canteen_location = CanteenLocation::find(Session::get('location_id'));

    $organisation = Organisation::find($canteen_location->organisation_id);
    $canteen_setting = $organisation->current_canteen_settings();

    $breaks = $organisation->breaks;

    $accessible_break = null;

    $carbon_now = Carbon::now();

    foreach ($breaks as $break){

        $carbon_start = Carbon::create($carbon_now->format('Y-m-d') . ' ' . $break->hour_from)->subMinutes($canteen_setting->access_minutes);
        $carbon_end = Carbon::create($carbon_now->format('Y-m-d') . ' ' . $break->hour_to)->addMinutes($canteen_setting->access_minutes);

        if($carbon_now->gte($carbon_start) && $carbon_now->lte($carbon_end)){
            $accessible_break = $break;
            break;
        }

    }

    ?>

    <div id="content_body" class="mh-100-svh" >

        @if($accessible_break!=null)
            <div class="row justify-content-end px-15px">
                <div class="col-auto text-white bg-primary p-1">
                    {{toUpper(ordinal($accessible_break->break_num))}} {{toUpper(translate('Break'))}}: {{substr($accessible_break->hour_from, 0, 5)}}
                </div>
            </div>
        @endif

        @include('frontend.inc.canteen_cashier_nav', ['route' => null, 'route_text' => null])

        <div class="cashier-body">
            <div class="cashier-grid flex-grow-1 d-flex flex-column justify-content-center pb-10px pb-md-50px">
                <div class="row xl-gutters-35">

                    <div class="col-lg-6 mb-20px mb-lg-0">


                        <div class="background-soft-grey cashier-dashboard-box fw-500 h-100">
                              <form name="export_form" action="{{route('canteen_cashier.report_export')}}" method="POST" target="_blank" class="position-relative h-100">
                                  @csrf

                                  <input type="hidden" name="timestamp" value="{{\Carbon\Carbon::now()->format('d/m/y H:i:S')}}">
                                <input type="hidden" name="export_type" value="total_quantities">

                                <div class="px-60px h-100 cashier-dashboard-box-wrap ">

                                    <div class="mb-15px d-flex align-items-end justify-content-start">
                                        <svg class="h-50px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 79.88 95.91" fill="var(--primary)">
                                             <use xlink:href="{{static_asset('assets/img/icons/reports.svg')}}#content"></use>
                                        </svg>
                                        <span class="lg-fs-35 md-fs-25 sm-fs-25 fw-600 px-10px">{{toUpper(translate('Reports'))}}</span>

                                    </div>

                                    <div class="form-group fs-20">
                                           <input type="date" name="selected_date" data-date-format="DD/MM/YYYY" data-date="Select Date"
                                                       class="form-control dd_mm_formatted" placeholder="Select Date" required>
                                    </div>

                                    <div class="form-group mb-20px mb-md-25px fs-20">
                                        <div class="form-control-with-label cust always-focused">
                                            <label class="fs-14 opacity-50">{{translate('Select Break')}}</label>
                                            <select class="form-control sk-selectpicker fw-500" name="selected_break_id" required>
                                                @foreach ($breaks as $key => $break)
                                                    <option value="{{$break->id }}"> {{ordinal($break->break_num)}} {{translate('Break')}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="position-absolute bottom-0 w-100 row no-gutters">
                                    <div class="col">
                                        <button class="btn btn-outline-primary btn-block fs-20 fw-600 border border-width-2 border-primary justify-content-center quantities-report d-flex align-items-center" type="submit">
                                            <svg class="h-15px mr-2"
                                                 xmlns="http://www.w3.org/2000/svg"
                                                 viewBox="0 0 34.92 18.66">
                                                <use
                                                    xlink:href="{{static_asset('assets/img/icons/meal-button.svg')}}#content"></use>
                                            </svg>
                                            {{toUpper(translate('Generate Meal Report'))}}
                                        </button>
                                    </div>
                                    <div class="col">
                                      <button class="btn btn-outline-primary btn-block fs-20 fw-600 border border-width-2 border-primary meal-codes-report d-flex align-items-center justify-content-center" type="submit">
                                          <svg class="h-20px mr-2 order-button"
                                               xmlns="http://www.w3.org/2000/svg"
                                               viewBox="0 0 20.49 26.09">
                                              <use
                                                  xlink:href="{{static_asset('assets/img/icons/order-button.svg')}}#content"></use>
                                          </svg>
                                          {{toUpper(translate('Generate Order Report'))}}
                                      </button>
                                    </div>
                                </div>
                            </form>
                       </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="text-center background-soft-grey cashier-dashboard-box">
                            <div class="d-flex flex-column p-0">
                                <div class="cashier-dashboard-box-wrap p-sm-40px">
                                    <span class="d-flex flex-grow-1 flex-column justify-content-center">
                                                 <svg class="h-50px h-sm-100px h-lg-150px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 171.42 125.76" fill="var(--primary)">
                                                     <use xlink:href="{{static_asset('assets/img/icons/scans.svg')}}#content"></use>
                                                 </svg>
                                             </span>

                                    <span class="d-block fs-15 md-fs-20 text-primary-60 fw-700 l-space-05 mt-10px mt-md-20px">{{toUpper(translate('RFID SCAN'))}}</span>
                                </div>

                                <a href="{{route('canteen_cashier.dashboard')}}" class="btn btn-primary btn-block fs-20 fw-500">
                                    {{toUpper(translate('Start Scanning'))}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
{{--            <div class="cashier-grid flex-grow-1 d-flex flex-column justify-content-center pb-10px pb-md-50px">--}}

{{--                <div class="row">--}}
{{--                    <div class="col-md-6 col-12 card">--}}
{{--                        <div class="bg-primary-20">--}}
{{--                            <span>opa</span>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="col-md-6 col-12 card">--}}
{{--                        <div class="bg-primary-20">--}}
{{--                            <span>opa</span>--}}
{{--                        </div>--}}

{{--                    </div>--}}

{{--                </div>--}}
{{--            </div>--}}

            <div class="position-absolute text-white text-center fs-12 sm-fs-14 md-fs-18 xxl-fs-27 lh-1 scans-report">
                <div class="row no-gutters">
                    <div class="col-6 col-sm-auto bg-primary">
                        <div class="px-md-20px p-5px">
                            <span class="fw-300">{{toUpper(translate('Served kids: '))}} </span>
                            <span id="success_scans" class="fw-600">
                                @if(!Session::has('canteen_served_kids')) 0 @else {{ Session::get('canteen_served_kids') }} @endif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('frontend.inc.canteen_cashier_footer')
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        $('.dd_mm_formatted').on("change keyup keypress", function() {
            if (this.value.length > 0) {
                this.setAttribute(
                    "data-date",
                    moment(this.value, "YYYY-MM-DD").format(this.getAttribute("data-date-format"))
                );
            }else{
                this.setAttribute("data-date", "Select Date");
            }

        }).trigger("change");



        $(document).on('click', '.meal-codes-report', function (){

            $('input[name=export_type]').val('meal_codes');
            // $('form[name=export_form]').submit();
        });

        $(document).on('click', '.quantities-report', function (){

            $('input[name=export_type]').val('total_quantities');
            // $('form[name=export_form]').submit();
        });





        // Returns a function, that, as long as it continues to be invoked, will not
        // be triggered. The function will be called after it stops being called for
        // N milliseconds. If `immediate` is passed, trigger the function on the
        // leading edge, instead of the trailing.
        function debounce(func, wait, immediate) {
            var timeout;
            return function () {
                var context = this, args = arguments;
                var later = function () {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        };
    </script>
@endsection
