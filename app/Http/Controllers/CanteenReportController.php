<?php

namespace App\Http\Controllers;

use App\CanteenReportExport;
use App\Models\ApiClient\ApiClient;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Auth;
use App\Models\Organisation;


class CanteenReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        return view('backend.reports.canteen_report');

    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        $datefiltering = explode(' - ', $request->datefilter);

        //formatted: yyyy-mm-dd
        $start_date = substr($datefiltering[0], strlen($datefiltering[0])-4, strlen($datefiltering[0])) .'-' .substr($datefiltering[0], 3, 2) .'-' .substr($datefiltering[0], 0, 2);
        $end_date = substr($datefiltering[1], strlen($datefiltering[1])-4, strlen($datefiltering[1])) .'-' .substr($datefiltering[1], 3, 2) .'-' .substr($datefiltering[1], 0, 2);

        $response = true;

        if($request->has('organisation')){
            $organisations = $request->organisation;
            if ($organisations[0] != 'all') {
                $organisations = Organisation::whereIn('id', $organisations)->select('id', 'name')->get();
            } else {
                $organisations = Organisation::select('id', 'name')->where('canteen', '1')->get();
            }
        }else{
            $organisations = Organisation::select('id', 'name')->where('canteen', '1')->get();
        }

        $start_carbon = Carbon::create($start_date);
        $end_carbon = Carbon::create($end_date);

        $period = CarbonPeriod::create($start_carbon, $end_carbon);

        $dates = array();
        $formatted_dates = array();
        $day_count = count($period);


        // Iterate over the period to get all dates
        foreach ($period as $date) {
            $dates[] = $date->format('d/m/y');
            $formatted_dates[] = $date->format('Y-m-d');
        }


        return view('backend.reports.canteen_report', compact('response', 'dates', 'formatted_dates', 'organisations', 'day_count', 'start_date', 'end_date', 'start_carbon', 'end_carbon'));

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function excel_export(Request $request)
    {

        $organisation_ids = explode(',', $request->organisations);

        $organisations = Organisation::whereIn('id', $organisation_ids)->get();

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $start_carbon = Carbon::create($start_date);
        $end_carbon = Carbon::create($end_date);

        $period = CarbonPeriod::create($start_carbon, $end_carbon);

        $dates = array();
        $formatted_dates = array();
        $day_count = count($period);


        // Iterate over the period to get all dates
        foreach ($period as $date) {
            $dates[] = $date->format('d/m/y');
            $formatted_dates[] = $date->format('Y-m-d');
        }

        $filename = 'Canteen Report ' . Carbon::now();
        return Excel::download(new CanteenReportExport($organisations, $start_date, $end_date, $dates, $formatted_dates), $filename . '.xlsx');


//        dd('export', $request->all(), $organisation_ids, $organisations);


    }



}
