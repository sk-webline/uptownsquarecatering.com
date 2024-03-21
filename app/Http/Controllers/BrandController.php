<?php

namespace App\Http\Controllers;

use App\Models\Btms\ItemBrand;
use Illuminate\Http\Request;
use App\Brand;
use App\BrandTranslation;
use App\Product;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search =null;
        $brands = Brand::orderBy('order_level', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $brands = $brands->where('name', 'like', '%'.$sort_search.'%');
        }
        $brands = $brands->paginate(15);

        $btms_brands  = ItemBrand::all();
        return view('backend.product.brands.index', compact('brands', 'sort_search', 'btms_brands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $brand = new Brand;
        $brand->name = $request->name;
        $brand->about_desc = $request->about_desc;
        $brand->banner_desc = $request->banner_desc;
        $brand->slogan_title = $request->slogan_title;
        $brand->slogan_description = $request->slogan_description;
        $brand->slogan_title_2 = $request->slogan_title_2;
        $brand->slogan_description_2 = $request->slogan_description_2;
        $brand->type = $request->type;
        $brand->video_link = $request->video_link;
        $brand->order_level = 0;
        $brand->accounting_code = $request->accounting_code ?? null;
        if($request->order_level != null) {
          $brand->order_level = $request->order_level;
        }
        $brand->meta_title = $request->meta_title;
        $brand->meta_description = $request->meta_description;
        if ($request->slug != null) {
            $brand->slug = str_replace(' ', '-', $request->slug);
        }
        else {
            $brand->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.Str::random(5);
        }

        $brand->logo = $request->logo;
        $brand->header = $request->header;
        $brand->banner = $request->banner;
        $brand->save();

        $brand_translation = BrandTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'brand_id' => $brand->id]);
        $brand_translation->name = $request->name;
        $brand_translation->about_desc = $request->about_desc;
        $brand_translation->banner_desc = $request->banner_desc;
        $brand_translation->slogan_title = $request->slogan_title;
        $brand_translation->slogan_description = $request->slogan_description;
        $brand_translation->slogan_title_2 = $request->slogan_title_2;
        $brand_translation->slogan_description_2 = $request->slogan_description_2;
        $brand_translation->save();

        flash(translate('Brand has been inserted successfully'))->success();
        return redirect()->route('brands.index');

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
        $brand  = Brand::findOrFail($id);
        $btms_brands  = ItemBrand::all();

        return view('backend.product.brands.edit', compact('brand','lang', 'btms_brands'));
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
        $brand = Brand::findOrFail($id);
        if($request->lang == env("DEFAULT_LANGUAGE")){
            $brand->name = $request->name;
            $brand->about_desc = $request->about_desc;
            $brand->banner_desc = $request->banner_desc;
            $brand->slogan_title = $request->slogan_title;
            $brand->slogan_description = $request->slogan_description;
            $brand->slogan_title_2 = $request->slogan_title_2;
            $brand->slogan_description_2 = $request->slogan_description_2;
        }
        if($request->order_level != null) {
          $brand->order_level = $request->order_level;
        }
        $brand->meta_title = $request->meta_title;
        $brand->meta_description = $request->meta_description;
        if ($request->slug != null) {
            $brand->slug = strtolower($request->slug);
        }
        else {
            $brand->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.Str::random(5);
        }
        $brand->logo = $request->logo;
        $brand->header = $request->header;
        $brand->banner = $request->banner;
        $brand->type = $request->type;
        $brand->video_link = $request->video_link;
        $brand->accounting_code = $request->accounting_code ?? null;
        $brand->save();

        $brand_translation = BrandTranslation::firstOrNew(['lang' => $request->lang, 'brand_id' => $brand->id]);
        $brand_translation->name = $request->name;
        $brand_translation->about_desc = $request->about_desc;
        $brand_translation->banner_desc = $request->banner_desc;
        $brand_translation->slogan_title = $request->slogan_title;
        $brand_translation->slogan_description = $request->slogan_description;
        $brand_translation->slogan_title_2 = $request->slogan_title_2;
        $brand_translation->slogan_description_2 = $request->slogan_description_2;
        $brand_translation->save();

        flash(translate('Brand has been updated successfully'))->success();
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
        $brand = Brand::findOrFail($id);
        Product::where('brand_id', $brand->id)->delete();
        foreach ($brand->brand_translations as $key => $brand_translation) {
            $brand_translation->delete();
        }
        Brand::destroy($id);

        flash(translate('Brand has been deleted successfully'))->success();
        return redirect()->route('brands.index');

    }
}
