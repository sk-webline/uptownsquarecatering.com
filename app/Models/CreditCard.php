<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\CouponUsage
 *

 * @mixin \Eloquent
 */
class CreditCard extends Model
{
    use SoftDeletes;
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
