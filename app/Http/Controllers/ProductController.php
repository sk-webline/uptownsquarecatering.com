<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\ProductTranslation;
use App\ProductStock;
use App\Category;
use App\FlashDealProduct;
use App\ProductTax;
use App\ProductType;
use App\Language;
use Auth;
use App\SubSubCategory;
use PHPUnit\Util\Type;
use Session;
use Carbon\Carbon;
use ImageOptimizer;
use DB;
use CoreComponentRepository;
use Illuminate\Support\Str;
use Artisan;
use Illuminate\Support\Facades\Redis;

class ProductController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function admin_products(Request $request)
  {
    //CoreComponentRepository::instantiateShopRepository();

    $type = 'In House';
    $col_name = null;
    $query = null;
    $sort_search = null;

    $products = Product::where('added_by', 'admin');

    if ($request->type != null){
      $var = explode(",", $request->type);
      $col_name = $var[0];
      $query = $var[1];
      $products = $products->orderBy($col_name, $query);
      $sort_type = $request->type;
    }
    if ($request->search != null){
      $products = $products
        ->where('name', 'like', '%'.$request->search.'%');
      $sort_search = $request->search;
    }

    $products = $products->where('digital', 0)->orderBy('created_at', 'desc')->paginate(15);

    return view('backend.product.products.index', compact('products','type', 'col_name', 'query', 'sort_search'));
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function seller_products(Request $request)
  {
    $col_name = null;
    $query = null;
    $seller_id = null;
    $sort_search = null;
    $products = Product::where('added_by', 'seller');
    if ($request->has('user_id') && $request->user_id != null) {
      $products = $products->where('user_id', $request->user_id);
      $seller_id = $request->user_id;
    }
    if ($request->search != null){
      $products = $products
        ->where('name', 'like', '%'.$request->search.'%');
      $sort_search = $request->search;
    }
    if ($request->type != null){
      $var = explode(",", $request->type);
      $col_name = $var[0];
      $query = $var[1];
      $products = $products->orderBy($col_name, $query);
      $sort_type = $request->type;
    }

    $products = $products->where('digital', 0)->orderBy('created_at', 'desc')->paginate(15);
    $type = 'Seller';

    return view('backend.product.products.index', compact('products','type', 'col_name', 'query', 'seller_id', 'sort_search'));
  }

  public function all_products(Request $request)
  {
    $col_name = null;
    $query = null;
    $seller_id = null;
    $sort_search = null;
    $products_to_show = $request->products_to_show ?? 'all';
    $products = Product::whereNull('products.disable_date')->orderBy('created_at', 'desc');
    if ($request->has('user_id') && $request->user_id != null) {
      $products = $products->where('products.user_id', $request->user_id);
      $seller_id = $request->user_id;
    }
    if ($request->search != null){
      $products = $products->select('products.*')
        ->leftJoin('product_stocks', 'product_stocks.product_id', '=', 'products.id');
        $products = $products->where(function($query) use ($request) {
        $query->where('products.name', 'like', '%'.$request->search.'%')
          ->orWhere('products.barcode', 'like', '%'.$request->search.'%')
          ->orWhere('products.part_number', 'like', '%'.$request->search.'%')
          ->orWhere('product_stocks.part_number', 'like', '%'.$request->search.'%');
      });
      $sort_search = $request->search;
    }
    if ($request->type != null){
      $var = explode(",", $request->type);
      $col_name = $var[0];
      $query = $var[1];
      $products = $products->orderBy('products.'.$col_name, $query);
      $sort_type = $request->type;
    }
    if ($request->has('products_to_show') && $products_to_show != 'all') {
      if ($products_to_show == 'for_sale') {
        $products = $products->where('products.import_from_btms', 1);
      }
      elseif ($products_to_show == 'not_for_sale') {
        $products = $products->where('products.import_from_btms', 0);
      }
    }
    $products = $products->paginate(15);
    $type = 'All';

    return view('backend.product.products.index', compact('products','type', 'col_name', 'query', 'seller_id', 'sort_search', 'products_to_show'));
  }


  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $categories = Category::where('parent_id', 0)
      ->where('digital', 0)
      ->with('childrenCategories')
      ->get();

    $types = ProductType::all();

    return view('backend.product.products.create', compact('categories', 'types'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
//        dd($request->all());
    $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();

    $product = new Product;
    $product->name = $request->name;
    $product->subtitle = $request->subtitle;
    $product->added_by = $request->added_by;
    if(Auth::user()->user_type == 'seller'){
      $product->user_id = Auth::user()->id;
    }  else {
      $product->user_id = \App\User::where('user_type', 'admin')->first()->id;
    }
    $product->category_id = $request->category_id;
    $product->brand_id = $request->brand_id == 0 ? null : $request->brand_id;
    $product->type_id = $request->type_id;
    $product->current_stock = $request->current_stock ?? 1 ;
    $product->part_number = $request->part_number;
    $product->barcode = $request->barcode;
    $product->product_layout = $request->product_layout;

    if ($refund_request_addon != null && $refund_request_addon->activated == 1) {
      if ($request->refundable != null) {
        $product->refundable = 1;
      }
      else {
        $product->refundable = 0;
      }
    }
    $product->photos = $request->photos;
    $product->thumbnail_img = $request->thumbnail_img;
    $product->unit = $request->unit;
    $product->weight = $request->weight;
    $product->min_qty = $request->min_qty;
    $product->low_stock_quantity = $request->low_stock_quantity;
    $product->stock_visibility_state = $request->stock_visibility_state;

    $tags = array();
    if($request->tags[0] != null){
      foreach (json_decode($request->tags[0]) as $key => $tag) {
        array_push($tags, $tag->value);
      }
    }
    $product->tags = implode(',', $tags);

    $product->short_description = $request->short_description;
    $product->description = $request->description;
    $product->video_provider = $request->video_provider;
    $product->video_link = $request->video_link;
    $product->unit_price = $request->unit_price;
    $product->wholesale_price = $request->wholesale_price;
    $product->purchase_price = $request->purchase_price;
//        $product->tax = $request->tax;
//        $product->tax_type = $request->tax_type;
    $product->discount = $request->discount;
    $product->discount_type = $request->discount_type;
    $product->shipping_type = $request->shipping_type;
    $product->est_shipping_days  = $request->est_shipping_days;

    if ($request->has('shipping_type')) {
      if($request->shipping_type == 'free'){
        $product->shipping_cost = 0;
      }
      elseif ($request->shipping_type == 'flat_rate') {
        $product->shipping_cost = $request->flat_shipping_cost;
      }
      elseif ($request->shipping_type == 'product_wise') {
        $product->shipping_cost = json_encode($request->shipping_cost);
      }
    }
    if ($request->has('is_quantity_multiplied')) {
      $product->is_quantity_multiplied = 1;
    }

    $product->meta_title = $request->meta_title;
    $product->meta_description = $request->meta_description;

    if($request->has('meta_img')){
      $product->meta_img = $request->meta_img;
    } else {
      $product->meta_img = $product->thumbnail_img;
    }

    if($product->meta_title == null) {
      $product->meta_title = $product->name;
    }

    if($product->meta_description == null) {
      $product->meta_description = strip_tags($product->description);
    }

    if($product->meta_img == null) {
      $product->meta_img = $product->thumbnail_img;
    }

    if($request->hasFile('pdf')){
      $product->pdf = $request->pdf->store('uploads/products/pdf');
    }

    $product->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.Str::random(5);

    if($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0){
      $product->colors = json_encode($request->colors);
    }
    else {
      $colors = array();
      $product->colors = json_encode($colors);
    }

    $choice_options = array();

    if($request->has('choice_no')){
      foreach ($request->choice_no as $key => $no) {
        $str = 'choice_options_'.$no;

        $item['attribute_id'] = $no;

        $data = array();
        foreach (json_decode($request[$str][0]) as $key => $eachValue) {
          array_push($data, $eachValue->value);
        }

        $item['values'] = $data;
        array_push($choice_options, $item);
      }
    }

    if (!empty($request->choice_no)) {
      $product->attributes = json_encode($request->choice_no);
    }
    else {
      $product->attributes = json_encode(array());
    }

    $product->choice_options = json_encode($choice_options, JSON_UNESCAPED_UNICODE);

    $product->published = 1;
    if($request->button == 'unpublish' || $request->button == 'draft') {
      $product->published = 0;
    }

    if ($request->has('cash_on_delivery')) {
      $product->cash_on_delivery = 1;
    }
    if ($request->has('outlet')) {
      $product->outlet = 1;
    }
    if ($request->has('used')) {
      $product->used = 1;
    }
    if ($request->has('featured')) {
      $product->featured = 1;
    }
    if ($request->has('cyprus_shipping_only')) {
      $product->cyprus_shipping_only = 1;
    }
    if ($request->has('todays_deal')) {
      $product->todays_deal = 1;
    }
    $product->cash_on_delivery = 0;
    if ($request->cash_on_delivery) {
      $product->cash_on_delivery = 1;
    }
    //$variations = array();

    $product->save();

    //VAT & Tax
    if($request->tax_id) {
      foreach ($request->tax_id as $key => $val) {
        $product_tax = new ProductTax;
        $product_tax->tax_id = $val;
        $product_tax->product_id = $product->id;
        $product_tax->tax = $request->tax[$key];
        $product_tax->tax_type = $request->tax_type[$key];
        $product_tax->save();
      }
    }
    //Flash Deal
    if($request->flash_deal_id) {
      $flash_deal_product = new FlashDealProduct;
      $flash_deal_product->flash_deal_id = $request->flash_deal_id;
      $flash_deal_product->product_id = $product->id;
      $flash_deal_product->discount = $request->flash_discount;
      $flash_deal_product->discount_type = $request->flash_discount_type;
      $flash_deal_product->save();
    }

    //combinations start
    $options = array();
    if($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
      $colors_active = 1;
      array_push($options, $request->colors);
    }

    if($request->has('choice_no')){
      foreach ($request->choice_no as $key => $no) {
        $name = 'choice_options_'.$no;
        $data = array();
        foreach (json_decode($request[$name][0]) as $key => $item) {
          array_push($data, $item->value);
        }
        array_push($options, $data);
      }
    }

    //Generates the combinations of customer choice options
    $combinations = makeCombinations($options);
    if(count($combinations[0]) > 0){
      $product->variant_product = 1;
      foreach ($combinations as $key => $combination){
        $str = '';
        foreach ($combination as $key => $item){
          if($key > 0 ){
            $str .= '-'.str_replace(' ', '', $item);
          }
          else{
            if($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0){
//              $color_name = \App\Color::where('code', $item)->first()->name;
//              $str .= $color_name;
              $str .= $item;
            }
            else{
              $str .= str_replace(' ', '', $item);
            }
          }
        }
        $product_stock = ProductStock::where('product_id', $product->id)->where('variant', $str)->first();
        if($product_stock == null){
          $product_stock = new ProductStock;
          $product_stock->product_id = $product->id;
        }

        $product_stock->variant = $str;
        $product_stock->price = $request['price_'.str_replace('.', '_', $str)];
        $product_stock->whole_price = $request['whole_price_'.str_replace('.', '_', $str)];
//        $product_stock->sku = $request['sku_'.str_replace('.', '_', $str)];
        $product_stock->sku = $request['part_number_'.str_replace('.', '_', $str)];
        $product_stock->part_number = $request['part_number_'.str_replace('.', '_', $str)];
        $product_stock->qty = $request['qty_'.str_replace('.', '_', $str)];
        $product_stock->weight = $request['weight_'.str_replace('.', '_', $str)];
        $product_stock->image = $request['img_'.str_replace('.', '_', $str)];
        $product_stock->save();
      }
    }
    else{
      $product_stock = new ProductStock;
      $product_stock->product_id = $product->id;
      $product_stock->price = $request->unit_price ?? 0;
      $product_stock->whole_price = $request->wholesale_price ?? 0;
      $product_stock->qty = $request->current_stock ?? 1 ;
      $product_stock->weight = $request->weight;
      $product_stock->part_number = $request->part_number;
      $product_stock->sku         = $request->part_number;
      $product_stock->save();
    }
    //combinations end

    $product->save();

    // Product Translations
    $product_translation = ProductTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'product_id' => $product->id]);
    $product_translation->name = $request->name;
    $product_translation->subtitle = $request->subtitle;
    $product_translation->unit = $request->unit;
    $product_translation->short_description = $request->short_description;
    $product_translation->description = $request->description;
    $product_translation->save();

    flash(translate('Product has been inserted successfully'))->success();

    Artisan::call('view:clear');
    Artisan::call('cache:clear');

    if(Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff'){
      return redirect()->route('products.admin');
    }
    else{
      if(\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated){
        $seller = Auth::user()->seller;
        $seller->remaining_uploads -= 1;
        $seller->save();
      }
      return redirect()->route('seller.products');
    }
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
  public function admin_product_edit(Request $request, $id)
  {
    $product = Product::findOrFail($id);
    $lang = $request->lang;
    $tags = json_decode($product->tags);
    $categories = Category::where('parent_id', 0)
      ->where('digital', 0)
      ->with('childrenCategories')
      ->get();
    $types = ProductType::all();
    return view('backend.product.products.edit', compact('product', 'categories', 'types', 'tags','lang'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function seller_product_edit(Request $request, $id)
  {
    $product = Product::findOrFail($id);
    $lang = $request->lang;
    $tags = json_decode($product->tags);
    $categories = Category::all();
    $types = ProductType::all();
    return view('backend.product.products.edit', compact('product', 'categories', 'types', 'tags','lang'));
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

    $refund_request_addon       = \App\Addon::where('unique_identifier', 'refund_request')->first();
    $product                    = Product::findOrFail($id);
    $product->type_id           = $request->type_id;
    $product->category_id = $request->category_id;
    if (!$product->import_from_btms) { // disable for btms products
        $product->brand_id = $request->brand_id == 0 ? null : $request->brand_id;
        $product->current_stock = $request->current_stock;
        $product->part_number = $request->part_number;
        $product->barcode = $request->barcode;
    }
    $product->product_layout    = $request->product_layout;
    $product->cash_on_delivery = 0;
    $product->outlet = 0;
    $product->used = 0;
    $product->featured = 0;
    $product->cyprus_shipping_only = 0;
    $product->todays_deal = 0;
    $product->is_quantity_multiplied = 0;


    if ($refund_request_addon != null && $refund_request_addon->activated == 1) {
      if ($request->refundable != null) {
        $product->refundable = 1;
      }
      else {
        $product->refundable = 0;
      }
    }

    if($request->lang == env("DEFAULT_LANGUAGE")){
      $product->name = $request->name;
      $product->subtitle = $request->subtitle;
      $product->unit = $request->unit;
      $product->short_description = $request->short_description;
      $product->description = $request->description;
      $product->slug = strtolower($request->slug);
    }

    $product->photos                 = $request->photos;
    $product->thumbnail_img          = $request->thumbnail_img;
    $product->weight                 = $request->weight;
    $product->min_qty                = $request->min_qty;
    $product->low_stock_quantity     = $request->low_stock_quantity;
    $product->stock_visibility_state = $request->stock_visibility_state;

    $tags = array();
    if($request->tags[0] != null){
      foreach (json_decode($request->tags[0]) as $key => $tag) {
        array_push($tags, $tag->value);
      }
    }
    $product->tags           = implode(',', $tags);

    $product->video_provider = $request->video_provider;
    $product->video_link     = $request->video_link;
    if (!$product->import_from_btms) { // disable for btms products
        $product->unit_price = $request->unit_price;
        $product->wholesale_price = $request->wholesale_price;
        $product->purchase_price = $request->purchase_price;
        $product->discount       = $request->discount;
    }
//        $product->tax            = $request->tax;
//        $product->tax_type       = $request->tax_type;
    $product->shipping_type  = $request->shipping_type;
    $product->est_shipping_days  = $request->est_shipping_days;
    if ($request->has('shipping_type')) {
      if($request->shipping_type == 'free'){
        $product->shipping_cost = 0;
      }
      elseif ($request->shipping_type == 'flat_rate') {
        $product->shipping_cost = $request->flat_shipping_cost;
      }
      elseif ($request->shipping_type == 'product_wise') {
        $product->shipping_cost = json_encode($request->shipping_cost);
      }
    }

    if ($request->has('is_quantity_multiplied')) {
      $product->is_quantity_multiplied = 1;
    }
    if ($request->has('cash_on_delivery')) {
      $product->cash_on_delivery = 1;
    }
    if ($request->has('outlet')) {
      $product->outlet = 1;
    }
    if ($request->has('used')) {
      $product->used = 1;
    }
    if ($request->has('featured')) {
      $product->featured = 1;
    }
    if ($request->has('cyprus_shipping_only')) {
      $product->cyprus_shipping_only = 1;
    }
    if ($request->has('published')) {
      $product->published = 1;
    }
    if ($request->has('todays_deal')) {
      $product->todays_deal = 1;
    }

    $product->discount_type     = $request->discount_type;
    $product->meta_title        = $request->meta_title;
    $product->meta_description  = $request->meta_description;
    $product->meta_img          = $request->meta_img;

    if($product->meta_title == null) {
      $product->meta_title = $product->name;
    }

    if($product->meta_description == null) {
      $product->meta_description = strip_tags($product->description);
    }

    if($product->meta_img == null) {
      $product->meta_img = $product->thumbnail_img;
    }

    $product->pdf = $request->pdf;
    if (!$product->import_from_btms) { // disable for btms products
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $product->colors = json_encode($request->colors);
        } else {
            $colors = array();
            $product->colors = json_encode($colors);
        }

        $choice_options = array();

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;

                $item['attribute_id'] = $no;

                $data = array();
                foreach (json_decode($request[$str][0]) as $key => $eachValue) {
                    array_push($data, $eachValue->value);
                }

                $item['values'] = $data;
                array_push($choice_options, $item);
            }
        }

        foreach ($product->stocks as $key => $stock) {
            $stock->delete();
        }

        if (!empty($request->choice_no)) {
            $product->attributes = json_encode($request->choice_no);
        } else {
            $product->attributes = json_encode(array());
        }

        $product->choice_options = json_encode($choice_options, JSON_UNESCAPED_UNICODE);


        //combinations start
        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        }

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $data = array();
                foreach (json_decode($request[$name][0]) as $key => $item) {
                    array_push($data, $item->value);
                }
                array_push($options, $data);
            }
        }

        $combinations = makeCombinations($options);

        if (count($combinations[0]) > 0) {
            $product->variant_product = 1;
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $key => $item) {
                    if ($key > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
//                            $color_name = \App\Color::where('code', $item)->first()->name;
//                            $str .= $color_name;
                            $str .= $item;
                        } else {
                            $str .= str_replace(' ', '', $item);
                        }
                    }
                }

                $product_stock = ProductStock::where('product_id', $product->id)->where('variant', $str)->first();
                if ($product_stock == null) {
                    $product_stock = new ProductStock;
                    $product_stock->product_id = $product->id;
                }

                $product_stock->variant = $str;
                $product_stock->price = $request['price_' . str_replace('.', '_', $str)];
                $product_stock->whole_price = $request['whole_price_' . str_replace('.', '_', $str)];
//                $product_stock->sku = $request['sku_' . str_replace('.', '_', $str)];
                $product_stock->sku = $request['part_number_' . str_replace('.', '_', $str)];
                $product_stock->part_number = $request['part_number_' . str_replace('.', '_', $str)];
                $product_stock->qty = $request['qty_' . str_replace('.', '_', $str)];
                $product_stock->weight = $request['weight_'.str_replace('.', '_', $str)];
                $product_stock->image = $request['img_' . str_replace('.', '_', $str)];

                $product_stock->save();
            }
        } else {
            $product_stock = new ProductStock;
            $product_stock->product_id = $product->id;
            $product_stock->price = $request->unit_price;
            $product_stock->whole_price = $request->wholesale_price ?? $request->unit_price;
            $product_stock->qty = $request->current_stock ?? 0;
            $product_stock->weight = $request->weight;
            $product_stock->part_number = $request->part_number;
            $product_stock->sku = $request->part_number;
            $product_stock->save();
        }
    }
    else if($product->import_from_btms) {
      foreach ($product->stocks as $stock) {
        $stock->image = $request->{'img_' . $stock->variant};
        $stock->save();
      }
    }
    $product->save();

    //Flash Deal
    if($request->flash_deal_id) {
      if($product->flash_deal_product){
        $flash_deal_product = FlashDealProduct::findOrFail($product->flash_deal_product->id);
      } if(!$flash_deal_product) {
        $flash_deal_product = new FlashDealProduct;
      }
      $flash_deal_product->flash_deal_id = $request->flash_deal_id;
      $flash_deal_product->product_id = $product->id;
      $flash_deal_product->discount = $request->flash_discount;
      $flash_deal_product->discount_type = $request->flash_discount_type;
      $flash_deal_product->save();
//            dd($flash_deal_product);
    }

    //VAT & Tax
    if($request->tax_id) {
      ProductTax::where('product_id', $product->id)->delete();
      foreach ($request->tax_id as $key => $val) {
        $product_tax = new ProductTax;
        $product_tax->tax_id = $val;
        $product_tax->product_id = $product->id;
        $product_tax->tax = $request->tax[$key];
        $product_tax->tax_type = $request->tax_type[$key];
        $product_tax->save();
      }
    }

    // Product Translations
    $product_translation = ProductTranslation::firstOrNew(['lang' => $request->lang, 'product_id' => $product->id]);
    $product_translation->name = $request->name;
    $product_translation->subtitle = $request->subtitle;
    $product_translation->unit = $request->unit;
    $product_translation->short_description = $request->short_description;
    $product_translation->description = $request->description;
    $product_translation->save();

    flash(translate('Product has been updated successfully'))->success();

    $cache_key = 'product_*'.$product->id.'*';
    Redis::del(Redis::keys($cache_key.'*'));

    Artisan::call('view:clear');
    Artisan::call('cache:clear');

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
    $product = Product::findOrFail($id);
    foreach ($product->product_translations as $key => $product_translations) {
      $product_translations->delete();
    }
    if(Product::destroy($id)){

      flash(translate('Product has been deleted successfully'))->success();

      Artisan::call('view:clear');
      Artisan::call('cache:clear');

      if(Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff'){
        return redirect()->route('products.admin');
      }
      else{
        return redirect()->route('seller.products');
      }
    }
    else{
      flash(translate('Something went wrong'))->error();
      return back();
    }
  }

  /**
   * Duplicates the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function duplicate(Request $request, $id)
  {
    $product = Product::find($id);
    $product_new = $product->replicate();
    $product_new->slug = substr($product_new->slug, 0, -5).Str::random(5);

    if($product_new->save()){
      flash(translate('Product has been duplicated successfully'))->success();
      if(Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff'){
        if($request->type == 'In House')
          return redirect()->route('products.admin');
        elseif($request->type == 'Seller')
          return redirect()->route('products.seller');
        elseif($request->type == 'All')
          return redirect()->route('products.all');
      }
      else{
        return redirect()->route('seller.products');
      }
    }
    else{
      flash(translate('Something went wrong'))->error();
      return back();
    }
  }

  public function get_products_by_brand(Request $request)
  {
    $products = Product::where('brand_id', $request->brand_id)->get();
    return view('partials.product_select', compact('products'));
  }

  public function updateTodaysDeal(Request $request)
  {
    $product = Product::findOrFail($request->id);
    $product->todays_deal = $request->status;
    if($product->save()){
      return 1;
    }
    return 0;
  }

  public function updateCyprusShippingOnly(Request $request)
  {
    $product = Product::findOrFail($request->id);
    $product->cyprus_shipping_only = $request->status;
    if($product->save()){
      return 1;
    }
    return 0;
  }

  public function updatePublished(Request $request)
  {
    $product = Product::findOrFail($request->id);
    $product->published = $request->status;

    if($product->added_by == 'seller' && \App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated){
      $seller = $product->user->seller;
      if($seller->invalid_at != null && Carbon::now()->diffInDays(Carbon::parse($seller->invalid_at), false) <= 0){
        return 0;
      }
    }

    $product->save();
    return 1;
  }

  public function updateFeatured(Request $request)
  {
    $product = Product::findOrFail($request->id);
    $product->featured = $request->status;
    if($product->save()){
      return 1;
    }
    return 0;
  }

  public function updateSellerFeatured(Request $request)
  {
    $product = Product::findOrFail($request->id);
    $product->seller_featured = $request->status;
    if($product->save()){
      return 1;
    }
    return 0;
  }

  public function updateSubtitle(Request $request)
  {
    $category = Category::find($request->category);

    if($category == null) {
      return 2;
    } else {
      if($category->for_sale == 1){
        return 1;
      } else {
        return 0;
      }
    }

  }

  public function sku_combination(Request $request)
  {
    $options = array();
    if($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0){
      $colors_active = 1;
      array_push($options, $request->colors);
    }
    else {
      $colors_active = 0;
    }

    $unit_price = $request->unit_price;
    $whole_price = $request->wholesale_price;
    $product_name = $request->name;
    $part_number = $request->part_number;

    if($request->has('choice_no')){
      foreach ($request->choice_no as $key => $no) {
        $name = 'choice_options_'.$no;
        $data = array();
        foreach (json_decode($request[$name][0]) as $key => $item) {
          array_push($data, $item->value);
        }
        array_push($options, $data);
      }
    }

    $combinations = makeCombinations($options);
    return view('backend.product.products.sku_combinations', compact('combinations', 'unit_price', 'whole_price', 'colors_active', 'product_name', 'part_number'));
  }

  public function sku_combination_edit(Request $request)
  {
    $product = Product::findOrFail($request->id);

    $options = array();
    if($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0){
      $colors_active = 1;
      array_push($options, $request->colors);
    }
    else {
      $colors_active = 0;
    }

    $product_name = $request->name;
    $unit_price = $request->unit_price;
    $whole_price = $request->wholesale_price;

    if($request->has('choice_no')){
      foreach ($request->choice_no as $key => $no) {
        $name = 'choice_options_'.$no;
        $data = array();
        // TODO: George => Problem with choice_no input
        // UPDATE 10/10/2022: Νομίζω διορθώθηκε
        if (!$request->has($name)) continue;
        foreach (json_decode($request[$name][0]) as $key => $item) {
          array_push($data, $item->value);
        }
        array_push($options, $data);
      }
    }

    $combinations = makeCombinations($options);
    return view('backend.product.products.sku_combinations_edit', compact('combinations', 'unit_price', 'whole_price', 'colors_active', 'product_name', 'product'));
  }

}
