<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;

class StoreCity extends Model
{
    public function getTranslation($field = '', $lang = false){
        $lang = $lang == false ? App::getLocale() : $lang;
        $store_city_translation = $this->hasMany(StoreCityTranslation::class)->where('lang', $lang)->first();
        return $store_city_translation != null ? $store_city_translation->$field : $this->$field;
    }

    public function store_city_translations(){
      return $this->hasMany(StoreCityTranslation::class);
    }
}
