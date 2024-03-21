<?php

namespace App\Http\Controllers;

use App\Models\Btms\VatCodes;
use Illuminate\Http\Request;
use App\Country;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $countries = Country::paginate(15);
        return view('backend.setup_configurations.countries.index', compact('countries'));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
      $country = Country::find($id);
      $btms_vat_codes =  VatCodes::getVatCodes();

      return view('backend.setup_configurations.countries.edit', compact('country','btms_vat_codes'));
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
      $country = Country::find($id);
      $country->status = 0;
      $country->vat_included = 0;
      if ($request->has('status')) {
        $country->status = 1;
      }
      if ($request->has('vat_included')) {
        $country->vat_included = 1;
      }
      if (!empty($request->btms_vat)) {
          $btms_vat_code = VatCodes::getVatCodeFromCode($request->btms_vat);
          $vat_percentage = (int) number_format($btms_vat_code->{'Percentage'}, 0);
          $country->vat_percentage = $vat_percentage;
          $country->btms_vat_code = $request->btms_vat;
          if ($vat_percentage == 0) {
              $country->vat_included = 0;
          }
          elseif ($vat_percentage > 0) {
              $country->vat_included = 1;
          }
      }

      $country->save();

      flash(translate('Country has been updated successfully'))->success();
      return back();
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

    public function updateStatus(Request $request){
        $country = Country::findOrFail($request->id);
        $country->status = $request->status;
        if($country->save()){
            return 1;
        }
        return 0;
    }

  public function updateVatInclude(Request $request){
    $country = Country::findOrFail($request->id);
    $country->vat_included = $request->status;
    if($country->save()){
      return 1;
    }
    return 0;
  }

  public function get_phone_code(Request $request) {
    $country = Country::find($request->country_id);
    $phone_code = findCountryCode($country->code);

    echo json_encode($phone_code);
  }


}
