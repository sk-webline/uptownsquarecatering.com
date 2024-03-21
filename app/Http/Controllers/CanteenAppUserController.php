<?php

namespace App\Http\Controllers;

use App\Models\CanteenAppUser;
use App\Models\Card;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\User;

class CanteenAppUserController extends Controller
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
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store_ajax(Request $request)
    {

        //check if username is unique
        if(!Auth::check()){
            return response()->json(['status' => 0, 'msg' => translate('Sorry! Something went wrong 2.')]);
        }

        $validator = Validator::make($request->all(), [
            'card_id' => 'required',
            'username' => 'unique:canteen_app_users,username',
            'daily_limit' => 'required|numeric|min:0',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            // Validation failed
            $errors = $validator->errors();
            // Customize the response based on your requirements
            return response()->json(['status' => 'validator_error', 'errors'=> $errors, 'validator_error' => array_key_first($validator->errors()->toArray()), 'message' => $errors->first()]);
        }

        $card = Card::find($request->card_id);

        if($card==null){
            response()->json(['status' => 0, 'msg' => translate('Card not found')]);
        }

        $canteen_user = new CanteenAppUser();
        $canteen_user->card_id =  $card->id;
        $canteen_user->user_id =  Auth::user()->id;
        $canteen_user->username =  $request->username;
        $canteen_user->password = Hash::make($request->password);
        $canteen_user->daily_limit = $request->daily_limit;

        if($canteen_user->save()){

            $credit_cards = Auth::user()->credit_cards;

            return response()->json(['status' => 1, 'msg' => translate('Canteen user created successfully'),
                'view' => view('frontend.partials.canteen_card_dashboard', compact('canteen_user', 'credit_cards'))->render()]);
        }else{
            return response()->json(['status' => 0, 'msg' => translate('Sorry! Something went wrong. 3')]);
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
    public function edit(Request $request)
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
    public function update(Request $request)
    {

     //


    }

//    /**
//     * Show the form for editing the specified resource.
//     *
//     * @param int $id
//     * @return \Illuminate\Http\Response
//     */
//    public function destroy(Request $request)
//    {
//
//        return $request->all();
//
//        $card_token = CardToken::find($request->card_token_id);
//
//        if($card_token==null){
//            flash(translate('Sorry! Something went wrong!'))->error();
//            return redirect()->back();
//        }
//
//        if($card_token->delete()){
//            flash(translate('Credit Card was deleted successfully!'))->error();
//        }else{
//            flash(translate('Sorry! Something went wrong!'))->error();
//        }
//
//        return redirect()->back();
//
//    }


    public function unassign_credit_card(Request $request)
    {


        $canteen_user = CanteenAppUser::find($request->canteen_user_id);

        if($canteen_user==null || !Auth::check() || Auth::user()->id != $canteen_user->user_id){
            flash(translate('Sorry! Something went wrong!'))->error();
            return redirect()->back();
        }

        $canteen_user->credit_card_token_id = null;

        if($canteen_user->save()){
            flash(translate('Card was unassigned successfully!'))->success();
        }else{
            flash(translate('Sorry! Something went wrong!'))->error();
        }

        return redirect()->back();


    }


    public function change_password(Request $request)
    {

//        return $request->all();

        if($request->password==null || strlen($request->password)<=0 || $request->password!=$request->password_confirmation){
            return response()->json(['status'=>'0', 'msg'=> translate('Sorry! Something went wrong!')]);
        }

        $canteen_user = CanteenAppUser::find($request->canteen_user_id);

        if($canteen_user==null || !Auth::check() || Auth::user()->id != $canteen_user->user_id){
            return response()->json(['status'=>'0', 'msg'=> translate('Canteen User not found')]);
        }


        $canteen_user->password = Hash::make($request->password);

        if($canteen_user->save()){
            return response()->json(['status'=>'1', 'msg'=> translate('Password was updated successfully')]);
        }else{
            return response()->json(['status'=>'0', 'msg'=> translate('Sorry! Something went wrong!')]);
        }


    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_username_ajax(Request $request)
    {

        $canteen_user = CanteenAppUser::find($request->canteen_user_id);

        if($canteen_user == null || !Auth::check() || Auth::user()->id != $canteen_user->user_id){
            response()->json(['status' => 0, 'msg' => translate('Canteen user not found')]);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:canteen_app_users,username',
        ]);

        if ($validator->fails()) {
            // Validation failed
            $errors = $validator->errors();
            // Customize the response based on your requirements
            return response()->json(['status' => 'validator_error', 'errors'=> $errors]);
        }

        $canteen_user->username =  $request->username;

        if($canteen_user->save()){
            return response()->json(['status' => 1, 'msg' => translate('Username updated successfully')]);
        }else{
            return response()->json(['status' => 0, 'msg' => translate('Sorry! Something went wrong.')]);
        }

    }

    public function update_daily_limit(Request $request)
    {

        $canteen_user = CanteenAppUser::find($request->canteen_user_id);

        if($request->daily_limit<0 || $canteen_user==null || !Auth::check() || Auth::user()->id != $canteen_user->user_id){
            flash(translate('Sorry! Something went wrong!'))->error();
            return redirect()->back();
        }


        $canteen_user->daily_limit = $request->daily_limit;

        if($canteen_user->save()){
            flash(translate('Daily Limit was updated successfully!'))->success();
        }else{
            flash(translate('Sorry! Something went wrong!'))->error();
        }

        return redirect()->back();


    }

    public function canteen_orders_history($canteen_user_id){

        $canteen_user_id = decrypt($canteen_user_id);

        $canteen_user = CanteenAppUser::find($canteen_user_id);

        if($canteen_user==null || $canteen_user->user_id != Auth::user()->id){
            flash(translate('Sorry! Something went wrong!'))->error();
            return redirect()->route('dashboard');
        }

        return view('frontend.user.customer.canteen_orders_history', compact('canteen_user'));

    }







}
