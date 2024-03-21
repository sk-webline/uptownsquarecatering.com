<?php

namespace App\Http\Controllers;

use App\Models\ApiClient\ApiClient;
use App\Models\CardUsageHistory;
use App\Models\CateringPlanPurchase;
use App\Models\OrganisationLocation;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Psr\Http\Message\UriInterface;
use Illuminate\Http\Request;
use Auth;
use App\Models\Organisation;
use App\Models\OrganisationSetting;
use App\Models\Card;
use App\Models\ApiClient\ZeroVendingApiMethods;

class MealReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        return view('backend.reports.meal_report');

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
        $start_date_string_temp = substr($start_date_string, strlen($start_date_string) - 4, strlen($start_date_string)) . '-' . substr($start_date_string, 3, 2) . '-' . substr($start_date_string, 0, 2);

        $response = array();
        $day_count = 0;
        $end_date_string = substr($request->datefilter, 13, 22);

        //formatted: yyyy-mm-dd
        $end_date_string_temp = substr($end_date_string, strlen($end_date_string) - 4, strlen($end_date_string)) . '-' . substr($end_date_string, 3, 2) . '-' . substr($end_date_string, 0, 2);


        $hour_range_string = $request->hourpicker;

        $start_hour_string = substr($hour_range_string, 0, 5);
        $end_hour_string = substr($hour_range_string, 8, 5);


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
            $totals[] = array('total' => 'Total', 'date' => $today->format('d/m/y'), 'snack' => 0, 'meal' => 0);
            $today->addDay();
            $day_count = $day_count + 1;

        }


        $end_date->addDay();

        foreach ($organisations as $organisation) {

            $cards_to_check = [];

            foreach ($organisation->cards as $card) {
                $cards_to_check[] = $card->id;
            }

            $today = Carbon::create($start_date_string_temp);

            $key = 0;

            while ($end_date->gte($today)) {

                $s = $today->setTimeFromTimeString($start_hour_string)->format('Y-m-d H:i:s');
                $e = $today->setTimeFromTimeString($end_hour_string)->format('Y-m-d H:i:s');

                $snack_counter = CardUsageHistory::whereIn('card_id', $cards_to_check)->whereBetween('created_at', [$s, $e])->where('purchase_type', '=', 'snack')->count();
                $meal_counter = CardUsageHistory::whereIn('card_id', $cards_to_check)->whereBetween('created_at', [$s, $e])->where('purchase_type', '=', 'lunch')->count();

                $response[] = array('organisation' => $organisation->name,
                    'date' => $today->format('d/m/y'), 'snack' => $snack_counter, 'meal' => $meal_counter);

                $totals[$key]['snack'] += $snack_counter;
                $totals[$key]['meal'] += $meal_counter;

                $key = $key+1;

                $today->addDay();

            }



        }

//        return $totals;



        return view('backend.reports.meal_report', compact('response','day_count', 'dates', 'organisations', 'start_date_string'
            , 'end_date_string', 'start_hour_string', 'end_hour_string', 'totals' ));
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
