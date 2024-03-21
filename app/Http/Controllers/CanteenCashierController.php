<?php

namespace App\Http\Controllers;

use App\CanteenCashierReportExport;
use App\Models\CanteenAppUser;
use App\Models\CanteenDeliveryLog;
use App\Models\CanteenLocation;
use App\Models\CanteenMenu;
use App\Models\CanteenPurchaseDelivery;
use App\Models\Card;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\Organisation;
use App\Models\OrganisationBreak;
use App\Models\OrganisationCanteenCashier;
use App\Models\PlatformSetting;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use Auth;
use Hash;
use Cookie;
use Mail;
use PDF;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\CanteenPurchase;
use Illuminate\Validation\Rule;


class CanteenCashierController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * Show the application frontend home.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $canteen_cashiers = User::where('user_type', 'canteen_cashier')->paginate(15);

        if ($request->has('search')) {
            $sort_search = $request->search;
            $canteen_cashiers = User::where('user_type', 'canteen_cashier');
            $canteen_cashiers = $canteen_cashiers->where('name', 'like', '%' . $sort_search . '%')->orWhere('phone', 'like', '%' . $sort_search . '%')->paginate(15);;
        }

        return view('backend.canteen_cashier.index', compact('canteen_cashiers'));


    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed'
        ]);
    }


    /**
     * Show the application frontend home.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $organisations = Organisation::select('id', 'name')->get();

        return view('backend.canteen_cashier.create', compact('organisations'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validator($request->all())->validate();

        $cashier = new User();

        $cashier->name = $request->name . ' ' . $request->surname;

        $cashier->username = $request->username;

        $cashier->phone = $request->phone;

        $cashier->country = 'Cyprus';

        $cashier->city = $request->city;

        $cashier->user_type = 'canteen_cashier';

        $cashier->password = \Illuminate\Support\Facades\Hash::make($request->password);

        if ($request->has('active_cashier')) {
            $cashier->active = 1;
        } else {
            $cashier->active = 0;
        }

//        return $request->organisations;

        if ($cashier->save()) {
            foreach ($request->organisations as $organisation) {

                //create row in organisation cashier
                $organisation_cashier = new OrganisationCanteenCashier();
                $organisation_cashier->user_id = $cashier->id;
                $organisation_cashier->organisation_id = $organisation;
                $organisation_cashier->save();
            }
            flash(translate('Canteen Cashier has been added successfully'))->success();
        } else {
            flash(translate('Sorry! Something went wrong!'))->error();
        }

        return redirect()->route('canteen_cashiers.index');


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $cashier = User::findorfail($id);

        if($cashier->user_type != 'canteen_cashier'){
            return redirect()->route('canteen_cashiers.index');
        }

        $organisations = Organisation::select('id', 'name')->get();
        $my_organisations = OrganisationCanteenCashier::where('user_id', $id)->select('organisation_id')->get();

        $checked_organisations = array();

        foreach($my_organisations as $my_organisations){
            $checked_organisations[] = $my_organisations->organisation_id;
        }


        list($name, $surname) = explode(' ', $cashier->name);

        return view('backend.canteen_cashier.edit', compact('cashier', 'organisations', 'checked_organisations', 'name', 'surname'));
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

        $cashier = User::findorfail($id);

        if($cashier->user_type != 'canteen_cashier'){
            return redirect()->route('canteen_cashiers.index');
        }

        if( strlen($request->password)>0){
            $this->validator($request->all())->validate();

        }else{

            $this->validate($request, [
                'name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'username' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users')->ignore($id), // Assuming you are using the authenticated user's ID
                ],
            ]);

        }

        $cashier->name = $request->name . ' ' . $request->surname;

        $cashier->username = $request->username;

        $cashier->phone = $request->phone;

        $cashier->country = 'Cyprus';

        $cashier->city = $request->city;

        if( strlen($request->password)>0) {

            $cashier->password = \Illuminate\Support\Facades\Hash::make($request->password);

        }

        if ($request->has('active_cashier')) {
            $cashier->active = 1;
        } else {
            $cashier->active = 0;
        }

        if ($cashier->save()) {

            $old_organisations = OrganisationCanteenCashier::where('user_id', $cashier->id)->get();

            foreach($old_organisations as $old){
                $old->delete();
            }

            foreach ($request->organisations as $organisation) {

                //create row in organisation cashier
                $organisation_cashier = new OrganisationCanteenCashier();
                $organisation_cashier->user_id = $cashier->id;
                $organisation_cashier->organisation_id = $organisation;
                $organisation_cashier->save();
            }

            flash(translate('Cashier has been updated successfully'))->success();


        } else {
            flash(translate('Sorry! Something went wrong!'))->error();
        }

        return redirect()->route('canteen_cashiers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $cashier = User::findorfail($id);

        if($cashier->user_type != 'canteen_cashier'){
            flash(translate('Sorry! Something went wrong!'))->error();
            return redirect()->route('canteen_cashiers.index');
        }

        if ($cashier->delete()) {

            $temp = OrganisationCanteenCashier::where('user_id', $id)->get();

            foreach ($temp as $t){
                $t->delete();
            }

            flash(translate('Cashier has been deleted successfully'))->success();
        } else {
            flash(translate('Sorry! Something went wrong!'))->error();
        }

        return redirect()->back();
    }


    public function login()
    {
        if (Auth::check()){
            return redirect()->route('canteen_cashier.select_location');
        }


        return view('frontend.cashier_login');

    }

    public function select_location()
    {

        if(Session::has('organisation_id')){
            Session::forget('organisation_id');
        }

        if(Session::has('location_id')){
            Session::forget('location_id');
        }

        if(Session::has('canteen_served_kids')){
            Session::forget('canteen_served_kids');
        }


        $organisations = Organisation::join('organisation_canteen_cashiers', 'organisations.id', '=', 'organisation_canteen_cashiers.organisation_id')
            ->where('organisation_canteen_cashiers.user_id', '=', Auth::user()->id)->where('organisation_canteen_cashiers.deleted_at', '=', null)
            ->select('organisations.id', 'organisations.name')->get();


        return view('frontend.user.canteen_cashier.select_location', compact('organisations'));

    }


    public function location_selection(Request $request)
    {

        if ($request->organisation_id != null && $request->organisation_id != '') {

            $organisation = Organisation::find($request->organisation_id);

            if ($organisation!=null && $request->location_id != null && $request->location_id != '') {
                $location = CanteenLocation::find($request->location_id);
                if($location!=null){
                    $request->session()->put('organisation_id', $organisation->id);
                    $request->session()->put('location_id', $location->id);

                    $cancel_minutes = PlatformSetting::where('type', 'minutes_for_cancel_meals')->first()->value;

                    $request->session()->put('cancel_minutes', $cancel_minutes);

                    return redirect()->route('canteen_cashier.select_operation');
                }
            }
        }

        flash(translate('Please select Organisation and Location!'))->error();

        return redirect()->back();

    }

    public function select_operation()
    {
        return view('frontend.user.canteen_cashier.select_operation');
    }

    public function report_export(Request $request){

        if(!$request->has('selected_date') || !$request->has('selected_break_id')){
            flash(translate('Sorry! Something went wrong.'))->error();
            return redirect()->back();
        }

        $carbon = Carbon::create($request->selected_date);

        $break = OrganisationBreak::find($request->selected_break_id);

        if($break==null || $break->organisation_id != Session::get('organisation_id')){
            flash(translate('Sorry! Something went wrong.'))->error();
            return redirect()->back();
        }

        $organisation = Organisation::find(Session::get('organisation_id'));
        $canteen_setting = $organisation->current_canteen_settings();

        if($canteen_setting == null){
            flash(translate('No active canteen period.'))->error();
            return redirect()->back();
        }

        if (!$request->has('export_type') || !($request->export_type !='total_quantities' || $request->export_type!='meal_codes')){
            flash(translate('Sorry! Something went wrong.'))->error();
            return redirect()->back();
        }

        if($request->export_type == 'total_quantities'){

            $this->export_total_quantities($canteen_setting, $carbon, $break, $organisation);

        }else{
            $this->export_meal_codes($canteen_setting, $carbon, $break, $organisation);
        }


//        return Excel::download(new CanteenCashierReportExport($canteen_products, $purchases, $request->selected_date, $break), $filename . '.xlsx');

    }

    public function export_total_quantities($canteen_setting, $carbon, $break, $organisation){

        $product_ids = CanteenMenu::where('canteen_setting_id', $canteen_setting->id)->pluck('canteen_product_id')->toArray();
        $product_ids = array_unique($product_ids);
        $canteen_products = \App\Models\CanteenProduct::whereIn('id', $product_ids)->get();

        $purchases = CanteenPurchase::select('canteen_purchases.date', 'canteen_purchases.break_num', 'canteen_purchases.canteen_product_id', DB::raw('SUM(canteen_purchases.quantity) as total_quantity'))
            ->where('canteen_setting_id', $canteen_setting->id)
            ->where('date', $carbon->format('Y-m-d'))
            ->where('break_num', $break->break_num)
            ->groupBy('canteen_purchases.canteen_product_id','canteen_purchases.date', 'canteen_purchases.break_num')
            ->get();

        $filename = 'Canteen Report ' . $carbon->format('d_m_Y') . ' ' . ordinal($break->break_num) . ' Break';

        ini_set('memory_limit', '256M');
        Artisan::call('view:clear');

        $direction = 'ltr';
        $text_align = 'left';
        $not_text_align = 'right';
        $font_family = "'Roboto','sans-serif'";

//        $products = [];
//        for($i=0; $i < 37; $i++) {
//            foreach ($canteen_products as $key => $product) {
//                if($key>0){
//                    $products[] = (object)$product;
//                }
//
//            }
//        }

//        if ($request->has('debug')) {
//        return view('frontend.user.canteen_cashier.quantities_report', [
//            'font_family' => $font_family,
//            'direction' => $direction,
//            'text_align' => $text_align,
//            'not_text_align' => $not_text_align,
//            'carbon_date' => $carbon,
//            'break' => $break,
//            'canteen_products' => $canteen_products,
//            'purchases' => $purchases,
//            'organisation' => $organisation
//        ]);
//        }


//        dd($purchases);


        return PDF::loadView('frontend.user.canteen_cashier.quantities_report', [
            'font_family' => $font_family,
            'direction' => $direction,
            'text_align' => $text_align,
            'not_text_align' => $not_text_align,
            'carbon_date' => $carbon,
            'break' => $break,
            'canteen_products' => $canteen_products,
            'purchases' => $purchases,
            'organisation' => $organisation
        ], [], [])->stream($filename . '.pdf');

    }

    public function export_meal_codes($canteen_setting, $carbon, $break, $organisation){

        $purchases = CanteenPurchase::select('canteen_app_users.username','cards.name as card_name','canteen_products.name as product_name', 'canteen_purchases.canteen_app_user_id', 'canteen_purchases.date','canteen_purchases.meal_code', 'canteen_purchases.break_num', DB::raw('SUM(canteen_purchases.quantity) as total_quantity'))
            ->join('canteen_products', 'canteen_products.id', '=', 'canteen_purchases.canteen_product_id')
            ->join('canteen_app_users', 'canteen_app_users.id', '=', 'canteen_purchases.canteen_app_user_id')
            ->join('cards', 'cards.id', '=', 'canteen_app_users.card_id')
            ->where('canteen_purchases.date', '=', $carbon->format('Y-m-d'))
            ->where('canteen_purchases.break_num', '=', $break->break_num)
            ->where('canteen_purchases.canteen_setting_id', $canteen_setting->id)
            ->groupBy('canteen_purchases.canteen_app_user_id', 'canteen_purchases.canteen_product_id','canteen_purchases.date', 'canteen_purchases.break_num')
            ->orderBy('canteen_purchases.canteen_app_user_id')
            ->get();

        $filename = 'Canteen Meal Codes Report ' . $carbon->format('d_m_Y') . ' ' . ordinal($break->break_num) . ' Break';

        ini_set('memory_limit', '256M');
        Artisan::call('view:clear');

        $direction = 'ltr';
        $text_align = 'left';
        $not_text_align = 'right';
        $font_family = "'Roboto','sans-serif'";

        $products = [];
        for($i=0; $i < 37; $i++) {
            foreach ($purchases as $key => $purchase) {
                if($key>0){
                    $products[] = (object)$purchase;
                }

            }
        }

//        if ($request->has('debug')) {
//        return view('frontend.user.canteen_cashier.meal_codes_report', [
//            'font_family' => $font_family,
//            'direction' => $direction,
//            'text_align' => $text_align,
//            'not_text_align' => $not_text_align,
//            'carbon_date' => $carbon,
//            'break' => $break,
//            'canteen_users' => $canteen_users,
//            'purchases' => $purchases,
//            'organisation' => $organisation
//        ]);
//        }


        return PDF::loadView('frontend.user.canteen_cashier.meal_codes_report', [
            'font_family' => $font_family,
            'direction' => $direction,
            'text_align' => $text_align,
            'not_text_align' => $not_text_align,
            'carbon_date' => $carbon,
            'break' => $break,
            'purchases' => $products,
            'canteen_setting' => $canteen_setting,
            'organisation' => $organisation
        ], [], [])->stream($filename . '.pdf');

    }

    public function operation_selected(Request $request)
    {
        return redirect()->route('canteen_cashier.dashboard');
    }

    public function dashboard(Request $request)
    {

        return view('frontend.user.canteen_cashier.dashboard');

    }

    public function unscheduled(Request $request)
    {

//        return $request->all();
        if($request->rfid_check == 'rfid_no'){
            $card = Card::where('rfid_no', $request->rfid_no)->first();
        }elseif($request->rfid_check == 'rfid_no_dec'){
            $card = Card::where('rfid_no_dec', $request->rfid_no)->first();
        }else{
            return back();
        }

        Session::put('rfid_check', $request->rfid_check);
        Session::put('scanning_type', 'unscheduled');

        if($card == null){
            return back()->withErrors(['wrong_card' => $request->rfid_no, 'error_msg' => 'Wrong card rfid no'])->withInput(['rfid_check' => $request->rfid_check]);
        }

        $canteen_user = $card->canteen_app_user;

        if($canteen_user == null){
            return back()->withErrors(['wrong_card' => $request->rfid_no, 'error_msg' => 'No registered app user found!'])->withInput(['rfid_check' => $request->rfid_check]);
        }

        $carbon_now = Carbon::now();

        $canteen_deliveries = CanteenPurchaseDelivery::all()->pluck('canteen_purchase_id');

        $canteen_purchases = CanteenPurchase::where('canteen_app_user_id', $canteen_user->id)->where('date', $carbon_now->format('Y-m-d'))
            ->whereNotIn('id', $canteen_deliveries)->get();

        $organisation = $card->organisation;
        $breaks = $organisation->breaks;

        return view('frontend.user.canteen_cashier.unscheduled', compact('canteen_user', 'card', 'canteen_purchases', 'organisation', 'breaks'));


    }

    public function unscheduled_delivery(Request $request){

        if(!$request->has('canteen_app_user') || !$request->has('date')){
            flash(translate('Sorry! Something went wrong.'))->error();
            return redirect()->route('canteen_cashier.dashboard');
        }

        $canteen_location = CanteenLocation::find(Session::get('location_id'));

        $canteen_user = CanteenAppUser::find($request->canteen_app_user);

        if($canteen_user == null || $canteen_location==null){
            flash(translate('Sorry! Something went wrong.'))->error();
            return redirect()->route('canteen_cashier.dashboard');
        }

        $break_nums = [];

        foreach ($request->all() as $key => $input){
            if (strpos($key, 'breakNum_') !== false) {

                $temp = explode('_', $key);
                $break_nums[] = end($temp);
            }
        }

        $canteen_purchases = CanteenPurchase::where('canteen_app_user_id', $canteen_user->id)->where('date', $request->date)->whereIn('break_num', $break_nums)->get();

        foreach ($canteen_purchases as $purchase){

            $delivery = new CanteenPurchaseDelivery();
            $delivery->canteen_app_user_id = $canteen_user->id;
            $delivery->canteen_purchase_id = $purchase->id;
            $delivery->canteen_location_id = $canteen_location->id;
            $delivery->save();
        }

        if(Session::has('canteen_served_kids')){
            $canteen_served_kids = Session::get('canteen_served_kids') + 1;
            Session::put('canteen_served_kids', $canteen_served_kids);
        }else{
            Session::put('canteen_served_kids', 1);
        }

        return redirect()->route('canteen_cashier.dashboard');
    }

    public function current_break_scanning(Request $request){


//        dd($request->all());

        if(!$request->has('rfid_check')){
            flash(translate('Sorry! Something went wrong.'))->error();
            return redirect()->route('canteen_cashier.dashboard');
        }

        if($request->rfid_check == 'rfid_no'){
            $card = Card::where('rfid_no', $request->rfid_no)->first();
        }elseif($request->rfid_check == 'rfid_no_dec'){
            $card = Card::where('rfid_no_dec', $request->rfid_no)->first();
        }else{
            return back();
        }

//        dd($card, $request->all());

        Session::put('rfid_check', $request->rfid_check);
        Session::put('scanning_type', 'break_scanning');

        if($card == null){
            return back()->withErrors(['wrong_card' => $request->rfid_no, 'error_msg' => 'Wrong card rfid no'])->withInput(['rfid_check' => $request->rfid_check]);
        }

        $canteen_user = $card->canteen_app_user;

        if($canteen_user == null){
            return back()->withErrors(['wrong_card' => $request->rfid_no, 'error_msg' => 'No registered app user found!'])->withInput(['rfid_check' => $request->rfid_check]);
        }

        //find current break

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

        if($accessible_break == null){
            return back()->withErrors(['wrong_card' => $request->rfid_no, 'error_msg' => 'No accessible break currently'])->withInput(['rfid_check' => $request->rfid_check]);
        }

        $canteen_deliveries = CanteenPurchaseDelivery::all()->pluck('canteen_purchase_id');

//        dd($canteen_deliveries);

        $current_canteen_purchases = CanteenPurchase::where('canteen_app_user_id', $canteen_user->id)->where('date', $carbon_now->format('Y-m-d'))
            ->where('break_num', $accessible_break->break_num)->count();

        if($current_canteen_purchases == 0){
            return back()->withErrors(['wrong_card' => $request->rfid_no, 'error_msg' => 'No order found for ' . ordinal($accessible_break->break_num) . ' Break'])->withInput(['rfid_check' => $request->rfid_check]);
        }

        $current_canteen_purchases = CanteenPurchase::where('canteen_app_user_id', $canteen_user->id)->where('date', $carbon_now->format('Y-m-d'))
            ->whereNotIn('id', $canteen_deliveries)->where('break_num', $accessible_break->break_num)->get();

        if(count($current_canteen_purchases) == 0){
            return back()->withErrors(['wrong_card' => $request->rfid_no, 'error_msg' => 'The order has already been executed', 'view_order_break_id' => $accessible_break->id ])->withInput(['rfid_check' => $request->rfid_check]);
        } // view order

        $canteen_purchases = CanteenPurchase::where('canteen_app_user_id', $canteen_user->id)->where('date', $carbon_now->format('Y-m-d'))
            ->whereNotIn('id', $canteen_deliveries)->get();

        $current_canteen_purchases_ids = [];
        $meal_code = null;
        $total_items = 0;

        foreach ($current_canteen_purchases as $key => $purchase){
            $current_canteen_purchases_ids[] = $purchase->id;
            $total_items += $purchase->quantity;
        }

        $meal_code =  $current_canteen_purchases[0]->meal_code;
        $organisation = $card->organisation;
        $breaks = $organisation->breaks;


        return view('frontend.user.canteen_cashier.scheduled_break', compact('accessible_break', 'canteen_user', 'card', 'canteen_purchases', 'current_canteen_purchases_ids', 'organisation', 'breaks', 'meal_code', 'total_items'));

    }

    public function current_break_delivery(Request $request){
//        dd($request->all());

        if(!$request->has('canteen_app_user') || !$request->has('date') || !$request->has('break_id')){
            flash(translate('Sorry! Something went wrong.'))->error();
            return redirect()->route('canteen_cashier.dashboard');
        }

        $canteen_location = CanteenLocation::find(Session::get('location_id'));
        $canteen_user = CanteenAppUser::find($request->canteen_app_user);
        $break = OrganisationBreak::find($request->break_id);

        if($canteen_user == null || $canteen_location==null || $break==null){
            flash(translate('Sorry! Something went wrong.'))->error();
            return redirect()->route('canteen_cashier.dashboard');
        }

        $canteen_purchases = CanteenPurchase::where('canteen_app_user_id', $canteen_user->id)->where('date', $request->date)->where('break_num', $break->break_num)->get();

        foreach ($canteen_purchases as $purchase){

            $delivery = new CanteenPurchaseDelivery();
            $delivery->canteen_app_user_id = $canteen_user->id;
            $delivery->canteen_purchase_id = $purchase->id;
            $delivery->canteen_location_id = $canteen_location->id;
            $delivery->save();

            $log = new CanteenDeliveryLog();
            $log->type = 'delivery';
            $log->canteen_app_user_id = $canteen_user->id;
            $log->canteen_purchase_id = $purchase->id;
            $log->canteen_location_id = $canteen_location->id;
            $log->canteen_cashier_id = auth()->user()->id;
            $log->save();
        }

        if(Session::has('canteen_served_kids')){
            $canteen_served_kids = Session::get('canteen_served_kids') + 1;
            Session::put('canteen_served_kids', $canteen_served_kids);
        }else{
            Session::put('canteen_served_kids', 1);
        }

        return redirect()->route('canteen_cashier.dashboard');
    }

    public function view_order(Request $request){


        if(!$request->has('rfid_no') || !$request->has('rfid_check') || !$request->has('break_id')){
            flash(translate('Sorry! Something went wrong.'))->error();
            return redirect()->route('canteen_cashier.dashboard');
        }

        if($request->rfid_check == 'rfid_no'){
            $card = Card::where('rfid_no', $request->rfid_no)->first();
        }elseif($request->rfid_check == 'rfid_no_dec'){
            $card = Card::where('rfid_no_dec', $request->rfid_no)->first();
        }else{
            return back();
        }

        Session::put('rfid_check', $request->rfid_check);
        Session::put('scanning_type', 'break_scanning');

        if($card == null){
            return back()->withErrors(['wrong_card' => $request->rfid_no, 'error_msg' => 'Wrong card rfid no'])->withInput(['rfid_check' => $request->rfid_check]);
        }

        $canteen_user = $card->canteen_app_user;

        if($canteen_user == null){
            return back()->withErrors(['wrong_card' => $request->rfid_no, 'error_msg' => 'No registered app user found!'])->withInput(['rfid_check' => $request->rfid_check]);
        }

        $accessible_break = OrganisationBreak::find($request->break_id);

        if($accessible_break == null){
            flash(translate('Sorry! Something went wrong.'))->error();
            return back();
        }

        $carbon_now = Carbon::now();

        $delivered = CanteenPurchase::where('break_num', $accessible_break->break_num)->where('date', $carbon_now->format('Y-m-d'))->where('canteen_app_user_id', $canteen_user->id)->get();

        if(count($delivered) <= 0){
            flash(translate('Sorry! Something went wrong.'))->error();
            return back();
        }

        $canteen_purchases = CanteenPurchase::where('date', $carbon_now->format('Y-m-d'))->where('canteen_app_user_id', $canteen_user->id)->get();

        $current_canteen_purchases_ids = [];
        $total_items = 0;
        $temp = null;

        foreach ($delivered as $key => $purchase){
            $current_canteen_purchases_ids[] = $purchase->id;
            $total_items += $purchase->quantity;

            if ($temp == null){
                $temp = CanteenPurchaseDelivery::where('canteen_purchase_id', $purchase->id)->first();
            }
        }

        $meal_code =  $delivered[0]->meal_code;

        if ($temp != null){
            $received_time = Carbon::create($temp->created_at)->format('H:i') ;
        }else{
            $received_time = null;
        }

        $organisation = $card->organisation;
        $breaks = $organisation->breaks;
        $executed = true;

        return view('frontend.user.canteen_cashier.scheduled_break', compact('executed', 'received_time','accessible_break', 'canteen_user', 'card', 'canteen_purchases', 'current_canteen_purchases_ids', 'organisation', 'breaks', 'meal_code', 'total_items'));


    }

    public function undo_delivery(Request $request){

        if(!$request->has('break_id') || !$request->has('date') || !$request->has('canteen_user')){
            flash(translate('Sorry! Something went wrong.'))->error();
            return redirect()->route('canteen_cashier.dashboard');
        }

        $canteen_user = CanteenAppUser::find($request->canteen_user);
        $break = OrganisationBreak::find($request->break_id);


        $organisation = Organisation::find(Session::get('organisation_id'));

        if($organisation== null || $organisation->id != $break->organisation_id){
            flash(translate('Sorry! Info do not match'))->error();
            return redirect()->route('canteen_cashier.dashboard');
        }

        $canteen_setting = $organisation->current_canteen_settings();
        $cancel_minutes = $canteen_setting->max_undo_delivery_minutes;

        $purchases_ids = CanteenPurchase::where('date', $request->date)->where('break_num',$break->break_num)->where('canteen_app_user_id', $canteen_user->id)->pluck('id');

        $deliveries = CanteenPurchaseDelivery::whereIn('canteen_purchase_id', $purchases_ids)->get();

        if(count($deliveries) <= 0 ){
            flash(translate('Sorry! Something went wrong.'))->error();
            return redirect()->route('canteen_cashier.dashboard');
        }

        $carbon_now = Carbon::now();
        $received_time = Carbon::create($deliveries[0]->created_at)->format('H:i');
        $minutes_diff = Carbon::create($carbon_now->format('Y-m-d') . ' ' . $received_time)->diffInMinutes($carbon_now);

        if($minutes_diff > $cancel_minutes){
            flash(translate('Sorry! Undo delivery is no longer available'))->error();
            return redirect()->route('canteen_cashier.dashboard');
        }

        foreach ($deliveries as $key => $delivery){
            $delivery->delete();

            $log = new CanteenDeliveryLog();
            $log->type = 'return';
            $log->canteen_app_user_id = $delivery->canteen_app_user_id;
            $log->canteen_purchase_id = $delivery->canteen_purchase_id;
            $log->canteen_location_id = Session::get('location_id');
            $log->canteen_cashier_id = auth()->user()->id;
            $log->save();

        }

        flash('Delivery undo succeeded')->success();
        return redirect()->route('canteen_cashier.dashboard');

    }


}
