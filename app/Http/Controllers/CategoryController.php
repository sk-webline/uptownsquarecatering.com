<?php

namespace App\Http\Controllers;

use App\Models\Btms\ItemCategory;
use Illuminate\Http\Request;
use App\Category;
use App\CategoryBrand;
use App\HomeCategory;
use App\Product;
use App\Language;
use App\CategoryTranslation;
use App\CategoryBrandTranslation;
use App\Utility\CategoryUtility;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $sort_search =null;
//        $categories = Category::orderBy('name', 'asc');
    $categories = Category::orderBy('order_level', 'desc');
    if ($request->has('search')){
      $sort_search = $request->search;
      $categories = $categories->where('name', 'like', '%'.$sort_search.'%');
    }
    $categories = $categories->paginate(50);
    return view('backend.product.categories.index', compact('categories', 'sort_search'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $categories = Category::where('parent_id', 0)
      ->with('childrenCategories')
      ->get();
    $btms_categories = ItemCategory::whereNull('Related Category Code')->where('Level', 2)->orderBy('Name')->get();
    $btms_categories = null;
    return view('backend.product.categories.create', compact('categories', 'btms_categories'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $category = new Category;
    $category->name = $request->name;
    $category->b2b_description = $request->b2b_description;
    $category->short_description = $request->short_description;
    $category->order_level = 0;
    if($request->order_level != null) {
      $category->order_level = $request->order_level;
    }
    $category->digital = $request->digital;
    $category->header = $request->header;
    $category->banner = $request->banner;
    $category->icon = $request->icon;
      $category->show_b2b = 0;
    if ($request->has('show_b2b')) {
      $category->show_b2b = 1;
    }
    $category->b2b_banner = $request->b2b_banner;
    $category->meta_title = $request->meta_title;
    $category->meta_description = $request->meta_description;

    if ($request->btms_category != "0") {
      list($btms_level, $btms_category) = explode('|', $request->btms_category);
        $category->btms_category_level = $btms_level;
        $category->btms_category_code = $btms_category;
    }
    else {
      $category->btms_category_code = null;
    }

    if ($request->parent_id != "0") {
      $category->parent_id = $request->parent_id;

      $parent = Category::find($request->parent_id);
      $category->level = $parent->level + 1 ;
    }

    if ($request->slug != null) {
      $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
    }
    else {
      $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.Str::random(5);
    }
    if ($request->commision_rate != null) {
      $category->commision_rate = $request->commision_rate;
    }

    $category->save();

    $category_translation = CategoryTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'category_id' => $category->id]);
    $category_translation->name = $request->name;
    $category_translation->b2b_description = $request->b2b_description;
    $category_translation->short_description = $request->short_description;
    $category_translation->save();

    flash(translate('Category has been inserted successfully'))->success();
    return redirect()->route('categories.index');
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
    $category = Category::findOrFail($id);
    $categories = Category::where('parent_id', 0)
      ->with('childrenCategories')
      ->whereNotIn('id', CategoryUtility::children_ids($category->id, true))->where('id', '!=' , $category->id)
      ->orderBy('name','asc')
      ->get();

//    $btms_categories = Cache::remember('btms_item_categories', Carbon::now()->addMinutes(30), function () {
//      return ItemCategory::getAllCategories();
//    });
    $btms_categories = (object) [];

    return view('backend.product.categories.edit', compact('category', 'categories', 'lang', 'btms_categories'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function brands_edit(Request $request, $id)
  {
    $lang = $request->lang;
    $category = Category::findOrFail($id);
    $brands = getBrandsByMainCategory($id);

    return view('backend.product.categories.brands_edit', compact('category', 'brands', 'lang'));
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
    $category = Category::findOrFail($id);
    if($request->lang == env("DEFAULT_LANGUAGE")){
      $category->name = $request->name;
      $category->b2b_description = $request->b2b_description;
      $category->short_description = $request->short_description;
    }
    if($request->order_level != null) {
      $category->order_level = $request->order_level;
    }
    $category->digital = $request->digital;
    $category->header = $request->header;
    $category->banner = $request->banner;
    $category->icon = $request->icon;
      $category->show_b2b = 0;
    if ($request->has('show_b2b')) {
      $category->show_b2b = 1;
    }
    $category->b2b_banner = $request->b2b_banner;
    $category->meta_title = $request->meta_title;
    $category->meta_description = $request->meta_description;

    $previous_level = $category->level;

    if ($request->has('btms_category') && $request->btms_category != "0") {
        list($btms_level, $btms_category) = explode('|', $request->btms_category);
        $category->btms_category_level = $btms_level;
        $category->btms_category_code = $btms_category;
    }
    else {
        $category->btms_category_code = null;
    }

    if ($request->parent_id != "0") {
      $category->parent_id = $request->parent_id;

      $parent = Category::find($request->parent_id);
      $category->level = $parent->level + 1 ;
    }
    else{
      $category->parent_id = 0;
      $category->level = 0;
    }

    if($category->level > $previous_level){
      CategoryUtility::move_level_down($category->id);
    }
    elseif ($category->level < $previous_level) {
      CategoryUtility::move_level_up($category->id);
    }

    if ($request->slug != null) {
      $category->slug = strtolower($request->slug);
    }
    else {
      $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.Str::random(5);
    }


    if ($request->commision_rate != null) {
      $category->commision_rate = $request->commision_rate;
    }

    $category->save();

    $category_translation = CategoryTranslation::firstOrNew(['lang' => $request->lang, 'category_id' => $category->id]);
    $category_translation->name = $request->name;
    $category_translation->b2b_description = $request->b2b_description;
    $category_translation->short_description = $request->short_description;
    $category_translation->save();

    flash(translate('Category has been updated successfully'))->success();
    return back();
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update_brands(Request $request, $id)
  {

    foreach ($request->ids as $key => $bid){
      $category_brand = CategoryBrand::firstOrNew(['brand_id' => $bid, 'category_id' => $request->category_id]);
      $category_brand->category_id = $request->category_id;
      if($request->lang == env("DEFAULT_LANGUAGE")) {
        $category_brand->description = $request->descriptions[$key];
      }
      $category_brand->save();

      $category_brand_translation = CategoryBrandTranslation::firstOrNew(['lang' => $request->lang, 'category_brand_id' => $category_brand->id]);
      $category_brand_translation->description = $request->descriptions[$key];
      $category_brand_translation->save();
    }


    flash(translate("Category's brand descriptions has been updated successfully"))->success();
    return back();
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id){
    $category = Category::findOrFail($id);

    // Category Translations Delete
    foreach ($category->category_translations as $key => $category_translation) {
      $category_translation->delete();
    }

    foreach (Product::where('category_id', $category->id)->get() as $product) {
      $product->category_id = null;
      $product->save();
    }

    CategoryUtility::delete_category($id);

    flash(translate('Category has been deleted successfully'))->success();
    return redirect()->route('categories.index');
  }

  public function updateForSale(Request $request){
    $category = Category::findOrFail($request->id);
    $category->for_sale = $request->status;
    if($category->save()){
      return 1;
    }
    return 0;
  }

  public function updateFeatured(Request $request){
    $category = Category::findOrFail($request->id);
    $category->featured = $request->status;
    if($category->save()){
      return 1;
    }
    return 0;
  }

  public function updateShowB2B(Request $request){
    $category = Category::findOrFail($request->id);
    $category->show_b2b = $request->status;
    if($category->save()){
      return 1;
    }
    return 0;
  }

  public function updateShowHeader(Request $request){
    $category = Category::findOrFail($request->id);
    $category->show_header = $request->status;
    if($category->save()){
      return 1;
    }
    return 0;
  }
}
