<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductType;
use App\ProductTypeTranslation;
use App\Product;
use Illuminate\Support\Str;

class ProductTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search =null;
        $types = ProductType::orderBy('name', 'asc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $types = $types->where('name', 'like', '%'.$sort_search.'%');
        }
        $types = $types->paginate(15);
        return view('backend.product.types.index', compact('types', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('backend.product.types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $type = new ProductType;
        $type->name = $request->name;
        $type->slogan = $request->slogan;
        $type->short_description = $request->short_description;
        $type->meta_title = $request->meta_title;
        $type->meta_description = $request->meta_description;
        if ($request->slug != null) {
            $type->slug = str_replace(' ', '-', $request->slug);
        }
        else {
            $type->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.Str::random(5);
        }
        $type->banner = $request->banner;
        $type->save();

        $type_translation = ProductTypeTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'product_type_id' => $type->id]);
        $type_translation->name = $request->name;
        $type_translation->slogan = $request->slogan;
        $type_translation->short_description = $request->short_description;
        $type_translation->save();

        flash(translate('Product Type has been inserted successfully'))->success();
        return redirect()->route('product_types.index');

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
        $lang   = $request->lang;
        $type  = ProductType::findOrFail($id);
        return view('backend.product.types.edit', compact('type','lang'));
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
        $type = ProductType::findOrFail($id);
        if($request->lang == env("DEFAULT_LANGUAGE")){
            $type->name = $request->name;
            $type->slogan = $request->slogan;
            $type->short_description = $request->short_description;
        }
        $type->banner = $request->banner;
        $type->meta_title = $request->meta_title;
        $type->meta_description = $request->meta_description;
        if ($request->slug != null) {
            $type->slug = strtolower($request->slug);
        }
        else {
            $type->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.Str::random(5);
        }

        $type->save();

        $type_translation = ProductTypeTranslation::firstOrNew(['lang' => $request->lang, 'product_type_id' => $type->id]);
        $type_translation->name = $request->name;
        $type_translation->slogan = $request->slogan;
        $type_translation->short_description = $request->short_description;
        $type_translation->save();

        flash(translate('Product Type has been updated successfully'))->success();
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
        $type = ProductType::findOrFail($id);
        Product::where('product_type_id', $type->id)->delete();
        foreach ($type->product_type_translations as $key => $type_translation) {
            $type_translation->delete();
        }
        ProductType::destroy($id);

        flash(translate('Product Type has been deleted successfully'))->success();
        return redirect()->route('product_types.index');

    }
}
