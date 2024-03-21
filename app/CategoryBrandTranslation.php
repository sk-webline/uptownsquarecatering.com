<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryBrandTranslation extends Model
{
    protected $fillable = ['name', 'lang', 'category_brand_id'];

    public function category_brand(){
    	return $this->belongsTo(CategoryBrand::class);
    }
}
