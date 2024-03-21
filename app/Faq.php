<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App;

class Faq extends Model
{
    use SoftDeletes;

  public function getTranslation($field = '', $lang = false){
    $lang = $lang == false ? App::getLocale() : $lang;
    $translation = $this->hasMany(FaqTranslation::class)->where('lang', $lang)->first();
    return $translation != null ? $translation->$field : $this->$field;
  }

  public function faq_translations(){
    return $this->hasMany(FaqTranslation::class);
  }

}
