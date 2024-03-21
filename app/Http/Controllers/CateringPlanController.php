<?php

namespace App\Http\Controllers;

use App\Models\ApiClient\ApiClient;
use App\Models\Card;
use App\Models\CateringPlan;
use App\Models\CateringPlanPurchase;
use App\Models\Organisation;
use App\Models\OrganisationExtraDay;
use App\Models\OrganisationPrice;
use App\Models\OrganisationPriceRange;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Session;
use Mpdf\Tag\Input;
use Psr\Http\Message\UriInterface;
use Illuminate\Http\Request;
use Auth;
use App\Models\OrganisationSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class CateringPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $organisation_setting_id)
    {
        $sort_search = null;

        $organisation_setting = OrganisationSetting::findorfail($organisation_setting_id);

        $catering_plans = $organisation_setting->catering_plans();


        if ($request->has('search')) {
            $sort_search = $request->search;
            $catering_plans = $catering_plans->where('name', 'like', '%' . $sort_search . '%');
        }

        $catering_plans = $catering_plans->paginate(15); //->get();

        return view('backend.organisation.organisation_settings.catering_plans.index', compact('organisation_setting', 'catering_plans', 'sort_search'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($organisation_settings_id)
    {

        $organisation_setting = OrganisationSetting::findorfail($organisation_settings_id);

        $all_prices = DB::table('organisation_price_ranges')
            ->join('organisation_prices', 'organisation_price_ranges.id', '=', 'organisation_prices.organisation_price_range_id')
            ->where('organisation_price_ranges.organisation_setting_id', '=', $organisation_setting->id)
            ->select('organisation_prices.id', 'organisation_price_ranges.start_range', 'organisation_price_ranges.end_range', 'organisation_prices.type',
                'organisation_prices.quantity', 'organisation_prices.price')
            ->get();


        return view('backend.organisation.organisation_settings.catering_plans.create', compact('organisation_setting', 'all_prices'));


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $organisation_setting_id)
    {


        $organisation_setting = OrganisationSetting::findorfail($organisation_setting_id);

        $catering_plan = new CateringPlan();
        $catering_plan->organisation_setting_id = $organisation_setting_id;
        $catering_plan->name = $request->name;
        $catering_plan->description = $request->description;
        $catering_plan->from_date = $request->from_date;
        $catering_plan->to_date = $request->to_date;
        $catering_plan->publish_date = $request->publish_date;

        if ($catering_plan->from_date >= $catering_plan->to_date) {

            flash(translate('End Date should be greater than Start Date!'))->error();
            return redirect()->back()->withInput($request->all());
        }

        if ($catering_plan->publish_date > $catering_plan->from_date) {

            flash(translate('Start Date should be greater than Publish Date!'))->error();
            return redirect()->back()->withInput($request->all());

        }

        if ($request->has('active')) {
            $catering_plan->active = 1;
        } else {
            $catering_plan->active = 0;
        }

        $working_week_days = json_decode($organisation_setting->working_week_days);

        $holidays = json_decode($organisation_setting->holidays);

        $num_of_working_days = 0;

        $this_date = Carbon::create($request->from_date);

        $last_date = Carbon::create($request->to_date);


        while ($last_date->gte($this_date)) {

            $day = substr($this_date->format('l'), 0, 3);

            if (in_array($day, $working_week_days)) {

                if (!in_array($this_date->format('Y-m-d'), $holidays)) {

                    $num_of_working_days = $num_of_working_days + 1;

                }
            } else {
                if ($organisation_setting->extra_days()->where('date', '=', $this_date->format('Y-m-d'))->count() > 0) {
                    $num_of_working_days = $num_of_working_days + 1;

                }
            }


            $this_date->addDay();

        }


        $catering_plan->num_of_working_days = $num_of_working_days;
        $catering_plan->price = $request->price;
        $catering_plan->snack_num = $request->snack_num;
        $catering_plan->meal_num = $request->meal_num;


        if ($catering_plan->save()) {
            flash(translate('Catering Plan has been inserted successfully'))->success();

        } else {
            flash(translate('Sorry! Something went wrong!'))->error();

        }

        return redirect()->route('catering_plans.index', $organisation_setting->id);

    }

    public function remove(Request $request)
    {

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

        $catering_plan = CateringPlan::findOrFail($id);

        $organisation_setting = $catering_plan->organisation_setting()->get()[0];

        $all_prices = DB::table('organisation_price_ranges')
            ->join('organisation_prices', 'organisation_price_ranges.id', '=', 'organisation_prices.organisation_price_range_id')
            ->where('organisation_price_ranges.organisation_setting_id', '=', $organisation_setting->id)
            ->select('organisation_prices.id', 'organisation_price_ranges.start_range', 'organisation_price_ranges.end_range', 'organisation_prices.type',
                'organisation_prices.quantity', 'organisation_prices.price')
            ->get();

        $purchased = CateringPlanPurchase::where('catering_plan_id', $catering_plan->id)->count();

        if ($purchased > 0) {
            $purchased = 1;
        }


        return view('backend.organisation.organisation_settings.catering_plans.edit', compact('organisation_setting', 'catering_plan', 'all_prices', 'purchased'));

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


        $catering_plan = CateringPlan::findorfail($id);

        $organisation_setting = OrganisationSetting::findorfail($catering_plan->organisation_setting_id);

        $catering_plan->name = $request->name;
        $catering_plan->description = $request->description;

        if ($request->from_date >= $request->to_date) {

            flash(translate('End Date should be greater than Start Date1'))->error();
            return redirect()->back()->withInput($request->all());
        }

        if ($request->publish_date > $request->from_date) {

            flash(translate('Start Date should be greater than Publish Date!'))->error();
            return redirect()->back()->withInput($request->all());

        }

        if ($request->has('active')) {
            $catering_plan->active = 1;
        } else {
            $catering_plan->active = 0;
        }

        if ($request->from_date != $catering_plan->from_date || $request->to_date != $catering_plan->to_date) {
            $catering_plan->from_date = $request->from_date;
            $catering_plan->to_date = $request->to_date;


            $working_week_days = json_decode($organisation_setting->working_week_days);

            $holidays = json_decode($organisation_setting->holidays);

            $num_of_working_days = 0;

            $this_date = Carbon::create($request->from_date);
            $last_date = Carbon::create($request->to_date);

            while ($last_date->gte($this_date)) {

                $day = substr($this_date->format('l'), 0, 3);

                if (in_array($day, $working_week_days)) {

                    if (!in_array($this_date->format('Y-m-d'), $holidays)) {

                        $num_of_working_days = $num_of_working_days + 1;

//                    echo $this_date->format('Y-m-d') . ' ';
                    }
                } else {
                    if ($organisation_setting->extra_days()->where('date', '=', $this_date->format('Y-m-d'))->count() > 0) {
                        $num_of_working_days = $num_of_working_days + 1;

//                    echo $this_date->format('Y-m-d') . ' ';
                    }
                }

                $this_date->addDay();

            }

            $catering_plan->num_of_working_days = $num_of_working_days;

        }

        $catering_plan->publish_date = $request->publish_date;
        $catering_plan->price = $request->price;
        $catering_plan->snack_num = $request->snack_num;
        $catering_plan->meal_num = $request->meal_num;


        if ($catering_plan->save()) {
            flash(translate('Catering Plan has been updated successfully'))->success();

        } else {
            flash(translate('Sorry! Something went wrong!'))->error();

        }

        return redirect()->route('catering_plans.index', $organisation_setting->id);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public static function available_subscriptions_exist($card_id)
    {

        $card = Card::findorfail($card_id);

        if (Auth::check() && (Auth::user()->id == $card->user_id)) {

            $organisation = Organisation::findorfail($card->organisation_id);

            $organisation_setting = $organisation->currentSettings();

            if ($organisation_setting == null) {
                return array('status'=>0);
            }

            $today = Carbon::now()->format('Y-m-d');

            $catering_plans = CateringPlan::where('organisation_setting_id', $organisation_setting->id)
                ->where('active', '=', '1')
                ->where('to_date', '>=', $today)->get();
            

            if (count($catering_plans) <= 0 && $organisation->custom_packets == 0) {
                return array('status'=>0);
            }else{
                return array('status'=>1);
            }
        }

        return array('status'=>0);
    }





    /**
     * Get the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function get_subscriptions($card_id, Request $request)
    {

//        self::available_subscriptions_exist();

        $card = Card::findorfail(decrypt($card_id));

        if (Auth::check() && (Auth::user()->id == $card->user_id)) {

//            IF()

            $organisation = Organisation::findorfail($card->organisation_id);

            $organisation_setting = $organisation->currentSettings();

            if($organisation_setting==null){
                return back();
            }

            $min_days = $organisation_setting->preorder_days_num;

            $today = Carbon::now()->format('Y-m-d');

//            $min_date_for_order = Carbon::now()->addDays($min_days)->format('Y-m-d');

//            $catering_plans = CateringPlan::where('organisation_setting_id', $organisation_setting->id)
//                ->where('active', '=', '1')->where('publish_date', '<=', $today)
//                ->where('to_date', '>=',  $today)
//                ->get();

            $catering_plans = CateringPlan::where('organisation_setting_id', $organisation_setting->id)
                ->where('active', '=', '1')->where('to_date', '>=',  $today)
                ->get();

            if(count($catering_plans)<=0 && $organisation->custom_packets == 0){
                return redirect()->route('dashboard');
            }

//            return $catering_plans;

            foreach ($catering_plans as $c) {
                $c->from_date = Carbon::create($c->from_date)->format('d/m/Y');
                $c->to_date = Carbon::create($c->to_date)->format('d/m/Y');
            }


            if (Session::get('checked_full_dates_card_id') == '-' || Session::get('checked_full_dates_card_id') != $card->id) {

//                dd('ne');

                $full_plan_purchases = CateringPlanPurchase::where('card_id', $card->id)->get();
//                ->where('snack_quantity', '=', $organisation_setting->max_snack_quantity)
//                    ->where('meal_quantity', '=', $organisation_setting->max_meal_quantity)

//                return count($full_plan_purchases);

                $dates_only = array();

                $meal_count_dates = array();

                $full_dates = array();

                $active_plan = array();

                $count = 0;

                foreach ($full_plan_purchases as $plan) {

                        $this_date = Carbon::now();
                        $from_date = Carbon::create($plan->from_date);
                        $to_date = Carbon::create($plan->to_date);
//active plan:
                        if (($this_date->gte($from_date) && $to_date->gte($this_date)) || $from_date->gte($this_date)) {


                            $active_days_january = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_january);
                            $active_days_february = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_february);
                            $active_days_march = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_march);
                            $active_days_april = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_april);
                            $active_days_may = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_may);
                            $active_days_june = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_june);
                            $active_days_july = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_july);
                            $active_days_august = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_august);
                            $active_days_september = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_september);
                            $active_days_october = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_october);
                            $active_days_november = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_november);
                            $active_days_december = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_december);

                            foreach ($active_days_january as $day) {

                                if (Carbon::create($day)->gte(Carbon::now()->subDay())) {

                                    if (in_array($day, $dates_only)) {

                                        foreach ($meal_count_dates as $key => $meal_count_date) {
                                            if ($meal_count_dates[$key]['date'] == $day) {

                                                $snack = $meal_count_date['snack'] + $plan->snack_quantity;
                                                $meal = $meal_count_date['meal'] + $plan->meal_quantity;

                                                $meal_count_dates[$key]['snack'] = $snack;
                                                $meal_count_dates[$key]['meal'] = $meal;

                                            }


                                        }


                                    } else {
                                        $dates_only[] = $day;
                                        $meal_count_dates[] = array('date' => $day, 'snack' => $plan->snack_quantity, 'meal' => $plan->meal_quantity);

                                    }
                                }

                            }



                            foreach ($active_days_february as $day) {

                                if (Carbon::create($day)->gte(Carbon::now()->subDay())) {

                                    if (in_array($day, $dates_only)) {

                                        foreach ($meal_count_dates as $key => $meal_count_date) {


                                            if ($meal_count_dates[$key]['date'] == $day) {

                                                $snack = $meal_count_date['snack'] + $plan->snack_quantity;
                                                $meal = $meal_count_date['meal'] + $plan->meal_quantity;

                                                $meal_count_dates[$key]['snack'] = $snack;
                                                $meal_count_dates[$key]['meal'] = $meal;

                                            }


                                        }

                                    } else {
                                        $dates_only[] = $day;
                                        $meal_count_dates[] = array('date' => $day, 'snack' => $plan->snack_quantity, 'meal' => $plan->meal_quantity);

                                    }
                                }

                            }


                            foreach ($active_days_march as $day) {

                                if (Carbon::create($day)->gte(Carbon::now()->subDay())) {
//                                $meal_count_dates[] = $day;

                                    if (in_array($day, $dates_only)) {

                                        foreach ($meal_count_dates as $key => $meal_count_date) {


                                            if ($meal_count_dates[$key]['date'] == $day) {

                                                $snack = $meal_count_date['snack'] + $plan->snack_quantity;
                                                $meal = $meal_count_date['meal'] + $plan->meal_quantity;

                                                $meal_count_dates[$key]['snack'] = $snack;
                                                $meal_count_dates[$key]['meal'] = $meal;

                                            }


                                        }


                                    } else {
                                        $dates_only[] = $day;
                                        $meal_count_dates[] = array('date' => $day, 'snack' => $plan->snack_quantity, 'meal' => $plan->meal_quantity);

                                    }
                                }

                            }


                            foreach ($active_days_april as $day) {

                                if (Carbon::create($day)->gte(Carbon::now()->subDay())) {
//                                $meal_count_dates[] = $day;

                                    if (in_array($day, $dates_only)) {

                                        foreach ($meal_count_dates as $key => $meal_count_date) {


                                            if ($meal_count_dates[$key]['date'] == $day) {

                                                $snack = $meal_count_date['snack'] + $plan->snack_quantity;
                                                $meal = $meal_count_date['meal'] + $plan->meal_quantity;

                                                $meal_count_dates[$key]['snack'] = $snack;
                                                $meal_count_dates[$key]['meal'] = $meal;

                                            }


                                        }

                                    } else {
                                        $dates_only[] = $day;
                                        $meal_count_dates[] = array('date' => $day, 'snack' => $plan->snack_quantity, 'meal' => $plan->meal_quantity);

                                    }
                                }

                            }


                            foreach ($active_days_may as $day) {

                                if (Carbon::create($day)->gte(Carbon::now()->subDay())) {
//                                $meal_count_dates[] = $day;

                                    if (in_array($day, $dates_only)) {
                                        foreach ($meal_count_dates as $key => $meal_count_date) {


                                            if ($meal_count_dates[$key]['date'] == $day) {

                                                $snack = $meal_count_date['snack'] + $plan->snack_quantity;
                                                $meal = $meal_count_date['meal'] + $plan->meal_quantity;

                                                $meal_count_dates[$key]['snack'] = $snack;
                                                $meal_count_dates[$key]['meal'] = $meal;

                                            }
                                        }
                                    } else {
                                        $dates_only[] = $day;
                                        $meal_count_dates[] = array('date' => $day, 'snack' => $plan->snack_quantity, 'meal' => $plan->meal_quantity);

                                    }
                                }

                            }


                            foreach ($active_days_june as $day) {

                                if (Carbon::create($day)->gte(Carbon::now()->subDay())) {
//                                $meal_count_dates[] = $day;

                                    if (in_array($day, $dates_only)) {

                                        foreach ($meal_count_dates as $key => $meal_count_date) {


                                            if ($meal_count_dates[$key]['date'] == $day) {

                                                $snack = $meal_count_date['snack'] + $plan->snack_quantity;
                                                $meal = $meal_count_date['meal'] + $plan->meal_quantity;

                                                $meal_count_dates[$key]['snack'] = $snack;
                                                $meal_count_dates[$key]['meal'] = $meal;

                                            }


                                        }
                                    } else {
                                        $dates_only[] = $day;
                                        $meal_count_dates[] = array('date' => $day, 'snack' => $plan->snack_quantity, 'meal' => $plan->meal_quantity);

                                    }
                                }

                            }

                            foreach ($active_days_july as $day) {

                                if (Carbon::create($day)->gte(Carbon::now()->subDay())) {
//                                $meal_count_dates[] = $day;

                                    if (in_array($day, $dates_only)) {

                                        foreach ($meal_count_dates as $key => $meal_count_date) {


                                            if ($meal_count_dates[$key]['date'] == $day) {

                                                $snack = $meal_count_date['snack'] + $plan->snack_quantity;
                                                $meal = $meal_count_date['meal'] + $plan->meal_quantity;

                                                $meal_count_dates[$key]['snack'] = $snack;
                                                $meal_count_dates[$key]['meal'] = $meal;

                                            }
                                        }

                                    } else {
                                        $dates_only[] = $day;
                                        $meal_count_dates[] = array('date' => $day, 'snack' => $plan->snack_quantity, 'meal' => $plan->meal_quantity);

                                    }
                                }

                            }
                            foreach ($active_days_august as $day) {

                                if (Carbon::create($day)->gte(Carbon::now()->subDay())) {
//                                $meal_count_dates[] = $day;

                                    if (in_array($day, $dates_only)) {

                                        foreach ($meal_count_dates as $key => $meal_count_date) {


                                            if ($meal_count_dates[$key]['date'] == $day) {

                                                $snack = $meal_count_date['snack'] + $plan->snack_quantity;
                                                $meal = $meal_count_date['meal'] + $plan->meal_quantity;

                                                $meal_count_dates[$key]['snack'] = $snack;
                                                $meal_count_dates[$key]['meal'] = $meal;

                                            }


                                        }
                                    } else {
                                        $dates_only[] = $day;
                                        $meal_count_dates[] = array('date' => $day, 'snack' => $plan->snack_quantity, 'meal' => $plan->meal_quantity);

                                    }
                                }

                            }
                            foreach ($active_days_september as $day) {

                                if (Carbon::create($day)->gte(Carbon::now()->subDay())) {
//                                $meal_count_dates[] = $day;

                                    if (in_array($day, $dates_only)) {

                                        foreach ($meal_count_dates as $key => $meal_count_date) {


                                            if ($meal_count_dates[$key]['date'] == $day) {

                                                $snack = $meal_count_date['snack'] + $plan->snack_quantity;
                                                $meal = $meal_count_date['meal'] + $plan->meal_quantity;

                                                $meal_count_dates[$key]['snack'] = $snack;
                                                $meal_count_dates[$key]['meal'] = $meal;

                                            }


                                        }

                                    } else {
                                        $dates_only[] = $day;
                                        $meal_count_dates[] = array('date' => $day, 'snack' => $plan->snack_quantity, 'meal' => $plan->meal_quantity);

                                    }
                                }

                            }
                            foreach ($active_days_october as $day) {

                                if (Carbon::create($day)->gte(Carbon::now()->subDay())) {
//                                $meal_count_dates[] = $day;

                                    if (in_array($day, $dates_only)) {

                                        foreach ($meal_count_dates as $key => $meal_count_date) {


                                            if ($meal_count_dates[$key]['date'] == $day) {

                                                $snack = $meal_count_date['snack'] + $plan->snack_quantity;
                                                $meal = $meal_count_date['meal'] + $plan->meal_quantity;

                                                $meal_count_dates[$key]['snack'] = $snack;
                                                $meal_count_dates[$key]['meal'] = $meal;

                                            }


                                        }

                                    } else {
                                        $dates_only[] = $day;
                                        $meal_count_dates[] = array('date' => $day, 'snack' => $plan->snack_quantity, 'meal' => $plan->meal_quantity);

                                    }
                                }

                            }
                            foreach ($active_days_november as $day) {

                                if (Carbon::create($day)->gte(Carbon::now()->subDay())) {
//                                $meal_count_dates[] = $day;

                                    if (in_array($day, $dates_only)) {

                                        foreach ($meal_count_dates as $key => $meal_count_date) {


                                            if ($meal_count_dates[$key]['date'] == $day) {

                                                $snack = $meal_count_date['snack'] + $plan->snack_quantity;
                                                $meal = $meal_count_date['meal'] + $plan->meal_quantity;

                                                $meal_count_dates[$key]['snack'] = $snack;
                                                $meal_count_dates[$key]['meal'] = $meal;

                                            }


                                        }

                                    } else {
                                        $dates_only[] = $day;
                                        $meal_count_dates[] = array('date' => $day, 'snack' => $plan->snack_quantity, 'meal' => $plan->meal_quantity);

                                    }
                                }

                            }
                            foreach ($active_days_december as $day) {

                                if (Carbon::create($day)->gte(Carbon::now()->subDay())) {
//                                $meal_count_dates[] = $day;

                                    if (in_array($day, $dates_only)) {

                                        foreach ($meal_count_dates as $key => $meal_count_date) {


                                            if ($meal_count_dates[$key]['date'] == $day) {

                                                $snack = $meal_count_date['snack'] + $plan->snack_quantity;
                                                $meal = $meal_count_date['meal'] + $plan->meal_quantity;

                                                $meal_count_dates[$key]['snack'] = $snack;
                                                $meal_count_dates[$key]['meal'] = $meal;

                                            }


                                        }


                                    } else {

                                        $dates_only[] = $day;

                                        $meal_count_dates[] = array('date' => $day, 'snack' => $plan->snack_quantity, 'meal' => $plan->meal_quantity);

                                    }
                                }
                            }


                        }


                }


                $request->session()->put('checked_full_dates_card_id', $card->id);

                foreach ($meal_count_dates as $meal_count_date) {
                    if ($meal_count_date['snack'] == $organisation_setting->max_snack_quantity && $meal_count_date['meal'] == $organisation_setting->max_meal_quantity) {
                        $full_dates[] = $meal_count_date['date'];
                    }
                }



                $d = Carbon::now();

                for($i=0; $i<$min_days+1; $i++){
                    $full_dates[] = $d->format('Y-m-d');
                    $d->addDay();
                }

                $request->session()->put('full_dates', $full_dates);

            }


            $all_prices = DB::table('organisation_price_ranges')
                ->join('organisation_prices', 'organisation_price_ranges.id', '=', 'organisation_prices.organisation_price_range_id')
                ->where('organisation_price_ranges.organisation_setting_id', '=', $organisation_setting->id)
                ->select('organisation_prices.id', 'organisation_price_ranges.start_range', 'organisation_price_ranges.end_range', 'organisation_prices.type',
                    'organisation_prices.quantity', 'organisation_prices.price')
                ->get();


            return view('frontend.user.customer.add_new_subscription', compact('card', 'organisation_setting', 'catering_plans', 'all_prices'));
        }

        return redirect()->back();
    }


    /**
     * Get the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function plan_max_quantities(Request $request)
    {

//        return $request->selected_dates;

        $card = Card::findorfail($request->card_id);

        $selected_dates = $request->selected_dates;

        $response_snack = 0;
        $response_meal = 0;

        if (Auth::check() && (Auth::user()->id == $card->user_id)) {

            $organisation = Organisation::findorfail($card->organisation_id);

            $organisation_setting = $organisation->currentSettings();

            $response_snack = $organisation_setting->max_snack_quantity;
            $response_meal = $organisation_setting->max_meal_quantity;

            $today = Carbon::now()->format('Y-m-d');

            $subscriptions = CateringPlanPurchase::where('card_id', $request->card_id)->where('to_date', '>=', $today)->get();

            foreach ($subscriptions as $plan) {

                $this_date = Carbon::now();
                $from_date = Carbon::create($plan->from_date);
                $to_date = Carbon::create($plan->to_date);
//active plan:
                if (($this_date->gte($from_date) && $to_date->gte($this_date)) || $from_date->gte($this_date)) {

                    $active_days_january = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_january);
                    $active_days_february = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_february);
                    $active_days_march = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_march);
                    $active_days_april = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_april);
                    $active_days_may = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_may);
                    $active_days_june = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_june);
                    $active_days_july = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_july);
                    $active_days_august = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_august);
                    $active_days_september = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_september);
                    $active_days_october = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_october);
                    $active_days_november = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_november);
                    $active_days_december = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_december);

                    foreach ($selected_dates as $selected_date) {

                        if (in_array($selected_date, $active_days_january)) {

                            $response_snack = $response_snack - $plan->snack_quantity;
                            $response_meal = $response_meal - $plan->meal_quantity;
                            break;

                        } else if (in_array($selected_date, $active_days_january)) {
                            $response_snack = $response_snack - $plan->snack_quantity;
                            $response_meal = $response_meal - $plan->meal_quantity;
                            break;

                        } else if (in_array($selected_date, $active_days_february)) {
                            $response_snack = $response_snack - $plan->snack_quantity;
                            $response_meal = $response_meal - $plan->meal_quantity;
                            break;

                        } else if (in_array($selected_date, $active_days_march)) {
                            $response_snack = $response_snack - $plan->snack_quantity;
                            $response_meal = $response_meal - $plan->meal_quantity;
                            break;

                        } else if (in_array($selected_date, $active_days_april)) {
                            $response_snack = $response_snack - $plan->snack_quantity;
                            $response_meal = $response_meal - $plan->meal_quantity;
                            break;

                        } else if (in_array($selected_date, $active_days_may)) {
                            $response_snack = $response_snack - $plan->snack_quantity;
                            $response_meal = $response_meal - $plan->meal_quantity;
                            break;
                        } else if (in_array($selected_date, $active_days_june)) {
                            $response_snack = $response_snack - $plan->snack_quantity;
                            $response_meal = $response_meal - $plan->meal_quantity;
                            break;
                        } else if (in_array($selected_date, $active_days_july)) {
                            $response_snack = $response_snack - $plan->snack_quantity;
                            $response_meal = $response_meal - $plan->meal_quantity;
                            break;
                        } else if (in_array($selected_date, $active_days_august)) {
                            $response_snack = $response_snack - $plan->snack_quantity;
                            $response_meal = $response_meal - $plan->meal_quantity;
                            break;
                        } else if (in_array($selected_date, $active_days_september)) {
                            $response_snack = $response_snack - $plan->snack_quantity;
                            $response_meal = $response_meal - $plan->meal_quantity;
                            break;
                        } else if (in_array($selected_date, $active_days_october)) {
                            $response_snack = $response_snack - $plan->snack_quantity;
                            $response_meal = $response_meal - $plan->meal_quantity;
                            break;
                        } else if (in_array($selected_date, $active_days_november)) {
                            $response_snack = $response_snack - $plan->snack_quantity;
                            $response_meal = $response_meal - $plan->meal_quantity;
                            break;
                        } else if (in_array($selected_date, $active_days_december)) {
                            $response_snack = $response_snack - $plan->snack_quantity;
                            $response_meal = $response_meal - $plan->meal_quantity;
                            break;
                        }
                    }


                }
            }


        }

        return array('snack' => $response_snack, 'meal' => $response_meal);


    }


    public function meal_description(Request $request)
    {
        $catering_plan = CateringPlan::find($request->id);

        return array('view' => view('modals.catering_plan_description_modal', compact('catering_plan'))->render());
    }


}
