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

class CanteenLocationController extends Controller
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
    public function create($organisation_id)
    {
        $organisation = Organisation::findOrFail($organisation_id);

        if ($organisation->canteen == 1) {
            return view('backend.organisation.canteen.canteen_locations.create', compact('organisation'));
        } else {
            return back();
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $organisation_id)
    {

        $organisation = Organisation::find($organisation_id);

        if($organisation == null){
            flash(translate('Sorry! Something went wrong!'))->error();
            return redirect()->route('canteen.index', $organisation_id);
        }

        $request->validate([
            'name' => 'required',
        ]);


        $location = new CanteenLocation();
        $location->name = $request->name;
        $location->organisation_id = $organisation->id;

        if($location->save()){
            flash(translate('Canteen Location has been inserted successfully'))->success();

        } else {
            flash(translate('Sorry! Something went wrong!'))->error();

        }

        return redirect()->route('canteen.index', $organisation_id);

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
        $location = CanteenLocation::findOrFail($id);

        $organisation = Organisation::findOrFail($location->organisation_id);

        return view('backend.organisation.canteen.canteen_locations.edit', compact('location', 'organisation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $location = CanteenLocation::findOrFail($id);

        if($location == null){
            flash(translate('Sorry! Something went wrong!'))->error();
            return back();
        }

        $request->validate([
            'name' => 'required',
        ]);

        $location->name = $request->name;

        if($location->save()){
            flash(translate('Canteen Location has been updated successfully'))->success();

        } else {
            flash(translate('Sorry! Something went wrong!'))->error();

        }

        return redirect()->route('canteen.index', $location->organisation_id);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $location = CanteenLocation::findorfail($id);
        if($location->delete()){
            flash(translate('Canteen Location has been deleted successfully'))->success();

        } else {
            flash(translate('Sorry! Something went wrong!'))->error();

        }

        return redirect()->back();
    }



}
