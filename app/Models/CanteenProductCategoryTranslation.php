<?php

namespace App\Models;

use App\Models\CanteenProductCategory;
use Illuminate\Database\Eloquent\Model;

class CanteenProductCategoryTranslation extends Model
{
    protected $fillable = ['name', 'lang', 'canteen_product_category_id'];

    public function canteen_product_categories(){
        return $this->belongsTo(CanteenProductCategory::class);
    }
}
