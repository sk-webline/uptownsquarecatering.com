<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlatformSetting;



class PlatformSettingsController extends Controller
{

    public function index()
    {
        return view('backend.setup_configurations.platform_settings');
    }

    //updates the VAT percentage
    public function setVAT(Request $request){

        $vat= PlatformSetting::where('type', 'vat_percentage')->first();

        $vat->value = $request->vat_percentage;

        if($vat->save()){
            flash(translate('VAT Percentage has been inserted successfully'))->success();
        }else{
            flash(translate('Sorry! Something went wrong!'))->success();
        }

        return redirect()->back();
    }

    //updates the Minutes for cancelling meal
    public function set_minutes_for_cancel(Request $request){

        $minutes= PlatformSetting::where('type', 'minutes_for_cancel_meals')->first();

        $minutes->value = $request->cancel_minutes;

        if($minutes->save()){
            flash(translate('Minutes for Meal Cancellation have been inserted successfully'))->success();
        }else{
            flash(translate('Sorry! Something went wrong!'))->success();
        }

        return redirect()->back();
    }

    public function set_max_failed_login_attempts(Request $request){

        $max_attempts= PlatformSetting::where('type', 'login_mistakes_lock_num')->first();

        $max_attempts->value = $request->max_attempts;

        if($max_attempts->save()){
            flash(translate('Failed Attempts for Login have been inserted successfully'))->success();
        }else{
            flash(translate('Sorry! Something went wrong!'))->success();
        }

        return redirect()->back();
    }

    public function set_lock_minutes(Request $request){

        $max_attempts= PlatformSetting::where('type', 'login_lock_minutes')->first();

        $max_attempts->value = $request->lock_minutes;

        if($max_attempts->save()){
            flash(translate('Lock Minutes for Login have been inserted successfully'))->success();
        }else{
            flash(translate('Sorry! Something went wrong!'))->success();
        }

        return redirect()->back();
    }

    public function set_check_lock_minutes(Request $request){

        $max_attempts =PlatformSetting::where('type', 'login_lock_check_minutes')->first();

        $max_attempts->value = $request->lock_check_minutes;

        if($max_attempts->save()){
            flash(translate('Check Lock Minutes for Login have been inserted successfully'))->success();
        }else{
            flash(translate('Sorry! Something went wrong!'))->success();
        }

        return redirect()->back();
    }
}
