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


class OrganisationPriceController extends Controller
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

        $organisation_setting = OrganisationSetting::findOrFail($organisation_settings_id);
        $organisation = $organisation_setting->organisation()->get()[0];

        return view('backend.organisation.organisation_settings.organisation_prices.create', compact('organisation_setting', 'organisation'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $organisation_setting_id)
    {
        $old_price_ranges = OrganisationSetting::findorfail($organisation_setting_id)->organisation_price_ranges()->get();


        foreach($old_price_ranges as $old_price_range){
            $old_prices = $old_price_range->organisation_prices()->get();

            foreach ($old_prices as $old_price){
                $old_price->delete();
            }

            $old_price_range->delete();
        }

        $organisation_id = OrganisationSetting::findorfail($organisation_setting_id)->organisation_id;

        foreach ($request->start_range as $key => $start_range) {

            $end_range = $request->end_range[$key];

            $organisation_price_range = new OrganisationPriceRange();

            $organisation_price_range->organisation_setting_id = $organisation_setting_id;
            $organisation_price_range->start_range =$start_range;
            $organisation_price_range->end_range =$end_range;

            $organisation_price_range->save();
            if ($request->has('snack_prices')) {
                for ($i = 0; $i < sizeof($request->snack_prices); $i++) {

                    $organisation_price = new OrganisationPrice();
                    $organisation_price->organisation_price_range_id = $organisation_price_range->id;
                    $organisation_price->quantity = $i + 1;
                    $organisation_price->price = $request->snack_prices[$i][$key];
                    $organisation_price->type = 'snack';

                    if (!$organisation_price->save()) {

                        flash(translate('Sorry! Something went wrong!'))->error();
                        return redirect()->route('organisations.index');

                    }

                }
            }
            if ($request->has('meal_prices')) {
                for ($i = 0; $i < sizeof($request->meal_prices); $i++) {

                    $organisation_price = new OrganisationPrice();
                    $organisation_price->organisation_price_range_id = $organisation_price_range->id;
                    $organisation_price->quantity = $i + 1;
                    $organisation_price->price = $request->meal_prices[$i][$key];
                    $organisation_price->type = 'meal';

                    if (!$organisation_price->save()) {

                        flash(translate('Sorry! Something went wrong!'))->error();
                        return redirect()->route('organisations.index');

                    }

                }
            }

        }

        flash(translate('Organisation Prices has been inserted successfully'))->success();

        return redirect()->route('organisation_settings.index', $organisation_id);

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
