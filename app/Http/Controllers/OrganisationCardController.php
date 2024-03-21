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


class OrganisationCardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $organisation_id)
    {
        $sort_search = null;
        $organisation = Organisation::findorfail($organisation_id);
        $cards = $organisation->cards();

        if ($request->has('search')) {
            $sort_search = $request->search;
            $cards = $organisation->cards()->where('rfid_no', 'like', '%' . $sort_search . '%');
        }

        $cards = $cards->paginate(15); //->get();

        return view('backend.organisation.organisation_cards.index', compact('organisation', 'cards', 'sort_search'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($organisation_settings_id)
    {

        //

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $organisation_setting_id)
    {

        //

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

    /**
     * Check if the rfid_no given exists.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function rfid_card_exists(Request $request)
    {
        $card = Card::where('rfid_no', '123456')->get();

        return json_encode($card);


    }


}
