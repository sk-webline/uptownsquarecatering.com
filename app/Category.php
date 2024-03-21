<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App;
use Illuminate\Support\Facades\Redis;

class Category extends Model
{
    protected static function boot() {
      parent::boot();
      if  (get_setting('show_webshop') != 'on') {
        static::addGlobalScope('getOnlyNotForSaleCategories', function (Builder $builder) {
          $builder->where('categories.for_sale', '0');
        });
      }
    }
    public function getTranslation($field = '', $lang = false){
      $lang = $lang == false ? App::getLocale() : $lang;
      $cache_key = "category_translation_".$lang."_".$this->id."_".$field;

      if (Redis::exists($cache_key)) {
        $return_value =  Redis::get($cache_key);
      }
      else {
        $category_translation = $this->hasMany(CategoryTranslation::class)->where('lang', $lang)->first();
        $return_value = $category_translation != null ? $category_translation->$field : $this->$field;
        Redis::setex($cache_key, config('cache.expiry.category'), $return_value);
      }

      return $return_value;
    }

    public function category_translations(){
    	return $this->hasMany(CategoryTranslation::class);
    }

    public function products(){
    	return $this->hasMany(Product::class);
    }
    public function countProducts(){
    	return Product::where('category_id', $this->id)->count();
    }

    public function classified_products(){
    	return $this->hasMany(CustomerProduct::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function childrenCategories()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('categories');
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
