<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service;
use App\Language;
use App\ServiceTranslation;

class ServiceController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $sort_search = null;
    $services = Service::orderBy('order_level', 'desc');
    if ($request->has('search')){
      $sort_search = $request->search;
      $services = $services->where('name', 'like', '%'.$sort_search.'%');
    }
    $services = $services->paginate(15);
    return view('backend.services.index', compact('services', 'sort_search'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(){
    return view('backend.services.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $service = new Service;
    $service->name = $request->name;
    $service->banner = $request->banner;
    $service->short_description = $request->short_description;
    $service->order_level = 0;
    if($request->order_level != null) {
      $service->order_level = $request->order_level;
    }
    $service->save();

    $service_translation = ServiceTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'service_id' => $service->id]);
    $service_translation->name = $request->name;
    $service_translation->short_description = $request->short_description;
    $service_translation->save();

    flash(translate('Service has been inserted successfully'))->success();
    return redirect()->route('services.index');
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
    $lang = $request->lang;
    $service = Service::findOrFail($id);

    return view('backend.services.edit', compact('service', 'lang'));
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
    $service = Service::findOrFail($id);
    if($request->lang == env("DEFAULT_LANGUAGE")){
      $service->name = $request->name;
      $service->short_description = $request->short_description;
    }
    if($request->order_level != null) {
      $service->order_level = $request->order_level;
    }
    $service->banner = $request->banner;
    $service->save();

    $service_translation = ServiceTranslation::firstOrNew(['lang' => $request->lang, 'service_id' => $service->id]);
    $service_translation->name = $request->name;
    $service_translation->short_description = $request->short_description;
    $service_translation->save();

    flash(translate('Service has been updated successfully'))->success();
    return back();
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id){
    $service = Service::findOrFail($id);

    foreach ($service->service_translations as $key => $service_translation) {
      $service_translation->delete();
    }

    $service->delete();

    flash(translate('Service has been deleted successfully'))->success();
    return redirect()->route('services.index');
  }
}
