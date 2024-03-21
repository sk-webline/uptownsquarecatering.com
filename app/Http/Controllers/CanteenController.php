<?php

namespace App\Http\Controllers;

use App\Models\ApiClient\ApiClient;
use App\Models\CanteenLocation;
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

class CanteenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($organisation_id, Request $request)
    {
        $sort_search = null;

        $organisation = Organisation::findorfail($organisation_id);

        $canteen_periods = CanteenSetting::where('organisation_id',$organisation_id);

//        dd($canteen_periods);
        $canteen_periods = $canteen_periods->paginate(15);

        $canteen_locations = CanteenLocation::where('organisation_id',$organisation_id);

        if ($request->has('search')) {
            $sort_search = $request->search;
            $canteen_locations = $canteen_locations->where('name', 'like', '%' . $sort_search . '%');
        }

        $canteen_locations = $canteen_locations->paginate(15);

        return view('backend.organisation.canteen.index', compact('organisation', 'sort_search', 'canteen_periods', 'canteen_locations'));

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



}
