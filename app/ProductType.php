<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;
use Illuminate\Support\Facades\Redis;

class ProductType extends Model
{
  public function getTranslation($field = '', $lang = false){
      $lang = $lang == false ? App::getLocale() : $lang;

      $cache_key = "product_type_translation_".$lang."_".$this->id."_".$field;

    if (Redis::exists($cache_key)) {
      $return_value =  Redis::get($cache_key);
    }
    else {
      $product_type_translation = $this->hasMany(ProductTypeTranslation::class)->where('lang', $lang)->first();
      $return_value = $product_type_translation != null ? $product_type_translation->$field : $this->$field;
      Redis::setex($cache_key, config('cache.expiry.product'), $return_value);
    }

    return $return_value;
  }

  public function product_type_translations(){
    return $this->hasMany(ProductTypeTranslation::class);
  }

}
