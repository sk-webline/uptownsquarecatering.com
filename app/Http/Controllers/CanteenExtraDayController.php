<?php

namespace App\Http\Controllers;

use App\Models\ApiClient\ApiClient;
use App\Models\CanteenExtraDay;
use App\Models\CanteenPurchase;
use App\Models\CanteenSetting;
use App\Models\OrganisationLocation;
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

class CanteenExtraDayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request)
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

        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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

    public function update($id, Request $request)
    {

        $canteen_setting_id = $id;

        $canteen_setting = CanteenSetting::findorfail($canteen_setting_id);

        if($request->has('extra_day')){
            $new_days = $request->extra_day;
        }else{
            $new_days = array();
        }


        $old_extra_dates = CanteenExtraDay::where('canteen_setting_id', $canteen_setting->id)->pluck('date')->toArray();
        $old_extra_days = CanteenExtraDay::where('canteen_setting_id', $canteen_setting->id)->get();

        $canteen_purchases_dates = CanteenPurchase::where('canteen_setting_id', $canteen_setting->id)->whereIn('date', $old_extra_dates)->pluck('date')->toArray();

        foreach ($old_extra_days as $old_extra_day){

            if(in_array($old_extra_day->date, $canteen_purchases_dates) && !in_array($old_extra_day->date,$new_days)){
                flash(translate('Date of canteen purchase cannot be deleted'))->error();
                return redirect()->back();
            }
            $old_extra_day->delete();

        }

        foreach ($new_days as $new_day){

            if($new_day!=null){
                $extra_day = new CanteenExtraDay();
                $extra_day->canteen_setting_id = $canteen_setting->id;
                $extra_day->created_by = Auth::user()->id;
                $extra_day->date = $new_day;
                $extra_day->save();
            }

        }

        flash(translate('Canteen Extra Days have been updated successfully'))->success();
        return redirect()->back();

    }



}
