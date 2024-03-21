<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreCityTranslation extends Model
{
  protected $fillable = ['name', 'lang', 'store_city_id'];

  public function store_city(){
    return $this->belongsTo(StoreCity::class);
  }
}
