<?php

namespace App\Http\Controllers;

use App\Models\CanteenExtraDay;
use App\Models\CanteenLanguage;
use App\Models\CanteenMenu;
use App\Models\CanteenProduct;
use App\Models\CanteenProductCategory;
use App\Models\CanteenProductTranslation;
use App\Models\CanteenSetting;
use App\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use App\Models\Organisation;

class CanteenProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $sort_search = null;
        $category_filter = null;
        $products = null;

        if($request->reset == null) {
            if ($request->has('search') && $request->search !=null) {
                $sort_search = $request->search;
                $products = CanteenProduct::where('canteen_products.name', 'like', '%' . $sort_search . '%');
            }

            if ($request->has('category_filter') && $request->category_filter !=null && $products==null) {
                $products = CanteenProduct::where('canteen_products.canteen_product_category_id', '=', $request->category_filter);
                $category_filter =  $request->category_filter;
            }elseif($request->has('category_filter') && $request->category_filter){
                $products = $products->where('canteen_products.canteen_product_category_id', '=', $request->category_filter);
                $category_filter =  $request->category_filter;
            }
        }

        if($products == null){
            $products = CanteenProduct::join('canteen_product_categories', 'canteen_product_categories.id', '=', 'canteen_products.canteen_product_category_id')
                ->select('canteen_products.*', 'canteen_product_categories.name as category')->paginate(10);

            // $products = CanteenProduct::paginate(10);
        }else{
            $products = $products->join('canteen_product_categories', 'canteen_product_categories.id', '=', 'canteen_products.canteen_product_category_id')
                ->select('canteen_products.*', 'canteen_product_categories.name as category')->paginate(10);
        }

        return view('backend.canteen.products.index', compact('products', 'sort_search' , 'category_filter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.canteen.products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'category' => 'required|numeric',
            'price' => 'required|numeric|min:0.1',
            'thumbnail_img' => 'required'
        ]);

        $product = new CanteenProduct();
        $product->name = $request->name;
        $product->canteen_product_category_id = $request->category;
        $product->price = $request->price;
        $product->thumbnail_img = $request->thumbnail_img;

        if ($product->save()) {

            $canteen_languages = CanteenLanguage::all();

            foreach ($canteen_languages as $lang){
                if($request->has($lang->code) && $request->{$lang->code} !=null){

                    $translation = new CanteenProductTranslation();
                    $translation->lang = $lang->code;
                    $translation->name = $request->{$lang->code};
                    $translation->canteen_product_id = $product->id;
                    $translation->save();
                }
            }

            flash(translate('Canteen Product created successfully'))->success();
            return redirect()->route('canteen_products.index');


        } else {
            flash(translate('Sorry! Something went wrong!'))->error();
            return redirect()->back();

        }

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

        $product = CanteenProduct::find($id);

        if($product==null){
            flash(translate('Sorry! Something went wrong!'))->error();
            return back();
        }

        return view('backend.canteen.products.edit', compact('product'));


    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required',
            'category' => 'required|numeric',
            'price' => 'required|numeric|min:0.1',
            'thumbnail_img' => 'required'
        ]);

        $product = CanteenProduct::find($id);

        if($product==null){
            flash(translate('Sorry! Something went wrong!'))->error();
            return back();
        }

        $product->name = $request->name;
        $product->canteen_product_category_id = $request->category;
        $product->price = $request->price;
        $product->thumbnail_img = $request->thumbnail_img;

        if($request->has('status')){
            $product->status = 1;
        }else{
            $product->status = 0;
        }

        if ($product->save()) {

            $canteen_languages = CanteenLanguage::all();

            foreach ($canteen_languages as $lang){
                if($request->has($lang->code) && $request->{$lang->code} !=null){

                    $translation = CanteenProductTranslation::where('canteen_product_id', $product->id)->where('lang', '=', $lang->code)->first();

                    if($translation==null){
                        $translation = new CanteenProductTranslation();
                        $translation->lang = $lang->code;
                        $translation->canteen_product_id = $product->id;
                    }

                    $translation->name = $request->{$lang->code};
                    $translation->save();
                }
            }

            flash(translate('Canteen Product created successfully'))->success();
            return redirect()->route('canteen_products.index');


        } else {
            flash(translate('Sorry! Something went wrong!'))->error();
            return redirect()->back();

        }







    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $product = CanteenProduct::findorfail($id);

        $menus = CanteenMenu::where('canteen_product_id', $product->id)->pluck('canteen_setting_id');
        if(count($menus)>0){
            $error_message = translate('If you want to delete this product first remove it from all menus listed below');

            $errors_table['delete'] = $error_message;

            $error_canteen_settings = \App\Models\CanteenSetting::whereIn( 'id', $menus )->get();

            foreach ($error_canteen_settings as $error_canteen_setting){
                $errors_table[$error_canteen_setting->id] = $error_canteen_setting->organisation->name . ' - ' .date("d/m/Y", strtotime($error_canteen_setting->date_from)) . ' - ' . date("d/m/Y", strtotime($error_canteen_setting->date_to));
            }

            return back()->withErrors($errors_table);
        }

        $upload = Upload::find($product->thumbnail_img);
        if($upload!=null){
            $upload->delete();
        }

        foreach ($product->translations as $translations){
            $translations->delete();
        }

        $product->delete();

        flash(translate('Canteen Product deleted successfully'))->success();
        return redirect()->back();
    }


}
