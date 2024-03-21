<?php

namespace App\Http\Controllers;

use App\CustomCardsGeneratedExport;
use App\Models\ApiClient\ZeroVendingApiMethods;
use App\Models\CateringPlanPurchase;
use App\Models\ChangeRfidLog;
use App\Models\Organisation;
use App\Models\Card;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use App\Models\OrganisationSetting;
use Illuminate\Support\Facades\DB;

use Excel;

//use Illuminate\Database\Eloquent\Collection;


class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $organisation_id)
    {
        $sort_search = null;
//        $organisation = Organisation::findorfail($organisation_id);

        $organisation = Organisation::where('id', $organisation_id)->first(); //($organisation_id);
        $cards = $organisation->cards();

        if ($request->has('search')) {
            $sort_search = $request->search;
            $cards = $organisation->cards()->where('rfid_no', 'like', '%' . $sort_search . '%');

            if($cards->count()<=0){
                $cards = $organisation->cards()->where('rfid_no_dec', 'like', '%' . $sort_search . '%');
            }
        }

        $cards = $cards->paginate(15);

        return view('backend.organisation.organisation_cards.index', compact('organisation', 'cards', 'sort_search'));

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

    public function edit_card_name(Request $request)
    {

        if ($_SERVER['REMOTE_ADDR'] == '82.102.76.201') {
            return $request->all();
        }

        $card = Card::findorfail($request->edit_card_name_id);

        if($request->card_name_edit==null){
            flash(translate('Something went wrong.'))->error();
            return redirect()->back();
        }

        $card->name = $request->card_name_edit;

        if($card->save()){
            flash(translate('Card Name was updated successfuly.'))->success();
        }else {
            flash(translate('Something went wrong.'))->error();
        }

        return redirect()->back();
    }

    public function change_card_name(Request $request)
    {

        $card = Card::findorfail($request->edit_card_name_id);

        if($request->card_name==null){
            flash(translate('Something went wrong.'))->error();
            return redirect()->back();
        }

        $card->name = $request->card_name;

        if($card->save()){
            flash(translate('Card Name was updated successfuly.'))->success();
        }else {
            flash(translate('Something went wrong.'))->error();
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function remove_card_from_user($card_id)
    {
//        return 0;

        $card = Card::findorfail(decrypt($card_id));

        $user_id = $card->user_id;

        $card->name = null;
        $card->user_id = null;
        $card->required_field_value = null;

        if( $card->save()){
            flash(translate('Card has been deleted successfully'))->success();
        }else{
            flash(translate('Something went wrong.'))->error();
        }

        return redirect()->route('customers.view_catering_plans', encrypt($user_id));
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
    public function update(Request $request, $organisation_setting_id)
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sync($organisation_id)
    {

        $apiModel = new ZeroVendingApiMethods();

        $token = config('app.zerovending.token');

        $organisation = Organisation::findorfail($organisation_id);

        $cards = $apiModel->get_organisation_cards($token, $organisation->zero_vending_id);

        $cards = json_decode($cards);

        if (sizeof($cards) > 0) {
            foreach ($cards as $card) {
                $check_card = Card::where('organisation_id', $organisation_id)->where('rfid_no', '=', $card->rfid_no)->first();
                if ($check_card == null) {
                    $new_card = new Card();
                    $new_card->rfid_no = $card->rfid_no;
                    $new_card->rfid_no_dec = $card->rfid_no_dec;
                    $new_card->organisation_id = $organisation->id;
                    $new_card->required_field_name = $organisation->required_field_name;
                    $new_card->save();
                }
                else {
                    $check_card->rfid_no_dec = $card->rfid_no_dec;
                    $check_card->save();
                }
            }
        }

        flash(translate('Organisation has been synchronized successfully'))->success();

        return redirect()->back();

    }


    public function generate_custom_cards($organisation_id, Request $request)
    {

        $max_num=500;

        $organisation = Organisation::findorfail($organisation_id);

        if ($organisation == null) {
            flash(translate('Something went wrong!'))->error();
            return back();
        }

        $prefix_code = $request->prefix_code;

        $cards_num = $request->cards_num;

        $request->session()->put('cards_num', $cards_num);

        if ($cards_num > $max_num) {
            $length = $max_num;
        } else {
            $length = $cards_num;
        }

        $created_cards = 0;

        while($created_cards<$cards_num){

            $created_cards= $created_cards+$max_num;
            $response = self::store_custom_cards($length,$prefix_code, $organisation);

            if ($response == 0) {
                flash(translate('Sorry! Something went wrong!'))->error();
                return back();
            }

            if (($created_cards + $max_num) < $cards_num) {
                $length =  $max_num;
            } else {
                $length = $cards_num - $created_cards;
            }

        }
        $filename = $organisation->name." - $cards_num Cards Generated with prefix `$prefix_code` ".Carbon::now()->format('Y-m-d H-i');
        flash(translate('Custom Cards generated successfully'))->success();
        return Excel::download(new CustomCardsGeneratedExport, $filename.'.xlsx');

//        return $re;


    }

    public function store_custom_cards($length, $prefix_code, $organisation){

        $cards_array = array();

        for ($i = 0; $i < $length; $i++) {

            $generated_number = random_number_generator();

            $temp = $prefix_code . $generated_number;

            while (in_array($temp, $cards_array)) {
                $generated_number = random_number_generator();
                $temp = $prefix_code . $generated_number;
            }

            $cards_array[] = $temp;
        }

        $existing_cards = Card::whereIn('rfid_no', $cards_array)->select('rfid_no')->get();

        foreach ($existing_cards as $existing_card) {
            $cards_to_change[] = $existing_card['rfid_no'];
        }

        while (count($existing_cards) > 0) {

            for ($i = 0; $i < $length; $i++) {

                if (in_array($cards_array[$i], $cards_to_change)) {

                    $generated_number = random_number_generator();

                    $temp = $prefix_code . $generated_number;

                    while (in_array($temp, $cards_array) || in_array($temp, $cards_to_change)) {
                        $generated_number = random_number_generator();
                        $temp = $prefix_code . $generated_number;
                    }

                    $cards_array[$i] = $temp;

                }

            }

            $existing_cards = Card::whereIn('rfid_no', $cards_array)->select('rfid_no')->get();

            foreach ($existing_cards as $existing_card) {
                $cards_to_change[] = $existing_card['rfid_no'];
            }

        }

        for ($i = 0; $i < $length; $i++) {

            $card = new Card();
            $card->rfid_no = $cards_array[$i];
            $card->organisation_id = $organisation->id;
            $card->required_field_name = $organisation->required_field_name;
            $card->auto_generate = 1;

            if (!$card->save()) {

//                flash(translate('Sorry! Something went wrong!'))->error();
//                return back();

                return 0;
            }


        }

        return 1;

    }

    /**
     * Check if the rfid_no given exists.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function rfid_card_exists(Request $request)
    {
        $card = Card::where('rfid_no', $request->rfid_no)->first();

        if ($card != null) {
            $organisation = Organisation::findorfail($card->organisation_id);

            $response = ["status" => 1,
                "id" => $card->id, "rfid_no" => $card->rfid_no, "user_id" => $card->user_id, "organisation_id" => $organisation->id, "required_field_name" => $organisation->required_field_name
            ];
            return $response;
        }

        $response = ["status" => 0];

        return $response;


    }

    public function rfid_card_available(Request $request)
    {


        $card = Card::where('rfid_no', $request->rfid_no)->first();

        $response = [
            "status" => 0
        ];

        if ($card != null && ($card->user_id == null)) {
            $organisation = Organisation::findorfail($card->organisation_id);

            $response = [
                "status" => 1, "id" => $card->id, "rfid_no" => $card->rfid_no, "user_id" => $card->user_id, "organisation_id" => $organisation->id, "required_field_name" => $organisation->required_field_name
            ];

        } else if ($card != null && ($card->user_id != null)) {
            $response = [
                "status" => 2
            ];
        }

        return json_encode($response);


    }

    public function rfid_can_be_edited(Request $request)
    {

        $old_card = Card::findorfail($request->old_card_id);

        $response = [
            "status" => 0,
            "message" => translate('This RFID does not exist')
        ];

        if ($old_card != null) {

            $new_card = Card::where('rfid_no', $request->rfid_no)->first();


            if ($new_card != null && ($new_card->user_id == null) && $new_card->organisation_id == $old_card->organisation_id) {
                $organisation = Organisation::findorfail($old_card->organisation_id);

                $response = ["status" => 1];

            } else if ($new_card != null && ($new_card->user_id != null) && $new_card->organisation_id == $old_card->organisation_id) {
                //used card
                $response = [
                    "status" => 2,
                    "message" => translate('This RFID is already registered')
                ];
            }

        }

        return json_encode($response);


    }

    public function register_new_card()
    {

        return view('frontend.user.customer.register_new_card');

    }

    public function register_card(Request $request)
    {

        $card = Card::where('rfid_no', $request->card_to_register)->first();

        $organisation = Organisation::findorfail($card->organisation_id);

        if ($card->user_id == null || $card->user_id == '') {
            $card->name = $request->card_name;
            $card->user_id = Auth::user()->id;
            $card->required_field_name = $organisation->required_field_name;
            $card->required_field_value = $request->required_field;

            if ($card->save()) {
                flash(translate('Card has been registered successfully!'))->success();
            } else {
                flash(translate('Sorry! Something went wrong!'))->error();
            }

        } else {
            flash(translate('This card is already registered in a different account!'))->error();
        }


        return redirect()->route('dashboard');

    }

    /**
     * Check if the rfid_no given exists.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function organisation_working_details($card_id)
    {
        $card = Card::findorfail($card_id);

        $organisation = Organisation::findorfail($card->organisation_id);

        $latest_organisation_setting = $organisation->currentSettings();

        return $latest_organisation_setting;


    }

    public function get_valid_dates(Request $request)
    {

        $card = Card::findorfail($request->card_id);

        $organisation = Organisation::findorfail($card->organisation_id);

        $latest_organisation_setting = $organisation->currentSettings();

        $working_week_days = json_decode($latest_organisation_setting->working_week_days);

        $holidays = json_decode(OrganisationSetting::findorfail($latest_organisation_setting->id)->holidays);

        $response = array();

        $this_date = Carbon::create($request->start_date);

        $last_date = Carbon::create($request->end_date);


        while ($last_date->gte($this_date)) {


            $day = substr($this_date->format('l'), 0, 3);

            if (in_array($day, $working_week_days)) {

                if (!in_array($this_date->format('Y-m-d'), $holidays)) {

                    $response[] = $this_date->format('Y-m-d');

                }
            } else {
                if ($latest_organisation_setting->extra_days()->where('date', '=', $this_date->format('Y-m-d'))->count() > 0) {
                    $response = [$this_date->format('Y-m-d')];
                }
            }


            $this_date->addDay();

        }

        return $response;


    }

    public function card_upcoming_meals(Request $request)
    {

        if (request()->ip() == '82.102.76.2011'){
            return $request->all();
        }

        $card = Card::find($request->card_id);

        if (request()->ip() == '82.102.76.2011'){
            return $request->all();
        }

        if($card==null){
            return array('status' => 0 , 'msg' => 'card not found', 'view' => '');
        }


        $today = Carbon::now()->format('Y-m-d');

        $events = array();

        $start_day = Carbon::now(); //1;
        $start_flag = 0;
        $last_day = Carbon::now();

        $start_day_string = null;
        $last_day_string = null;

        $active_subs = CateringPlanPurchase::where('card_id', $request->card_id)->where('from_date', '<=', $today)->where('to_date', '>=', $today)->get();

        $dates = array();

        $has_green = 0;
        $has_blue = 0;
        $has_orange = 0;


        foreach ($active_subs as $active_sub) {

            if ($start_flag == 0) {
                $start_flag = 1;
            }

            if (Carbon::create($active_sub->to_date)->gte($last_day)) {
                $last_day = Carbon::create($active_sub->to_date);
                $last_day_string = $active_sub->to_date;
            }

//            if($request->ip() == '82.102.76.201'){
//                return array( 'active_subs'=> $active_subs, '$last_day' => $last_day, '$last_day_string' =>$last_day_string);
//            }

            $color = '';
            if ($active_sub->snack_quantity > 0 && $active_sub->meal_quantity > 0) {
                $color = 1; //green
            } elseif ($active_sub->snack_quantity > 0 && $active_sub->meal_quantity == 0) {
                $color = 2; //blue
            } elseif ($active_sub->snack_quantity == 0 && $active_sub->meal_quantity > 0) {
                $color = 3; //orange
            }

            $active_days_january = json_decode(CateringPlanPurchase::findorfail($active_sub->id)->active_days_january);
            $active_days_february = json_decode(CateringPlanPurchase::findorfail($active_sub->id)->active_days_february);
            $active_days_march = json_decode(CateringPlanPurchase::findorfail($active_sub->id)->active_days_march);
            $active_days_april = json_decode(CateringPlanPurchase::findorfail($active_sub->id)->active_days_april);
            $active_days_may = json_decode(CateringPlanPurchase::findorfail($active_sub->id)->active_days_may);
            $active_days_june = json_decode(CateringPlanPurchase::findorfail($active_sub->id)->active_days_june);
            $active_days_july = json_decode(CateringPlanPurchase::findorfail($active_sub->id)->active_days_july);
            $active_days_august = json_decode(CateringPlanPurchase::findorfail($active_sub->id)->active_days_august);
            $active_days_september = json_decode(CateringPlanPurchase::findorfail($active_sub->id)->active_days_september);
            $active_days_october = json_decode(CateringPlanPurchase::findorfail($active_sub->id)->active_days_october);
            $active_days_november = json_decode(CateringPlanPurchase::findorfail($active_sub->id)->active_days_november);
            $active_days_december = json_decode(CateringPlanPurchase::findorfail($active_sub->id)->active_days_december);

            foreach ($active_days_january as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $active_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $active_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {

                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_february as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $active_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $active_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_march as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $active_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $active_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_april as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $active_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $active_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_may as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $active_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $active_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_june as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $active_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $active_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_july as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $active_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $active_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_august as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $active_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $active_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_september as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $active_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $active_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_october as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $active_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $active_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_november as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $active_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $active_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_december as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $active_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $active_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }

        }

        if (request()->ip() == '82.102.76.2011'){
            return $active_subs;
        }



        $upcoming_subs = CateringPlanPurchase::where('card_id', $request->card_id)->where('from_date', '>=', $today)->select('id', 'from_date', 'to_date', 'snack_quantity', 'meal_quantity')->get();

        foreach ($upcoming_subs as $upcoming_sub) {

            if (Carbon::create($upcoming_sub->to_date)->gte($last_day)) {
                $last_day = Carbon::create($upcoming_sub->to_date);
                $last_day_string = $upcoming_sub->to_date;
            }

            if (Carbon::create($upcoming_sub->from_date)->gte($start_day) && $start_flag == 0) {
                $start_day = Carbon::create($upcoming_sub->from_date);
                $start_flag = 1;
            }

            if ($upcoming_sub->snack_quantity > 0 && $upcoming_sub->meal_quantity > 0) {
                $color = 1;
            } elseif ($upcoming_sub->snack_quantity > 0 && $upcoming_sub->meal_quantity == 0) {
                $color = 2;
            } elseif ($upcoming_sub->snack_quantity == 0 && $upcoming_sub->meal_quantity > 0) {
                $color = 3;
            }

            $active_days_january = json_decode(CateringPlanPurchase::findorfail($upcoming_sub->id)->active_days_january);
            $active_days_february = json_decode(CateringPlanPurchase::findorfail($upcoming_sub->id)->active_days_february);
            $active_days_march = json_decode(CateringPlanPurchase::findorfail($upcoming_sub->id)->active_days_march);
            $active_days_april = json_decode(CateringPlanPurchase::findorfail($upcoming_sub->id)->active_days_april);
            $active_days_may = json_decode(CateringPlanPurchase::findorfail($upcoming_sub->id)->active_days_may);
            $active_days_june = json_decode(CateringPlanPurchase::findorfail($upcoming_sub->id)->active_days_june);
            $active_days_july = json_decode(CateringPlanPurchase::findorfail($upcoming_sub->id)->active_days_july);
            $active_days_august = json_decode(CateringPlanPurchase::findorfail($upcoming_sub->id)->active_days_august);
            $active_days_september = json_decode(CateringPlanPurchase::findorfail($upcoming_sub->id)->active_days_september);
            $active_days_october = json_decode(CateringPlanPurchase::findorfail($upcoming_sub->id)->active_days_october);
            $active_days_november = json_decode(CateringPlanPurchase::findorfail($upcoming_sub->id)->active_days_november);
            $active_days_december = json_decode(CateringPlanPurchase::findorfail($upcoming_sub->id)->active_days_december);

            foreach ($active_days_january as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $upcoming_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $upcoming_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_february as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $upcoming_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $upcoming_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_march as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $upcoming_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $upcoming_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_april as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $upcoming_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $upcoming_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_may as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $upcoming_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $upcoming_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_june as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $upcoming_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $upcoming_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_july as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $upcoming_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $upcoming_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_august as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $upcoming_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $upcoming_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_september as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $upcoming_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $upcoming_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_october as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $upcoming_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $upcoming_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_november as $day) {


                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $upcoming_sub->meal_quantity > 0) {
//                            return 'hi';
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $upcoming_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }
            foreach ($active_days_december as $day) {

                if (in_array($day, $dates)) {

                    for ($i = 0; $i < sizeof($events); $i++) {

                        if ($events[$i]['date'] == $day) {
                            if ($events[$i]['color'] == 2 && $upcoming_sub->meal_quantity > 0) {
                                $events[$i]['color'] = 1;
                            } else if ($events[$i]['color'] == 3 && $upcoming_sub->snack_quantity > 0) {
                                $events[$i]['color'] = 1;
                            }
                        }

                    }
                } else {


                    $events [] = array('date' => $day, 'color' => $color);
                    $dates[] = $day;

                }

            }


        }

        foreach ($events as $key => $event) {
            if ($events[$key]['color'] == 1) {
                $has_green = 1;
            } else if ($events[$key]['color'] == 2) {
                $has_blue = 1;

            } else if ($events[$key]['color'] == 3) {
                $has_orange = 1;
            }
        }


        $start_day = Carbon::now()->format('Y-m-d');
        $last_day = $last_day_string;

        $monthsDifference =  Carbon::now()->diffInMonths(Carbon::parse($last_day));
        return array('view' => view('frontend.partials.upcomingMealsCalendar', compact('card', 'events', 'start_day', 'last_day', 'start_flag', 'has_orange', 'has_blue', 'has_green', 'monthsDifference'))->render(), '$monthsDifference'=>$monthsDifference, 'events' => $events,'upcoming_subs'=> $upcoming_subs, 'active_subs'=> $active_subs);

    }

    public function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function subscription_history($card_id)
    {
        $card = Card::findorfail(decrypt($card_id));

        $organisation = Organisation::findorfail($card->organisation_id);

        $latest_organisation_setting = $organisation->currentSettings();


        $subscription_purchases = CateringPlanPurchase::where('card_id', $card->id)
            ->where('user_id', '=', Auth::user()->id)
            ->select('id', 'created_at', 'from_date', 'to_date', 'snack_quantity', 'meal_quantity', 'price')
            ->orderBy('created_at', 'desc')->get();

        return view('frontend.user.customer.subscription_history', compact('card', 'subscription_purchases', 'latest_organisation_setting'));

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function meals_history($card_id)
    {

        $card = Card::findorfail(decrypt($card_id));

        $user_id = Auth::user()->id;

        $sql = "SELECT
                    DATE(created_at) AS purchase_date,
                    GROUP_CONCAT(IF(purchase_type = 'snack', TIME(created_at), NULL)) AS snack_times,
                    GROUP_CONCAT(IF(purchase_type = 'lunch', TIME(created_at), NULL)) AS lunch_times
                FROM
                    card_usage_history
                WHERE
                   card_id= ? && user_id= ?
                GROUP BY
                    purchase_date
                ORDER BY created_at DESC";

        $card_usages = DB::select(DB::raw($sql), [$card->id, $user_id]);

        $card_usages = array_paginate($card_usages, 10, null);

        $pagination_path = '/dashboard/meals_history/' . $card_id;

        $card_usages->setPath($pagination_path);

        return view('frontend.user.customer.meals_history', compact('card', 'card_usages'));

    }

    public function edit_rfid_no(Request $request)
    {

        $user_id = \Illuminate\Support\Facades\Auth::user()->id;
        $from_card_id = $request->old_card_id;
        $to_rfid_no = $request->rfid_no;

        $old_card = Card::findorfail($from_card_id);
        $new_card = Card::where('rfid_no', $to_rfid_no)->first();

        $to_rfid_no_dec = $new_card->rfid_no_dec;

        if ($new_card == null || $old_card == null) {
            flash(translate('Something went wrong.'))->error();
            return redirect()->route('dashboard');
        }

        if (($new_card->user_id != null) && ($new_card->organisation_id != $old_card->organisation_id) && ($old_card->user_id != $user_id)) {
            flash(translate('Something went wrong.'))->error();
            return redirect()->route('dashboard');
        }

        $from_rfid_no = $old_card->rfid_no;
        $from_rfid_no_dec = $old_card->rfid_no_dec;

        $to_card_id = $new_card->id;

        $old_card->rfid_no = -1;
        $old_card->rfid_no_dec = -11;

        if ($old_card->save()) {

            $new_card->rfid_no = $from_rfid_no;
            $new_card->rfid_no_dec = $from_rfid_no_dec;

            if ($new_card->save()) {

                $old_card->rfid_no = $to_rfid_no;
                $old_card->rfid_no_dec = $to_rfid_no_dec;

                if ($old_card->save()) {
                    $log = new ChangeRfidLog();

                    $log->user_id = $user_id;
                    $log->from_rfid_no = $from_rfid_no;
                    $log->from_card_id = $from_card_id;
                    $log->to_rfid_no = $to_rfid_no;
                    $log->to_card_id = $to_card_id;

                    $log->save();
                    flash(translate('RFID No was updated successfuly.'))->success();
                } else {
                    flash(translate('Something went wrong.'))->error();
                }
            }
        }

        return redirect()->route('dashboard');


    }
    public function change_card_details(Request $request)
    {


        $user_id = \Illuminate\Support\Facades\Auth::user()->id;
        $from_card_id = $request->old_card_id;
        $to_rfid_no = $request->rfid_no;

        $old_card = Card::findorfail($from_card_id);
        $new_card = Card::where('rfid_no', $to_rfid_no)->first();

        $to_rfid_no_dec = $new_card->rfid_no_dec;

        if ($new_card == null || $old_card == null) {
            flash(translate('Something went wrong.'))->error();
            return redirect()->back();
        }

        if (($new_card->user_id != null) && ($new_card->organisation_id != $old_card->organisation_id) && ($old_card->user_id != $user_id)) {
            flash(translate('Something went wrong.'))->error();
            return redirect()->back();
        }

        $from_rfid_no = $old_card->rfid_no;
        $from_rfid_no_dec = $old_card->rfid_no_dec;

        $to_card_id = $new_card->id;

        $old_card->rfid_no = -1;
        $old_card->rfid_no_dec = -11;


        if ($old_card->save()) {
            $new_card->rfid_no = $from_rfid_no;
            $new_card->rfid_no_dec = $from_rfid_no_dec;

            if ($new_card->save()) {

                $old_card->rfid_no = $to_rfid_no;
                $old_card->rfid_no_dec = $to_rfid_no_dec;

                if ($old_card->save()) {
                    $log = new ChangeRfidLog();

                    $log->user_id = $user_id;
                    $log->from_rfid_no = $from_rfid_no;
                    $log->from_card_id = $from_card_id;
                    $log->to_rfid_no = $to_rfid_no;
                    $log->to_card_id = $to_card_id;

                    $log->save();
                    flash(translate('RFID No was updated successfuly.'))->success();
                } else {
                    flash(translate('Something went wrong.'))->error();
                }
            }
        }

        return redirect()->back();

    }

    /**
     * Get active plan purchases for this card
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public static function get_on_going_subscriptions(Request $request)
    {

        $card_id = $request->card_id;
        $card = Card::findorfail($card_id);

        $subs = CateringPlanPurchase::where('card_id', $card->id)->where('catering_plan_purchases.to_date', '>=' , Carbon::today()->format('Y-m-d'))
            ->join('catering_plans', 'catering_plans.id', '=', 'catering_plan_purchases.catering_plan_id')
            ->select('catering_plans.name', 'catering_plan_purchases.from_date', 'catering_plan_purchases.to_date', 'catering_plan_purchases.created_at')
            ->get();
        if(count($subs)>0){
            return response()->json( ['status' => 0, 'view' => view('modals.active_subscriptions_modal', compact('subs'))->render()]);
        }else{
            return response()->json( ['status' => 1]);
        }

        return response()->json( $request->all());

    }


}
