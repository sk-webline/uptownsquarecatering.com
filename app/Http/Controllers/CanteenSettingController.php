<?php

namespace App\Http\Controllers;

use App\Models\ApiClient\ApiClient;
use App\Models\CanteenExtraDay;
use App\Models\CanteenPurchase;
use App\Models\CanteenSetting;
use App\Models\OrganisationBreak;
use App\Models\OrganisationLocation;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\UriInterface;
use Illuminate\Http\Request;
use Auth;
use App\Models\Organisation;
use App\Models\OrganisationSetting;
use App\Models\Card;
use App\Models\ApiClient\ZeroVendingApiMethods;

class CanteenSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($organisation_id)
    {
        $organisation = Organisation::findOrFail($organisation_id);

        if ($organisation->canteen == 1) {
            return view('backend.organisation.canteen.canteen_settings.create', compact('organisation'));
        } else {
            return back();
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $organisation_id)
    {

        $request->validate([
            'start_date' => 'required',
            'end_date' => 'required',
            'from_hour' => 'required',
            'to_hour' => 'required',
            'minimum_preorder_minutes' => 'required|numeric|gt:0',
            'minimum_cancellation_minutes' => 'required|numeric|gt:0',
            'access_minutes' => 'required|numeric|gt:0',
            'max_undo_delivery_minutes' => 'required|numeric|gt:0'
        ]);

        if ($request->start_date >= $request->end_date) {
            flash(translate('End Date should be greater than Start Date!'))->error();
            return redirect()->back()->withInput($request->all());
        }

        $organisation = Organisation::findorfail($organisation_id);
        $existing_canteen_settings = CanteenSetting::where('organisation_id', $organisation->id)->get();

        $this_start = Carbon::create($request->start_date);
        $this_end = Carbon::create($request->end_date);

        // check existing periods
        foreach ($existing_canteen_settings as $setting) {

            $check_start = Carbon::create($setting->date_from);
            $check_end = Carbon::create($setting->date_to);

            if ($this_start->gte($check_start) && $this_start->lte($check_start)) {
                flash(translate(translate('There is already a period registered in between this date range!')))->error();
                return redirect()->back()->withInput($request->all());
            } else if ($this_start->lte($check_start) && $this_end->gte($check_end)) {
                flash(translate(translate('There is already a period registered in between this date range!')))->error();
                return redirect()->back()->withInput($request->all());
            } else if ($this_start->lte($check_start) && $this_end->gte($check_start)) {
                flash(translate(translate('There is already a period registered in between this date range!')))->error();
                return redirect()->back()->withInput($request->all());
            } else if ($this_start->gte($check_start) && $this_end->lte($check_end)) {
                flash(translate(translate('There is already a period registered in between this date range!')))->error();
                return redirect()->back()->withInput($request->all());
            }

        }

        if (in_array(null, $request->from_hour) || in_array(null, $request->to_hour)) {
            flash(translate('Please fill all inputs of break hours'))->error();
            return back();
        }

//        break hours
        foreach ($request->from_hour as $key => $starting_hour) {

            if ($request->from_hour[$key] >= $request->to_hour[$key]) {
                flash(translate('Break End Hour should be greater than Start Hour'))->error();
                return back();
            }

            if ($key > 0) {
                if ($request->from_hour[$key] < $request->to_hour[$key - 1]) {
                    flash(translate('Break Hours should be sorted'))->error();
                    return back();
                }
            }

        }

        $canteen_setting = new CanteenSetting();
        $canteen_setting->organisation_id = $organisation_id;
        $canteen_setting->minimum_preorder_minutes = $request->minimum_preorder_minutes;
        $canteen_setting->minimum_cancellation_minutes = $request->minimum_cancellation_minutes;
        $canteen_setting->access_minutes = $request->access_minutes;
        $canteen_setting->max_undo_delivery_minutes = $request->max_undo_delivery_minutes;

        $canteen_setting->date_from = $request->start_date;
        $canteen_setting->date_to = $request->end_date;

        $working_week_days = array();

        if ($request->has('monday')) {
            $working_week_days[] = 'Mon';
        }

        if ($request->has('tuesday')) {
            $working_week_days[] = 'Tue';
        }

        if ($request->has('wednesday')) {
            $working_week_days[] = 'Wed';
        }

        if ($request->has('thursday')) {
            $working_week_days[] = 'Thu';
        }

        if ($request->has('friday')) {
            $working_week_days[] = 'Fri';
        }

        if ($request->has('saturday')) {
            $working_week_days[] = 'Sat';
        }

        if ($request->has('sunday')) {
            $working_week_days[] = 'Sun';
        }

        $canteen_setting->working_week_days = json_encode($working_week_days);

        $working_days_january = array();
        $working_days_february = array();
        $working_days_march = array();
        $working_days_april = array();
        $working_days_may = array();
        $working_days_june = array();
        $working_days_july = array();
        $working_days_august = array();
        $working_days_september = array();
        $working_days_october = array();
        $working_days_november = array();
        $working_days_december = array();

        $start_date = date_create($request->start_date);
        $end_date = date_create($request->end_date);

        $holidays = array();
        $holidays_length = 0;
        $count = 0;

        if ($request->has('holidays')) {
            foreach (explode(',', $request->holidays[0]) as $key => $value) {
                $holidays[] = $value;
                $holidays_length++;
            }
        }

        while ($start_date <= $end_date) {

            $date = $start_date->format('Y-m-d');
            $day = date('D', strtotime($date));
            $is_business_day = 0;

            foreach ($working_week_days as $week_day) {
                if ($day == $week_day) {
                    $is_business_day = 1;
                }
            };

            if ($holidays !== []) {

                if ($date != $holidays[$count] && $is_business_day == 1) {

                    $month = date('F', strtotime($date));

                    if ($month == 'January') {
                        $working_days_january[] = $date;

                    } else if ($month == 'February') {
                        $working_days_february[] = $date;

                    } else if ($month == 'March') {
                        $working_days_march[] = $date;

                    } else if ($month == 'April') {
                        $working_days_april[] = $date;

                    } else if ($month == 'May') {
                        $working_days_may[] = $date;

                    } else if ($month == 'June') {
                        $working_days_june[] = $date;

                    } else if ($month == 'July') {
                        $working_days_july[] = $date;

                    } else if ($month == 'August') {
                        $working_days_august[] = $date;

                    } else if ($month == 'September') {
                        $working_days_september[] = $date;

                    } else if ($month == 'October') {
                        $working_days_october[] = $date;

                    } else if ($month == 'November') {
                        $working_days_november[] = $date;

                    } else {
                        $working_days_december[] = $date;
                    }

                } else {

                    if (($count + 1) < $holidays_length) {
                        $count++;
                    }
                }

            }

            $start_date->modify('+1 day');

        }

        $canteen_setting->working_days_january = json_encode($working_days_january);
        $canteen_setting->working_days_february = json_encode($working_days_february);
        $canteen_setting->working_days_march = json_encode($working_days_march);
        $canteen_setting->working_days_april = json_encode($working_days_april);
        $canteen_setting->working_days_may = json_encode($working_days_may);
        $canteen_setting->working_days_june = json_encode($working_days_june);
        $canteen_setting->working_days_july = json_encode($working_days_july);
        $canteen_setting->working_days_august = json_encode($working_days_august);
        $canteen_setting->working_days_september = json_encode($working_days_september);
        $canteen_setting->working_days_october = json_encode($working_days_october);
        $canteen_setting->working_days_november = json_encode($working_days_november);
        $canteen_setting->working_days_december = json_encode($working_days_december);

        $canteen_setting->holidays = json_encode($holidays);

        if ($canteen_setting->save()) {

            foreach ($request->from_hour as $key => $starting_hour) {

                $organisation_break = new OrganisationBreak();
                $organisation_break->break_num = $key+1;
                $organisation_break->organisation_id = $organisation->id;
                $organisation_break->canteen_setting_id = $canteen_setting->id;
                $organisation_break->hour_from = $request->from_hour[$key];
                $organisation_break->hour_to =  $request->to_hour[$key];

                $organisation_break->save();

            }

            if ($request->extra_days != null){

                $extra_days =  explode(",", $request->extra_days);

                foreach ($extra_days as $extra_day){
                    $canteen_extra_day = new CanteenExtraDay();
                    $canteen_extra_day->canteen_setting_id = $canteen_setting->id;
                    $canteen_extra_day->date = $extra_day;
                    $canteen_extra_day->created_by = Auth::user()->id;
                    $canteen_extra_day->save();
                }

            }

            flash(translate('Canteen Period created successfully'))->success();
            return redirect()->route('canteen.index', $organisation->id);


        } else {
            flash(translate('Sorry! Something went wrong!'))->error();
            return redirect()->back();

        }

    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $canteen_setting =CanteenSetting::findOrFail($id);

        $organisation = $canteen_setting->organisation;

        if ($organisation->canteen == 1) {
            return view('backend.organisation.canteen.canteen_settings.edit', compact('organisation', 'canteen_setting'));
        } else {
            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

//        dd($request->all());

        $canteen_setting = CanteenSetting::findorfail($id);
        $organisation = Organisation::findorfail($canteen_setting->organisation_id);

        $request->validate([
            'start_date' => 'required',
            'end_date' => 'required',
            'from_hour' => 'required',
            'to_hour' => 'required',
            'minimum_preorder_minutes' => 'required|numeric|gt:0',
            'minimum_cancellation_minutes' => 'required|numeric|gt:0',
            'access_minutes' => 'required|numeric|gte:0',
            'max_undo_delivery_minutes' => 'required|numeric|gt:0'
        ]);

        if ($request->start_date >= $request->end_date) {
            flash(translate('End Date should be greater than Start Date!'))->error();
            return redirect()->back()->withInput($request->all());
        }

        $existing_canteen_settings = CanteenSetting::where('organisation_id', $organisation->id)->where('id', '!=', $canteen_setting->id )->get();

        $this_start = Carbon::create($request->start_date);
        $this_end = Carbon::create($request->end_date);

        // check existing periods
        foreach ($existing_canteen_settings as $setting) {

            $check_start = Carbon::create($setting->date_from);
            $check_end = Carbon::create($setting->date_to);

            if ($this_start->gte($check_start) && $this_start->lte($check_start)) {
                flash(translate(translate('There is already a period registered in between this date range!')))->error();
                return redirect()->back()->withInput($request->all());
            } else if ($this_start->lte($check_start) && $this_end->gte($check_end)) {
                flash(translate(translate('There is already a period registered in between this date range!')))->error();
                return redirect()->back()->withInput($request->all());
            } else if ($this_start->lte($check_start) && $this_end->gte($check_start)) {
                flash(translate(translate('There is already a period registered in between this date range!')))->error();
                return redirect()->back()->withInput($request->all());
            } else if ($this_start->gte($check_start) && $this_end->lte($check_end)) {
                flash(translate(translate('There is already a period registered in between this date range!')))->error();
                return redirect()->back()->withInput($request->all());
            }

        }

        if (in_array(null, $request->from_hour) || in_array(null, $request->to_hour)) {
            flash(translate('Please fill all inputs of break hours'))->error();
            return back();
        }

        $carbon = Carbon::now();
        $active_purchases = CanteenPurchase::where('canteen_setting_id', $canteen_setting->id)->get();
        $active_purchases_dates = [];

        if(count($active_purchases) > 0){
            $active_purchases = true;
            $active_purchases_dates = CanteenPurchase::where('canteen_setting_id', $canteen_setting->id)->where('date', '>=', $carbon->format('Y-m-d'))->orderBy('date')->pluck('date')->toArray();
            $active_purchases_dates = array_unique($active_purchases_dates);
            $last_purchase_date = end($active_purchases_dates);
            if($request->end_date < $last_purchase_date){
                flash(translate('End Date should be greater than the last date of an upcoming purchase (' . $last_purchase_date . ')'))->error();
                return back();
            }
        }else{
            $active_purchases = false;
        }

//        break hours
        foreach ($request->from_hour as $key => $starting_hour) {

            if ($request->from_hour[$key] >= $request->to_hour[$key]) {
                flash(translate('Break End Hour should be greater than Start Hour'))->error();
                return back();
            }

            if ($key > 0) {
                if ($request->from_hour[$key] < $request->to_hour[$key - 1]) {
                    flash(translate('Break Hours should be sorted'))->error();
                    return back();
                }
            }

        }

        $canteen_setting->organisation_id = $organisation->id;
        $canteen_setting->date_from = $request->start_date;
        $canteen_setting->date_to = $request->end_date;

        $canteen_setting->minimum_preorder_minutes = $request->minimum_preorder_minutes;
        $canteen_setting->minimum_cancellation_minutes = $request->minimum_cancellation_minutes;
        $canteen_setting->access_minutes = $request->access_minutes;
        $canteen_setting->max_undo_delivery_minutes = $request->max_undo_delivery_minutes;

        if($active_purchases == false){
            $working_week_days = array();

            if ($request->has('monday')) {
                $working_week_days[] = 'Mon';
            }

            if ($request->has('tuesday')) {
                $working_week_days[] = 'Tue';
            }

            if ($request->has('wednesday')) {
                $working_week_days[] = 'Wed';
            }

            if ($request->has('thursday')) {
                $working_week_days[] = 'Thu';
            }

            if ($request->has('friday')) {
                $working_week_days[] = 'Fri';
            }

            if ($request->has('saturday')) {
                $working_week_days[] = 'Sat';
            }

            if ($request->has('sunday')) {
                $working_week_days[] = 'Sun';
            }

            $canteen_setting->working_week_days = json_encode($working_week_days);
        }else{
            $working_week_days = json_decode($canteen_setting->working_week_days);
        }


        $working_days_january = array();
        $working_days_february = array();
        $working_days_march = array();
        $working_days_april = array();
        $working_days_may = array();
        $working_days_june = array();
        $working_days_july = array();
        $working_days_august = array();
        $working_days_september = array();
        $working_days_october = array();
        $working_days_november = array();
        $working_days_december = array();

        $start_date = date_create($request->start_date);
        $end_date = date_create($request->end_date);

        $holidays = array();
        $holidays_length = 0;
        $count = 0;

        if ($request->has('holidays')) {
            foreach (explode(',', $request->holidays[0]) as $key => $value) {

                if($active_purchases && in_array($value, $active_purchases_dates)){
                    flash(translate('Holiday and Upcoming Purchase date conflict'))->error();
                    return back();
                }

                $holidays[] = $value;
                $holidays_length++;
            }
        }

        while ($start_date <= $end_date) {

            $date = $start_date->format('Y-m-d');
            $day = date('D', strtotime($date));
            $is_business_day = 0;

            foreach ($working_week_days as $week_day) {
                if ($day == $week_day) {
                    $is_business_day = 1;
                }
            };

            if ($holidays !== []) {

                if ($date != $holidays[$count] && $is_business_day == 1) {

                    $month = date('F', strtotime($date));

                    if ($month == 'January') {
                        $working_days_january[] = $date;

                    } else if ($month == 'February') {
                        $working_days_february[] = $date;

                    } else if ($month == 'March') {
                        $working_days_march[] = $date;

                    } else if ($month == 'April') {
                        $working_days_april[] = $date;

                    } else if ($month == 'May') {
                        $working_days_may[] = $date;

                    } else if ($month == 'June') {
                        $working_days_june[] = $date;

                    } else if ($month == 'July') {
                        $working_days_july[] = $date;

                    } else if ($month == 'August') {
                        $working_days_august[] = $date;

                    } else if ($month == 'September') {
                        $working_days_september[] = $date;

                    } else if ($month == 'October') {
                        $working_days_october[] = $date;

                    } else if ($month == 'November') {
                        $working_days_november[] = $date;

                    } else {
                        $working_days_december[] = $date;
                    }

                } else {

                    if (($count + 1) < $holidays_length) {
                        $count++;
                    }
                }

            }

            $start_date->modify('+1 day');

        }

        $canteen_setting->working_days_january = json_encode($working_days_january);
        $canteen_setting->working_days_february = json_encode($working_days_february);
        $canteen_setting->working_days_march = json_encode($working_days_march);
        $canteen_setting->working_days_april = json_encode($working_days_april);
        $canteen_setting->working_days_may = json_encode($working_days_may);
        $canteen_setting->working_days_june = json_encode($working_days_june);
        $canteen_setting->working_days_july = json_encode($working_days_july);
        $canteen_setting->working_days_august = json_encode($working_days_august);
        $canteen_setting->working_days_september = json_encode($working_days_september);
        $canteen_setting->working_days_october = json_encode($working_days_october);
        $canteen_setting->working_days_november = json_encode($working_days_november);
        $canteen_setting->working_days_december = json_encode($working_days_december);

        $canteen_setting->holidays = json_encode($holidays);

        if ($canteen_setting->save()) {

            if($active_purchases == false){
                foreach ($canteen_setting->breaks as $key => $break) {
                    $break->delete();
                }

                foreach ($request->from_hour as $key => $starting_hour) {

                    $organisation_break = new OrganisationBreak();
                    $organisation_break->break_num = $key+1;
                    $organisation_break->organisation_id = $organisation->id;
                    $organisation_break->canteen_setting_id = $canteen_setting->id;
                    $organisation_break->hour_from = $request->from_hour[$key];
                    $organisation_break->hour_to =  $request->to_hour[$key];

                    $organisation_break->save();

                }
            }else{

                $last_key = OrganisationBreak::where('canteen_setting_id', $canteen_setting->id)->count();

                foreach ($request->from_hour as $key => $starting_hour) {

                    $break = OrganisationBreak::where('canteen_setting_id', $canteen_setting->id)
                        ->where('hour_from', $request->from_hour[$key])->where('hour_to',$request->to_hour[$key])->first();

                    if($break == null){
                        if($key+1 > $last_key){
                            $organisation_break = new OrganisationBreak();
                            $organisation_break->break_num = $key+1;
                            $organisation_break->organisation_id = $organisation->id;
                            $organisation_break->canteen_setting_id = $canteen_setting->id;
                            $organisation_break->hour_from = $request->from_hour[$key];
                            $organisation_break->hour_to =  $request->to_hour[$key];
                            $organisation_break->save();
                        }else{
                            flash(translate('Something went wrong with the Organisation Breaks'))->error();
                            return redirect()->back();
                        }
                    }

                }



            }



            flash(translate('Canteen Period updated successfully'))->success();
            return redirect()->route('canteen.index', $organisation->id);


        } else {
            flash(translate('Sorry! Something went wrong!'))->error();
            return redirect()->back();

        }


        dd($request->all());



    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $canteen_settings = CanteenSetting::findorfail($id);

        $active_purchases = CanteenPurchase::where('canteen_setting_id', $canteen_settings->id)->get();

        if(count($active_purchases) > 0){
            flash(translate('Sorry! Delete is unavailable because of purchases!'))->error();
            return redirect()->back();
        }

        $breaks = OrganisationBreak::where('canteen_setting_id', $canteen_settings->id);

        foreach ($breaks as $break){
//            $break->delete();
        }

        $canteen_settings->delete();

        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function get_settings_details(Request $request)
    {
        $organisation = Organisation::find($request->organisation_id);

        if ($organisation == null){
            return response()->json([
                'status'=> 0,
                'message' => translate('Organisation not found')
            ]);
        }

        $canteen_period = CanteenSetting::find($request->canteen_setting_id);

        if ($canteen_period == null){
            return response()->json([
                'status'=> 0,
                'message' => translate('Canteen Period not found')
            ]);
        }

        if ($canteen_period->organisation_id != $organisation->id){
            return response()->json([
                'status'=> 0,
                'message' => translate('Canteen Period and Organisation do not match')
            ]);
        }

        if($canteen_period->date_to < Carbon::today()->format('Y-m-d')){
            return response()->json([
                'status'=> 0,
                'message' => translate('Canteen Period is expired')
            ]);
        }

        $extra_days = CanteenExtraDay::where('canteen_setting_id', $canteen_period->id)->pluck('date');

        return response()->json([
            'status'=> 1,
            'start_date' => Carbon::create($canteen_period->date_from)->format('Y-m-d') ,
            'end_date' => Carbon::create($canteen_period->date_to)->format('Y-m-d') ,
            'working_week_days' => json_decode($canteen_period->working_week_days),
            'holidays' => json_decode($canteen_period->holidays),
            'extra_days' => $extra_days
        ]);


    }




}
