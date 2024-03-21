<?php

namespace App\Http\Controllers;

use App\Models\ApiClient\ApiClient;
use App\Models\Card;
use App\Models\CardUsageHistory;
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
use Illuminate\Support\Facades\Auth;
use MongoDB\Driver\Session;
use Psr\Http\Message\UriInterface;
use Illuminate\Http\Request;
use App\Models\OrganisationSetting;
use Carbon\Carbon;


class CateringPlanPurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $organisation_setting_id)
    {
//
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($organisation_settings_id)
    {
//
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $organisation_setting_id)
    {
//
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

//
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
//
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $catering_plan_purchase = CateringPlanPurchase::findorfail(decrypt($id));
        $user_id = $catering_plan_purchase->user_id;

        if (\Carbon\Carbon::create($catering_plan_purchase->to_date)->lt(\Carbon\Carbon::today())) {
            flash("Something went wrong!")->error();
            return redirect()->route('customers.view_catering_plans', encrypt($user_id));
        }

        if ($catering_plan_purchase->delete()) {
            flash(translate('Catering Plan Subscription was deleted successfully!'))->success();
        } else {
            flash("Something went wrong!")->error();
        }

        return redirect()->route('customers.view_catering_plans', encrypt($user_id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public static function find_valid_subscription($card_id, $num)
    {
        //

        $response = array();

        $return_value = array('status_code' => 0, 'status' => 'No Subscription', 'response' => array());

        $today = Carbon::now()->format('Y-m-d');

        $plans = CateringPlanPurchase::where('card_id', $card_id)->where('to_date', '>=', $today)->orderby('from_date', 'desc')->get();

        $status_code = 0;
        $status = 'No Subscription';

        if (count($plans) <= 0) {

            $expired_plans = CateringPlanPurchase::where('card_id', $card_id)->where('to_date', '<', $today)->count();

            if ($expired_plans > 0) {
                $status = 'Subscription Expired';
            }

            return array('status_code' => $status_code, 'response' => $response, 'status' => $status);

        }

        if ($num == null) {
            $num = count($plans) + 1;
        }

        $this_date = Carbon::now();

        foreach ($plans as $key => $plan) {


            if ($key < $num) {

                $from_date = Carbon::create($plan->from_date);
                $to_date = Carbon::create($plan->to_date);

                if ($plan->catering_plan_id == 'custom') {
                    $details = array(
                        'id' => $plan->id,
                        'name' => 'Custom Meal Subscription',
                        'snack_quantity' => $plan->snack_quantity,
                        'meal_quantity' => $plan->meal_quantity,
                        'from_date' => $plan->from_date,
                        'to_date' => $plan->to_date,
                    );

                } else {

                    $catering_plan_name = CateringPlan::findorfail($plan->catering_plan_id)->name;

                    $details = array(
                        'id' => $plan->id,
                        'name' => $catering_plan_name,
                        'snack_quantity' => $plan->snack_quantity,
                        'meal_quantity' => $plan->meal_quantity,
                        'from_date' => $plan->from_date,
                        'to_date' => $plan->to_date,
                    );

                }

                $plan_status = self::get_subscription_status($plan);

                if (($status_code == 0 || $status_code == 3) && $plan_status == 0) {
                    $status = 'No subscription';
                    $status_code = 0;
                } else {
                    if ($plan_status == 1) {
                        $status = 'Active Subscription';
                        $status_code = 1;
                    } else if ($plan_status == 2 && $status_code != 1) {
                        $status = 'Upcoming Subscription';
                        $status_code = 2;
                    } else if ($plan_status == 3 && $status_code != 1 && $status_code != 2) {
                        $status = 'Expired Subscription';
                        $status_code = 3;
                    }
                }

                $response[] = $details;

            }

        }

        if ($status_code == 3) {
            $status_code = 0;
        }

        $return_value = array('status_code' => $status_code, 'response' => $response, 'status' => $status);

        return $return_value;

    }

    public static function get_subscription_status($subscription)
    {

        $response = 0; // 'No subscription'
        $today = Carbon::now();

        $start = Carbon::create($subscription->from_date);
        $end = Carbon::create($subscription->to_date);

        if ($end->gte($today) && $today->gte($start)) {
            return $response = 1; // 'Active Subscription';
        } else if ($start->gte($today)) {
            return $response = 2; // 'Upcoming Subscription';
        } else if ($today->gte($end)) {
            return $response = 3; // 'Expired Subscription';
        }

        return $response;

    }

    public function serve_meal_type(Request $request)
    {

        $type = $request->type;
        $rfid_no = $request->rfid_no;
        $organisation_id = $request->session()->get('organisation_id');

        $organisation = Organisation::findorfail($organisation_id);


        if ($request->has('rfid_check')) {
            $card = Card::where($request->rfid_check, $rfid_no)->where('organisation_id', '=', $organisation_id)->first();
        } else {
            $card = Card::where('rfid_no_dec', $rfid_no)->where('organisation_id', '=', $organisation_id)->first();
        }


//        $card = Card::where('rfid_no_dec', $rfid_no)->where('organisation_id', '=', $organisation_id)->first();

        if ($card == '') {
            if ($organisation->required_field_name != null && $organisation->required_field_name != '') {
                $card = Card::where('required_field_value', $rfid_no)->where('organisation_id', '=', $organisation_id)->first();
            }
        }

        if ($card == '') {
            return array('status' => 0, 'card_name' => '', 'message' => translate("RFID doesn't exist. Please scan another one."));
        } else {

            $response = array('status' => 0, 'card_name' => '', 'message' => translate("Not Available $type."));

            $active_purchases = CateringPlanPurchase::where('card_id', $card->id)
                ->where('from_date', '<=', Carbon::now()->format('Y-m-d'))
                ->where('to_date', '>=', Carbon::now()->format('Y-m-d'))->get();

            foreach ($active_purchases as $plan) {

                if ($type == 'snack') {
                    if ($plan->snack_quantity > 0) {
                        $response = $this->checkMealAvailability($plan, $card, $type);
                        if (!is_array($response)) {
                            $response = json_decode($response, true);
                        }
                        if ($response['status'] == 0) {

                            $today = Carbon::today()->format('Y-m-d H:i:s');
                            $now = Carbon::now()->format('Y-m-d H:i:s');

                            $last_card_usage = CardUsageHistory::where('card_id', $card->id)->where('purchase_type', $type)
                                ->where('catering_plan_purchases_id', '!=' , null)->where('created_at', '>', $today)->orderBy('created_at', 'desc')->first();

                            if($last_card_usage!=null){
                                $last_card_usage_msg = translate('Previous Meal').':'. ' ' . translate('lunch');
                                $last_card_usage_time = Carbon::create($last_card_usage->created_at)->format('d/m/Y H:i');
                            }else{
                                $last_card_usage_msg=null;
                                $last_card_usage_time=null;
                            }

                            return array('status' => 0, 'card_name' => '', 'message' => translate("Not Available $type."), 'last_card_usage_msg' => $last_card_usage_msg, 'last_card_usage_time' => $last_card_usage_time);
                        } else {
                            return $response;
                        }


                    }
                } elseif ($type == 'lunch') {
                    if ($plan->meal_quantity > 0) {
                        $response = $this->checkMealAvailability($plan, $card, $type);
                        if (!is_array($response)) {
                            $response = json_decode($response, true);
                        }
                        if ($response['status'] == 0) {

                            $today = Carbon::today()->format('Y-m-d H:i:s');
//                            $now = Carbon::now()->format('Y-m-d H:i:s');

                            $last_card_usage = CardUsageHistory::where('card_id', $card->id)->where('purchase_type', $type)
                                ->where('catering_plan_purchases_id', '!=' , null)->where('created_at', '>', $today)->orderBy('created_at', 'desc')->first();

                            if($last_card_usage!=null){
                                $last_card_usage_msg = translate('Previous Meal').':'. ' ' . translate('lunch');
                                $last_card_usage_time = Carbon::create($last_card_usage->created_at)->format('d/m/Y H:i');
                            }else{
                                $last_card_usage_msg=null;
                                $last_card_usage_time=null;
                            }


                            return array('status' => 0, 'card_name' => '', 'message' => translate("Not Available $type."), 'last_card_usage_msg' => $last_card_usage_msg, 'last_card_usage_time' => $last_card_usage_time);
                        } else {
                            return $response;
                        }
                    }
                }
            }
            return $response;
        }
    }

    public function checkMealAvailability($plan, $card, $type)
    {

        $month = Carbon::now()->format('m');

        $result = array('status' => 0);

        if ($month == '01') {

            $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_january);

            return $this->checkDayAvailability($active_days, $plan, $card, $type);

        } else if ($month == '02') {

            $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_february);

            return $this->checkDayAvailability($active_days, $plan, $card, $type);

        } else if ($month == '03') {
            $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_march);

            return $this->checkDayAvailability($active_days, $plan, $card, $type);
        } else if ($month == '04') {
            $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_april);

            return $this->checkDayAvailability($active_days, $plan, $card, $type);
        } else if ($month == '05') {
            $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_may);

            return $this->checkDayAvailability($active_days, $plan, $card, $type);
        } else if ($month == '06') {
            $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_june);

            return $this->checkDayAvailability($active_days, $plan, $card, $type);
        } else if ($month == '07') {
            $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_july);

            return $this->checkDayAvailability($active_days, $plan, $card, $type);
        } else if ($month == '08') {

            $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_august);

            return $this->checkDayAvailability($active_days, $plan, $card, $type);

        } else if ($month == '09') {

            $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_september);

            return $this->checkDayAvailability($active_days, $plan, $card, $type);

        } else if ($month == '10') {
            $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_october);
            return $this->checkDayAvailability($active_days, $plan, $card, $type);

        } else if ($month == '11') {
            $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_november);

            return $this->checkDayAvailability($active_days, $plan, $card, $type);

        } else {
            $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_december);

            return $this->checkDayAvailability($active_days, $plan, $card, $type);
        }

        return array('status' => 0, 'card_name' => $card->name);


    }

    public function checkDayAvailability($active_days, $plan, $card, $type)
    {

        $today = Carbon::now()->format('Y-m-d H:i:s');
        $yesterday = Carbon::yesterday()->endOfDay()->format('Y-m-d H:i:s');

        if (in_array(Carbon::now()->format('Y-m-d'), $active_days)) {

            $usage = CardUsageHistory::where('card_id', $card->id)->where('purchase_type', '=', $type)
                ->where('created_at', '>', $yesterday)->where('created_at', '<=', $today)->count();


            if ($type == 'snack' && $usage < $plan->snack_quantity) {

                //create new usage

                $new_use = new CardUsageHistory();
                $new_use->catering_plan_purchases_id = $plan->id;
                $new_use->card_id = $card->id;
                $new_use->user_id = $card->user_id;
                $new_use->purchase_type = $type;
                $new_use->cashier_id = Auth::user()->id;
//                $new_use->location_id = Auth::user()->id;
//                $new_use->location_id = $type;


                if ($new_use->save()) {
                    return json_encode(array('status' => 1));
                } else {
                    return json_encode(array('status' => 0, 'card_name' => $card->name));
                }


            } elseif ($type == 'lunch' && $usage < $plan->meal_quantity) {
                $new_use = new CardUsageHistory();
                $new_use->catering_plan_purchases_id = $plan->id;
                $new_use->card_id = $card->id;
                $new_use->user_id = $card->user_id;
                $new_use->purchase_type = $type;
                $new_use->cashier_id = Auth::user()->id;

                if ($new_use->save()) {
                    return json_encode(array('status' => 1));
                } else {
                    return json_encode(array('status' => 0, 'card_name' => $card->name));
                }
            }

            return array('status' => 0, 'card_name' => $card->name);

        }

    }


    public function get_card_today_plan(Request $request)
    {

        $rfid_no = $request->rfid_input;

        $organisation_id = $request->session()->get('organisation_id');

        $organisation = Organisation::findorfail($organisation_id);

        $organisation_setting = $organisation->currentSettings();

        $today_purchased_plans[] = array();

//        $card = Card::where('rfid_no_dec', $rfid_no)->where('organisation_id', '=', $organisation_id)->first();

        if ($request->has('rfid_check')) {
            $card = Card::where('rfid_no_dec', $rfid_no)->where('organisation_id', '=', $organisation_id)->first();
//                return 'true';
        } else {
            $card = Card::where('rfid_no', $rfid_no)->where('organisation_id', '=', $organisation_id)->first();
        }


        if ($card == '') {
            if ($organisation->required_field_name != null && $organisation->required_field_name != '') {
                $card = Card::where('required_field_value', $rfid_no)->where('organisation_id', '=', $organisation_id)->first();
            }
        }

        if ($card == '') {
            $request->session()->put('error_input', $request->rfid_input);

            return redirect()->back();
        } else {

            if ($request->session()->has('error_msg')) {

                $request->session()->forget('error_msg');

            }

            $available_snack_num = 0;
            $available_meal_num = 0;


            $active_purchases = CateringPlanPurchase::where('card_id', $card->id)->where('from_date', '<=', Carbon::now()->format('Y-m-d'))
                ->where('to_date', '>=', Carbon::now()->format('Y-m-d'))->get();

            foreach ($active_purchases as $plan) {

                $month = Carbon::now()->format('m');

                if ($month == '01') {

                    $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_january);

                    if (in_array(Carbon::now()->format('Y-m-d'), $active_days)) {

                        $available_snack_num = $available_snack_num + $plan->snack_quantity;
                        $available_meal_num = $available_meal_num + $plan->meal_quantity;

                        $today_purchased_plans[] = array('id' => $plan->id, 'snack_quantity' => $plan->snack_quantity, 'meal_quantity' => $plan->meal_quantity);
                    }


                } else if ($month == '02') {

                    $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_february);

                    if (in_array(Carbon::now()->format('Y-m-d'), $active_days)) {

                        $available_snack_num = $available_snack_num + $plan->snack_quantity;
                        $available_meal_num = $available_meal_num + $plan->meal_quantity;
                        $today_purchased_plans[] = array('id' => $plan->id, 'snack_quantity' => $plan->snack_quantity, 'meal_quantity' => $plan->meal_quantity);
                    }

                } else if ($month == '03') {
                    $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_march);

                    if (in_array(Carbon::now()->format('Y-m-d'), $active_days)) {

                        $available_snack_num = $available_snack_num + $plan->snack_quantity;
                        $available_meal_num = $available_meal_num + $plan->meal_quantity;
                        $today_purchased_plans[] = array('id' => $plan->id, 'snack_quantity' => $plan->snack_quantity, 'meal_quantity' => $plan->meal_quantity);
                    }

                } else if ($month == '04') {
                    $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_april);

                    if (in_array(Carbon::now()->format('Y-m-d'), $active_days)) {

                        $available_snack_num = $available_snack_num + $plan->snack_quantity;
                        $available_meal_num = $available_meal_num + $plan->meal_quantity;
                        $today_purchased_plans[] = array('id' => $plan->id, 'snack_quantity' => $plan->snack_quantity, 'meal_quantity' => $plan->meal_quantity);
                    }

                } else if ($month == '05') {
                    $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_may);

                    if (in_array(Carbon::now()->format('Y-m-d'), $active_days)) {

                        $available_snack_num = $available_snack_num + $plan->snack_quantity;
                        $available_meal_num = $available_meal_num + $plan->meal_quantity;
                        $today_purchased_plans[] = array('id' => $plan->id, 'snack_quantity' => $plan->snack_quantity, 'meal_quantity' => $plan->meal_quantity);
                    }

                } else if ($month == '06') {
                    $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_june);

                    if (in_array(Carbon::now()->format('Y-m-d'), $active_days)) {

                        $available_snack_num = $available_snack_num + $plan->snack_quantity;
                        $available_meal_num = $available_meal_num + $plan->meal_quantity;
                        $today_purchased_plans[] = array('id' => $plan->id, 'snack_quantity' => $plan->snack_quantity, 'meal_quantity' => $plan->meal_quantity);
                    }

                } else if ($month == '07') {
                    $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_july);

                    if (in_array(Carbon::now()->format('Y-m-d'), $active_days)) {

                        $available_snack_num = $available_snack_num + $plan->snack_quantity;
                        $available_meal_num = $available_meal_num + $plan->meal_quantity;
                        $today_purchased_plans[] = array('id' => $plan->id, 'snack_quantity' => $plan->snack_quantity, 'meal_quantity' => $plan->meal_quantity);
                    }

                } else if ($month == '08') {

                    $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_august);

                    if (in_array(Carbon::now()->format('Y-m-d'), $active_days)) {

                        $available_snack_num = $available_snack_num + $plan->snack_quantity;
                        $available_meal_num = $available_meal_num + $plan->meal_quantity;
                        $today_purchased_plans[] = array('id' => $plan->id, 'snack_quantity' => $plan->snack_quantity, 'meal_quantity' => $plan->meal_quantity);
                    }

                } else if ($month == '09') {

                    $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_september);

                    if (in_array(Carbon::now()->format('Y-m-d'), $active_days)) {

                        $available_snack_num = $available_snack_num + $plan->snack_quantity;
                        $available_meal_num = $available_meal_num + $plan->meal_quantity;
                        $today_purchased_plans[] = array('id' => $plan->id, 'snack_quantity' => $plan->snack_quantity, 'meal_quantity' => $plan->meal_quantity);
                    }

                } else if ($month == '10') {
                    $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_october);

                    if (in_array(Carbon::now()->format('Y-m-d'), $active_days)) {

                        $available_snack_num = $available_snack_num + $plan->snack_quantity;
                        $available_meal_num = $available_meal_num + $plan->meal_quantity;
                        $today_purchased_plans[] = array('id' => $plan->id, 'snack_quantity' => $plan->snack_quantity, 'meal_quantity' => $plan->meal_quantity);
                    }

                } else if ($month == '11') {
                    $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_november);

                    if (in_array(Carbon::now()->format('Y-m-d'), $active_days)) {

                        $available_snack_num = $available_snack_num + $plan->snack_quantity;
                        $available_meal_num = $available_meal_num + $plan->meal_quantity;
                        $today_purchased_plans[] = array('id' => $plan->id, 'snack_quantity' => $plan->snack_quantity, 'meal_quantity' => $plan->meal_quantity);
                    }

                } else {
                    $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_december);

                    if (in_array(Carbon::now()->format('Y-m-d'), $active_days)) {

                        $available_snack_num = $available_snack_num + $plan->snack_quantity;
                        $available_meal_num = $available_meal_num + $plan->meal_quantity;
                        $today_purchased_plans[][] = array('id' => $plan->id, 'snack_quantity' => $plan->snack_quantity, 'meal_quantity' => $plan->meal_quantity);
                    }
                }

            }


//            check today usage

            $today = Carbon::now()->format('Y-m-d H:i:s');
            $yesterday = Carbon::yesterday()->endOfDay()->format('Y-m-d H:i:s');


            $snack_usage = CardUsageHistory::where('card_id', $card->id)->where('purchase_type', '=', 'snack')
                ->where('created_at', '>', $yesterday)->where('created_at', '<=', $today)->count();

            $lunch_usage = CardUsageHistory::where('card_id', $card->id)->where('purchase_type', '=', 'lunch')
                ->where('created_at', '>', $yesterday)->where('created_at', '<=', $today)->count();

            $meal_plan_snack = $available_snack_num;
            $meal_plan_lunch = $available_meal_num;

            $available_snack_num = $available_snack_num - $snack_usage;
            $available_meal_num = $available_meal_num - $lunch_usage;


            $today_plan = array('card' => $card, 'available_snack_num' => $available_snack_num, 'available_meal_num' => $available_meal_num,
                'meal_plan_snack' => $meal_plan_snack, 'meal_plan_lunch' => $meal_plan_lunch, 'today_purchased_plans' => $today_purchased_plans);

            $request->session()->put('today_plan', $today_plan);


            return view('frontend.user.cashier.card_meal_plan_today', compact('organisation_setting'));


        }

    }

    public function submit_card_meal(Request $request)
    {
        $card = Card::findorfail($request->card_id);

        $plans = $request->plans;

        $today = Carbon::now()->format('Y-m-d H:i:s');
        $yesterday = Carbon::yesterday()->endOfDay()->format('Y-m-d H:i:s');


        foreach ($plans as $plan) {

            $plan_usage = CardUsageHistory::where('card_id', $card->id)->where('catering_plan_purchases_id', $plan['id'])
                ->where('purchase_type', $request->type)->where('created_at', '>', $yesterday)->where('created_at', '<=', $today)->count();

            if ($request->type == 'snack') {
                if ($plan_usage < $plan['snack_quantity']) {
                    //add usage
                    $new_use = new CardUsageHistory();
                    $new_use->catering_plan_purchases_id = $plan['id'];
                    $new_use->card_id = $card->id;
                    $new_use->user_id = $card->user_id;
                    $new_use->purchase_type = $request->type;

                    if ($new_use->save()) {
                        return 1;
                    } else {
                        return 0;

//                        return array('usage' => $plan, "$request->type" => $request->type, '$plan_usage' => $plan_usage);
                    }
                }

            } else if ($request->type == 'lunch') {
                if ($plan_usage < $plan['meal_quantity']) {
                    //add usage
                    $new_use = new CardUsageHistory();
                    $new_use->catering_plan_purchases_id = $plan['id'];
                    $new_use->card_id = $card->id;
                    $new_use->user_id = $card->user_id;
                    $new_use->purchase_type = $request->type;

                    if ($new_use->save()) {
                        return 1;
                    } else {
                        return 0;
                    }
                }
            }

        }


        return 0;

    }

    public function cancel_meal($card_id, $card_usage_id, Request $request)
    {

        if ($request->session()->has('today_plan')) {

            $card = Card::findorfail(decrypt($card_id));

            $card_usage = CardUsageHistory::findorfail(decrypt($card_usage_id));

            if ($card->id == $card_usage->card_id) {

                if ($card_usage->purchase_type == 'snack') {

                    $old_snack = $request->session()->get('today_plan')['available_snack_num'];

                    $new_session = $request->session()->get('today_plan');
                    $new_session['$available_snack_num'] = ($old_snack + 1);

                    $request->session()->put('available_snack_num', $new_session);

                } else if ($card_usage->purchase_type == 'lunch') {

                    $old_meal = $request->session()->get('today_plan')['available_meal_num'];

                    $new_session = $request->session()->get('today_plan');
                    $new_session['$available_meal_num'] = ($old_meal + 1);

                    $request->session()->put('available_snack_num', $new_session);

                }

                $card_usage->delete();

                return 1;


            }
        }

        return view('frontend.user.cashier.card_meal_plan_today');


    }

    public function date_exists_in_purchases($organisation_id, $date)
    {

        $date_to_check = Carbon::create($date)->format('Y-m-d');

        $organisation = Organisation::findorfail($organisation_id);

        $organisation_setting = $organisation->currentSettings();

        $active_purchases = CateringPlanPurchase::where('organisation_settings_id', $organisation_setting->id)->get();

        foreach ($active_purchases as $plan) {

            $month = Carbon::create($date)->format('m');

            if ($month == '01') {

                $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_january);

                if (in_array($date_to_check, $active_days)) {

                    return 1;
                }


            } else if ($month == '02') {

                $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_february);

                if (in_array($date_to_check, $active_days)) {

                    return 1;
                }

            } else if ($month == '03') {
                $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_march);

                if (in_array($date_to_check, $active_days)) {

                    return 1;
                }

            } else if ($month == '04') {
                $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_april);

                if (in_array($date_to_check, $active_days)) {

                    return 1;
                }

            } else if ($month == '05') {
                $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_may);

                if (in_array($date_to_check, $active_days)) {

                    return 1;
                }

            } else if ($month == '06') {
                $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_june);

                if (in_array($date_to_check, $active_days)) {

                    return 1;
                }

            } else if ($month == '07') {
                $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_july);

                if (in_array($date_to_check, $active_days)) {

                    return 1;
                }

            } else if ($month == '08') {

                $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_august);

                if (in_array($date_to_check, $active_days)) {

                    return 1;
                }

            } else if ($month == '09') {

                $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_september);

                if (in_array($date_to_check, $active_days)) {

                    return 1;
                }

            } else if ($month == '10') {
                $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_october);

                if (in_array($date_to_check, $active_days)) {

                    return 1;
                }

            } else if ($month == '11') {
                $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_november);

                if (in_array($date_to_check, $active_days)) {

                    return 1;
                }

            } else {
                $active_days = json_decode(CateringPlanPurchase::findorfail($plan->id)->active_days_december);

                if (in_array($date_to_check, $active_days)) {

                    return 1;
                }
            }

        }

        return 0;

    }

}
