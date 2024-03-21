<?php

namespace App\Http\Controllers;

use App\Models\ApiClient\ApiClient;
use App\Models\Organisation;
use App\Models\OrganisationExtraDay;
use App\Models\OrganisationLocation;
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


class OrganisationLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $organisation_id)
    {

        $sort_search = null;

        $organisation = Organisation::findOrFail($organisation_id);

        $organisation_locations = OrganisationLocation::where('organisation_id',$organisation_id);

        if ($request->has('search')) {
            $sort_search = $request->search;
            $organisation_locations = $organisation_locations->where('name', 'like', '%' . $sort_search . '%');
        }

        $organisation_locations = $organisation_locations->paginate(15);

        return view('backend.organisation.organisation_locations.index', compact('organisation', 'organisation_locations'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($organisation_id)
    {

        $organisation = Organisation::findOrFail($organisation_id);

        return view('backend.organisation.organisation_locations.create', compact('organisation'));

    }

    /**
     * Store organisation location (catering locations) for Catering
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $organisation_id)
    {

       $location = new OrganisationLocation();
       $location->name = $request->name;
       $location->organisation_id = $organisation_id;

       if($location->save()){
           flash(translate('Organisation Location has been inserted successfully'))->success();

       } else {
           flash(translate('Sorry! Something went wrong!'))->error();

       }

        return redirect()->route('catering.index', $organisation_id);



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
        $location = OrganisationLocation::findOrFail($id);

        $organisation = Organisation::findOrFail($location->organisation_id);

        return view('backend.organisation.organisation_locations.edit', compact('location', 'organisation'));
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

        $location = OrganisationLocation::findOrFail($id);

        $location->name = $request->name;

        if($location->save()){
            flash(translate('Organisation Location has been updated successfully'))->success();

        } else {
            flash(translate('Sorry! Something went wrong!'))->error();

        }

        return redirect()->route('catering.index', $location->organisation_id);



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $location = OrganisationLocation::findorfail($id);
        if($location->delete()){
            flash(translate('Organisation Location has been deleted successfully'))->success();

        } else {
            flash(translate('Sorry! Something went wrong!'))->error();

        }

        return redirect()->back();
    }
}
