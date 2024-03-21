<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StoreCity;
use App\StoreCityTranslation;

class StoreCityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $sort_search =null;
      $cities = StoreCity::orderBy('order_level', 'desc');
      if ($request->has('search')){
        $sort_search = $request->search;
        $cities = $cities->where('name', 'like', '%'.$sort_search.'%');
      }
      $cities = $cities->paginate(15);
      return view('backend.product.stores.cities.index', compact('cities', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('backend.product.stores.cities.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $city = new StoreCity;
        $city->name = $request->name;
        $city->thumbnail_img = $request->thumbnail_img;
        $city->order_level = 0;
        if($request->order_level != null) {
          $city->order_level = $request->order_level;
        }
        $city->save();

        $store_city_translation = StoreCityTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'store_city_id' => $city->id]);
        $store_city_translation->name = $request->name;
        $store_city_translation->save();

        flash(translate('City has been inserted successfully'))->success();
        return redirect()->route('store_cities.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function edit(Request $request, $id)
     {
         $lang  = $request->lang;
         $city  = StoreCity::findOrFail($id);
         return view('backend.product.stores.cities.edit', compact('city', 'lang'));
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
        $city = StoreCity::findOrFail($id);
        if($request->lang == env("DEFAULT_LANGUAGE")){
            $city->name = $request->name;
        }
        $city->thumbnail_img = $request->thumbnail_img;
        if($request->order_level != null) {
          $city->order_level = $request->order_level;
        }
        $city->save();

        $store_city_translation = StoreCityTranslation::firstOrNew(['lang' => $request->lang, 'store_city_id' => $city->id]);
        $store_city_translation->name = $request->name;
        $store_city_translation->save();

        flash(translate('City has been updated successfully'))->success();
        return redirect()->route('store_cities.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $city = StoreCity::findOrFail($id);

        foreach ($city->store_city_translations as $key => $store_city_translation) {
          $store_city_translation->delete();
        }

        StoreCity::destroy($id);

        flash(translate('City has been deleted successfully'))->success();
        return redirect()->route('store_cities.index');
    }
}
