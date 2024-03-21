<?php

namespace App\Models;

use App\Models\CanteenProductCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CanteenPurchaseDelivery extends Model
{

    use SoftDeletes;

    protected $table = 'canteen_purchase_deliveries';

    protected $fillable = ['canteen_app_user_id', 'canteen_purchase_id', 'canteen_location_id'];

    public function canteen_purchase(){
        return $this->belongsTo(CanteenPurchase::class);
    }

    public function canteen_location(){
        return $this->belongsTo(CanteenLocation::class);
    }

    public function canteen_user(){
        return $this->belongsTo(CanteenAppUser::class);
    }




}
