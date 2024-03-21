<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FaqTranslation extends Model
{
    protected $fillable = ['title', 'lang', 'faq_id'];

    public function faq(){
    	return $this->belongsTo(Faq::class);
    }
}
