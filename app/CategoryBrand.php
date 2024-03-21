<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;

class CategoryBrand extends Model
{
    protected $fillable = ['brand_id'];

    public function getTranslation($field = '', $lang = false){
        $lang = $lang == false ? App::getLocale() : $lang;
        $category_brand_translation = $this->hasMany(CategoryBrandTranslation::class)->where('lang', $lang)->first();
        return $category_brand_translation != null ? $category_brand_translation->$field : $this->$field;
    }

    public function category_translations(){
    	return $this->hasMany(CategoryBrandTranslation::class);
    }
}
