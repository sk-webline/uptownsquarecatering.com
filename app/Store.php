<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;

class Store extends Model
{
  public function getTranslation($field = '', $lang = false){
    $lang = $lang == false ? App::getLocale() : $lang;
    $store_translation = $this->hasMany(StoreTranslation::class)->where('lang', $lang)->first();
    return $store_translation != null ? $store_translation->$field : $this->$field;
  }

  public function store_translations(){
    return $this->hasMany(StoreTranslation::class);
  }

  public function city() {
    return $this->belongsTo(StoreCity::class);
  }
}
