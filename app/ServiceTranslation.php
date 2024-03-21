<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceTranslation extends Model
{
    protected $fillable = ['name', 'lang', 'service_id'];

    public function service(){
    	return $this->belongsTo(Service::class);
    }
}
