<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AppRefundDetail extends Model
{
    protected $guarded = [];

    protected $table = 'app_refund_details';

    public function order()
    {
        return $this->belongsTo(AppOrder::class);
    }

    public function order_detail()
    {
        return $this->belongsTo(AppOrderDetail::class);
    }


}
