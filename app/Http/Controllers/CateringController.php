<?php

namespace App\Http\Controllers;

use App\Models\ApiClient\ApiClient;
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

class CateringController extends Controller
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

        $organisation_periods = OrganisationSetting::where('organisation_id', $organisation_id);
        $organisation_periods = $organisation_periods->paginate(15);

        $organisation_locations = OrganisationLocation::where('organisation_id',$organisation_id);

        if ($request->has('search')) {
            $sort_search = $request->search;
            $organisation_locations = $organisation_locations->where('name', 'like', '%' . $sort_search . '%');
        }

        $organisation_locations = $organisation_locations->paginate(15);

        return view('backend.organisation.catering.index', compact('organisation', 'sort_search', 'organisation_periods', 'organisation_locations'));

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
