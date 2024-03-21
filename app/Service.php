<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;

class Service extends Model
{
    public function getTranslation($field = '', $lang = false){
        $lang = $lang == false ? App::getLocale() : $lang;
        $service_translation = $this->hasMany(ServiceTranslation::class)->where('lang', $lang)->first();
        return $service_translation != null ? $service_translation->$field : $this->$field;
    }

    public function service_translations(){
    	return $this->hasMany(ServiceTranslation::class);
    }
}
