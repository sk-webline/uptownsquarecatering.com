<?php

namespace App;

use App\Models\Btms\GroupDiscounts;
use App\Models\Btms\ItemStock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use phpDocumentor\Reflection\Types\Object_;

class Product extends Model {


    protected $current_discount_perc;
    protected $current_discount_amount;
    protected $current_pricelist_id;

    protected $fillable = [
        'name', 'added_by', 'user_id', 'category_id', 'brand_id', 'video_provider', 'video_link', 'unit_price',
        'purchase_price', 'unit', 'slug', 'colors', 'choice_options', 'variations', 'current_stock', 'thumbnail_img'
    ];

    public function __construct()
    {
      $this->current_discount_perc = 0;
      $this->current_discount_amount = 0;
      $this->current_pricelist_id = 0;
    }

  public function getTranslation($field = '', $lang = false) {
    $lang = $lang == false ? App::getLocale() : $lang;
    $cache_key = "product_translation_".$lang."_".$this->id."_".$field;
    if (Redis::exists($cache_key)) {
      return Redis::get($cache_key);
    }
    else {
      $product_translations = $this->hasMany(ProductTranslation::class)->where('lang', $lang)->first();
      $return_value = $product_translations != null ? $product_translations->$field : $this->$field;
      Redis::setex($cache_key, config('cache.expiry.product'), $return_value);
      return $return_value;
    }
  }

    public function product_translations() {
        return $this->hasMany(ProductTranslation::class);
    }

    public function category() {
      return $this->belongsTo(Category::class);
    }

    public function brand() {
        return $this->belongsTo(Brand::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function orderDetails() {
        return $this->hasMany(OrderDetail::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class)->where('status', 1);
    }

    public function wishlists() {
        return $this->hasMany(Wishlist::class);
    }

    public function stocks() {
        return $this->hasMany(ProductStock::class);
    }

    public function taxes() {
        return $this->hasMany(ProductTax::class);
    }

    public function flash_deal_product() {
        return $this->hasOne(FlashDealProduct::class);
    }

  /**
   * @param $with_discount
   * @param $with_symbol
   * @param $with_vat
   * @return object|string
   */
   public function getPriceForCurrentUserOLD($with_discount = true, $with_symbol = true, $with_vat = true)
    {
        $this->current_pricelist_id = 18;
        if(isPartner() && $this->import_from_btms) {
            $lowest_price = $this->wholesale_price;
            $highest_price = $this->wholesale_price;
        }
        else {
            $lowest_price = $this->unit_price;
            $highest_price = $this->unit_price;
        }

        if ($this->variant_product) {
            foreach ($this->stocks as $key => $stock) {
                if(isPartner() && $this->import_from_btms) {
                    if($lowest_price > $stock->whole_price){
                        $lowest_price = $stock->whole_price;
                    }
                    if($highest_price < $stock->whole_price){
                        $highest_price = $stock->whole_price;
                    }
                }
                else {
                    if ($lowest_price > $stock->price) {
                        $lowest_price = $stock->price;
                    }
                    if ($highest_price < $stock->price) {
                        $highest_price = $stock->price;
                    }
                }
            }
        }
        if (isPartner() && $this->import_from_btms && $this->discount == '0,00') {
            $account_discount_group = Auth::user()->btms_discount_group;
            if ($account_discount_group !== null) {
             $discount_group = GroupDiscounts::where('Discount Code', $account_discount_group)->where('Category Code', $this->category->btms_category_code)->first();
                if ($discount_group !== null) {
                    $discount = $discount_group->{'Discount Percentage'};

                    $discount_lowest_price = ($lowest_price*$discount)/100;
                    $discount_highest_price = ($highest_price*$discount)/100;

                    $lowest_price -= $discount_lowest_price;
                    $highest_price -= $discount_highest_price;

                    $this->current_discount_perc = $discount;
                    $this->current_discount_amount = ($discount_lowest_price == $discount_highest_price) ? $discount_lowest_price : 0;
                    if ($this->variant_product) {
                        $this->current_pricelist_id = 18;
                    }
                    else {
                        $this->current_pricelist_id = $this->wholesale_price < $highest_price ? 19 : 18;
                    }
                }
            }
        }
        elseif ($with_discount) {
            if ($this->discount != null && $this->discount != 0) {
                if($this->discount_type == 'percent'){
                    $discount_lowest_price = ($lowest_price*$this->discount)/100;
                    $discount_highest_price = ($highest_price*$this->discount)/100;

                    $lowest_price -= $discount_lowest_price;
                    $highest_price -= $discount_highest_price;

                    $this->current_discount_perc = $this->discount;
                    $this->current_discount_amount = ($discount_lowest_price == $discount_highest_price) ? $discount_lowest_price : 0;

                }
                elseif($this->discount_type == 'amount') {
                    $this->current_discount_amount = $this->discount;
                  $lowest_price -= $this->discount;
                  $highest_price -= $this->discount;
                }
              $this->current_pricelist_id = 20;
            }
        }
        $price_before_vat = $lowest_price;
        if ($with_vat) {
            $lowest_price = calcVatPrice($lowest_price);
            $highest_price = calcVatPrice($highest_price);
        }

        if($lowest_price == $highest_price){
            return format_price($lowest_price, $with_symbol);
        }
        else{
//            return (object) array('lowest_price' => format_price($lowest_price), 'highest_price' => format_price($highest_price));
            return format_price($lowest_price).' - '.format_price($highest_price);
        }
    }

  /**
   * @param $with_discount
   * @param $with_symbol
   * @param $with_vat
   * @return object|string
   */
  public function getPriceForCurrentUser($with_discount = true, $with_symbol = true, $with_vat = true)
  {

    if(isPartner() && $this->import_from_btms && $this->wholesale_price != null) {
      $lowest_price = $this->wholesale_price;
      $highest_price = $this->wholesale_price;
      $this->current_pricelist_id = 19;
    }
    else {
      $lowest_price = $this->unit_price;
      $highest_price = $this->unit_price;
      $this->current_pricelist_id = 18;
    }

    if ($this->variant_product) {
      foreach ($this->stocks as $key => $stock) {
        if(isPartner() && $this->import_from_btms && $stock->whole_price != null && $stock->clearance_price == null) {
          if($lowest_price > $stock->whole_price){
            $lowest_price = $stock->whole_price;
          }
          if($highest_price < $stock->whole_price){
            $highest_price = $stock->whole_price;
          }
        }
        else if ($this->import_from_btms && $stock->clearance_price !== null) {
          if($lowest_price > $stock->clearance_price){
            $lowest_price = $stock->clearance_price;
          }
          if($highest_price < $stock->clearance_price){
            $highest_price = $stock->clearance_price;
          }
        }
        else {
          if ($lowest_price > $stock->price) {
            $lowest_price = $stock->price;
          }
          if ($highest_price < $stock->price) {
            $highest_price = $stock->price;
          }
        }
      }
    }

    if (isPartner() && $this->import_from_btms && $this->clearance_price == null) {
      $account_discount_group = Auth::user()->btms_discount_group;
      if ($account_discount_group !== null) {
        $discount_group = GroupDiscounts::where('Discount Code', $account_discount_group)->where('Category Code', $this->category->btms_category_code)->first();
        if ($discount_group !== null) {
          $discount = $discount_group->{'Discount Percentage'};

          $discount_lowest_price = ($lowest_price*$discount)/100;
          $discount_highest_price = ($highest_price*$discount)/100;

          $lowest_price -= $discount_lowest_price;
          $highest_price -= $discount_highest_price;

          $this->current_discount_perc = $discount;
          $this->current_discount_amount = ($discount_lowest_price == $discount_highest_price) ? $discount_lowest_price : 0;
        }
      }
    }
    elseif ($with_discount && !$this->variant_product && $this->clearance_price !== null) {
      $lowest_price = $this->clearance_price;
      $highest_price = $this->clearance_price;
      $this->current_discount_perc = 0;
      $this->current_discount_amount = 0;
      $this->current_pricelist_id = 20;
    }

    if ($with_vat) {
      $lowest_price = calcVatPrice($lowest_price);
      $highest_price = calcVatPrice($highest_price);
    }

    if($lowest_price == $highest_price){
      return format_price($lowest_price, $with_symbol);
    }
    else{
//            return (object) array('lowest_price' => format_price($lowest_price), 'highest_price' => format_price($highest_price));
      return format_price($lowest_price).' - '.format_price($highest_price);
    }
  }

  /**
   * @return object
   */
  public function getCurrentDiscount()
    {
     return (object) [
       'amount' => $this->current_discount_amount,
       'percentage' => $this->current_discount_perc,
       'price_list_id' => $this->current_pricelist_id,
     ];
    }

  public function updateStock():void {
    $item_stock = ItemStock::where('Item Code', $this->part_number)->first();
    if  ($item_stock != null) {
      $this->current_stock = $item_stock->{'Available'};
      $this->save();
    }
  }
}
