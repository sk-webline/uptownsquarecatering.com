<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App;


/**
 * App\Models\Category
 *
 * @property int $id
 * @property int $name
 */

class CanteenProductCategory extends Model
{

    protected $table = 'canteen_product_categories';

    use SoftDeletes;

    protected $fillable = ['name'];

    protected static function boot()
    {
        parent::boot();

    }

    public function getTranslation($field = '', $lang = false){
        $lang = $lang == false ? App::getLocale() : $lang;
        $category_translation = $this->hasMany(CanteenProductCategoryTranslation::class)->where('lang', $lang)->first();
        return $category_translation != null ? $category_translation->$field : $this->$field;
    }


    /**
     * Get the organisation setting of this organisation.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(CanteenProductCategoryTranslation::class);
    }

    /**
     * Get the organisation setting of this organisation.
     */
    public function products(): HasMany
    {
        return $this->hasMany(CanteenProduct::class);
    }

}
