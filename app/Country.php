<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /*protected static function boot() {
        parent::boot();

        static::addGlobalScope('post_office_country', function (Builder $builder)  {
            $builder->whereNotNull('post_name_el')->whereNotNull('post_name_en');
        });
    }*/

    public static function getActiveCountriesForShipping()
    {
        return self::where('status', 1)->whereNotNull('post_name_el')->whereNotNull('post_name_en')->get();
    }
}
