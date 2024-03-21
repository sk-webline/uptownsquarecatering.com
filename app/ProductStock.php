<?php

namespace App;

use App\Models\Btms\GroupDiscounts;
use App\Models\Btms\ItemStock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProductStock extends Model
{
    protected $current_discount_perc;
    protected $current_discount_amount;

    protected $current_pricelist_id;

    public function __construct()
    {
      $this->current_discount_perc = 0;
      $this->current_discount_amount = 0;
      $this->current_pricelist_id = 0;
    }

    //
    public function product(){
    	return $this->belongsTo(Product::class);
    }

    public function getPriceForCurrentUserOLD($with_discount = true, $with_symbol = true, $with_vat = true)
    {
        $product = $this->product;
        $has_discount = ($product->discount != null && $product->discount != 0) ? 1 : 0;
        $this->current_pricelist_id = 18;
        if (isPartner() && $product->import_from_btms && $this->whole_price != null && !$has_discount) {
            $price = $this->whole_price;
        }
        else {
            $price = $this->price;
        }

        if (isPartner() && $product->import_from_btms && $product->discount == '0,00') {
            $account_discount_group = Auth::user()->btms_discount_group;
            if ($account_discount_group !== null) {
                $discount_group = GroupDiscounts::where('Discount Code', $account_discount_group)->where('Category Code', $product->category->btms_category_code)->first();
                if ($discount_group !== null) {
                    $discount = $discount_group->{'Discount Percentage'};
                    $discount_amount = ($price*$discount)/100;
                    $price -= $discount_amount;

                    $this->current_discount_perc = $discount;
                    $this->current_discount_amount = $discount_amount;
                    $this->current_pricelist_id = $this->whole_price != null ? 19 : 18;
                }
            }
        }
        elseif ($with_discount && $has_discount) {
                if($product->discount_type == 'percent') {
                  $discount_amount = ($price*$product->discount)/100;
                  $price -= $discount_amount;
                  $this->current_discount_perc = $product->discount;
                  $this->current_discount_amount = $discount_amount;
                }
                elseif($product->discount_type == 'amount') {
                  $price -= $product->discount;
                  $this->current_discount_amount = $product->discount;
                }
                $this->current_pricelist_id = 20;
        }

        if ($with_vat) {
            $price = calcVatPrice($price);
        }

        return format_price($price, $with_symbol);
    }

    public function getPriceForCurrentUser($with_discount = true, $with_symbol = true, $with_vat = true)
  {
    $product = $this->product;


    if (isPartner() && $product->import_from_btms && $this->whole_price != null) {
      $price = $this->whole_price;
      $this->current_pricelist_id = 19;
    }
    else {
      $this->current_pricelist_id = 18;
      $price = $this->price;
    }


    if (isPartner() && $product->import_from_btms && $this->clearance_price == null) {
      $account_discount_group = Auth::user()->btms_discount_group;
      if ($account_discount_group !== null) {
        $discount_group = GroupDiscounts::where('Discount Code', $account_discount_group)->where('Category Code', $product->category->btms_category_code)->first();
        if ($discount_group !== null) {
          $discount = $discount_group->{'Discount Percentage'};
          $discount_amount = ($price*$discount)/100;
          $price -= $discount_amount;

          $this->current_discount_perc = $discount;
          $this->current_discount_amount = $discount_amount;
        }
      }
    }
    elseif ($with_discount && $this->clearance_price !== null) {
      $price = $this->clearance_price;
      $this->current_discount_perc = 0;
      $this->current_discount_amount = 0;
      $this->current_pricelist_id = 20;
    }

    if ($with_vat) {
      $price = calcVatPrice($price);
    }

    return format_price($price, $with_symbol);
  }

    public function getCurrentDiscount()
    {
      return (object) [
        'amount' => $this->current_discount_amount,
        'percentage' => $this->current_discount_perc,
        'price_list_id' => $this->current_pricelist_id,
      ];
    }

    public function updateStock(): void
    {
      $item_stock = ItemStock::where('Item Code', $this->part_number)->first();
      if  ($item_stock != null) {
        $this->qty = $item_stock->{'Available'};
        $this->save();
      }
    }
}
