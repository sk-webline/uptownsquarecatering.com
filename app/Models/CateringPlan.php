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

class CateringPlan extends Model
{

    use SoftDeletes;

    protected $fillable = ['organisation_setting_id', 'name','description','from_date', 'to_date','active','num_of_working_days', 'publish_date','price', 'snack_num','meal_num'];

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




}
