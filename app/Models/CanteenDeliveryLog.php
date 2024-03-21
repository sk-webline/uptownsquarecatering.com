<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Models\Category
 *
 */

class CanteenDeliveryLog extends Model
{

    protected $table = 'canteen_delivery_logs';

    protected $fillable = ['type','canteen_app_user_id','canteen_purchase_id','canteen_location_id','canteen_cashier_id'];

    protected static function boot()
    {
        parent::boot();

    }


}
