<?php

namespace App\Http\Controllers;

use App\Models\CanteenLanguage;
use App\Models\CanteenProductCategory;
use App\Models\CanteenProductCategoryTranslation;
use Illuminate\Http\Request;
use Auth;


class CanteenProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;

        if ($request->has('search')) {
            $sort_search = $request->search;
            $categories = CanteenProductCategory::where('name', 'like', '%' . $sort_search . '%')->paginate(10);

        }else{
            $categories = CanteenProductCategory::paginate(10);
        }


        return view('backend.canteen.categories.index', compact('categories', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.canteen.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

//        dd($request->all());

        $category = new CanteenProductCategory();
        $category->name = $request->name;

        $canteen_languages = CanteenLanguage::all();

        if($category->save()){

            foreach ($canteen_languages as $lang){
                foreach ($request->all() as $key => $name){

                    if($key == $lang->code && $name!=null){
                        $translation = new CanteenProductCategoryTranslation();
                        $translation->lang = $lang->code;
                        $translation->name = $name;
                        $translation->canteen_product_category_id = $category->id;
                        $translation->save();
                        break;
                    }
                }
            }

        } else {
            flash(translate('Sorry! Something went wrong!'))->error();
        }

        return redirect()->route('canteen_product_categories.index');

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
    public function edit($id, Request $request)
    {

        $category = CanteenProductCategory::findOrFail($id);

        return view('backend.canteen.categories.edit', compact('category'));
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

        $category = CanteenProductCategory::findOrFail($id);

        $category->name = $request->name;

        $canteen_languages = CanteenLanguage::all();

        if($category->save()){

            foreach ($canteen_languages as $lang){
                foreach ($request->all() as $key => $name){

                    if($key == $lang->code && $name!=null){
                        $translation = CanteenProductCategoryTranslation::where('canteen_product_category_id', $category->id)->where('lang', '=', $lang->code)->first();
                        if($translation==null){
                            $translation = new CanteenProductCategoryTranslation();
                        }
                        $translation->lang = $lang->code;
                        $translation->name = $name;
                        $translation->canteen_product_category_id = $category->id;
                        $translation->save();
                        break;
                    }
                }
            }

            flash(translate('Product Category has been updated successfully'))->success();


        } else {
            flash(translate('Sorry! Something went wrong!'))->error();

        }

        return redirect()->route('canteen_product_categories.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = CanteenProductCategory::findorfail($id);

        if(count($category->products)>0){

            $error_message = toUpper($category->name) . ': '. translate('If you want to delete this category first remove all products listed below');

            $errors_table['delete'] = $error_message;

            foreach ($category->products as $product){
                $errors_table[$product->id] =$product->name;
            }

            return back()->withErrors($errors_table);
        }

        if($category->delete()){
            flash(translate('Product Category has been deleted successfully'))->success();

        } else {
            flash(translate('Sorry! Something went wrong!'))->error();

        }

        return redirect()->back();
    }



}
