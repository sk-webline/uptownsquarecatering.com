<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Models\Category
 *
 * @property int $id
 * @property int $canteen_product_category_id
 * @property string $name
 * @property decimal $price
 * @property integer $thumbnail_img
 * @property tinyint $status
 */

class CanteenProduct extends Model
{

    protected $table = 'canteen_products';
    protected $fillable = ['canteen_product_category_id','name','price','thumbnail_img','status'];

    protected static function boot()
    {
        parent::boot();

    }
    public function getTranslation($field = '', $lang = false){
        $lang = $lang == false ? App::getLocale() : $lang;
        $product_translation = $this->hasMany(CanteenProductTranslation::class)->where('lang', $lang)->first();
        return $product_translation != null ? $product_translation->$field : $this->$field;
    }

    /**
     * Get the organisation that owns these settings.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CanteenProductCategory::class);
    }

    /**
     * Get the translations of this product
     */
    public function translations(): HasMany
    {
        return $this->hasMany(CanteenProductTranslation::class);
    }

    /**
     * Get the canteen menu references of this product
     */
    public function canteen_menus(): HasMany
    {
        return $this->hasMany(CanteenMenu::class);
    }

}
