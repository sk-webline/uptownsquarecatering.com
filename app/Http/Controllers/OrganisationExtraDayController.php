<?php

namespace App\Http\Controllers;

use App\Models\ApiClient\ApiClient;
use App\Models\Organisation;
use App\Models\OrganisationExtraDay;
use App\Models\OrganisationPrice;
use App\Models\OrganisationPriceRange;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\UriInterface;
use Illuminate\Http\Request;
use Auth;
use App\Models\OrganisationSetting;


class OrganisationExtraDayController extends Controller
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
    public function create($organisation_settings_id)
    {

        //

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $organisation_setting_id)
    {

        $organisation_id = OrganisationSetting::findorfail($organisation_setting_id)->organisation_id;

        $old_extra_days = OrganisationExtraDay::where("organisation_setting_id", $organisation_setting_id)->get();


        if($old_extra_days->count()>0){
            foreach ($old_extra_days as $old_extra_day){
                $old_extra_day->delete();
            }
        }


        $counter=0;
        $extra_days = $request->extra_day;

        if($extra_days!=null) {
            foreach ($extra_days as $extra_day) {
                $day = new OrganisationExtraDay();
                $day->organisation_setting_id = $organisation_setting_id;
                $day->date = $extra_day;
                $day->created_by = \Illuminate\Support\Facades\Auth::user()->id;

                if ($day->save()) {
                    $counter++;
                }
            }

            if ($counter == sizeof($extra_days)) {
                flash(translate('Extra Days have been inserted successfully'))->success();
            } else {
                flash(translate('Sorry! Something went wrong!'))->error();
            }

        }

        return redirect()->route('organisation_settings.index', ['organisation_id'=>$organisation_id] );

    }

    public function remove(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $organisation_setting_id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
