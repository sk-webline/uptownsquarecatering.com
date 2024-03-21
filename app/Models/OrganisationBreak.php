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
 * @property string $name
 */

class OrganisationBreak extends Model
{

    protected $table = 'organisation_breaks';

//    use SoftDeletes;

    protected $fillable = ['break_num','organisation_id', 'canteen_setting_id', 'hour_from', 'hour_to'];

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
     * Get the organisation that owns these settings.
     */
    public function canteen_setting(): BelongsTo
    {
        return $this->belongsTo(CanteenSetting::class);
    }

    /**
     * Get the canteen menu references of this product
     */
    public function canteen_menus(): HasMany
    {
        return $this->hasMany(CanteenMenu::class);
    }


}
