<?php

namespace App\Http\Controllers;

use App\Models\ApiClient\ApiClient;
use App\Models\CanteenLocation;
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

class OrganisationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;

        $organisations = Organisation::orderBy('id', 'asc');

        if ($request->has('search')) {
            $sort_search = $request->search;
            $organisations = $organisations->where('name', 'like', '%' . $sort_search . '%');
        }

//        $organisations_existing = Organisation::select('zero_vending_id')->get();

        $organisations = $organisations->paginate(15);

        $apiModel = new ZeroVendingApiMethods();

        $token = config('app.zerovending.token');

        $results = $apiModel->get_organisations($token);
        $results = json_decode($results);

        $z_organisations = array();

        foreach ($results as $result){
            if(Organisation::where('zero_vending_id',$result->id)->count()==0){
                $z_organisations[] = $result;
            }
        }

        $zero_vending_organisations = $z_organisations;

        return view('backend.organisation.index', compact('organisations', 'zero_vending_organisations', 'sort_search'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if($_SERVER['REMOTE_ADDR'] != '82.102.76.201'){
            return redirect()->route('organisations.index');
        }

        return view('backend.organisation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if($_SERVER['REMOTE_ADDR'] != '82.102.76.201'){
            return redirect()->route('organisations.index');
        }

        $organisation = new Organisation();
        $organisation->name = $request->name;
//        $organisation->catering = 1;

        if ($request->has('catering')) {
            $organisation->catering = 1;
        } else {
            $organisation->catering = 0;
        }

        if ($request->has('canteen')) {
            $organisation->canteen = 1;
        } else {
            $organisation->canteen = 0;
        }

        if ($request->has('top_up')) {
            $organisation->top_up = 1;
        } else {
            $organisation->top_up = 0;
        }

        if ($request->has('custom_packets')) {
            $organisation->custom_packets = 1;
        } else {
            $organisation->custom_packets = 0;
        }

        if ($request->has('required_field')) {
            $organisation->required_field_name = $request->required_field_name;
        } else {
            $organisation->required_field_name = null;
        }


        if ($organisation->save()) {
            flash(translate('Organisation has been inserted successfully'))->success();
//            return redirect()->route('organisation_settings.create', ['organisation_id'=>$organisation->id]  );

        } else {
            flash(translate('Sorry! Something went wrong!'))->error();

        }

        return redirect()->route('catering.index', ['organisation_id' => $organisation->id]);


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

        $organisation = Organisation::findorfail($id);


        return view('backend.organisation.edit', compact('organisation'));
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
        $organisation = Organisation::findorfail($id);

        $organisation->name = $request->name;

        if ($request->has('catering') && $organisation->catering == 0) {
            $organisation->catering = 1;
        } else if ($organisation->catering != 1 && !$request->has('catering')) {
            $organisation->catering = 0;
        }

        if ($request->has('custom_packets') && $organisation->custom_packets == 0) {
            $organisation->custom_packets = 1;
        } else if ($organisation->custom_packets != 1 && !$request->has('custom_packets')) {
            $organisation->custom_packets = 0;
        }

        if ($request->has('canteen')) {
            $organisation->canteen = 1;
        } else {
            $organisation->canteen = 0;
        }

        if ($request->has('top_up')) {
            $organisation->top_up = 1;
        } else {
            $organisation->top_up = 0;
        }

        if ($request->has('custom_packets')) {
            $organisation->custom_packets = 1;
        } else {
            $organisation->custom_packets = 0;
        }

        if ($request->has('required_field')) {

            $organisation->required_field_name = $request->required_field_name;

            foreach(Card::where('organisation_id', $organisation->id)->get() as $card){
                $card->required_field_name = $request->required_field_name;
                $card->save();
            }

        } else {
            $organisation->required_field_name = null;

            $organisation->required_field_name = $request->required_field_name;

            foreach(Card::where('organisation_id', $organisation->id)->get() as $card){
                $card->required_field_name = null;
                $card->required_field_value = null;
                $card->save();
            }

        }

        $organisation->email_for_order_id = $request->email_for_order;

        if ($organisation->save()) {
            flash(translate('Organisation has been updated successfully'))->success();

        } else {
            flash(translate('Sorry! Something went wrong!'))->error();
        }

        return redirect()->route('organisations.index', ['organisation_id' => $organisation->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $organisation = Organisation::findOrFail($id);

        foreach (OrganisationSetting::where('organisation_id', $organisation->id)->get() as $organisationSetting) {
//            $organisationSetting->organisation_id = null;
//            $organisationSetting->save();

            $organisationSetting->delete();
        }

        foreach (OrganisationLocation::where('organisation_id', $organisation->id)->get() as $organisationLocation) {
//            $organisationSetting->organisation_id = null;
//            $organisationSetting->save();

            $organisationLocation->delete();
        }

        foreach(Card::where('organisation_id', $organisation->id)->get() as $card){
            $card->organisation_id = null;
            $card->save();
        }

        $organisation->delete();

        flash(translate('Organisation has been deleted successfully'))->success();

        return redirect()->back();

    }

    public function import(Request $request)
    {

        $organisation = new Organisation();

        $organisation->name = $request->organisation_name;
        $organisation->zero_vending_id = $request->organisation_id;

        $organisation->catering = 1;
        $organisation->top_up = 0;


        if ($organisation->save()) {
            flash(translate('Organisation has been inserted successfully'))->success();

            $apiModel = new ZeroVendingApiMethods();

            $token = config('app.zerovending.token');

            $organisation_id = $request->organisation_id;

            $cards = $apiModel->get_organisation_cards($token, $organisation_id);

            $cards = json_decode($cards);

            if (sizeof($cards) > 0) {
                foreach ($cards as $card) {

                    $new_card = new Card();
                    $new_card->rfid_no = $card->rfid_no;
                    $new_card->organisation_id = $organisation->id;
                    $new_card->required_field_name = $organisation->required_field_name;

                    $new_card->save();
                }
            }


        } else {
            flash(translate('Sorry! Something went wrong!'))->error();

        }

        return redirect()->route('organisations.edit', $organisation->id);

    }

    public function get_organisation_locations(Request $request)
    {

        $locations = OrganisationLocation::where('organisation_id', $request->organisation_id)->get();

        return $locations;


    }



    public function get_selected_organisations(Request $request)
    {


        if(in_array('all', $request->organisation_ids) || empty($request->organisation_ids)){
            $organisations = Organisation::all();
        }else{
            $organisations = Organisation::whereIn('id',$request->organisation_ids)->get();
        }

        return array('view' => view('backend.sales.all_orders.organisation_catering_plans_select_box', compact('organisations'))->render());


    }

    public function get_canteen_locations(Request $request)
    {

        $locations = CanteenLocation::where('organisation_id', $request->organisation_id)->get();

        return $locations;

    }




}
