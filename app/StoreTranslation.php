<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreTranslation extends Model
{
  protected $fillable = ['name', 'lang', 'store_id'];

  public function store(){
    return $this->belongsTo(Store::class);
  }
}
