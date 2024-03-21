<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;
use Illuminate\Support\Facades\Redis;

class Brand extends Model
{
  public function getTranslation($field = '', $lang = false) {
    $lang = $lang == false ? App::getLocale() : $lang;
    $cache_key = "brand_translation_".$lang."_".$this->id."_".$field;
    if (Redis::exists($cache_key)) {
      return Redis::get($cache_key);
    }
    else {
      $brand_translation = $this->hasMany(BrandTranslation::class)->where('lang', $lang)->first();
      $return_value = $brand_translation != null ? $brand_translation->$field : $this->$field;
      Redis::setex($cache_key, config('cache.expiry.brand'), $return_value);
      return $return_value;
    }

//      $lang = $lang == false ? App::getLocale() : $lang;
//      $brand_translation = $this->hasMany(BrandTranslation::class)->where('lang', $lang)->first();
//      return $brand_translation != null ? $brand_translation->$field : $this->$field;
  }

  public function brand_translations(){
    return $this->hasMany(BrandTranslation::class);
  }

}
