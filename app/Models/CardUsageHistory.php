<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\CardUsageHistory
 *
 * @property int $id
 * @property int $catering_plan_purchases_id
 * @property int $topup_id
 * @property int $card_id
 * @property int $user_id
 * @property int $purchase_type
 */

class CardUsageHistory extends Model
{

    use SoftDeletes;

    protected $table = 'card_usage_history';

    protected $fillable = ['catering_plan_purchases_id', 'topup_id','card_id', 'purchase_type','user_id', 'location_id','cashier_id'];





}
