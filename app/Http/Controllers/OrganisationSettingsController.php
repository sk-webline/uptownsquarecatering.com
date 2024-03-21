<?php

namespace App\Http\Controllers;

use App\Models\ApiClient\ApiClient;
use App\Models\Organisation;
use App\Models\OrganisationExtraDay;
use App\Models\OrganisationPrice;
use App\Models\OrganisationPriceRange;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Iyzipay\Model\Card;
use Psr\Http\Message\UriInterface;
use Illuminate\Http\Request;
use Auth;
use App\Models\OrganisationSetting;


class OrganisationSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $organisation_id)
    {

        $organisation = Organisation::findOrFail($organisation_id);
        $organisation_periods = OrganisationSetting::where('organisation_id', $organisation_id);
        $organisation_periods = $organisation_periods->paginate(15);


        return view('backend.organisation.organisation_settings.index', compact('organisation', 'organisation_periods'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($organisation_id)
    {

        $organisation = Organisation::findOrFail($organisation_id);

        return view('backend.organisation.organisation_settings.create', compact('organisation'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $organisation_id)
    {

        $organisation = Organisation::findorfail($organisation_id);

        $existing_organisation_settings = OrganisationSetting::where('organisation_id', $organisation->id)->get();

        foreach ($existing_organisation_settings as $setting) {

            $check_start = Carbon::create($setting->date_from);
            $check_end = Carbon::create($setting->date_to);

            $this_start = Carbon::create($request->start_date);
            $this_end = Carbon::create($request->end_date);

            if ($this_start->gte($check_start) && $this_start->lte($check_start)) {
                flash(translate(translate('There is already a period registered in between this date range!')))->error();
                return redirect()->back()->withInput($request->all());
            } else if ($this_start->lte($check_start) && $this_end->gte($check_end)) {
                flash(translate(translate('There is already a period registered in between this date range!')))->error();
                return redirect()->back()->withInput($request->all());
            } else if ($this_start->lte($check_start) && $this_end->gte($check_start)) {
                flash(translate(translate('There is already a period registered in between this date range!')))->error();
                return redirect()->back()->withInput($request->all());
            } else if ($this_start->gte($check_start) && $this_end->lte($check_end)) {
                flash(translate(translate('There is already a period registered in between this date range!')))->error();
                return redirect()->back()->withInput($request->all());
            }


        }


        $organisation_setting = new OrganisationSetting();
        $organisation_setting->organisation_id = $organisation_id;
        $organisation_setting->date_from = $request->start_date;
        $organisation_setting->date_to = $request->end_date;


        if ($organisation_setting->date_from >= $organisation_setting->date_to) {

            flash(translate('End Date should be greater than Start Date!'))->error();
            return redirect()->back()->withInput($request->all());
        }

        $organisation_setting->max_snack_quantity = $request->max_snack_quantity;
        $organisation_setting->max_meal_quantity = $request->max_meal_quantity;

        if ($request->has('absence')) {
            $organisation_setting->absence = 1;
            $organisation_setting->absence_days_num = $request->absence_days;
        } else {
            $organisation_setting->absence = 0;
            $organisation_setting->absence_days_num = null;
        }

        $organisation_setting->preorder_days_num = $request->preorder_days_num;

        $working_week_days = array();

        if ($request->has('monday')) {
            $working_week_days[] = 'Mon';
        }

        if ($request->has('tuesday')) {
            $working_week_days[] = 'Tue';
        }

        if ($request->has('wednesday')) {
            $working_week_days[] = 'Wed';
        }

        if ($request->has('thursday')) {
            $working_week_days[] = 'Thu';
        }

        if ($request->has('friday')) {
            $working_week_days[] = 'Fri';
        }

        if ($request->has('saturday')) {
            $working_week_days[] = 'Sat';
        }

        if ($request->has('sunday')) {
            $working_week_days[] = 'Sun';
        }

        $organisation_setting->working_week_days = json_encode($working_week_days);

        $working_days_january = array();
        $working_days_february = array();
        $working_days_march = array();
        $working_days_april = array();
        $working_days_may = array();
        $working_days_june = array();
        $working_days_july = array();
        $working_days_august = array();
        $working_days_september = array();
        $working_days_october = array();
        $working_days_november = array();
        $working_days_december = array();

        $start_date = date_create($request->start_date);
        $end_date = date_create($request->end_date);

        $holidays = array();
        $holidays_length = 0;
        $count = 0;


        if ($request->has('holidays')) {

            foreach (explode(',', $request->holidays[0]) as $key => $value) {

                $holidays[] = $value;
                $holidays_length++;
            }

        }

        while ($start_date <= $end_date) {

            $date = $start_date->format('Y-m-d');

            $day = date('D', strtotime($date));

            $is_business_day = 0;

            foreach ($working_week_days as $week_day) {
                if ($day == $week_day) {
                    $is_business_day = 1;
                }
            };

            if ($holidays !== []) {


                if ($date != $holidays[$count] && $is_business_day == 1) {

                    $month = date('F', strtotime($date));

                    if ($month == 'January') {
                        $working_days_january[] = $date;

                    } else if ($month == 'February') {
                        $working_days_february[] = $date;

                    } else if ($month == 'March') {
                        $working_days_march[] = $date;

                    } else if ($month == 'April') {
                        $working_days_april[] = $date;

                    } else if ($month == 'May') {
                        $working_days_may[] = $date;

                    } else if ($month == 'June') {
                        $working_days_june[] = $date;

                    } else if ($month == 'July') {
                        $working_days_july[] = $date;

                    } else if ($month == 'August') {
                        $working_days_august[] = $date;

                    } else if ($month == 'September') {
                        $working_days_september[] = $date;

                    } else if ($month == 'October') {
                        $working_days_october[] = $date;

                    } else if ($month == 'November') {
                        $working_days_november[] = $date;

                    } else {
                        $working_days_december[] = $date;
                    }

                } else {

                    if (($count + 1) < $holidays_length) {
                        $count++;
                    }
                }

            }

            $start_date->modify('+1 day');

        }

        $organisation_setting->working_days_january = json_encode($working_days_january);
        $organisation_setting->working_days_february = json_encode($working_days_february);
        $organisation_setting->working_days_march = json_encode($working_days_march);
        $organisation_setting->working_days_april = json_encode($working_days_april);
        $organisation_setting->working_days_may = json_encode($working_days_may);
        $organisation_setting->working_days_june = json_encode($working_days_june);
        $organisation_setting->working_days_july = json_encode($working_days_july);
        $organisation_setting->working_days_august = json_encode($working_days_august);
        $organisation_setting->working_days_september = json_encode($working_days_september);
        $organisation_setting->working_days_october = json_encode($working_days_october);
        $organisation_setting->working_days_november = json_encode($working_days_november);
        $organisation_setting->working_days_december = json_encode($working_days_december);

        $organisation_setting->holidays = json_encode($holidays);


        if ($organisation_setting->save()) {
            flash(translate('Organisation Settings have been inserted successfully'))->success();

            if ($organisation->catering == 1) {
                return redirect()->route('organisation_prices.create', ['organisation_setting_id' => $organisation_setting->id]);
            } else {
                return redirect()->route('organisation_settings.index', $organisation_setting->id);
            }


        } else {
            flash(translate('Sorry! Something went wrong!'))->error();
            return redirect()->back();

        }


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
        $organisation_setting = OrganisationSetting::findOrFail($id);

        $organisation = $organisation_setting->organisation()->first();


        return view('backend.organisation.organisation_settings.edit', compact('organisation_setting', 'organisation'));

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
        $organisation_setting = OrganisationSetting::findorfail($id);

        $organisation = Organisation::findorfail($organisation_setting->organisation_id);
        $go_to_prices = 0;

        $existing_organisation_settings = OrganisationSetting::where('organisation_id', $organisation_setting->organisation_id)->where('organisation_id', '!=', $organisation_setting->organisation_id)->get();

        foreach ($existing_organisation_settings as $setting) {

            $check_start = Carbon::create($setting->date_from);
            $check_end = Carbon::create($setting->date_to);

            $this_start = Carbon::create($request->start_date);
            $this_end = Carbon::create($request->end_date);

            if ($this_start->gte($check_start) && $this_start->lte($check_start)) {
                flash(translate(translate('There is already a period registered in between this date range!')))->error();
                return redirect()->back()->withInput($request->all());
            } else if ($this_start->lte($check_start) && $this_end->gte($check_end)) {
                flash(translate(translate('There is already a period registered in between this date range!')))->error();
                return redirect()->back()->withInput($request->all());
            } else if ($this_start->lte($check_start) && $this_end->gte($check_start)) {
                flash(translate(translate('There is already a period registered in between this date range!')))->error();
                return redirect()->back()->withInput($request->all());
            } else if ($this_start->gte($check_start) && $this_end->lte($check_end)) {
                flash(translate(translate('There is already a period registered in between this date range!')))->error();
                return redirect()->back()->withInput($request->all());
            }


        }

        $organisation_setting->date_from = $request->start_date;
        $organisation_setting->date_to = $request->end_date;

        if ($organisation_setting->date_from >= $organisation_setting->date_to) {

            flash(translate('End Date should be greater than Start Date1'))->error();
            return redirect()->back();
        }

        if ($organisation_setting->max_snack_quantity != $request->max_snack_quantity || $organisation_setting->max_meal_quantity != $request->max_meal_quantity) {
            $go_to_prices = 1;
        }


        if ($organisation_setting->max_snack_quantity > $request->max_snack_quantity) {

            $price_ranges = OrganisationPriceRange::where('organisation_setting_id', $organisation_setting->id)->get();
            foreach ($price_ranges as $price_range) {
                $prices = OrganisationPrice::where('organisation_price_range_id', $price_range->id)->where('quantity', '>', $request->max_snack_quantity)
                    ->where('type', '=', 'snack')->get();

                foreach ($prices as $price) {
                    $price->delete();
                }

            }


        }

        $organisation_setting->max_snack_quantity = $request->max_snack_quantity;

        if ($organisation_setting->max_meal_quantity > $request->max_meal_quantity) {

            $price_ranges = OrganisationPriceRange::where('organisation_setting_id', $organisation_setting->id)->get();
            foreach ($price_ranges as $price_range) {
                $prices = OrganisationPrice::where('organisation_price_range_id', $price_range->id)->where('quantity', '>', $request->max_meal_quantity)
                    ->where('type', '=', 'meal')->get();

                foreach ($prices as $price) {
                    $price->delete();
                }

//        $go_to_prices=1;

            }

        }

        $organisation_setting->max_meal_quantity = $request->max_meal_quantity;


        if ($request->has('absence')) {
            $organisation_setting->absence = 1;
            $organisation_setting->absence_days_num = $request->absence_days;
        } else {
            $organisation_setting->absence = 0;
            $organisation_setting->absence_days_num = null;
        }

        $organisation_setting->preorder_days_num = $request->preorder_days_num;

        $working_week_days = array();

        if ($request->has('monday')) {
//            array_push($working_week_days, 'Monday');
            $working_week_days[] = 'Mon';

        }


        if ($request->has('tuesday')) {
            $working_week_days[] = 'Tue';
        }

        if ($request->has('wednesday')) {
            $working_week_days[] = 'Wed';
        }

        if ($request->has('thursday')) {
            $working_week_days[] = 'Thu';
        }

        if ($request->has('friday')) {
            $working_week_days[] = 'Fri';
        }

        if ($request->has('saturday')) {
            $working_week_days[] = 'Sat';
        }

        if ($request->has('sunday')) {
            $working_week_days[] = 'Sun';
        }

        $organisation_setting->working_week_days = json_encode($working_week_days);

        $working_days_january = array();
        $working_days_february = array();
        $working_days_march = array();
        $working_days_april = array();
        $working_days_may = array();
        $working_days_june = array();
        $working_days_july = array();
        $working_days_august = array();
        $working_days_september = array();
        $working_days_october = array();
        $working_days_november = array();
        $working_days_december = array();

        $start_date = date_create($request->start_date);
        $end_date = date_create($request->end_date);

        $holidays = array();
        $holidays_length = 0;
        $count = 0;


        if ($request->has('holidays')) {

            foreach (explode(',', $request->holidays[0]) as $key => $value) {

                $holidays[] = $value;
                $holidays_length++;
            }

        }

        while ($start_date <= $end_date) {

            $date = $start_date->format('Y-m-d');

            $day = date('D', strtotime($date));

            $is_business_day = 0;

            foreach ($working_week_days as $week_day) {
                if ($day == $week_day) {
                    $is_business_day = 1;
                }
            };

            if ($holidays !== []) {


                if ($date != $holidays[$count] && $is_business_day == 1) {

                    $month = date('F', strtotime($date));

                    if ($month == 'January') {
                        $working_days_january[] = $date;

                    } else if ($month == 'February') {
                        $working_days_february[] = $date;

                    } else if ($month == 'March') {
                        $working_days_march[] = $date;

                    } else if ($month == 'April') {
                        $working_days_april[] = $date;

                    } else if ($month == 'May') {
                        $working_days_may[] = $date;

                    } else if ($month == 'June') {
                        $working_days_june[] = $date;

                    } else if ($month == 'July') {
                        $working_days_july[] = $date;

                    } else if ($month == 'August') {
                        $working_days_august[] = $date;

                    } else if ($month == 'September') {
                        $working_days_september[] = $date;

                    } else if ($month == 'October') {
                        $working_days_october[] = $date;

                    } else if ($month == 'November') {
                        $working_days_november[] = $date;

                    } else {
                        $working_days_december[] = $date;
                    }

                } else {

                    if (($count + 1) < $holidays_length) {
                        $count++;
                    }
                }

            }

            $start_date->modify('+1 day');

        }

        $organisation_setting->working_days_january = json_encode($working_days_january);
        $organisation_setting->working_days_february = json_encode($working_days_february);
        $organisation_setting->working_days_march = json_encode($working_days_march);
        $organisation_setting->working_days_april = json_encode($working_days_april);
        $organisation_setting->working_days_may = json_encode($working_days_may);
        $organisation_setting->working_days_june = json_encode($working_days_june);
        $organisation_setting->working_days_july = json_encode($working_days_july);
        $organisation_setting->working_days_august = json_encode($working_days_august);
        $organisation_setting->working_days_september = json_encode($working_days_september);
        $organisation_setting->working_days_october = json_encode($working_days_october);
        $organisation_setting->working_days_november = json_encode($working_days_november);
        $organisation_setting->working_days_december = json_encode($working_days_december);

        $organisation_setting->holidays = json_encode($holidays);

        if ($organisation_setting->save()) {
            flash(translate('Organisation Settings have been updated successfully'))->success();
//            return $organisation_setting;


        } else {
            flash(translate('Sorry! Something went wrong!'))->error();
        }

//        return view('organisation_settings.edit', $organisation_setting->id, (compact('go_to_prices')));

        //return redirect()->route('organisation_settings.edit', $organisation_setting->organisation_id);   route('organisation_settings.edit', $period->id
        return view('backend.organisation.organisation_settings.edit', compact('organisation_setting', 'organisation', 'go_to_prices'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $organisation_settings = OrganisationSetting::findorfail($id);
        $organisation_settings->delete();

        return redirect()->back();
    }
}
