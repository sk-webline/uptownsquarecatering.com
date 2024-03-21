<?php

namespace App\Http\Controllers;

use App\Models\ApiClient\ApiClient;
use App\Models\CateringPlanPurchase;
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

class CateringReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        return view('backend.reports.catering_report');

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

    public function remove(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $organisations = $request->organisation;

        $start_date_string = substr($request->datefilter, 0, 10);

        //formatted: yyyy-mm-dd
        $start_date_string_temp = substr($start_date_string, strlen($start_date_string)-4, strlen($start_date_string)) .'-' .substr($start_date_string, 3, 2) .'-' .substr($start_date_string, 0, 2);

        $response = array();
        $day_count = 0;


        $end_date_string = substr($request->datefilter, 13, 22);

        //formatted: yyyy-mm-dd
        $end_date_string_temp = substr($end_date_string, strlen($end_date_string)-4, strlen($end_date_string)) .'-' .substr($end_date_string, 3, 2) .'-' .substr($end_date_string, 0, 2);


        if ($organisations[0] != 'all') {
            $organisations = Organisation::whereIn('id', $organisations)->select('id', 'name')->get();
        } else {
            $organisations = Organisation::select('id', 'name')->get();
        }


        $end_date = Carbon::create($end_date_string_temp);

        $dates = array();
        $totals = array();

        $today = Carbon::create($start_date_string_temp);


        while ($end_date->gte($today)) {

            $dates[] = $today->format('d/m/y');
            $totals[] = array('total'=> 'Total','date' => $today->format('d/m/y'), 'snack'=>0, 'meal'=>0);
            $today->addDay();
            $day_count = $day_count + 1;

        }


        foreach ($organisations as $organisation) {
            $settings = $organisation->currentSettings();

            $today = Carbon::create($start_date_string_temp);

            if ($settings != null) {

                $key=0;

                while ($end_date->gte($today)) {

                    $snack_counter = 0;
                    $meal_counter = 0;

                    $month = $today->format('m');
                    $col = null;

                    if ($month == '01') {
                        $col = 'active_days_january';
                    } else if ($month == '02') {
                        $col = 'active_days_february';
                    } else if ($month == '03') {
                        $col = 'active_days_march';
                    } else if ($month == '04') {
                        $col = 'active_days_april';
                    } else if ($month == '05') {
                        $col = 'active_days_may';
                    } else if ($month == '06') {
                        $col = 'active_days_june';
                    } else if ($month == '07') {
                        $col = 'active_days_july';
                    } else if ($month == '08') {
                        $col = 'active_days_august';
                    } else if ($month == '09') {
                        $col = 'active_days_september';

                    } else if ($month == '10') {
                        $col = 'active_days_october';

                    } else if ($month == '11') {
                        $col = 'active_days_november';

                    } else {
                        $col = 'active_days_december';
                    }


                    $catering_plan_purchases = CateringPlanPurchase::where('organisation_settings_id', $settings->id)
                        ->whereJsonContains($col, $today->format('Y-m-d'))->select('id', 'snack_quantity', 'meal_quantity', $col)->get();

                    foreach ($catering_plan_purchases as $plan) {

                        $snack_counter = $snack_counter + $plan->snack_quantity;
                        $meal_counter = $meal_counter + $plan->meal_quantity;

                    }

                    $response[] = array('organisation' => $organisation->name,
                        'date' => $today->format('d/m/y'), 'snack' => $snack_counter, 'meal' => $meal_counter);

                    $totals[$key]['snack']+=$snack_counter;
                    $totals[$key]['meal']+=$meal_counter;


                    $today->addDay();
                    $key++;

                }


            } else {

                while ($end_date->gte($today)) {

                    $snack_counter = 0;
                    $meal_counter = 0;

                    $response[] = array('organisation' => $organisation->name,
                        'date' => $today->format('d/m/y'), 'snack' => $snack_counter, 'meal' => $meal_counter);

                    $today->addDay();

                }

            }

        }


        return view('backend.reports.catering_report', compact('response', 'day_count', 'dates', 'organisations', 'start_date_string'
        , 'end_date_string', 'totals'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {


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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


    }


}
