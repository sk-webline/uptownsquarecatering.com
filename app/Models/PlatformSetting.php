<?php

namespace App\Models;

use App\FlashDealProduct;
use App\ProductTax;
use App\User;
use App\Wishlist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $type
 * @property int $value
 */

class PlatformSetting extends Model
{
//    protected $fillable = ['type', 'value'];

    protected static function boot()
    {
        parent::boot();


    }

}
