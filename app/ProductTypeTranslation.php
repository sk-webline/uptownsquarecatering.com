<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductTypeTranslation extends Model
{
    protected $fillable = ['product_type_id','name', 'lang'];

    public function type(){
      return $this->belongsTo(ProductType::class);
    }
}
