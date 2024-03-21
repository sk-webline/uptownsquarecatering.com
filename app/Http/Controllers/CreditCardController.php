<?php

namespace App\Http\Controllers;

use App\Models\ApiClient\ApiClient;
use App\Models\CanteenAppUser;
use App\Models\CanteenExtraDay;
use App\Models\CanteenSetting;
use App\Models\CreditCard;
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

class CreditCardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('frontend.user.customer.credit_cards');
    }



    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_nickname(Request $request)
    {

        $credit_card = CreditCard::find($request->credit_card_id);

        if($request->nickname==null || $credit_card == null){
            flash(translate('Sorry! Password did not match.'))->error();
            return back();
        }

        $credit_card->nickname = $request->nickname;

        if($credit_card->save()){
            flash(translate('Nickname updated successfully'))->success();

        }else{
            flash(translate('Sorry! Password did not match.'))->error();
        }

        return back();

    }

    /**
     * Display a listing of the resource.
     *
     *
     */
    public function delete_credit_card(Request $request)
    {

        $credit_card = CreditCard::find($request->card_token_id);

        if($credit_card == null || !Auth::check() || $credit_card->user_id != Auth::user()->id ){
            flash(translate('Sorry! Password did not match.'))->error();
            return back();
        }

        if(Auth::check() && Auth::user()->id == $credit_card->user_id){

            CanteenAppUser::where('credit_card_token_id', $credit_card->id)->update(['credit_card_token_id' => null]);

//            foreach ($canteen_users as $canteen_user){
//                $canteen_user->credit_card_token_id = null;
//                $canteen_user->save();
//            }

            if($credit_card->delete()){
                flash(translate('Credit Card deleted successfully'))->success();

            }else{
                flash(translate('Sorry! Password did not match.'))->error();
            }

        }else{
            flash(translate('Sorry! Password did not match.'))->error();
        }

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function assigned_credit_card(Request $request)
    {

        $credit_card = CreditCard::find($request->selected_credit_card);
        $canteen_user = CanteenAppUser::find($request->canteen_user_id);

        if(!Auth::check() || $credit_card->user_id != Auth::user()->id || $request->selected_credit_card==null || $credit_card == null || $canteen_user==null){
            flash(translate('Sorry! Something went wrong.'))->error();
            return back();
        }

        $canteen_user->credit_card_token_id = $credit_card->id;

        if($canteen_user->save()){
            flash(translate('Credit Card assigned successfully'))->success();

        }else{
            flash(translate('Sorry! Something went wrong.'))->error();
        }

        return back();

    }


}
