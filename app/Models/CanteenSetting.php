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
 * @property int $id
 * @property int $organisation_id
 * @property int $max_snack_quantity
 * @property int $max_meal_quantity
 * @property tinyint $absence
 * @property int $absence_days_num
 * @property int $preorder_days_num
 * @property json $working_week_days
 * @property json $working_days_january
 * @property json $working_days_february
 * @property json $working_days_march
 * @property json $working_days_april
 * @property json $working_days_may
 * @property json $working_days_june
 * @property json $working_days_july
 * @property json $working_days_august
 * @property json $working_days_september
 * @property json $working_days_october
 * @property json $working_days_november
 * @property json $working_days_december
 */

class CanteenSetting extends Model
{

    protected $table = 'canteen_settings';

    use SoftDeletes;

    protected $fillable = ['organisation_id','date_from','date_to', 'minimum_preorder_minutes', 'minimum_cancellation_minutes', 'working_week_days','working_days_january', 'working_days_february','working_days_march',
        'working_days_april','working_days_may', 'working_days_june','working_days_july', 'working_days_august', 'working_days_september','working_days_october', 'working_days_november','working_days_december'  ];

    protected static function boot()
    {
        parent::boot();

    }

    /**
     * Get the organisation that owns these settings.
     */
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get the organisation setting of this organisation.
     */
    public function extra_days(): HasMany
    {
        return $this->hasMany(CanteenExtraDay::class);
    }

    /**
     * Get the organisation setting of this organisation.
     */
    public function breaks(): HasMany
    {
        return $this->hasMany(OrganisationBreak::class);
    }

    /**
     * Get the canteen menu references of this setting
     */
    public function canteen_menus(): HasMany
    {
        return $this->hasMany(CanteenMenu::class);
    }

}
