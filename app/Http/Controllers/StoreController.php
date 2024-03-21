<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Store;
use App\Language;
use App\StoreTranslation;
use App\StoreCity;

class StoreController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $sort_search =null;
    $stores = Store::orderBy('order_level', 'desc');
    if ($request->has('search')){
      $sort_search = $request->search;
      $stores = $stores->where('name', 'like', '%'.$sort_search.'%');
    }
    $stores = $stores->paginate(15);
    return view('backend.product.stores.index', compact('stores', 'sort_search'));
  }


  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $cities = StoreCity::all();
    return view('backend.product.stores.create', compact('cities'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $store = new Store;
    $store->name = $request->name;
    $store->city_id = ($request->city_id) ? $request->city_id : 0;
    $store->order_level = 0;
    $store->thumbnail_img = $request->thumbnail_img;
    if($request->order_level != null) {
      $store->order_level = $request->order_level;
    }
    $store->phone = $request->phone;
    if ($request->has('fax')) {
      $store->fax = $request->fax;
    }
    $store->address = $request->address;
    $store->google_map_url = $request->google_map_url;
    $store->working_days_1 = $request->working_days_1;
    $store->working_hours_1 = $request->working_hours_1;
    $store->working_days_2 = $request->working_days_2;
    $store->working_hours_2 = $request->working_hours_2;
    $store->working_days_3 = $request->working_days_3;
    $store->working_hours_3 = $request->working_hours_3;
    $store->x_pos = $request->x_pos;
    $store->y_pos = $request->y_pos;
    $store->x_pos_phone = $request->x_pos_phone;
    $store->y_pos_phone = $request->y_pos_phone;
    $store->save();

    $store_translation = StoreTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'store_id' => $store->id]);
    $store_translation->name = $request->name;
    $store_translation->address = $request->address;
    $store_translation->working_days_1 = $request->working_days_1;
    $store_translation->working_days_2 = $request->working_days_2;
    $store_translation->working_days_3 = $request->working_days_3;
    $store_translation->save();

    flash(translate('Store has been inserted successfully'))->success();
    return redirect()->route('stores.index');
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
  public function edit(Request $request, $id)
  {
    $cities = StoreCity::all();
    $lang = $request->lang;
    $store = Store::find($id);
    
    return view('backend.product.stores.edit', compact('store','lang', 'cities'));
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
    $store = Store::find($id);

    if($request->order_level != null) {
      $store->order_level = $request->order_level;
    }
    $store->city_id = ($request->city_id) ? $request->city_id : 0;
    $store->phone = $request->phone;
    if ($request->has('fax')) {
      $store->fax = $request->fax;
    }
    $store->working_hours_1 = $request->working_hours_1;
    $store->working_hours_2 = $request->working_hours_2;
    $store->working_hours_3 = $request->working_hours_3;
    $store->google_map_url = $request->google_map_url;
    $store->thumbnail_img = $request->thumbnail_img;

    if($request->lang == env("DEFAULT_LANGUAGE")) {
      $store->name = $request->name;
      $store->address = $request->address;
      $store->working_days_1 = $request->working_days_1;
      $store->working_days_2 = $request->working_days_2;
      $store->working_days_3 = $request->working_days_3;
    }
    $store->x_pos = $request->x_pos;
    $store->y_pos = $request->y_pos;
    $store->x_pos_phone = $request->x_pos_phone;
    $store->y_pos_phone = $request->y_pos_phone;

    $store->save();

    $store_translation = StoreTranslation::firstOrNew(['lang' => $request->lang, 'store_id' => $store->id]);
    $store_translation->name = $request->name;
    $store_translation->address = $request->address;
    $store_translation->working_days_1 = $request->working_days_1;
    $store_translation->working_days_2 = $request->working_days_2;
    $store_translation->working_days_3 = $request->working_days_3;
    $store_translation->save();

    flash(translate('Store has been updated successfully'))->success();
    return redirect()->route('stores.index');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $store = Store::findOrFail($id);

    foreach ($store->store_translations as $key => $store_translation) {
      $store_translation->delete();
    }

    Store::destroy($id);

    flash(translate('Store has been deleted successfully'))->success();
    return redirect()->route('stores.index');
  }
}
