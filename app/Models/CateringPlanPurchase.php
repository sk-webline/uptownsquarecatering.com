<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Card
 *
 * @property int $id
 * @property int $organisation_setting_id
 * @property string $name
 * @property string $description
 * @property date $from_date
 * @property date $to_date
 * @property tinyint $active
 * @property int $num_of_working_days
 * @property date $publish_date
 * @property decimal $price
 * @property int $snack_num
 * @property int $meal_num
 */

class CateringPlanPurchase extends Model
{

    use SoftDeletes;

    protected $fillable = ['user_id', 'organisation_setting_id', 'catering_plan_id','card_id','from_date', 'to_date','snack_quantiy', 'meal_quantiy','price', 'num_of_days','active_days_january', 'active_days_february','active_days_march','active_days_april', 'active_days_may','active_days_june', 'active_days_july','active_days_august', 'active_days_september','active_days_october', 'active_days_november','active_days_december'];

    protected static function boot()
    {
        parent::boot();

    }

    /**
     * Get the organisation that owns these settings.
     */
    public function organisation_setting(): BelongsTo
    {
        return $this->belongsTo(OrganisationSetting::class);
    }

    /**
     * Get the organisation that owns these settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the organisation that owns these settings.
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }




}
