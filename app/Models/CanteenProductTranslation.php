<?php

namespace App\Models;

use App\Models\CanteenProductCategory;
use Illuminate\Database\Eloquent\Model;

class CanteenProductTranslation extends Model
{
    protected $fillable = ['name', 'lang', 'canteen_product_id'];

    public function canteen_product_categories(){
        return $this->belongsTo(CanteenProduct::class);
    }
}
